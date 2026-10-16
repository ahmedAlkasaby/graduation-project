<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\BaseRequest;

class RegisterRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Default the audience before validation rather than inside rules():
     * rules() is asked for rules, and mutating the payload from there means
     * the default silently depends on rules() being called exactly once.
     */
    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();

        if (! $this->has('type')) {
            $this->merge(['type' => 'client']);
        }
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(
            $this->baseRules(),
            $this->roleContactRules(),
        );
    }

    protected function baseRules(): array
    {
        return [
            'full_name' => 'required|string|max:255',

            'password' => 'required|min:8|same:confirm_password',
            'confirm_password' => 'required|min:8',

            'code' => 'required_unless:sms_type,firebase|exists:user_codes,code',
            'sms_type' => 'nullable|in:sms,firebase',

            'imei' => 'required|string',
            'token' => 'required|string',
            'device_type' => 'required|in:android,huawei,apple,website',

            'locale' => 'nullable|in:ar,en',
            'type' => 'nullable|in:client,admin',
        ];
    }


}
