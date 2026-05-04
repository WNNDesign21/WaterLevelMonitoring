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
            $table->float('elevation_mdpl')->default(14.00)->after('longitude');
            $table->integer('sensor_to_bank')->default(100)->after('elevation_mdpl'); // cm
            $table->integer('river_depth')->default(100)->after('sensor_to_bank'); // cm
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn(['elevation_mdpl', 'sensor_to_bank', 'river_depth']);
        });
    }
};
