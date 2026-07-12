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
        Schema::create('charges', function (Blueprint $table) {
            $table->id();

            // Datos generales del cargo
            $table->string('n_charge');
            $table->string('asunto');
            $table->string('document_path')->nullable();
            $table->string('charge_period', 10)->nullable();
            $table->date('document_date')->nullable();

            // Interesado relacionado al cargo (Polimórfico)
            $table->string('interesado_type');
            $table->unsignedBigInteger('interesado_id');

            // Creador del cargo
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Índices
            $table->index(['interesado_type', 'interesado_id'], 'charges_interesado_polymorphic_index');
            $table->index(['user_id', 'charge_period', 'n_charge'], 'charges_user_period_correlative_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('charges');
    }
};
