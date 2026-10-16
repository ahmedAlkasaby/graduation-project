<?php

namespace App\Http\Requests\Api\Auth;

use App\Enums\DeviceTypeEnum;
use App\Enums\SocialProviderEnum;
use App\Enums\UserTypeEnum;
use App\Http\Requests\Api\BaseRequest;
use Illuminate\Validation\Rule;

class SocialLoginRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

   
    public function rules(): array
    {
        return [
            'provider' => ['required', Rule::enum(SocialProviderEnum::class)],
            'token' => 'required|string',
            'imei' => 'required|string',
            'device_type' => ['required', Rule::enum(DeviceTypeEnum::class)->only(DeviceTypeEnum::mobile())],
            'firebase_token' => 'required|string',
            'type' => ['nullable', Rule::in(UserTypeEnum::appValues())],
        ];
    }

    public function provider(): SocialProviderEnum
    {
        return $this->enum('provider', SocialProviderEnum::class);
    }

    /** The type a first sign in creates the account as: donor unless the app says otherwise. */
    public function userType(): UserTypeEnum
    {
        return $this->enum('type', UserTypeEnum::class) ?? UserTypeEnum::Donor;
    }
}
