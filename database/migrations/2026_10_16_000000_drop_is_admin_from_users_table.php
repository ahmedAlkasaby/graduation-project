<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * `type` (see App\Enums\UserTypeEnum) is now the only thing that says whether
 * a user is an admin. Existing rows are moved onto the new types before the
 * flag goes: every flagged user becomes `admin`, and any app user still on an
 * old type (`client`, …) becomes `donor`.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->where('is_admin', 1)->update(['type' => 'admin']);

        DB::table('users')
            ->where('is_admin', 0)
            ->where(fn ($query) => $query->whereNull('type')->orWhereNotIn('type', ['donor', 'volunteer']))
            ->update(['type' => 'donor']);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('is_admin')->default(0)->after('is_notify');
        });

        DB::table('users')->where('type', 'admin')->update(['is_admin' => 1]);
    }
};
