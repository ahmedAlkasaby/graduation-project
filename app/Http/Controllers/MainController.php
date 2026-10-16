<?php

namespace App\Http\Controllers;

use App\Traits\AdminTrait;
use App\Traits\HelperTrait;
use App\Traits\NotifiTrait;
use Illuminate\Routing\Controller as BaseController;

/**
 * The settings every controller reads: page size, phone format, panel URL.
 * Loaded once here rather than looked up per screen.
 */
class MainController extends BaseController
{
    use AdminTrait, HelperTrait, NotifiTrait;

    protected $user;

    protected $type;

    protected $limit;

    protected $api_limit;

    protected $admin_url;

    protected $phone_code;

    protected $phone_regex;

    protected $language;

    protected $site_url;

    protected $class;

    protected $table;

    public function __construct()
    {
        $settings = settings();

        $this->limit = (int) ($settings->get('table_limit') ?: 50);
        $this->api_limit = 10;
        $this->admin_url = $settings->get('admin_url') ?: 'admin';
        $this->phone_code = $settings->get('phone_code') ?: $this->phone_code_default;
        $this->phone_regex = $settings->get('phone_regex') ?: $this->phone_regex_default;
        $this->site_url = url('');
        $this->class = 'user';
        $this->table = 'users';
    }
}
