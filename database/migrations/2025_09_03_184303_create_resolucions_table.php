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
        Schema::create('resolucions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resolucion_type_id')->nullable()->constrained('resolucion_types')->nullOnDelete();
            $table->foreignId('asunto_type_id')->nullable()->constrained('asunto_types')->nullOnDelete();
            $table->string('rd', 20)->nullable();
            $table->dateTime('fecha')->nullable();
            $table->string('periodo', 20)->nullable();
            $table->string('signature_root')->nullable();
            $table->text('nombres_apellidos')->nullable();
            $table->text('dni')->nullable();
            $table->text('cedula')->nullable();
            $table->text('ruc')->nullable();
            $table->text('razon_social')->nullable();
            $table->text('asunto')->nullable();
            $table->string('procedencia')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resolucions');
    }
};
