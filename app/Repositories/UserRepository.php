<?php

namespace App\Repositories;

use App\Models\User;


class UserRepository
{
    public function getAdminNotifiableIds()
    {
        $adminIds =  User::active()->where('is_notify', 1)->pluck('id')->toArray();
        return $adminIds;
    }

    public function getAdminNotifiable()
    {
        $admins =  User::active()->where('is_notify', 1)->get();
        return $admins;
    }
}
