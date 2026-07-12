<?php

namespace Database\Seeders;

use App\Models\AsuntoType;
use App\Models\ResolucionType;
use Illuminate\Database\Seeder;

class AsuntoTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $asuntoTypes = [
            ['name' => 'LICENCIA POR SALUD'],
            ['name' => 'LICENCIA POR ESTUDIO'],
            ['name' => 'LICENCIA POR MATERNIDAD'],
            ['name' => 'LICENCIA POR PATERNIDAD'],
            ['name' => 'LICENCIA POR ADOPCIÓN'],
            ['name' => 'LICENCIA POR CUIDADO DE FAMILIAR ENFERMO'],
        ];

        $createdTypes = [];
        foreach ($asuntoTypes as $type) {
            $createdTypes[] = AsuntoType::firstOrCreate(['name' => $type['name']], $type);
        }

        $resolucionTypes = ResolucionType::all();

        foreach ($createdTypes as $asuntoType) {
            $asuntoType->resolucionTypes()->sync($resolucionTypes->pluck('id')->toArray());
        }
    }
}
