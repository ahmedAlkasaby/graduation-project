<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $settings = [
        'user_code_lifetime_minutes' => '10',
        'user_code_length' => '4',
    ];

    public function up(): void
    {
        foreach ($this->settings as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['type' => $key, 'value' => $value, 'group' => 'user_code', 'created_at' => now(), 'updated_at' => now()],
            );
        }

        settings()->refresh();
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', array_keys($this->settings))->delete();

        settings()->refresh();
    }
};
