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
        Schema::table('devices', function (Blueprint $table) {
            $table->float('siaga1_threshold')->default(14.5); // Awas
            $table->float('siaga2_threshold')->default(13.5); // Siaga
            $table->float('siaga3_threshold')->default(12.5); // Waspada
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn(['siaga1_threshold', 'siaga2_threshold', 'siaga3_threshold']);
        });
    }
};
