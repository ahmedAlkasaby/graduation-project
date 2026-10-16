<?php

namespace App\Services\Api\Auth\ResetPassword;

use App\Http\Controllers\Api\ApiHomeController;
use App\Models\PasswordReset;
use App\Models\User;
use App\Services\Api\Auth\UserCodeService;
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
        $userCode = null;
        if($sms_type != "firebase"){
            $userCode = app(UserCodeService::class)->pendingForUser($user, UserCodeService::TYPE_PASSWORD_RESET);
            if (!$userCode) {
                return [
                    'error' => true,
                    'field' => $field
                ];
            }
        }
        $this->changePassword($user, $request->password);

        // The code has done its job; burn it so it cannot reset the password again.
        $userCode?->update(['code_expire' => now()]);

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
        $user = User::where('is_admin', $type === 'admin' ? 1 : 0)->where($field, $email)->first();
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
}
