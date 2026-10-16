<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * What a user *did*: one row per request that reached a module route.
 *
 * Written by the CheckLastActive middlewares, so it records the route, the
 * verb and the resource the request addressed — not the data that changed.
 * That is the `logs` table's job.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('user_device_id')->nullable();
            $table->nullableMorphs('activity_loggable');
            $table->string('status')->nullable();
            $table->string('action')->nullable();
            $table->string('route')->nullable();
            $table->string('type')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_device_id')->references('id')->on('user_devices')
                ->onUpdate('cascade')->nullOnDelete();

            // The list is always "this user, newest first".
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
