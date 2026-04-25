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
            $table->string('rd', 20)->nullable();
            $table->string('periodo', 20)->nullable();
            $table->dateTime('fecha')->nullable();
            $table->string('signature_root')->nullable();
            $table->string('nombres_apellidos')->nullable();
            $table->string('dni')->nullable();
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
