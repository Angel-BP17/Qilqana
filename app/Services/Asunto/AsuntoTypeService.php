<?php

namespace App\Services\Asunto;

use App\Models\AsuntoType;
use Illuminate\Support\Facades\DB;

class AsuntoTypeService
{
    /**
     * Obtener todos los tipos de asunto con sus relaciones.
     */
    public function getAll()
    {
        return AsuntoType::with('resolucionTypes')->orderBy('name')->get();
    }

    /**
     * Crear un nuevo tipo de asunto y sincronizar con tipos de resolución.
     */
    public function create(array $data): AsuntoType
    {
        return DB::transaction(function () use ($data) {
            $asunto = AsuntoType::create([
                'name' => mb_strtoupper(trim($data['name']), 'UTF-8'),
                'description' => $data['description'] ?? null,
            ]);

            if (! empty($data['resolucion_type_ids'])) {
                $asunto->resolucionTypes()->sync($data['resolucion_type_ids']);
            }

            return $asunto;
        });
    }

    /**
     * Actualizar un tipo de asunto existente.
     */
    public function update(int $id, array $data): bool
    {
        return DB::transaction(function () use ($id, $data) {
            $asunto = AsuntoType::findOrFail($id);

            $asunto->update([
                'name' => mb_strtoupper(trim($data['name']), 'UTF-8'),
                'description' => $data['description'] ?? null,
            ]);

            if (isset($data['resolucion_type_ids'])) {
                $asunto->resolucionTypes()->sync($data['resolucion_type_ids']);
            }

            return true;
        });
    }

    /**
     * Eliminar un tipo de asunto.
     */
    public function delete(int $id): bool
    {
        $asunto = AsuntoType::findOrFail($id);

        return (bool) $asunto->delete();
    }
}
