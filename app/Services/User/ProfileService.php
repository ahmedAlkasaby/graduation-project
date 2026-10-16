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

        if (isset($input['name_first']) || isset($input['name_last'])) {
            $nameFirst = $input['name_first'] ?? $user->name_first;
            $nameLast  = $input['name_last'] ?? $user->name_last;

            $input['user_name'] = trim($nameFirst . ' ' . $nameLast);
            $input['name'] = $input['user_name'];
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
