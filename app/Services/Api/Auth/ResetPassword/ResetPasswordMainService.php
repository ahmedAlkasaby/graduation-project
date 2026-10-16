<?php

namespace App\Services\Api\Auth\ResetPassword;

use App\Http\Controllers\Api\ApiHomeController;
use App\Models\PasswordReset;
use App\Models\User;
use Carbon\Carbon;

class ResetPasswordMainService extends ApiHomeController 
{


    public function handle($request)
    {
        $passwordReset=PasswordReset::findByTokenAndEmail($request->token, $request->email);
        if (!$passwordReset) {
            return [
                'error' => true,
                'field' => 'token'
            ];
        }
        $type = $request->type ?? "client";
        $sms_type = $request->sms_type ?? 'email';
        $field = $this->checkValidateEmailOrPhoneAndGetField($passwordReset->email);
        $user = $this->checkEmailInUsersAndGetUser($field, $passwordReset->email, $type);
        if (!$field || !$user) {
            return [
                'error' => true,
                'field' => $field
            ];
        }
        if($sms_type != "firebase"){
            $user = $this->checkExpirationOfCodeInUsers($field, $passwordReset->email, $type);
            if (!$user) {
                return [
                    'error' => true,
                    'field' => $field
                ];
            }
        }
        $this->changePassword($user, $request->password);
        return[
            'error'=>false,
            'user'=>$user
        ];
    }
     public function checkValidateEmailOrPhoneAndGetField($emailOrPhone)
    {
        $field = filter_var($emailOrPhone, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        if ($field == "phone") {
            if (!preg_match("/{$this->phone_regex}/", $emailOrPhone)) {
                return false;
            }
        }
        return $field;
    }

    public function changePassword($user, $password)
    {
        $user->password = bcrypt($password);
        $user->save();
    }
    public function checkEmailInUsersAndGetUser($field, $email, $type = "client")
    {
        $is_field = "is_" . $type;
        $user = User::where($is_field, 1)->where($field, $email)->first();
        if (!$user) {
            return false;
        }
        return $user;
    } 
    public function checkTimeOfResetPassword($passwordReset)
    {
        if (Carbon::parse($passwordReset->updated_at)->addMinutes($this->sms_minutes)->isPast()) {
            $passwordReset->delete();
            return false;
        }
        return true;
    }
    public function checkExpirationOfCodeInUsers($field,$passwordResetEmail,$type="client")
    {
        $is_field = "is_" . $type;
        $user_code = User::where($is_field, 1)->where('sms_code_expire', '>=', Carbon::now())->where($field, $passwordResetEmail)->first();

        if (!$user_code) {
            return false;
        }
        return $user_code;
    }
}
