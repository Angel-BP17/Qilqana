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
        Schema::table('resolucions', function (Blueprint $row) {
            $row->foreignId('asunto_type_id')->nullable()->after('resolucion_type_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resolucions', function (Blueprint $row) {
            $row->dropForeign(['asunto_type_id']);
            $row->dropColumn('asunto_type_id');
        });
    }
};
