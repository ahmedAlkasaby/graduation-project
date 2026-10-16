<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\BaseRequest;

class LoginRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * `email` carries either an email address or a phone number — which one
     * decides the column matched, see LoginService::methodFor().
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|string',
            'password' => 'required|min:8',
            'imei' => 'required|string',
            'token' => 'required|string',
            'device_type' => 'required|in:android,huawei,apple,website',
            'type' => 'nullable|in:client,admin',
        ];
    }
}
