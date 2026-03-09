<?php
namespace App\Services;

use App\Filters\ChargeFilter;
use App\Models\Charge;
use App\Models\LegalEntity;
use App\Models\NaturalPerson;
use App\Models\Setting;
use App\Models\User;
use App\Services\Contracts\ChargeServiceInterface;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\Response;

class ChargeService implements ChargeServiceInterface
{
    public function __construct(protected ChargeFilter $filter)
    {
    }
    public function getAll(array $data): array
    {
        // Lista de cargos enviados
        $sentCharges = $this->filter->getAllSentCharges($data['sent'], $data['user']);

        // Lista de cargos recibidos
        $receivedCharges = $this->filter->getAllReceivedCharges($data['received'], $data['user']);

        // Lista de cargos de resolución
        $canViewResolutionCharges =
            $data['user']?->hasRole('ADMINISTRADOR') ||
            $data['user']?->can('modulo resoluciones');
        $resolutionCharges = $canViewResolutionCharges ? $this->filter->getAllResolutionCharges($data['resolucion'], $data['user']) : collect();

        // Lista de cargos creados para personas naturales y jurídicas
        $createdCharges = $this->filter->getAllCreatedCharges($data['created'], $data['user']);

        // Contadores
        $signedCount = $this->countSignedCharges();
        $unsignedCount = $this->countUnsignedCharges();

        // Usuarios para asignar firma
        $users = $this->usersToAssing($data['user']);

        //Periodos disponibles
        $periodOptions = Charge::query()
            ->whereNotNull('charge_period')
            ->distinct()
            ->orderByDesc('charge_period')
            ->pluck('charge_period')
            ->all();

        if ($data['default_period'] && !in_array($data['default_period'], $periodOptions, true)) {
            array_unshift($periodOptions, $data['default_period']);
        }

        // Configuración de recarga de cargos
        $refreshIntervalSeconds = (int) Setting::getValue(
            'charges_refresh_interval',
            '5'
        );

        $resolutionPeriod = $data['resolucion']['period'];
        $receivedPeriod = $data['received']['period'];
        $sentPeriod = $data['sent']['period'];
        $createdPeriod = $data['created']['period'];
        $defaultPeriod = $data['default_period'];

        return compact(
            'sentCharges',
            'receivedCharges',
            'signedCount',
            'unsignedCount',
            'users',
            'resolutionCharges',
            'canViewResolutionCharges',
            'createdCharges',
            'refreshIntervalSeconds',
            'defaultPeriod',
            'sentPeriod',
            'receivedPeriod',
            'resolutionPeriod',
            'createdPeriod',
            'periodOptions'
        );
    }

    public function find(array $criteria): array
    {
        throw new \Exception('Not implemented');
    }

    public function getById(int $id): array
    {
        throw new \Exception('Not implemented');
    }

    public function create(array $data): bool
    {
        $assignedTo = $data['assigned_to'] ?? null;
        if ($data['tipo_interesado'] !== 'Trabajador UGEL') {
            $assignedTo = $data['user']->id;
        }

        $chargePeriod = $this->getChargePeriod();
        $nCharge = $this->nextChargeNumberForUser($data['user']->id, $chargePeriod);
        $naturalPersonId = null;
        $legalEntityId = null;

        if ($data['tipo_interesado'] === 'Persona Natural') {
            $payload = $this->naturalPersonPayload($data);
            $dni = isset($payload['dni']) ? trim((string) $payload['dni']) : '';
            if ($dni !== '') {
                $payload['dni'] = $dni;
                $naturalPersonId = NaturalPerson::firstOrCreate(
                    ['dni' => $dni],
                    $payload
                )->id;
            } else {
                $naturalPersonId = NaturalPerson::create($payload)->id;
            }
        }

        if ($data['tipo_interesado'] === 'Persona Juridica') {
            $payload = $this->legalEntityPayload($data);
            $ruc = isset($payload['ruc']) ? trim((string) $payload['ruc']) : '';
            if ($ruc !== '') {
                $payload['ruc'] = $ruc;
                $legalEntityId = LegalEntity::firstOrCreate(
                    ['ruc' => $ruc],
                    $payload
                )->id;
            } else {
                $legalEntityId = LegalEntity::create($payload)->id;
            }
        }

        $charge = Charge::create([
            'n_charge' => $nCharge,
            'charge_period' => $chargePeriod,
            'document_date' => $data['document_date'] ?? null,
            'user_id' => $data['user']->id,
            'tipo_interesado' => $data['tipo_interesado'],
            'natural_person_id' => $naturalPersonId,
            'legal_entity_id' => $legalEntityId,
            'asunto' => $data['asunto'],
        ]);

        $charge->signature()->create([
            'assigned_to' => $assignedTo,
            'signature_status' => 'pendiente',
            'signature_requested_at' => $assignedTo ? now() : null,
        ]);

        return true;
    }

