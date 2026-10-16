<?php

namespace App\Http\Requests\Api\Auth\PasswordReset;

use App\Http\Requests\Api\BaseRequest;

class ResetRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
          $rules =  [
            'email' => 'required|string',
            'password' => 'required|string|min:8|same:confirm_password',
            'token' => 'required|string',
            'type' => ['nullable', 'string', 'in:client,admin'],
            'sms_type' => 'nullable|in:sms,firebase',
        ];
        return $rules;
    }
}
