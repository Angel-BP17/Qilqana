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
        Schema::create('signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('charge_id')->constrained('charges')->onDelete('cascade')->unique();

            // Datos de la firma digital
            $table->enum('signature_status', ['pendiente', 'firmado', 'rechazado'])->default('pendiente');

            // Almacenamiento
            $table->string('signature_root')->nullable();
            $table->string('evidence_root')->nullable();
            $table->string('carta_poder_path')->nullable();

            // Usuarios involucrados en la firma
            $table->foreignId('signed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');

            // Tiempos de la firma
            $table->timestamp('signature_requested_at')->nullable();
            $table->timestamp('signature_completed_at')->nullable();

            // Comentarios y otros
            $table->text('signature_comment')->nullable();
            $table->boolean('titularidad')->default(false);
            $table->string('parentesco')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signatures');
    }
};
