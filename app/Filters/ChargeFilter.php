<?php

namespace App\Filters;

use App\Models\Charge;
use App\Models\LegalEntity;
use App\Models\NaturalPerson;
use App\Models\User;

class ChargeFilter
{
    public function getAllSentCharges($searchfilters, $user)
    {
        return Charge::with(['user', 'interesado', 'signature', 'signature.signer', 'signature.assignedTo', 'resolucions'])
            ->where('user_id', $user?->id)
            ->whereDoesntHave('resolucions')
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
            'interesado',
            'signature',
            'signature.signer',
            'signature.assignedTo',
            'resolucions',
        ])
            ->whereDoesntHave('resolucions')
            ->whereHas('signature', fn ($q) => $q->where('assigned_to', $user?->id))
            ->where('interesado_type', User::class)
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
        return Charge::with(['user', 'interesado', 'signature', 'signature.signer', 'signature.assignedTo', 'resolucions'])
            ->where('user_id', $user?->id)
            ->whereDoesntHave('resolucions')
            ->where('interesado_type', '!=', User::class)
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
        return Charge::with(['user', 'interesado', 'signature', 'signature.signer', 'signature.assignedTo', 'resolucions'])
            ->whereHas('resolucions')
            ->when(! $user?->hasRole('REGISTRADOR RESOLUCIONES'), function ($q) use ($user) {
                $q->where(function ($q2) use ($user) {
                    $q2->where(function ($q3) use ($user) {
                        $q3->where('interesado_id', $user?->id)
                            ->where('interesado_type', \App\Models\User::class);
                    })->orWhereHas('signature', function ($q3) use ($user) {
                        $q3->where('assigned_to', $user?->id);
                    });
                });
            })
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
                ->orWhereHasMorph('interesado', [NaturalPerson::class, LegalEntity::class], function ($morphQuery, $type) use ($search) {
                    if ($type === NaturalPerson::class) {
                        $morphQuery->where('nombres', 'like', "%{$search}%")
                            ->orWhere('apellido_paterno', 'like', "%{$search}%")
                            ->orWhere('apellido_materno', 'like', "%{$search}%")
                            ->orWhere('dni', 'like', "%{$search}%");
                    } elseif ($type === LegalEntity::class) {
                        $morphQuery->where('razon_social', 'like', "%{$search}%")
                            ->orWhere('ruc', 'like', "%{$search}%")
                            ->orWhere('district', 'like', "%{$search}%");
                    }
                });
        });
    }
}
