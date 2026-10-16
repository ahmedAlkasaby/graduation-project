<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * What a user *changed*: one row per column that a save touched, holding the
 * value before and after.
 *
 * Written from the model events on SiteModelDefault, so any model extending it
 * is audited without the module having to ask for it.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('user_device_id')->nullable();
            $table->nullableMorphs('loggable');
            $table->string('key')->nullable();
            $table->mediumText('value_old')->nullable();
            $table->mediumText('value')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_device_id')->references('id')->on('user_devices')
                ->onUpdate('cascade')->nullOnDelete();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
