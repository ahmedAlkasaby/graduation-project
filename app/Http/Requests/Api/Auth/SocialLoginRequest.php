<?php

namespace App\Http\Requests\Api\Auth;

use App\Enums\SocialProviderEnum;
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
            'device_type' => 'required|in:android,huawei,apple',
            'firebase_token' => 'required|string',
        ];
    }

    public function provider(): SocialProviderEnum
    {
        return $this->enum('provider', SocialProviderEnum::class);
    }
}
