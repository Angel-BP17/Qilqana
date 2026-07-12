<?php

namespace App\Filters;

use App\Models\Resolucion;

class ResolucionFilter
{
    public function applyFilters($filters)
    {
        return Resolucion::with(['charges.signature', 'charges.signature.signer', 'charges.signature.assignedTo', 'charges.interesado', 'type', 'naturalPeople', 'legalEntities', 'users', 'asuntoType', 'levelModality'])
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
            ->when($filters['resolucion_type_id'] ?? null, fn ($q, $typeId) => $q->where('resolucion_type_id', $typeId))
            ->when($filters['asunto_type_id'] ?? null, fn ($q, $asuntoId) => $q->where('asunto_type_id', $asuntoId))
            ->when($filters['level_modality_id'] ?? null, fn ($q, $modalityId) => $q->where('level_modality_id', $modalityId))
            ->when($filters['desde'] ?? null, fn ($q, $desde) => $q->whereDate('fecha', '>=', $desde))
            ->when($filters['hasta'] ?? null, fn ($q, $hasta) => $q->whereDate('fecha', '<=', $hasta))
            ->orderByDesc('fecha')
            ->orderByDesc('id');
    }
}
