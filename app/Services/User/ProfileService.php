<?php

namespace App\Services\User;

use App\Models\User;

class ProfileService
{
    public function updateProfile(User $user,array $input, $primaryLogin)
    {
        if ($primaryLogin === 'email') {
            unset($input['email']);
        }

        if ($primaryLogin === 'phone') {
            unset($input['phone']);
        }

        unset($input['is_phone']);

        $user->update($input);

        return $user;
    }

    public function updatePhone(User $user,string $phone)
    {
        $user->update(['phone' => $phone]);
        return $user;
    }
}
