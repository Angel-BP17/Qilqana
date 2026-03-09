<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('charges', function (Blueprint $table) {
            $table->id();

            //Datos generales del cargo
            $table->string('n_charge');
            $table->string('asunto');
            $table->string('charge_period', 10)->nullable();
            $table->date('document_date')->nullable();

            //Interesado relacionado al cargo
            $table->enum('tipo_interesado', ['Persona Juridica', 'Persona Natural', 'Trabajador UGEL']);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('natural_person_id')->nullable()->constrained('natural_people')->nullOnDelete();
            $table->foreignId('legal_entity_id')->nullable()->constrained('legal_entities')->nullOnDelete();

            //Llave foranea a la tabla resoluciones
            $table->foreignId('resolucion_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
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
