<?php

namespace App\Models;

use Illuminate\Support\Str;

class PasswordReset extends SiteModel
{
    protected $table = 'password_resets';
    protected $fillable = [
        'email', 'token'
    ];

    public static function createToken($email)
    {
        $token = Str::random(60);
        self::updateOrCreate(
            ['email' => $email],
            [
                'email' => $email,
                'token' => $token,
            ]
        );

        return $token;
    }
   
    public static function findByTokenAndEmail($token, $email): ?self
    {
        return self::where('token', $token)
            ->where('email', $email)
            ->first();
    }

}
