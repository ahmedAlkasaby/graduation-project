<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_codes', function (Blueprint $table) {
            $table->dropColumn(['country_code', 'full_name']);
        });
    }

    public function down(): void
    {
        Schema::table('user_codes', function (Blueprint $table) {
            $table->string('country_code')->nullable()->after('email');
            $table->string('full_name')->nullable()->after('type');
        });
    }
};
