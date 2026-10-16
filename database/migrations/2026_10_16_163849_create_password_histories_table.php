<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One row per password change. `old_password` is the hash the account had
 * before the change; the new one is the user's current password, or the next
 * row's `old_password`.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('password_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('old_password');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_histories');
    }
};