    public function update(array $data, int $id): bool
    {
        $model = Charge::findOrFail($id);
        $signatureStatus = $model->signature?->signature_status ?? 'pendiente';
        if (
            $model->user_id === $data['user']->id &&
            in_array($signatureStatus, ['firmado', 'rechazado'], true)
        ) {
            return false;
        }

        $assignedTo = $data['assigned_to'] ?? null;
        $signatureData = [];

        if ($signatureStatus === 'pendiente') {
            if ($data['tipo_interesado'] !== 'Trabajador UGEL') {
                $assignedTo = $data['user']->id;
            }

            $signatureData['assigned_to'] = $assignedTo;

            if ($assignedTo && !$model->signature?->signature_requested_at) {
                $signatureData['signature_requested_at'] = now();
            }
        }

        $model->update([
            'document_date' => $data['document_date'] ?? null,
            'asunto' => $data['asunto'],
            'tipo_interesado' => $data['tipo_interesado'],
        ]);

        $naturalPersonId = null;
        $legalEntityId = null;

        if (in_array($data['tipo_interesado'], ['Persona Natural', 'Trabajador UGEL'], true)) {
            $payload = $this->naturalPersonPayload($data);
            if ($model->naturalPerson) {
                $model->naturalPerson->update($payload);
                $naturalPersonId = $model->naturalPerson->id;
            } else {
                $naturalPerson = NaturalPerson::create($payload);
                $naturalPersonId = $naturalPerson->id;
            }
        }

        if ($data['tipo_interesado'] === 'Persona Juridica') {
            $payload = $this->legalEntityPayload($data);
            if ($model->legalEntity) {
                $model->legalEntity->update($payload);
                $legalEntityId = $model->legalEntity->id;
            } else {
                $legalEntity = LegalEntity::create($payload);
                $legalEntityId = $legalEntity->id;
            }
        }

        $model->natural_person_id = $naturalPersonId;
        $model->legal_entity_id = $legalEntityId;
        $model->save();

        if (!empty($signatureData)) {
            $model->signature()->updateOrCreate(
                ['charge_id' => $model->id],
                $signatureData
            );
        }

        return true;
    }

