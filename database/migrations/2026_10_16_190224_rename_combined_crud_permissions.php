<?php

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;

/**
 * The app is API only, so there are no create/edit form routes. "x.create,x.store"
 * becomes "x.store" and "x.edit,x.update" becomes "x.update"; renaming the rows
 * keeps every role that already has them.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->rename(fn (string $name) => preg_replace('/^(\w+)\.create,\1\.store$/', '$1.store', $name));
        $this->rename(fn (string $name) => preg_replace('/^(\w+)\.edit,\1\.update$/', '$1.update', $name));
    }

    public function down(): void
    {
        $this->rename(fn (string $name) => preg_replace('/^(\w+)\.store$/', '$1.create,$1.store', $name));
        $this->rename(fn (string $name) => preg_replace('/^(\w+)\.update$/', '$1.edit,$1.update', $name));
    }

    private function rename(callable $newName): void
    {
        Permission::query()->get()->each(function (Permission $permission) use ($newName) {
            $name = $newName($permission->name);

            if ($name !== $permission->name) {
                $permission->update(['name' => $name]);
            }
        });
    }
};
