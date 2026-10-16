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
            $table->string('name')->nullable();
            $table->string('username')->nullable();
            $table->string('name_first')->nullable();
            $table->string('name_last')->nullable();
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
            $table->string('phone_code', 50)->nullable();
            $table->timestamp('last_active')->nullable();
            $table->rememberToken();

            // verification codes
            $table->string('code', 50)->nullable();
            $table->timestamp('code_expire')->nullable();
            $table->string('sms_code', 50)->nullable();
            $table->dateTime('sms_code_expire')->nullable();

            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('gender', 50)->nullable();
            $table->string('locale', 10)->default('en');
            $table->string('theme', 20)->default('light');

            $table->tinyInteger('active')->default(1);
            $table->tinyInteger('is_notify')->default(1);
            $table->tinyInteger('is_admin')->default(0);
            $table->tinyInteger('is_client')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index('type');
            $table->index('active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
