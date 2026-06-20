<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $charges = DB::table('charges')->whereNotNull('resolucion_id')->get();

        foreach ($charges as $charge) {
            DB::table('charge_resolucion')->insert([
                'charge_id' => $charge->id,
                'resolucion_id' => $charge->resolucion_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('charge_resolucion')->truncate();
    }
};
