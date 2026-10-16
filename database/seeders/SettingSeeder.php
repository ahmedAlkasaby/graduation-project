<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $general = [
            ['type' => 'site_open',        'key' => 'site_open',        'value' => 'yes',                              'group' => 'setting'],
            ['type' => 'site_title',       'key' => 'site_title',       'value' => 'Systemira',                        'group' => 'setting'],
            ['type' => 'site_url',         'key' => 'site_url',         'value' => 'http://127.0.0.1:8000',            'group' => 'setting'],
            ['type' => 'website_url',      'key' => 'website_url',      'value' => 'http://127.0.0.1:8000',            'group' => 'setting'],
            ['type' => 'admin_url',        'key' => 'admin_url',        'value' => 'admin',                            'group' => 'setting'],
            ['type' => 'admin_language',   'key' => 'admin_language',   'value' => 'ar',                               'group' => 'setting'],
            ['type' => 'site_email',       'key' => 'site_email',       'value' => 'info@systemira.com',               'group' => 'setting'],
            ['type' => 'site_phone',       'key' => 'site_phone',       'value' => '01029936932',                      'group' => 'setting'],
            ['type' => 'table_limit',      'key' => 'table_limit',      'value' => '100',                              'group' => 'setting'],
            ['type' => 'ssl_certificate',  'key' => 'ssl_certificate',  'value' => 'no',                               'group' => 'setting'],
            ['type' => 'logo_image',       'key' => 'logo_image',       'value' => '',                                 'group' => 'setting'],
        ];

        $social = [
            ['type' => 'facebook',   'key' => 'facebook',   'value' => 'https://www.facebook.com',  'group' => 'social', 'autoload' => 1],
            ['type' => 'twitter',    'key' => 'twitter',    'value' => 'https://www.twitter.com',   'group' => 'social', 'autoload' => 1],
            ['type' => 'youtube',    'key' => 'youtube',    'value' => 'https://youtube.com',        'group' => 'social', 'autoload' => 1],
            ['type' => 'instagram',  'key' => 'instagram',  'value' => 'https://instagram.com',      'group' => 'social', 'autoload' => 1],
            ['type' => 'whatsapp',   'key' => 'whatsapp',   'value' => '01029936932',                'group' => 'social', 'autoload' => 1],
        ];

        $debug = [
            ['type' => 'user_type_debug', 'key' => 'user_type_debug', 'value' => 'admin', 'group' => 'setting'],
            ['type' => 'user_id_debug',   'key' => 'user_id_debug',   'value' => '1',                 'group' => 'setting'],
            ['type' => 'app_debug',       'key' => 'app_debug',       'value' => 'yes',               'group' => 'setting'],
        ];

        $userCode = [
            ['type' => 'user_code_lifetime_minutes', 'key' => 'user_code_lifetime_minutes', 'value' => '10', 'group' => 'code'],
            ['type' => 'user_code_length',           'key' => 'user_code_length',           'value' => '4',  'group' => 'code'],
        ];

        foreach ([...$general, ...$social, ...$debug, ...$userCode] as $setting) {
            Setting::create($setting);
        }
    }
}
