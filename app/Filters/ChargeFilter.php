<?php

namespace App\Filters;

use App\Models\Charge;

class ChargeFilter
{
    public function getAllSentCharges($searchfilters, $user)
    {
        return Charge::with(['user', 'naturalPerson', 'legalEntity.representative.naturalPerson', 'signature', 'signature.signer', 'signature.assignedTo', 'resolucion'])
            ->where('user_id', $user?->id)
            ->whereNull('resolucion_id')
            ->when($searchfilters['period'], function ($q, $period) {
                $q->where(function ($q2) use ($period) {
                    $q2->where('charge_period', $period)->orWhereNull('charge_period');
                });
            })
            ->when(
                $searchfilters['search'],
                fn ($q, $search) => $this->applySearchFilter($q, $search)
            )
            ->when(
                $this->isValidSignatureStatus($searchfilters['signature_status']),
                fn ($q) => $q->whereHas(
                    'signature',
                    fn ($s) => $s->where('signature_status', $searchfilters['signature_status'])
                )
            )
            ->orderByDesc('created_at')
            ->get();
    }

    public function getAllReceivedCharges($searchfilters, $user)
    {
        return Charge::with([
            'user',
            'naturalPerson',
            'legalEntity.representative.naturalPerson',
            'signature',
            'signature.signer',
            'signature.assignedTo',
            'resolucion',
        ])
            ->whereNull('resolucion_id')
            ->whereHas('signature', fn ($q) => $q->where('assigned_to', $user?->id))
            ->whereNotIn('tipo_interesado', ['Persona Juridica', 'Persona Natural'])
            ->when($searchfilters['period'], function ($q, $period) {
                $q->where(function ($q2) use ($period) {
                    $q2->where('charge_period', $period)->orWhereNull('charge_period');
                });
            })
            ->when(
                $searchfilters['signature_status'] !== 'rechazado',
                fn ($q) => $q->whereHas(
                    'signature',
                    fn ($s) => $s->where('signature_status', '!=', 'rechazado')
                )
            )
            ->when(
                $searchfilters['search'],
                fn ($q, $search) => $this->applySearchFilter($q, $search)
            )
            ->when(
                $this->isValidSignatureStatus($searchfilters['signature_status']),
                fn ($q) => $q->whereHas(
                    'signature',
                    fn ($s) => $s->where('signature_status', $searchfilters['signature_status'])
                )
            )
            ->orderByDesc('created_at')
            ->get();
    }

    public function getAllCreatedCharges($searchfilters, $user)
    {
        return Charge::with(['user', 'naturalPerson', 'legalEntity.representative.naturalPerson', 'signature', 'signature.signer', 'signature.assignedTo', 'resolucion'])
            ->where('user_id', $user?->id)
            ->whereNull('resolucion_id')
            ->whereIn('tipo_interesado', ['Persona Juridica', 'Persona Natural'])
            ->when($searchfilters['period'], function ($q, $period) {
                $q->where(function ($q2) use ($period) {
                    $q2->where('charge_period', $period)->orWhereNull('charge_period');
                });
            })
            ->when(
                $searchfilters['search'],
                fn ($q, $search) => $this->applySearchFilter($q, $search)
            )
            ->when(
                $this->isValidSignatureStatus($searchfilters['signature_status']),
                fn ($q) => $q->whereHas(
                    'signature',
                    fn ($s) => $s->where('signature_status', $searchfilters['signature_status'])
                )
            )
            ->orderByDesc('created_at')
            ->get();
    }

    public function getAllResolutionCharges($searchfilters, $user)
    {
        return Charge::with(['user', 'naturalPerson', 'legalEntity.representative.naturalPerson', 'signature', 'signature.signer', 'signature.assignedTo', 'resolucion'])
            ->whereNotNull('resolucion_id')
            ->when($searchfilters['period'], function ($q, $period) {
                $q->where(function ($q2) use ($period) {
                    $q2->where('charge_period', $period)
                        ->orWhereNull('charge_period');
                });
            })
            ->when(
                $searchfilters['search'],
                fn ($q, $search) => $this->applySearchFilter($q, $search)
            )
            ->orderByDesc('created_at')
            ->get();
    }

    private function isValidSignatureStatus(?string $status): bool
    {
        return in_array($status, ['pendiente', 'firmado', 'rechazado'], true);
    }

    private function applySearchFilter($query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->where('n_charge', 'like', "%{$search}%")
                ->orWhere('asunto', 'like', "%{$search}%")
                ->orWhereHas('naturalPerson', function ($naturalPerson) use ($search) {
                    $naturalPerson->where('nombres', 'like', "%{$search}%")
                        ->orWhere('apellido_paterno', 'like', "%{$search}%")
                        ->orWhere('apellido_materno', 'like', "%{$search}%")
                        ->orWhere('dni', 'like', "%{$search}%");
                })
                ->orWhereHas('legalEntity', function ($legalEntity) use ($search) {
                    $legalEntity->where('razon_social', 'like', "%{$search}%")
                        ->orWhere('ruc', 'like', "%{$search}%")
                        ->orWhere('district', 'like', "%{$search}%");
                });
        });
    }
}
