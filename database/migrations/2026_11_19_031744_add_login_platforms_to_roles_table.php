<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Where the holders of a role may sign in: the Blade admin panel (web) and
 * the API login the app uses (mobile).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->boolean('can_login_web')->default(true)->after('description');
            $table->boolean('can_login_mobile')->default(true)->after('can_login_web');
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['can_login_web', 'can_login_mobile']);
        });
    }
};
