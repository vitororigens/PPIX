<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('userspp', function (Blueprint $table) {
            $table->id();
            $table->string('fcm_token');
            $table->integer('car_id')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('group_name');
            $table->string('password');
            $table->string('passwordBank')->nullable();
            $table->string('passwordApp')->nullable();
            $table->string('passwordEmergecy')->nullable();
            $table->string('passwordDevice')->nullable();
            $table->string('passwordDeviceEmergency')->nullable();
            $table->string('lat');
            $table->string('log');
            $table->timestampTz('last_location_at')->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();

            $table->foreign('car_id')->references('id')->on('cars')->onDelete('cascade');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('userspp');
    }
};
