<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();

            // social login
            $table->string('provider', 150)->nullable();
            $table->string('provider_id', 150)->nullable();
            $table->string('provider_token', 150)->nullable();

            $table->timestamp('email_verified_at')->nullable();
            $table->string('type', 50)->nullable();
            $table->string('image')->nullable();
            $table->string('phone')->nullable();
            $table->timestamp('last_active')->nullable();
            $table->rememberToken();

            $table->date('birth_date')->nullable();
            $table->string('gender', 50)->nullable();
            $table->string('locale', 10)->default('en');
            $table->string('theme', 20)->default('light');

            $table->tinyInteger('is_notify')->default(1);
            $table->tinyInteger('is_admin')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
