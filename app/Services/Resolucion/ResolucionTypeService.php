<?php

namespace App\Services\Resolucion;

use App\Models\ResolucionType;
use Illuminate\Database\Eloquent\Collection;

class ResolucionTypeService
{
    public function getAll(): Collection
    {
        return ResolucionType::orderBy('name')->get();
    }

    public function create(array $data): ResolucionType
    {
        return ResolucionType::create([
            'name' => mb_strtoupper($data['name'], 'UTF-8'),
            'abreviacion' => isset($data['abreviacion']) ? mb_strtoupper(trim($data['abreviacion']), 'UTF-8') : null,
            'description' => $data['description'] ?? null,
        ]);
    }

    public function update(array $data, ResolucionType $type): bool
    {
        return $type->update([
            'name' => mb_strtoupper($data['name'], 'UTF-8'),
            'abreviacion' => isset($data['abreviacion']) ? mb_strtoupper(trim($data['abreviacion']), 'UTF-8') : null,
            'description' => $data['description'] ?? null,
        ]);
    }

    public function delete(ResolucionType $type): bool
    {
        return $type->delete();
    }
}
