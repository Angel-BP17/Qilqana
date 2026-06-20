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
        Schema::table('resolucions', function (Blueprint $table) {
            $table->string('tipo_interesado')->after('periodo')->nullable()->default('Persona Natural');
            $table->string('cedula')->after('dni')->nullable();
            $table->string('razon_social')->after('cedula')->nullable();
            if (DB::getDriverName() !== 'sqlite') {
                $table->string('nombres_apellidos')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resolucions', function (Blueprint $table) {
            $table->dropColumn(['tipo_interesado', 'cedula', 'razon_social']);
            $table->string('nombres_apellidos')->nullable(false)->change();
        });
    }
};
