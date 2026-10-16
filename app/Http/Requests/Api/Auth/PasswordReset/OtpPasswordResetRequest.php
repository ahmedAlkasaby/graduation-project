<?php

namespace App\Http\Requests\Api\Auth\PasswordReset;

use App\Http\Requests\Api\BaseRequest;

class OtpPasswordResetRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

  
     public function rules(): array
    {
         $rules =  [
            'email' => 'required|string',
            'code' => 'required_unless:sms_type,firebase|string',
            'token' => 'required|string',
            'type' => ['nullable', Rule::enum(UserTypeEnum::class)],
            'sms_type' => 'nullable|in:sms,firebase,email',
        ];
        return $rules;
    }
}
