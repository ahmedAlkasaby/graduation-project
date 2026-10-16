<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\BaseRequest;

class CheckUserRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|string|email|unique:users,email',
            'phone' => 'nullable|string|unique:users,phone',
        ];
    }
}
