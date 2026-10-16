<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Traits\AdminHelperTrait;
use Illuminate\Database\Seeder;

/**
 * Generates the permission set from the module list in AdminHelperTrait, so a
 * new module only has to be named there.
 *
 * The app is API only, so a module has no create/edit form routes: its
 * permissions are index, store, update, show, status and destroy.
 */
class PermissionSeeder extends Seeder
{
    use AdminHelperTrait;

    /** Modules with nothing to manage — a single "view" permission each. */
    protected array $viewOnlyModels = [

        'activity_logs',
        'logs',
        'images',
        'settings',
    ];

    /** Permissions not tied to a resourceful route. */
    protected array $standalonePermissions = [
        'dashboard',
        'statistics',
    ];

    public function run(): void
    {
        $this->seedCrudPermissions($this->admin_models);
        $this->seedViewOnlyPermissions();
        $this->seedStandalonePermissions();
    }

    private function seedCrudPermissions(array $crudModels = []): void
    {
        foreach ($crudModels as $model) {
            $permissions = [
                "{$model}.index" => 'view',
                "{$model}.store" => 'create',
                "{$model}.update" => 'edit',
                "{$model}.show" => 'show',
                "{$model}.status" => 'status',
                "{$model}.destroy" => 'delete',
            ];

            foreach ($permissions as $name => $displayName) {
                Permission::firstOrCreate(
                    ['name' => $name],
                    ['description' => $model, 'display_name' => $displayName]
                );
            }
        }
    }

    private function seedViewOnlyPermissions(): void
    {
        foreach ($this->viewOnlyModels as $model) {
            Permission::firstOrCreate(
                ['name' => "{$model}.index"],
                ['description' => $model, 'display_name' => 'view']
            );
        }
    }

    private function seedStandalonePermissions(): void
    {
        foreach ($this->standalonePermissions as $model) {
            Permission::firstOrCreate(
                ['name' => $model],
                ['description' => $model, 'display_name' => 'view']
            );
        }
    }
}
