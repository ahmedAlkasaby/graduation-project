<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

/**
 * santigarcor/laratrust. Teams and the bundled admin panel are off — this
 * project has its own roles screen (Admin\RoleController).
 */
return [

    /*
     * Off because AppServiceProvider already registers a global morph map that
     * maps 'users' to App\Models\User — the pivot rows pick that up on their
     * own. Turning this on would only register the same mapping twice.
     */
    'use_morph_map' => false,

    'checkers' => [
        'user' => 'default',
        'role' => 'default',
    ],


    'cache' => [
        'enabled' => env('LARATRUST_ENABLE_CACHE', true),
        'expiration_time' => 3600,
    ],

    /*
     * The array key names the relationship on Role and Permission, and the
     * user_id / user_type columns on role_user and permission_user.
     */
    'user_models' => [
        'users' => User::class,
    ],

    'models' => [
        'role' => Role::class,
        'permission' => Permission::class,
    ],

    'tables' => [
        'roles' => 'roles',
        'permissions' => 'permissions',
        'role_user' => 'role_user',
        'permission_user' => 'permission_user',
        'permission_role' => 'permission_role',
    ],

    'foreign_keys' => [
        'user' => 'user_id',
        'role' => 'role_id',
        'permission' => 'permission_id',
    ],

    /*
     * Laratrust registers the 'role', 'permission' and 'ability' route
     * middleware itself — see bootstrap/app.php, which no longer aliases them.
     *
     * The admin panel does not lean on these: AutoCheckPermission resolves the
     * permission from the route name instead.
     */
    'middleware' => [
        'register' => true,
        'handling' => 'abort',
        'handlers' => [
            'abort' => [
                'code' => 403,
                'message' => 'User does not have any of the necessary access rights.',
            ],
            'redirect' => [
                'url' => '/admin/unauthorized',
                'message' => [
                    'key' => 'error',
                    'content' => '',
                ],
            ],
        ],
    ],

    'teams' => [
        'enabled' => false,
        'strict_check' => false,
    ],

    /*
     * Keep this on: the whole panel authorises through can() / @can, from
     * RolePolicy down to the sidebar links.
     */
    'permissions_as_gates' => true,

    'panel' => [
        'register' => false,
    ],
];
