<?php

namespace App\Http\Requests\Api\Auth;

use App\Enums\DeviceTypeEnum;
use App\Http\Requests\Api\BaseRequest;
use Illuminate\Validation\Rule;

class LoginRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

   
    public function rules(): array
    {
        return [
            'email' => 'required|string',
            'password' => 'required|min:8',
            'imei' => 'required|string',
            'token' => 'required|string',
            'device_type' => ['required', Rule::enum(DeviceTypeEnum::class)],
        ];
    }
}
