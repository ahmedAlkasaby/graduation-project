<?php

namespace App\Http\Controllers\Admin;


class DashboardController extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->setClass('home');
    }

    public function index()
    {
        return view('admin.dashboard.index');
    }
}
