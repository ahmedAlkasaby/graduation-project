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
            'case_categories' => $crud,
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

        // Runs the charity work end to end: categories, cases and every assistance action.
        'case_manager' => [
            'display_name' => 'Case Manager',
            'description' => 'Manages case categories, charity cases and their assistances',
            'permissions' => [
                'dashboard' => [''],
                'statistics' => [''],
                'case_categories' => $crud,
                'charity_cases' => ['index', 'show', 'store', 'update'],
                'case_assistances' => ['index', 'show', 'store', 'update', 'status', 'cancel_installment'],
                'cities' => ['index', 'show'],
                'regions' => ['index', 'show'],
            ],
        ],

        // Visits families and records their cases; sees assistances but cannot grant them.
        'social_researcher' => [
            'display_name' => 'Social Researcher',
            'description' => 'Registers and updates charity cases after field research',
            'permissions' => [
                'dashboard' => [''],
                'case_categories' => ['index', 'show'],
                'charity_cases' => ['index', 'show', 'store', 'update'],
                'case_assistances' => ['index', 'show'],
                'cities' => ['index', 'show'],
                'regions' => ['index', 'show'],
            ],
        ],

        // Handles the money side: assistances, their status and installments.
        'accountant' => [
            'display_name' => 'Accountant',
            'description' => 'Records assistances, changes their status and cancels installments',
            'permissions' => [
                'dashboard' => [''],
                'statistics' => [''],
                'charity_cases' => ['index', 'show'],
                'case_assistances' => ['index', 'show', 'store', 'update', 'status', 'cancel_installment'],
            ],
        ],

        // Types in new records; cannot change statuses, cancel or delete anything.
        'data_entry' => [
            'display_name' => 'Data Entry',
            'description' => 'Adds charity cases, assistances, cities and regions',
            'permissions' => [
                'dashboard' => [''],
                'case_categories' => ['index', 'show'],
                'charity_cases' => ['index', 'show', 'store'],
                'case_assistances' => ['index', 'show', 'store'],
                'cities' => ['index', 'show', 'store', 'update'],
                'regions' => ['index', 'show', 'store', 'update'],
            ],
        ],

        // Read-only access to everything the charity work produces, plus the audit trail.
        'auditor' => [
            'display_name' => 'Auditor',
            'description' => 'Views cases, assistances, statistics and the activity log without editing',
            'permissions' => [
                'dashboard' => [''],
                'statistics' => [''],
                'case_categories' => ['index', 'show'],
                'charity_cases' => ['index', 'show'],
                'case_assistances' => ['index', 'show'],
                'cities' => ['index', 'show'],
                'regions' => ['index', 'show'],
                'activity_logs' => ['index'],
            ],
        ],

        // Looks after accounts and the system itself, not the charity work.
        'system_manager' => [
            'display_name' => 'System Manager',
            'description' => 'Manages users, roles, settings, locations and system logs',
            'permissions' => [
                'dashboard' => [''],
                'users' => [...$crud, 'restore'],
                'roles' => ['index', 'show', 'store', 'update', 'destroy'],
                'settings' => ['index'],
                'cities' => $crud,
                'regions' => $crud,
                'activity_logs' => ['index'],
                'logs' => ['index'],
                'images' => ['index'],
            ],
        ],

    ],

];
