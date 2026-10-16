<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class ResetPasswordRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|string|email|exists:users,email',
            'code' => 'required|string|max:50',
            'password' => 'required|string|min:8|same:confirm_password',
            'confirm_password' => 'required|string|min:8',
        ];
    }
}
