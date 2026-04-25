<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class LegalEntityFilter
{
    public function apply(Builder $query, array $filters): Builder
    {
        return $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('ruc', 'like', "%{$search}%")
                    ->orWhere('razon_social', 'like', "%{$search}%")
                    ->orWhere('district', 'like', "%{$search}%");
            });
        });
    }
}
