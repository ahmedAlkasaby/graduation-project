<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/** The currency amounts are shown in. Only inserted when missing, so an edited value is kept. */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('settings')->where('key', 'currency')->doesntExist()) {
            DB::table('settings')->insert([
                'key' => 'currency',
                'type' => 'currency',
                'value' => 'EGP',
                'group' => 'setting',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        settings()->refresh();
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'currency')->delete();

        settings()->refresh();
    }
};
