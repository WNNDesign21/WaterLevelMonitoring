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
        Schema::table('water_level_histories', function (Blueprint $table) {
            // Drop existing recorded_at index if needed or just add composite
            // A composite index (device_id, recorded_at) is superior for our query patterns
            $table->index(['device_id', 'recorded_at'], 'idx_device_recorded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('water_level_histories', function (Blueprint $table) {
            $table->dropIndex('idx_device_recorded_at');
        });
    }
};