    public function delete(array $data, int $id): bool
    {
        Log::info('ChargeService.delete:start', [
            'charge_id' => $id,
            'user_id' => $data['user']->id ?? null,
        ]);

        try {
            $model = Charge::findOrFail($id);

            if ($model->signature?->signature_root) {
                $signatureRoot = $model->signature->signature_root;
                $signatureExists = Storage::disk('local')->exists($signatureRoot);
                Log::info('ChargeService.delete:signature_check', [
                    'charge_id' => $id,
                    'path' => $signatureRoot,
                    'exists' => $signatureExists,
                ]);

                if ($signatureExists) {
                    Storage::disk('local')->delete($signatureRoot);
                }
            }

            if ($model->signature?->evidence_root) {
                $evidenceRoot = $model->signature->evidence_root;
                $evidenceExists = Storage::disk('local')->exists($evidenceRoot);
                Log::info('ChargeService.delete:evidence_check', [
                    'charge_id' => $id,
                    'path' => $evidenceRoot,
                    'exists' => $evidenceExists,
                ]);

                if ($evidenceExists) {
                    Storage::disk('local')->delete($evidenceRoot);
                }
            }

            $model->delete();

            Log::info('ChargeService.delete:success', [
                'charge_id' => $id,
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('ChargeService.delete:error', [
                'charge_id' => $id,
                'user_id' => $data['user']->id ?? null,
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            throw $e;
        }
    }

    public function signStore(array $data, array $files, int $chargeId, int $userId): bool
    {
        $charge = Charge::with('signature')->findOrFail($chargeId);

        $isTitular = (bool) ($data['titularidad'] ?? false);

        // ================= FIRMA =================
        $signaturePath = "private/charges_signatures/charge_{$chargeId}.svg";
        Storage::disk('local')->put($signaturePath, $data['firma']);

        // ================= CARTA PODER =================
        $cartaPoderPath = null;
        if (!$isTitular && !empty($files['carta_poder'])) {
            $cartaPoderPath = $this->storeAndOptimizeImage(
                $files['carta_poder'],
                'private/charges_poder',
                50
            );
        }

        // ================= EVIDENCIA =================
        $evidencePath = $charge->signature?->evidence_root;

        if (!empty($files['evidence_root'])) {
            // borrar evidencia anterior
            if ($evidencePath && Storage::disk('local')->exists($evidencePath)) {
                Storage::disk('local')->delete($evidencePath);
            }

            $evidencePath = $this->storeAndOptimizeImage(
                $files['evidence_root'],
                'private/charges_evidence',
                null,
                "charge_{$chargeId}_evidence_" . now()->format('Ymd_His')
            );
        }

        // ================= BD =================
        $charge->signature()->updateOrCreate(
            ['charge_id' => $chargeId],
            [
                'signature_status' => 'firmado',
                'signed_by' => $userId,
                'signature_root' => $signaturePath,
                'signature_completed_at' => now(),
                'titularidad' => $isTitular,
                'parentesco' => $isTitular ? null : ($data['parentesco'] ?? null),
                'carta_poder_path' => $isTitular ? null : $cartaPoderPath,
                'evidence_root' => $evidencePath,
            ]
        );

        return true;
    }

    public function reject(array $data, int $chargeId, int $userId): bool
    {
        $charge = Charge::with('signature')->findOrFail($chargeId);

        $charge->signature()->updateOrCreate(['charge_id' => $chargeId], [
            'signature_status' => 'rechazado',
            'signature_comment' => $data['signature_comment'],
            'signed_by' => $userId,
            'signature_completed_at' => now(),
        ]);

        return true;
    }

    public function getReportSentData($data, User $user, $defaultPeriod): Response
    {
        $searchfilters = [
            'search' => $data['search'] ?? null,
            'signature_status' => $data['signature_status'] ?? null,
            'period' => $data['period'] ?? $defaultPeriod,
        ];

        $charges = $this->filter->getAllSentCharges($searchfilters, $user);

        return Pdf::loadView('charges.report', [
            'title' => 'REPORTE DE CARGOS ENVIADOS',
            'type' => 'sent',
            'charges' => $charges,
            'filters' => $searchfilters,
        ])->setPaper('a4')
            ->stream('reporte_cargos_enviados' . now()->format('Ymd_His') . '.pdf');
    }

    public function getReportCreatedData(array $criteria, User $user, $defaultPeriod): Response
    {
        $searchfilters = [
            'search' => $criteria['search'] ?? null,
            'signature_status' => $criteria['signature_status'] ?? null,
            'period' => $criteria['period'] ?? $defaultPeriod,
        ];

        $charges = $this->filter->getAllCreatedCharges($searchfilters, $user);

        return Pdf::loadView('charges.report', [
            'title' => 'REPORTE DE CARGOS CREADOS',
            'type' => 'sent',
            'charges' => $charges,
            'filters' => $searchfilters,
        ])->setPaper('a4')
            ->stream('reporte_cargos_creados' . now()->format('Ymd_His') . '.pdf');
    }

    public function getReportResolutionData(array $criteria, User $user, $defaultPeriod): Response
    {
        $canViewResolutionCharges = $user?->hasRole('ADMINISTRADOR') || $user?->can('modulo resoluciones');
        if (!$canViewResolutionCharges) {
            abort(403);
        }

        $search = $criteria['search'];
        $period = $criteria['period'];
        $query = Charge::with(['resolucion', 'signature'])
            ->whereNotNull('resolucion_id')
            ->whereHas('signature', function ($q) {
                $q->where('signature_status', 'firmado');
            })
            ->when($period, function ($q) use ($period) {
                $q->where('charge_period', $period);
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('n_charge', 'like', '%' . $search . '%')
                        ->orWhereHas('resolucion', function ($resolucion) use ($search) {
                            $resolucion->where('rd', 'like', '%' . $search . '%')
                                ->orWhere('nombres_apellidos', 'like', '%' . $search . '%')
                                ->orWhere('dni', 'like', '%' . $search . '%')
                                ->orWhere('asunto', 'like', '%' . $search . '%')
                                ->orWhere('periodo', 'like', '%' . $search . '%')
                                ->orWhere('fecha', 'like', '%' . $search . '%');
                        });
                });
            });

        $charges = $query->orderByDesc('created_at')->get();
        $filters = [
            'search' => $search,
            'period' => $period ?? $defaultPeriod,
        ];

        return Pdf::loadView('charges.report', [
            'title' => 'REPORTE DE CARGOS DE RESOLUCIONES',
            'type' => 'resolution',
            'charges' => $charges,
            'filters' => $filters,
        ])->setPaper('a4')
            ->stream('reporte_cargos_resoluciones' . now()->format('Ymd_His') . '.pdf');
    }

    public function getReportReceivedData(array $criteria, User $user, $defaultPeriod): Response
    {
        $search = $criteria['search'] ?? null;
        $statusFilter = $criteria['signature_status'] ?? null;
        $period = $criteria['period'] ?? $defaultPeriod;

        $query = Charge::with(['user', 'naturalPerson', 'legalEntity', 'signature', 'signature.assignedTo'])
            ->whereNull('resolucion_id')
            ->whereHas('signature', function ($q) use ($user) {
                $q->where('assigned_to', $user?->id);
            })
            ->whereNotIn('tipo_interesado', ['Persona Juridica', 'Persona Natural'])
            ->when($period, function ($q) use ($period) {
                $q->where('charge_period', $period);
            })
            ->when($statusFilter !== 'rechazado', function ($q) {
                $q->whereHas('signature', function ($signature) {
                    $signature->where('signature_status', '!=', 'rechazado');
                });
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('n_charge', 'like', '%' . $search . '%')
                        ->orWhere('asunto', 'like', '%' . $search . '%')
                        ->orWhereHas('naturalPerson', function ($naturalPerson) use ($search) {
                            $naturalPerson->where('nombres', 'like', '%' . $search . '%')
                                ->orWhere('apellido_paterno', 'like', '%' . $search . '%')
                                ->orWhere('apellido_materno', 'like', '%' . $search . '%')
                                ->orWhere('dni', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('legalEntity', function ($legalEntity) use ($search) {
                            $legalEntity->where('razon_social', 'like', '%' . $search . '%')
                                ->orWhere('ruc', 'like', '%' . $search . '%')
                                ->orWhere('district', 'like', '%' . $search . '%');
                        });
                });
            });

        if (in_array($statusFilter, ['pendiente', 'firmado', 'rechazado'], true)) {
            $query->whereHas('signature', function ($q) use ($statusFilter) {
                $q->where('signature_status', $statusFilter);
            });
        }

        $charges = $query->orderByDesc('created_at')->get();
        $filters = [
            'search' => $search,
            'signature_status' => $statusFilter,
            'period' => $period ?? $defaultPeriod,
        ];

        return Pdf::loadView('charges.report', [
            'title' => 'REPORTE DE CARGOS RECIBIDOS',
            'type' => 'received',
            'charges' => $charges,
            'filters' => $filters,
        ])->setPaper('a4')
            ->stream('reporte_cargos_recibidos' . now()->format('Ymd_His') . '.pdf');
    }

    private function storeAndOptimizeImage($file, $folder, $quality = 50, $customName = null)
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $filename = $customName
            ? "{$customName}.{$extension}"
            : $file->hashName();

        $path = $file->storeAs($folder, $filename, 'local');
        $absolutePath = Storage::disk('local')->path($path);

        if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
            $manager = new ImageManager(['driver' => 'gd']);
            $image = $manager->make($absolutePath);

            // Resize max 1600px
            if ($image->width() > 1600 || $image->height() > 1600) {
                $image->resize(1600, 1600, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            $finalQuality = $quality ?? ($extension === 'png' ? 8 : 50);
            $image->save($absolutePath, $finalQuality);
        }

        return $path;
    }


    private function nextChargeNumberForUser(?int $userId, ?string $period): string
    {
        if (!$userId) {
            return '1';
        }

        $query = Charge::where('user_id', $userId);
        if ($period) {
            $query->where('charge_period', $period);
        } else {
            $query->whereNull('charge_period');
        }
        $maxValue = $query->max(DB::raw('CAST(n_charge as UNSIGNED)'));
        $nextValue = ((int) $maxValue) + 1;

        return (string) $nextValue;
    }

    public function getChargePeriod(): ?string
    {
        $period = Setting::getValue('charge_period', '');
        return $period !== '' ? $period : null;
    }

    private function countSignedCharges(): int
    {
        return Charge::whereHas(
            'signature',
            fn($q) => $q->where('signature_status', 'firmado')
        )->count();
    }

    private function countUnsignedCharges(): int
    {
        return Charge::whereHas(
            'signature',
            fn($q) => $q->where('signature_status', 'pendiente')
        )->count();
    }

    private function usersToAssing($user)
    {
        return User::where('id', '!=', $user?->id)
            ->orderBy('name')
            ->get();
    }

    public function normalizePeriod(?string $value)
    {
        return $value !== '' ? $value : null;
    }

    private function naturalPersonPayload(array $data): array
    {
        return [
            'dni' => $data['dni'] ?? null,
            'nombres' => $data['nombres'] ?? null,
            'apellido_paterno' => $data['apellido_paterno'] ?? null,
            'apellido_materno' => $data['apellido_materno'] ?? null,
        ];
    }

    private function legalEntityPayload(array $data): array
    {
        return [
            'ruc' => $data['ruc'] ?? null,
            'razon_social' => $data['razon_social'] ?? null,
            'district' => $data['district'] ?? null,
            'contact_number' => $data['contact_number'] ?? null,
        ];
    }
}
