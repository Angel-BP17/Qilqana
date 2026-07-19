<?php

namespace App\Services\Charge;

use App\Models\Charge;
use App\Models\LegalEntity;
use App\Models\NaturalPerson;
use App\Models\Representative;
use App\Models\Resolucion;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\PendingChargeNotification;
use App\Services\Charge\Contracts\ChargeServiceInterface;
use App\Services\Support\ImageService;
use App\Traits\HasChargeLogic;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ChargeService implements ChargeServiceInterface
{
    use HasChargeLogic;

    public function __construct(
        protected ImageService $imageService
    ) {}

    public function getAll(array $data): array
    {
        $user = $data['user'];

        $resolutionCharges = ($user?->hasRole('ADMINISTRADOR') || $user?->can('modulo resoluciones'))
            ? $this->getAllResolutionCharges($data['resolucion'], $user)
            : collect();

        return [
            'sentCharges' => $this->getAllSentCharges($data['sent'], $user),
            'receivedCharges' => $this->getAllReceivedCharges($data['received'], $user),
            'createdCharges' => $this->getAllCreatedCharges($data['created'], $user),
            'resolutionCharges' => $resolutionCharges,
            'resolutionChargesCount' => $resolutionCharges->count(),
            'signedCount' => $this->countSignedCharges(),
            'unsignedCount' => $this->countUnsignedCharges(),
            'users' => $this->usersToAssign($user),
            'periodOptions' => $this->getPeriodOptions($data['default_period']),
            'refreshIntervalSeconds' => (int) Setting::getValue('charges_refresh_interval', '5'),
            'canViewResolutionCharges' => ($user?->hasRole('ADMINISTRADOR') || $user?->can('modulo resoluciones')),
            'defaultPeriod' => $data['default_period'],
            'sentPeriod' => $data['sent']['period'],
            'receivedPeriod' => $data['received']['period'],
            'resolutionPeriod' => $data['resolucion']['period'],
            'createdPeriod' => $data['created']['period'],
        ];
    }

    public function create(array $data): bool
    {
        return DB::transaction(function () use ($data) {
            $user = $data['user'];
            $resolucionIds = $data['resolucion_ids'] ?? [];
            $cargoPara = $data['cargo_para'] ?? 'otros';

            $documentPath = null;
            if (! empty($data['document_file'])) {
                $documentPath = $data['document_file']->store('private/charges_documents', 'local');
            }

            // CASO 1: Destinatarios de la resolución ("Interesados de la resolución")
            if ($cargoPara === 'interesados_resolucion' && ! empty($resolucionIds)) {
                $resoluciones = Resolucion::with(['naturalPeople', 'legalEntities', 'users'])
                    ->whereIn('id', $resolucionIds)
                    ->get();

                $interesadosUnicos = [];

                foreach ($resoluciones as $res) {
                    foreach ($res->naturalPeople as $person) {
                        $key = 'App\Models\NaturalPerson_'.$person->id;
                        $interesadosUnicos[$key] = [
                            'tipo' => 'Persona Natural',
                            'model' => $person,
                            'assigned_to' => $user->id,
                        ];
                    }
                    foreach ($res->legalEntities as $entity) {
                        $key = 'App\Models\LegalEntity_'.$entity->id;
                        $interesadosUnicos[$key] = [
                            'tipo' => 'Persona Juridica',
                            'model' => $entity,
                            'assigned_to' => $user->id,
                        ];
                    }
                    foreach ($res->users as $u) {
                        $key = 'App\Models\User_'.$u->id;
                        $interesadosUnicos[$key] = [
                            'tipo' => 'Trabajador UGEL',
                            'model' => $u,
                            'assigned_to' => $u->id,
                        ];
                    }
                }

                if (empty($interesadosUnicos)) {
                    // Si no tiene interesados, crear uno genérico asignado al creador
                    $this->crearCargoFisico($user->id, $this->getChargePeriod(), $data['document_date'] ?? null, $user->id, $user, $data['asunto'], $documentPath, $resolucionIds);
                } else {
                    foreach ($interesadosUnicos as $item) {
                        $this->crearCargoFisico($user->id, $this->getChargePeriod(), $data['document_date'] ?? null, $user->id, $item['model'], $data['asunto'], $documentPath, $resolucionIds, $item['assigned_to']);
                    }
                }

                return true;
            }

            // CASO 2: Destinatarios ajenos ("Otros") - Array estructurado
            if ($cargoPara === 'otros' && ! empty($data['destinatarios'])) {
                foreach ($data['destinatarios'] as $dest) {
                    $interesado = $this->resolverDestinatarioIndividual($dest);
                    if ($interesado) {
                        $assignedTo = ($dest['tipo'] === 'Trabajador UGEL') ? ($dest['assigned_to'] ?? $user->id) : $user->id;
                        $this->crearCargoFisico($user->id, $this->getChargePeriod(), $data['document_date'] ?? null, $user->id, $interesado, $data['asunto'], $documentPath, $resolucionIds, $assignedTo);
                    }
                }

                return true;
            }

            // CASO 3: Retrocompatibilidad (Formato plano anterior o tests)
            if (! empty($data['tipo_interesado'])) {
                $interesado = null;
                $assignedTo = $user->id;

                if ($data['tipo_interesado'] === 'Persona Natural') {
                    $personId = $this->resolveNaturalPerson($data);
                    $interesado = NaturalPerson::find($personId);
                } elseif ($data['tipo_interesado'] === 'Persona Juridica') {
                    $entityId = $this->resolveLegalEntity($data);
                    $interesado = LegalEntity::find($entityId);
                } elseif ($data['tipo_interesado'] === 'Trabajador UGEL') {
                    $assignedTo = $data['assigned_to'] ?? $user->id;
                    $interesado = User::find($assignedTo);
                }

                if ($interesado) {
                    $this->crearCargoFisico($user->id, $this->getChargePeriod(), $data['document_date'] ?? null, $user->id, $interesado, $data['asunto'], $documentPath, $resolucionIds, $assignedTo);

                    return true;
                }
            }

            return false;
        });
    }

    public function update(array $data, int $id): bool
    {
        $model = Charge::findOrFail($id);
        if ($model->user_id === $data['user']->id && in_array($model->signature?->signature_status, ['firmado', 'rechazado'], true)) {
            return false;
        }

        return DB::transaction(function () use ($data, $model) {
            $assignedTo = $data['assigned_to'] ?? null;
            if ($model->signature?->signature_status === 'pendiente') {
                if ($data['tipo_interesado'] !== 'Trabajador UGEL') {
                    $assignedTo = $data['user']->id;
                }
                $model->signature()->update([
                    'assigned_to' => $assignedTo,
                    'signature_requested_at' => ($assignedTo && ! $model->signature->signature_requested_at) ? now() : $model->signature->signature_requested_at,
                ]);
            }

            $documentPath = $model->document_path;
            if (! empty($data['document_file'])) {
                if ($documentPath) {
                    Storage::disk('local')->delete($documentPath);
                }
                $documentPath = $data['document_file']->store('private/charges_documents', 'local');
            }

            $model->update([
                'document_date' => $data['document_date'] ?? null,
                'asunto' => $data['asunto'],
                'document_path' => $documentPath,
            ]);

            // Sincronizar resoluciones
            if (isset($data['resolucion_ids'])) {
                $model->resolucions()->sync($data['resolucion_ids']);
            }

            $this->syncInteresado($model, $data);

            return true;
        });
    }

    public function delete(array $data, int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $model = Charge::findOrFail($id);
            if ($model->signature) {
                $this->imageService->deleteIfExists($model->signature->signature_root);
                $this->imageService->deleteIfExists($model->signature->evidence_root);
            }

            if ($model->document_path) {
                Storage::disk('local')->delete($model->document_path);
            }

            return (bool) $model->delete();
        });
    }

    public function signStore(array $data, array $files, int $chargeId, int $userId): bool
    {
        return DB::transaction(function () use ($data, $files, $chargeId, $userId) {
            $charge = Charge::with('signature')->findOrFail($chargeId);
            $isTitular = (bool) ($data['titularidad'] ?? false);

            $signaturePath = "private/charges_signatures/charge_{$chargeId}.svg";
            Storage::disk('local')->put($signaturePath, $data['firma']);

            $cartaPoderPath = $charge->signature?->carta_poder_path;
            if (! $isTitular && ! empty($files['carta_poder'])) {
                $cartaPoderPath = $this->imageService->storeAndOptimize($files['carta_poder'], 'private/charges_poder', 50);
            }

            $evidencePath = $charge->signature?->evidence_root;
            if (! empty($files['evidence_root'])) {
                $this->imageService->deleteIfExists($evidencePath);
                $evidencePath = $this->imageService->storeAndOptimize($files['evidence_root'], 'private/charges_evidence');
            }

            $charge->signature()->updateOrCreate(['charge_id' => $chargeId], [
                'signature_status' => 'firmado',
                'signed_by' => $userId,
                'signature_root' => $signaturePath,
                'signature_completed_at' => now(),
                'titularidad' => $isTitular,
                'parentesco' => $isTitular ? null : ($data['parentesco'] ?? null),
                'carta_poder_path' => $isTitular ? null : $cartaPoderPath,
                'evidence_root' => $evidencePath,
                'evidence_location' => ! empty($data['evidence_location']) ? json_decode($data['evidence_location'], true) : null,
            ]);

            return true;
        });
    }

    public function reject(array $data, int $chargeId, int $userId): bool
    {
        return DB::transaction(function () use ($data, $chargeId, $userId) {
            $charge = Charge::findOrFail($chargeId);
            $charge->signature()->updateOrCreate(['charge_id' => $chargeId], [
                'signature_status' => 'rechazado',
                'signature_comment' => $data['signature_comment'],
                'signed_by' => $userId,
                'signature_completed_at' => now(),
            ]);

            return true;
        });
    }

    private function resolveNaturalPerson(array $data): ?int
    {
        if ($data['tipo_interesado'] !== 'Persona Natural') {
            return null;
        }
        $payload = ['dni' => $data['dni'] ?? null, 'nombres' => $data['nombres'] ?? null, 'apellido_paterno' => $data['apellido_paterno'] ?? null, 'apellido_materno' => $data['apellido_materno'] ?? null];
        $dni = trim((string) ($payload['dni'] ?? ''));

        return ($dni !== '') ? NaturalPerson::firstOrCreate(['dni' => $dni], $payload)->id : NaturalPerson::create($payload)->id;
    }

    private function resolveLegalEntity(array $data): ?int
    {
        if ($data['tipo_interesado'] !== 'Persona Juridica') {
            return null;
        }

        $payload = [
            'ruc' => $data['ruc'] ?? null,
            'razon_social' => $data['razon_social'] ?? null,
            'district' => $data['district'] ?? null,
        ];

        $ruc = trim((string) ($payload['ruc'] ?? ''));
        $entity = ($ruc !== '')
            ? LegalEntity::firstOrCreate(['ruc' => $ruc], $payload)
            : LegalEntity::create($payload);

        // Resolver representante
        if (! empty($data['representative_dni'])) {
            $person = NaturalPerson::updateOrCreate(
                ['dni' => $data['representative_dni']],
                [
                    'nombres' => $data['representative_nombres'] ?? '',
                    'apellido_paterno' => $data['representative_apellido_paterno'] ?? '',
                    'apellido_materno' => $data['representative_apellido_materno'] ?? '',
                ]
            );

            $representative = $entity->representative;
            if ($representative) {
                $representative->update([
                    'natural_person_id' => $person->id,
                    'cargo' => $data['representative_cargo'] ?? null,
                    'fecha_desde' => $data['representative_since'] ?? null,
                ]);
            } else {
                Representative::create([
                    'legal_entity_id' => $entity->id,
                    'natural_person_id' => $person->id,
                    'cargo' => $data['representative_cargo'] ?? null,
                    'fecha_desde' => $data['representative_since'] ?? null,
                ]);
            }
        }

        return $entity->id;
    }

    private function syncInteresado(Charge $model, array $data): void
    {
        if ($data['tipo_interesado'] === 'Persona Natural') {
            $payload = ['dni' => $data['dni'] ?? null, 'nombres' => $data['nombres'] ?? null, 'apellido_paterno' => $data['apellido_paterno'] ?? null, 'apellido_materno' => $data['apellido_materno'] ?? null];
            $person = ($model->interesado instanceof NaturalPerson) ? $model->interesado : NaturalPerson::create($payload);
            $person->update($payload);
            $model->update([
                'interesado_type' => 'App\Models\NaturalPerson',
                'interesado_id' => $person->id,
            ]);
        } elseif ($data['tipo_interesado'] === 'Persona Juridica') {
            $legalEntityId = $this->resolveLegalEntity($data);
            $model->update([
                'interesado_type' => 'App\Models\LegalEntity',
                'interesado_id' => $legalEntityId,
            ]);
        } elseif ($data['tipo_interesado'] === 'Trabajador UGEL') {
            $assignedTo = $data['assigned_to'] ?? $data['user']->id;
            $model->update([
                'interesado_type' => 'App\Models\User',
                'interesado_id' => $assignedTo,
            ]);
        }
    }

    private function crearCargoFisico(int $creatorId, ?string $period, ?string $docDate, int $userId, $interesado, string $asunto, ?string $documentPath, array $resolucionIds, ?int $assignedTo = null): void
    {
        $assignedTo = $assignedTo ?? $creatorId;

        $charge = Charge::create([
            'n_charge' => $this->nextChargeNumberForUser($creatorId, $period),
            'charge_period' => $period,
            'document_date' => $docDate,
            'user_id' => $creatorId,
            'interesado_type' => get_class($interesado),
            'interesado_id' => $interesado->id,
            'asunto' => $asunto,
            'document_path' => $documentPath,
        ]);

        if (! empty($resolucionIds)) {
            $charge->resolucions()->attach($resolucionIds);
        }

        $charge->signature()->create([
            'assigned_to' => $assignedTo,
            'signature_status' => 'pendiente',
            'signature_requested_at' => now(),
        ]);

        $assignedUser = User::find($assignedTo);
        if ($assignedUser) {
            $assignedUser->notify(new PendingChargeNotification($charge));
        }
    }

    private function resolverDestinatarioIndividual(array $dest)
    {
        if ($dest['tipo'] === 'Persona Natural') {
            $payload = [
                'dni' => $dest['dni'] ?? null,
                'nombres' => $dest['nombres'] ?? null,
                'apellido_paterno' => $dest['apellido_paterno'] ?? null,
                'apellido_materno' => $dest['apellido_materno'] ?? null,
            ];
            $dni = trim((string) ($payload['dni'] ?? ''));

            return ($dni !== '') ? NaturalPerson::firstOrCreate(['dni' => $dni], $payload) : NaturalPerson::create($payload);
        }

        if ($dest['tipo'] === 'Persona Juridica') {
            $payload = [
                'ruc' => $dest['ruc'] ?? null,
                'razon_social' => $dest['razon_social'] ?? null,
                'district' => $dest['district'] ?? null,
            ];
            $ruc = trim((string) ($payload['ruc'] ?? ''));

            $entity = ($ruc !== '')
                ? LegalEntity::firstOrCreate(['ruc' => $ruc], $payload)
                : LegalEntity::create($payload);

            // Resolver representante
            if (! empty($dest['representative_dni'])) {
                $person = NaturalPerson::updateOrCreate(
                    ['dni' => $dest['representative_dni']],
                    [
                        'nombres' => $dest['representative_nombres'] ?? '',
                        'apellido_paterno' => $dest['representative_apellido_paterno'] ?? '',
                        'apellido_materno' => $dest['representative_apellido_materno'] ?? '',
                    ]
                );

                $representative = $entity->representative;
                if ($representative) {
                    $representative->update([
                        'natural_person_id' => $person->id,
                        'cargo' => $dest['representative_cargo'] ?? null,
                        'fecha_desde' => $dest['representative_since'] ?? null,
                    ]);
                } else {
                    Representative::create([
                        'legal_entity_id' => $entity->id,
                        'natural_person_id' => $person->id,
                        'cargo' => $dest['representative_cargo'] ?? null,
                        'fecha_desde' => $dest['representative_since'] ?? null,
                    ]);
                }
            }

            return $entity;
        }

        if ($dest['tipo'] === 'Trabajador UGEL') {
            return User::find($dest['assigned_to']);
        }

        return null;
    }

    private function getPeriodOptions(?string $defaultPeriod): array
    {
        $options = Charge::whereNotNull('charge_period')->distinct()->orderByDesc('charge_period')->pluck('charge_period')->all();
        if ($defaultPeriod && ! in_array($defaultPeriod, $options, true)) {
            array_unshift($options, $defaultPeriod);
        }

        return $options;
    }

    private function countSignedCharges(): int
    {
        return Charge::whereHas('signature', fn ($q) => $q->where('signature_status', 'firmado'))->count();
    }

    private function countUnsignedCharges(): int
    {
        return Charge::whereHas('signature', fn ($q) => $q->where('signature_status', 'pendiente'))->count();
    }

    private function usersToAssign($user): Collection
    {
        return User::where('id', '!=', $user?->id)->orderBy('name')->get();
    }

    public function getAllSentCharges(array $searchfilters, $user)
    {
        return Charge::with(['user', 'interesado', 'signature', 'signature.signer', 'signature.assignedTo', 'resolucions'])
            ->where('user_id', $user?->id)
            ->whereDoesntHave('resolucions')
            ->filterByPeriod($searchfilters['period'] ?? null)
            ->search($searchfilters['search'] ?? null)
            ->filterBySignatureStatus($searchfilters['signature_status'] ?? null)
            ->orderByDesc('created_at')
            ->get();
    }

    public function getAllReceivedCharges(array $searchfilters, $user)
    {
        return Charge::with(['user', 'interesado', 'signature', 'signature.signer', 'signature.assignedTo', 'resolucions'])
            ->whereDoesntHave('resolucions')
            ->whereHas('signature', fn ($q) => $q->where('assigned_to', $user?->id))
            ->where('interesado_type', User::class)
            ->filterByPeriod($searchfilters['period'] ?? null)
            ->when(
                ($searchfilters['signature_status'] ?? null) !== 'rechazado',
                fn ($q) => $q->whereHas('signature', fn ($s) => $s->where('signature_status', '!=', 'rechazado'))
            )
            ->search($searchfilters['search'] ?? null)
            ->filterBySignatureStatus($searchfilters['signature_status'] ?? null)
            ->orderByDesc('created_at')
            ->get();
    }

    public function getAllCreatedCharges(array $searchfilters, $user)
    {
        return Charge::with(['user', 'interesado', 'signature', 'signature.signer', 'signature.assignedTo', 'resolucions'])
            ->where('user_id', $user?->id)
            ->whereDoesntHave('resolucions')
            ->where('interesado_type', '!=', User::class)
            ->filterByPeriod($searchfilters['period'] ?? null)
            ->search($searchfilters['search'] ?? null)
            ->filterBySignatureStatus($searchfilters['signature_status'] ?? null)
            ->orderByDesc('created_at')
            ->get();
    }

    public function getAllResolutionCharges(array $searchfilters, $user)
    {
        return Charge::with(['user', 'interesado', 'signature', 'signature.signer', 'signature.assignedTo', 'resolucions'])
            ->whereHas('resolucions')
            ->when(! $user?->hasRole('REGISTRADOR RESOLUCIONES'), function ($q) use ($user) {
                $q->where(function ($q2) use ($user) {
                    $q2->where(function ($q3) use ($user) {
                        $q3->where('interesado_id', $user?->id)
                            ->where('interesado_type', User::class);
                    })->orWhereHas('signature', function ($q3) use ($user) {
                        $q3->where('assigned_to', $user?->id);
                    });
                });
            })
            ->filterByPeriod($searchfilters['period'] ?? null)
            ->search($searchfilters['search'] ?? null)
            ->orderByDesc('created_at')
            ->get();
    }
}
