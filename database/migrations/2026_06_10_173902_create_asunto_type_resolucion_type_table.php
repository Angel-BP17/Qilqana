<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asunto_type_resolucion_type', function (Blueprint $row) {
            $row->id();
            $row->foreignId('asunto_type_id')->constrained()->cascadeOnDelete();
            $row->foreignId('resolucion_type_id')->constrained()->cascadeOnDelete();
            $row->timestamps();

            // Unicidad de la relación
            $row->unique(['asunto_type_id', 'resolucion_type_id'], 'asunto_res_type_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asunto_type_resolucion_type');
    }
};
