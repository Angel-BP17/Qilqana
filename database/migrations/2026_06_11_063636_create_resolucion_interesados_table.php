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
        Schema::create('resolucion_interesados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resolucion_id')->constrained()->cascadeOnDelete();
            $table->morphs('interesado'); // Crea interesado_id e interesado_type
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resolucion_interesados');
    }
};
