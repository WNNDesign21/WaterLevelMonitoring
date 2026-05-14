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
        Schema::create('water_level_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_id');
            $table->float('avg_tma');
            $table->float('max_tma');
            $table->float('min_tma');
            $table->float('avg_distance');
            $table->timestamp('recorded_at')->index();
            $table->timestamps();

            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('water_level_histories');
    }
};
