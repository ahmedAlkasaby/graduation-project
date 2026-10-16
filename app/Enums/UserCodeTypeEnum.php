<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;


enum UserCodeTypeEnum: string
{
    use HasOptions;

    case VerifyRegister = 'verify_register';
    case ForgetPassword = 'forget_password';

    public function label(): string
    {
        return match ($this) {
            self::VerifyRegister => __('Verify Register'),
            self::ForgetPassword => __('Forget Password'),
        };
    }
}
