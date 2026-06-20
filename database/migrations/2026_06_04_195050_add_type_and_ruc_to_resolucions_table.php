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
        Schema::table('resolucions', function (Blueprint $table) {
            $table->foreignId('resolucion_type_id')->after('id')->nullable()->constrained('resolucion_types')->nullOnDelete();
            $table->string('ruc', 11)->after('dni')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resolucions', function (Blueprint $table) {
            $table->dropForeign(['resolucion_type_id']);
            $table->dropColumn(['resolucion_type_id', 'ruc']);
        });
    }
};
