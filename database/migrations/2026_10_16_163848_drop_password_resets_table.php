<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Password resets are now an emailed code in `user_codes`
 * (UserCodeTypeEnum::ForgetPassword), so the token table is no longer used.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('password_resets');
    }

    public function down(): void
    {
        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }
};
