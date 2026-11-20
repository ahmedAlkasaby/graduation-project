<?php

/*
|--------------------------------------------------------------------------
| Permissions and default roles
|--------------------------------------------------------------------------
|
| One place to read every permission and what each seeded role holds.
| Permissions are named "{module}.{action}" after the admin routes they guard
| ("api.v1.admin.users.index" needs "users.index"); '' stands for a permission
| named after the module itself ("dashboard"). "*" means every permission.
|
| LaratrustSeeder creates the permissions and roles and syncs every role
| below to exactly this list: `php artisan db:seed --class=LaratrustSeeder`.
|
| A super admin is a user type, not a role: it skips every permission check,
| so it is not listed here.
|
*/

$crud = ['index', 'show', 'store', 'update', 'status', 'destroy'];

return [

    'permissions_structure' => [

        // Modules backed by a model: its CRUD actions, plus any extra ones.
        'resources' => [
            'cities' => $crud,
            'regions' => $crud,
            'charity_cases' => ['index', 'show', 'store', 'update'],
            'case_assistances' => ['index', 'show', 'store', 'update', 'status', 'cancel_installment'],
            'roles' => ['index', 'show', 'store', 'update', 'destroy'],
            'users' => [...$crud, 'restore'],
            'activity_logs' => ['index'],
            'logs' => ['index'],
            'images' => ['index'],
            'settings' => ['index'],
        ],

        // Screens and cards that are not a model's CRUD.
        'custom' => [
            'dashboard' => [''],
            'statistics' => [''],
        ],

    ],

    /*
     * The label stored as a permission's display_name, by action. An action
     * not listed here keeps its own name.
     */
    'action_labels' => [
        '' => 'view',
        'index' => 'view',
        'show' => 'show',
        'store' => 'create',
        'update' => 'edit',
        'status' => 'status',
        'destroy' => 'delete',
        'restore' => 'restore',
        'cancel_installment' => 'cancel installment',
    ],

    'roles_structure' => [

        'admin' => [
            'display_name' => 'Admin',
            'description' => 'Full dashboard access',
            'permissions' => '*',
        ],

    ],

];
