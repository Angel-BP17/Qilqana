<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('resolucions', function (Blueprint $table) {
                // Eliminar columna obsoleta
                $table->dropColumn('tipo_interesado');

                // Cambiar tipos a TEXT para concatenación masiva
                $table->text('nombres_apellidos')->nullable()->change();
                $table->text('dni')->nullable()->change();
                $table->text('cedula')->nullable()->change();
                $table->text('ruc')->nullable()->change();
                $table->text('razon_social')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resolucions', function (Blueprint $table) {
            $table->string('tipo_interesado')->nullable()->after('periodo');

            // Revertir a VARCHAR (aproximado)
            $table->string('nombres_apellidos')->nullable()->change();
            $table->string('dni')->nullable()->change();
            $table->string('cedula')->nullable()->change();
            $table->string('ruc')->nullable()->change();
            $table->string('razon_social')->nullable()->change();
        });
    }
};
