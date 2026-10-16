<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('ip', 45);
            $table->string('ip_range', 45)->nullable();
            $table->string('device')->nullable();
            $table->string('platform')->nullable();
            $table->string('browser')->nullable();
            $table->tinyInteger('is_desktop')->default(1);
            $table->tinyInteger('is_phone')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');

            // The lookup updateOrCreateDevice() makes on every logged request.
            $table->index(['user_id', 'platform', 'browser', 'ip_range'], 'user_devices_lookup_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_devices');
    }
};
