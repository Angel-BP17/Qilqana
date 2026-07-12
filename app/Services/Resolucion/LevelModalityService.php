<?php

namespace App\Services\Resolucion;

use App\Models\LevelModality;
use Illuminate\Database\Eloquent\Collection;

class LevelModalityService
{
    public function getAll(): Collection
    {
        return LevelModality::orderBy('name')->get();
    }

    public function create(array $data): LevelModality
    {
        return LevelModality::create([
            'name' => mb_strtoupper($data['name'], 'UTF-8'),
            'description' => $data['description'] ?? null,
        ]);
    }

    public function update(array $data, LevelModality $levelModality): bool
    {
        return $levelModality->update([
            'name' => mb_strtoupper($data['name'], 'UTF-8'),
            'description' => $data['description'] ?? null,
        ]);
    }

    public function delete(LevelModality $levelModality): bool
    {
        return $levelModality->delete();
    }
}
