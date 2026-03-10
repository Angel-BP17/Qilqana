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

class ChargeService implements ChargeServiceInterface
{
    use HasChargeLogic;

    protected const CACHE_TTL = 300; // 5 minutos para listas dinámicas
    protected const REF_CACHE_TTL = 3600; // 1 hora para datos de referencia

    public function __construct(
        protected ChargeFilter $filter,
        protected ImageService $imageService
    ) {
    }

    public function getAll(array $data): array
    {
        $user = $data['user'];
        $userId = $user?->id ?? 0;
        
        // Clave única basada en filtros para evitar colisiones entre usuarios y búsquedas
        $cacheKey = "charges_index_{$userId}_" . md5(serialize($data));

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($data, $user) {
            $sentCharges = $this->filter->getAllSentCharges($data['sent'], $user);
            $receivedCharges = $this->filter->getAllReceivedCharges($data['received'], $user);

            $canViewResolutionCharges = $user?->hasRole('ADMINISTRADOR') || $user?->can('modulo resoluciones');
            $resolutionCharges = $canViewResolutionCharges 
                ? $this->filter->getAllResolutionCharges($data['resolucion'], $user) 
                : collect();

            $createdCharges = $this->filter->getAllCreatedCharges($data['created'], $user);

            return [
                'sentCharges' => $sentCharges,
                'receivedCharges' => $receivedCharges,
                'resolutionCharges' => $resolutionCharges,
                'createdCharges' => $createdCharges,
                'signedCount' => $this->getCachedSignedCount(),
                'unsignedCount' => $this->getCachedUnsignedCount(),
                'users' => $this->getCachedUsersToAssign($user),
                'periodOptions' => $this->getCachedPeriodOptions($data['default_period']),
                'refreshIntervalSeconds' => (int) Setting::getValue('charges_refresh_interval', '5'),
                'canViewResolutionCharges' => $canViewResolutionCharges,
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

            $chargePeriod = $this->getChargePeriod();
            $nCharge = $this->nextChargeNumberForUser($user->id, $chargePeriod);

            $naturalPersonId = $this->resolveNaturalPerson($data);
            $legalEntityId = $this->resolveLegalEntity($data);

            $charge = Charge::create([
                'n_charge' => $nCharge,
                'charge_period' => $chargePeriod,
                'document_date' => $data['document_date'] ?? null,
                'user_id' => $user->id,
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
        });

        if ($result) {
            $this->clearChargesCache($data['user']->id);
        }

        return $result;
    }

    public function update(array $data, int $id): bool
    {
        $model = Charge::findOrFail($id);
        $signatureStatus = $model->signature?->signature_status ?? 'pendiente';

        if ($model->user_id === $data['user']->id && in_array($signatureStatus, ['firmado', 'rechazado'], true)) {
            return false;
        }

        $result = DB::transaction(function () use ($data, $model, $signatureStatus) {
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

            $this->syncInteresado($model, $data);

            if (!empty($signatureData)) {
                $model->signature()->updateOrCreate(['charge_id' => $model->id], $signatureData);
            }

            return true;
        });

        if ($result) {
            $this->clearChargesCache($data['user']->id, $assignedTo ?? null);
        }

        return $result;
    }

    public function delete(array $data, int $id): bool
    {
        try {
            $model = Charge::findOrFail($id);
            $userId = $model->user_id;
            $assignedTo = $model->signature?->assigned_to;

            if ($model->signature) {
                $this->imageService->deleteIfExists($model->signature->signature_root);
                $this->imageService->deleteIfExists($model->signature->evidence_root);
            }
            
            $deleted = (bool) $model->delete();
            if ($deleted) {
                $this->clearChargesCache($userId, $assignedTo);
            }
            return $deleted;
        } catch (\Throwable $e) {
            Log::error("Error deleting charge {$id}: " . $e->getMessage());
            throw $e;
        }
    }

    public function signStore(array $data, array $files, int $chargeId, int $userId): bool
    {
        $result = DB::transaction(function () use ($data, $files, $chargeId, $userId) {
            $charge = Charge::with('signature')->findOrFail($chargeId);
            $isTitular = (bool) ($data['titularidad'] ?? false);

            $signaturePath = "private/charges_signatures/charge_{$chargeId}.svg";
            Storage::disk('local')->put($signaturePath, $data['firma']);

            $cartaPoderPath = null;
            if (!$isTitular && !empty($files['carta_poder'])) {
                $cartaPoderPath = $this->imageService->storeAndOptimize($files['carta_poder'], 'private/charges_poder', 50);
            }

            $evidencePath = $charge->signature?->evidence_root;
            if (!empty($files['evidence_root'])) {
                $this->imageService->deleteIfExists($evidencePath);
                $evidencePath = $this->imageService->storeAndOptimize(
                    $files['evidence_root'],
                    'private/charges_evidence',
                    null,
                    "charge_{$chargeId}_evidence_" . now()->format('Ymd_His')
                );
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

    // Métodos de obtención con caché
    private function getCachedSignedCount(): int { return Cache::remember('charges_signed_count', self::CACHE_TTL, fn() => $this->countSignedCharges()); }
    private function getCachedUnsignedCount(): int { return Cache::remember('charges_unsigned_count', self::CACHE_TTL, fn() => $this->countUnsignedCharges()); }
    private function getCachedUsersToAssign($user): Collection { $uid = $user?->id ?? 0; return Cache::remember("users_to_assign_{$uid}", self::REF_CACHE_TTL, fn() => $this->usersToAssign($user)); }
    private function getCachedPeriodOptions(?string $defaultPeriod): array { $key = 'period_options_' . ($defaultPeriod ?? 'none'); return Cache::remember($key, self::REF_CACHE_TTL, fn() => $this->getPeriodOptions($defaultPeriod)); }

    /**
     * Invalida la caché. Al no tener tags, usamos el patrón de borrar claves conocidas.
     */
    private function clearChargesCache(?int $userId = null, ?int $assignedTo = null): void
    {
        Cache::forget('charges_signed_count');
        Cache::forget('charges_unsigned_count');
        Cache::forget('period_options_none');
        if ($userId) Cache::forget("users_to_assign_{$userId}");
        if ($assignedTo) Cache::forget("users_to_assign_{$assignedTo}");
        
        // Las listas indexadas (charges_index_*) expirarán en 5 min. 
        // Para forzar una limpieza inmediata en driver 'file', se recomienda usar Tags si el driver lo permite.
    }

    private function resolveNaturalPerson(array $data): ?int
    {
        if ($data['tipo_interesado'] !== 'Persona Natural') return null;
        $payload = $this->naturalPersonPayload($data);
        $dni = trim((string) ($payload['dni'] ?? ''));
        return ($dni !== '') ? NaturalPerson::firstOrCreate(['dni' => $dni], $payload)->id : NaturalPerson::create($payload)->id;
    }

    private function resolveLegalEntity(array $data): ?int
    {
        if ($data['tipo_interesado'] !== 'Persona Juridica') return null;
        $payload = $this->legalEntityPayload($data);
        $ruc = trim((string) ($payload['ruc'] ?? ''));
        return ($ruc !== '') ? LegalEntity::firstOrCreate(['ruc' => $ruc], $payload)->id : LegalEntity::create($payload)->id;
    }

    private function syncInteresado(Charge $model, array $data): void
    {
        $naturalPersonId = null; $legalEntityId = null;
        if (in_array($data['tipo_interesado'], ['Persona Natural', 'Trabajador UGEL'], true)) {
            $payload = $this->naturalPersonPayload($data);
            $person = $model->naturalPerson ?: NaturalPerson::create($payload);
            $person->update($payload); $naturalPersonId = $person->id;
        }
        if ($data['tipo_interesado'] === 'Persona Juridica') {
            $payload = $this->legalEntityPayload($data);
            $entity = $model->legalEntity ?: LegalEntity::create($payload);
            $entity->update($payload); $legalEntityId = $entity->id;
        }
        $model->update(['natural_person_id' => $naturalPersonId, 'legal_entity_id' => $legalEntityId]);
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
    private function naturalPersonPayload(array $data): array { return ['dni' => $data['dni'] ?? null, 'nombres' => $data['nombres'] ?? null, 'apellido_paterno' => $data['apellido_paterno'] ?? null, 'apellido_materno' => $data['apellido_materno'] ?? null]; }
    private function legalEntityPayload(array $data): array { return ['ruc' => $data['ruc'] ?? null, 'razon_social' => $data['razon_social'] ?? null, 'district' => $data['district'] ?? null, 'contact_number' => $data['contact_number'] ?? null]; }
}
