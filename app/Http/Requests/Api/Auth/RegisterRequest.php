<?php

namespace App\Http\Requests\Api\Auth;

use App\Enums\DeviceTypeEnum;
use App\Enums\UserTypeEnum;
use App\Http\Requests\Api\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(
            $this->baseRules(),
            (new CheckUserRequest)->rules(),
        );
    }

    protected function baseRules(): array
    {
        return [
            'full_name' => 'required|string|max:255',

            'password' => 'required|min:8|same:confirm_password',
            'confirm_password' => 'required|min:8',

            'code' => 'required|exists:user_codes,code',

            'imei' => 'required|string',
            'token' => 'required|string',
            'device_type' => ['required', Rule::enum(DeviceTypeEnum::class)],

            'locale' => 'nullable|in:ar,en',

            // Admins are made on the dashboard; the app only signs up its own users.
            'type' => ['required', Rule::in(UserTypeEnum::appValues())],
        ];
    }
}
