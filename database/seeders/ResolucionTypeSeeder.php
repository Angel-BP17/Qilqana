<?php

namespace Database\Seeders;

use App\Models\ResolucionType;
use Illuminate\Database\Seeder;

class ResolucionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $resolucionTypes = [
            ['name' => 'RESOLUCIÓN DIRECTORAL', 'abreviacion' => 'RD', 'created_at' => now()],
            ['name' => 'RESOLUCIÓN REGIONAL', 'abreviacion' => 'RDR', 'created_at' => now()],
            ['name' => 'RESOLUCIÓN MINISTERIAL', 'abreviacion' => 'RM', 'created_at' => now()],
            ['name' => 'RESOLUCIÓN SUPREMA', 'abreviacion' => 'RS', 'created_at' => now()],
            ['name' => 'RESOLUCIÓN ADMINISTRATIVA', 'abreviacion' => 'RDA', 'created_at' => now()],
        ];

        foreach ($resolucionTypes as $type) {
            ResolucionType::firstOrCreate(['name' => $type['name']], $type);
        }
    }
}
