<?php

namespace App\Filters;

use App\Models\Resolucion;

class ResolucionFilter
{
    public function applyFilters($filters)
    {
        return Resolucion::with(['charge.signature', 'charge.signature.signer', 'charge.signature.assignedTo', 'charge.naturalPerson', 'charge.legalEntity'])
            ->when(
                $filters['search'] ?? null,
                // Agrupar OR para no romper el resto de filtros.
                fn ($q, $search) => $q->where(function ($q2) use ($search) {
                    $q2->where('nombres_apellidos', 'like', "%$search%")
                        ->orWhere('rd', 'like', "%$search%")
                        ->orWhere('asunto', 'like', "%$search%")
                        ->orWhere('procedencia', 'like', "%$search%")
                        ->orWhere('dni', 'like', "%$search%");
                })
            )
            ->when($filters['periodo'] ?? null, fn ($q, $periodo) => $q->where('periodo', $periodo))
            ->orderByDesc('fecha')
            ->orderByDesc('id');
    }
}
