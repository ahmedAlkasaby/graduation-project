<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected array $tables = [
        'activity_logs' => 'user_id',
        'logs' => 'user_id',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table => $after) {
            if (! Schema::hasTable($table) || Schema::hasColumn($table, 'user_device_id')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($after) {
                $blueprint->unsignedBigInteger('user_device_id')->nullable()->after($after);

                $blueprint->foreign('user_device_id')->references('id')->on('user_devices')
                    ->onUpdate('cascade')->nullOnDelete();

                // The list is always "this user, newest first".
                $blueprint->index(['user_id', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        foreach (array_keys($this->tables) as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'user_device_id')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                $blueprint->dropForeign([$table.'_user_device_id_foreign']);
                $blueprint->dropIndex([$table.'_user_id_created_at_index']);
                $blueprint->dropColumn('user_device_id');
            });
        }
    }
};
