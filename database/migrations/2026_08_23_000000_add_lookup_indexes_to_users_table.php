<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    protected array $searchable = ['name', 'name_first', 'name_last', 'email', 'phone', 'username'];

    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index('email');
            $table->index('phone');
            $table->index('username');
            $table->index('deleted_at');
            $table->index('created_at');

            $table->index(['provider', 'provider_id']);
        });

        if ($this->supportsFullText()) {
            Schema::table('users', function (Blueprint $table) {
                $table->fullText($this->searchable, 'users_search_fulltext');
            });
        }
    }

    public function down(): void
    {
        if ($this->supportsFullText()) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropFullText('users_search_fulltext');
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->dropIndex(['phone']);
            $table->dropIndex(['username']);
            $table->dropIndex(['deleted_at']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['provider', 'provider_id']);
        });
    }


    protected function supportsFullText(): bool
    {
        return in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb', 'pgsql'], true);
    }
};
