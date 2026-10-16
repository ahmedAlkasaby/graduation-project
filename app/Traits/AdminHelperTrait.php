<?php

namespace App\Traits;

use App\Enums\DeviceTypeEnum;
use App\Enums\UserTypeEnum;

/**
 * The vocabulary the admin panel is built on: which modules exist, which user
 * types exist, and the small lists the forms and filters read from.
 *
 * A new project adds its modules to $admin_models (and, if the module is soft
 * deletable, to $admin_delete_models / $admin_restore_models). PermissionSeeder
 * reads $admin_models to generate the permission set, so adding a module here
 * and re-seeding is all that is needed to make it appear in the role form.
 */
trait AdminHelperTrait
{
    protected $phone_code_default = '20';

    protected $phone_regex_default = '^01[0-2,5][0-9]{8}$';

    protected $default_language_use = 'en';

    protected $language_array = ['en', 'ar'];

    protected $admin_models = [
        'cities',
        'roles',
        'users',
    ];

    protected $admin_delete_models = [
        'cities',
        'users',
    ];

    protected $admin_restore_models = [
        'users',
    ];

    protected $admin_perms_models = [
        'activity_logs.index',
        'cities.index',
        'logs.index',
        'roles.index',
        'users.index',
    ];

    protected $log_not_in = ['id', 'password', 'remember_token', 'image', 'created_at', 'updated_at', 'deleted_at'];

    protected $setting_image = ['logo_image', 'share_image'];

    protected $setting_social_array = ['facebook', 'twitter', 'youtube', 'instagram', 'whatsapp', 'snapchat', 'tiktok'];

    protected $setting_array = [
        'site_email', 'site_phone', 'logo_image', 'site_language', 'admin_language',
        'table_limit', 'address', 'site_title', 'site_open', 'site_url', 'admin_url',
        'default_country',
    ];

    protected $setting_options_array = [
        'social_login', 'primary_login', 'app_open',
    ];

    protected $setting_array_api = ['site_email', 'site_phone', 'address', 'social_login', 'primary_login'];

    protected $limit_array = [25, 50, 100, 250];

    protected $limit_array_admin = [25, 50, 100, 250, 500, 1000, 2500, 5000, 10000];

    protected $limit_export_array_admin = [0, 25, 50, 100, 250, 500, 1000, 2500, 5000, 10000];

    protected $empty_value_array = ['NA', '0', 'null', 'NULL', '', 'All', 'None', '-All-', '-None-', '--All--', '--None--'];

    protected $property_ajax_array = ['active', 'is_notify', 'is_read'];

    protected $property_date_time_array = ['created_at', 'updated_at', 'deleted_at'];

    protected $device_type_array = [DeviceTypeEnum::Apple->value, DeviceTypeEnum::Android->value, DeviceTypeEnum::Huawei->value, DeviceTypeEnum::Website->value];


    // User types, see UserTypeEnum. Admin is the only type that reaches the
    // dashboard, and an admin has access to all of it.

    protected $type_array = [UserTypeEnum::Admin->value];

    protected $admin_roles = [UserTypeEnum::Admin->value];

    protected $user_admins = [UserTypeEnum::Admin->value];

    protected $user_roles_array = [UserTypeEnum::Donor->value, UserTypeEnum::Volunteer->value, UserTypeEnum::Admin->value];

    protected $access_all_type = [UserTypeEnum::Admin->value];

    protected $access_all_id = [1];

    protected $access_all_perms = ['roles.destroy'];
}
