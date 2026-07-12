<?php

namespace Database\Seeders;

use App\Models\LevelModality;
use Illuminate\Database\Seeder;

class LevelModalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modalities = [
            [
                'name' => 'PRIMARIA',
                'description' => 'Nivel de Educación Primaria',
            ],
            [
                'name' => 'SECUNDARIA',
                'description' => 'Nivel de Educación Secundaria',
            ],
        ];

        foreach ($modalities as $modality) {
            LevelModality::updateOrCreate(
                ['name' => $modality['name']],
                $modality
            );
        }
    }
}
