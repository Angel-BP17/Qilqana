<?php

namespace App\Services;

use App\Filters\ChargeFilter;
use App\Models\Charge;
use App\Models\LegalEntity;
use App\Models\NaturalPerson;
use App\Models\Setting;
use App\Models\User;
use App\Services\Contracts\ChargeServiceInterface;
use App\Traits\HasChargeLogic;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ChargeService implements ChargeServiceInterface
{
    use HasChargeLogic;

    protected const CACHE_TTL = 3600; 
    protected const REF_CACHE_TTL = 3600;

    public function __construct(
        protected ChargeFilter $filter,
        protected ImageService $imageService
    ) {
    }

    public function getAll(array $data): array
    {
        $user = $data['user'];
        $userId = $user?->id ?? 0;
        
        $version = Cache::get('charges_cache_version', 1);
        $cacheKey = "charges_v{$version}_index_{$userId}_" . md5(serialize($data));

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($data, $user) {
            $resolutionCharges = ($user?->hasRole('ADMINISTRADOR') || $user?->can('modulo resoluciones'))
                ? $this->filter->getAllResolutionCharges($data['resolucion'], $user) 
                : collect();

            return [
                'sentCharges' => $this->filter->getAllSentCharges($data['sent'], $user),
                'receivedCharges' => $this->filter->getAllReceivedCharges($data['received'], $user),
                'createdCharges' => $this->filter->getAllCreatedCharges($data['created'], $user),
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
        });
    }

    public function create(array $data): bool
    {
        $result = DB::transaction(function () use ($data) {
            $user = $data['user'];
            $assignedTo = ($data['tipo_interesado'] === 'Trabajador UGEL') ? ($data['assigned_to'] ?? null) : $user->id;

            $charge = Charge::create([
                'n_charge' => $this->nextChargeNumberForUser($user->id, $this->getChargePeriod()),
                'charge_period' => $this->getChargePeriod(),
                'document_date' => $data['document_date'] ?? null,
                'user_id' => $user->id,
                'tipo_interesado' => $data['tipo_interesado'],
                'natural_person_id' => $this->resolveNaturalPerson($data),
                'legal_entity_id' => $this->resolveLegalEntity($data),
                'asunto' => $data['asunto'],
            ]);

            $charge->signature()->create([
                'assigned_to' => $assignedTo,
                'signature_status' => 'pendiente',
                'signature_requested_at' => $assignedTo ? now() : null,
            ]);

            return true;
        });

        if ($result) {
            $this->clearChargesCache($data['user']->id);
        }

        return $result;
    }

    public function update(array $data, int $id): bool
    {
        $model = Charge::findOrFail($id);
        
        $result = DB::transaction(function () use ($data, $model) {
            $assignedTo = $data['assigned_to'] ?? null;
            if ($model->signature?->signature_status === 'pendiente') {
                if ($data['tipo_interesado'] !== 'Trabajador UGEL') $assignedTo = $data['user']->id;
                $model->signature()->update([
                    'assigned_to' => $assignedTo,
                    'signature_requested_at' => ($assignedTo && !$model->signature->signature_requested_at) ? now() : $model->signature->signature_requested_at
                ]);
            }

            $model->update([
                'document_date' => $data['document_date'] ?? null,
                'asunto' => $data['asunto'],
                'tipo_interesado' => $data['tipo_interesado'],
            ]);

            $this->syncInteresado($model, $data);
            return true;
        });

        if ($result) {
            $this->clearChargesCache($data['user']->id, $model->signature?->assigned_to);
        }

        return $result;
    }

    public function delete(array $data, int $id): bool
    {
        $model = Charge::findOrFail($id);
        $userId = $model->user_id;
        $assignedTo = $model->signature?->assigned_to;

        $result = DB::transaction(function () use ($model) {
            if ($model->signature) {
                $this->imageService->deleteIfExists($model->signature->signature_root);
                $this->imageService->deleteIfExists($model->signature->evidence_root);
            }
            return (bool) $model->delete();
        });

        if ($result) {
            $this->clearChargesCache($userId, $assignedTo);
        }

        return $result;
    }

    public function signStore(array $data, array $files, int $chargeId, int $userId): bool
    {
        $result = DB::transaction(function () use ($data, $files, $chargeId, $userId) {
            $charge = Charge::with('signature')->findOrFail($chargeId);
            $isTitular = (bool) ($data['titularidad'] ?? false);

            $signaturePath = "private/charges_signatures/charge_{$chargeId}.svg";
            Storage::disk('local')->put($signaturePath, $data['firma']);

            $cartaPoderPath = $charge->signature?->carta_poder_path;
            if (!$isTitular && !empty($files['carta_poder'])) {
                $cartaPoderPath = $this->imageService->storeAndOptimize($files['carta_poder'], 'private/charges_poder', 50);
            }

            $evidencePath = $charge->signature?->evidence_root;
            if (!empty($files['evidence_root'])) {
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
            ]);

            return true;
        });

        if ($result) {
            $this->clearChargesCache($userId);
        }

        return $result;
    }

    public function reject(array $data, int $chargeId, int $userId): bool
    {
        $result = DB::transaction(function () use ($data, $chargeId, $userId) {
            $charge = Charge::findOrFail($chargeId);
            $charge->signature()->updateOrCreate(['charge_id' => $chargeId], [
                'signature_status' => 'rechazado',
                'signature_comment' => $data['signature_comment'],
                'signed_by' => $userId,
                'signature_completed_at' => now(),
            ]);
            return true;
        });

        if ($result) {
            $this->clearChargesCache($userId);
        }

        return $result;
    }

    private function clearChargesCache(?int $userId = null, ?int $assignedTo = null): void
    {
        // Aseguramos que la versión exista antes de incrementar
        if (!Cache::has('charges_cache_version')) {
            Cache::put('charges_cache_version', 1, self::CACHE_TTL);
        }
        Cache::increment('charges_cache_version');
        
        Cache::forget('charges_signed_count');
        Cache::forget('charges_unsigned_count');
        Cache::forget('period_options_none');
        if ($userId) Cache::forget("users_to_assign_{$userId}");
        if ($assignedTo) Cache::forget("users_to_assign_{$assignedTo}");
    }

    private function resolveNaturalPerson(array $data): ?int
    {
        if ($data['tipo_interesado'] !== 'Persona Natural') return null;
        $payload = ['dni' => $data['dni'] ?? null, 'nombres' => $data['nombres'] ?? null, 'apellido_paterno' => $data['apellido_paterno'] ?? null, 'apellido_materno' => $data['apellido_materno'] ?? null];
        $dni = trim((string) ($payload['dni'] ?? ''));
        return ($dni !== '') ? NaturalPerson::firstOrCreate(['dni' => $dni], $payload)->id : NaturalPerson::create($payload)->id;
    }

    private function resolveLegalEntity(array $data): ?int
    {
        if ($data['tipo_interesado'] !== 'Persona Juridica') return null;
        $payload = ['ruc' => $data['ruc'] ?? null, 'razon_social' => $data['razon_social'] ?? null, 'district' => $data['district'] ?? null, 'contact_number' => $data['contact_number'] ?? null];
        $ruc = trim((string) ($payload['ruc'] ?? ''));
        return ($ruc !== '') ? LegalEntity::firstOrCreate(['ruc' => $ruc], $payload)->id : LegalEntity::create($payload)->id;
    }

    private function syncInteresado(Charge $model, array $data): void
    {
        if (in_array($data['tipo_interesado'], ['Persona Natural', 'Trabajador UGEL'], true)) {
            $payload = ['dni' => $data['dni'] ?? null, 'nombres' => $data['nombres'] ?? null, 'apellido_paterno' => $data['apellido_paterno'] ?? null, 'apellido_materno' => $data['apellido_materno'] ?? null];
            $person = $model->naturalPerson ?: NaturalPerson::create($payload);
            $person->update($payload);
            $model->update(['natural_person_id' => $person->id, 'legal_entity_id' => null]);
        } elseif ($data['tipo_interesado'] === 'Persona Juridica') {
            $payload = ['ruc' => $data['ruc'] ?? null, 'razon_social' => $data['razon_social'] ?? null, 'district' => $data['district'] ?? null, 'contact_number' => $data['contact_number'] ?? null];
            $entity = $model->legalEntity ?: LegalEntity::create($payload);
            $entity->update($payload);
            $model->update(['natural_person_id' => null, 'legal_entity_id' => $entity->id]);
        }
    }

    private function getPeriodOptions(?string $defaultPeriod): array
    {
        $options = Charge::whereNotNull('charge_period')->distinct()->orderByDesc('charge_period')->pluck('charge_period')->all();
        if ($defaultPeriod && !in_array($defaultPeriod, $options, true)) array_unshift($options, $defaultPeriod);
        return $options;
    }

    private function countSignedCharges(): int { return Charge::whereHas('signature', fn($q) => $q->where('signature_status', 'firmado'))->count(); }
    private function countUnsignedCharges(): int { return Charge::whereHas('signature', fn($q) => $q->where('signature_status', 'pendiente'))->count(); }
    private function usersToAssign($user): Collection { return User::where('id', '!=', $user?->id)->orderBy('name')->get(); }
}
