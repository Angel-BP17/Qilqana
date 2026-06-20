<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('charges', function (Blueprint $table) {
                $table->dropForeign(['resolucion_id']);
                $table->dropColumn('resolucion_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('charges', function (Blueprint $table) {
            $table->foreignId('resolucion_id')->nullable()->constrained()->onDelete('cascade');
        });
    }
};
