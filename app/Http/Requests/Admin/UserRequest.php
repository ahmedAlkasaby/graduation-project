<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserTypeEnum;
use App\Traits\AdminHelperTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Creating or editing a user from the dashboard.
 *
 * Roles only belong to the "admin" type: a super admin skips every permission
 * check, and donors and volunteers never reach the dashboard. For any other
 * type `roles` is excluded from the validated data, so it is never stored.
 */
class UserRequest extends FormRequest
{
    use AdminHelperTrait;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $userId = $this->route('user')?->id;
        $isCreate = $this->isMethod('POST');
        $phoneRegex = settings()->getPhoneRegex();
        $rolesOnlyForAdmin = 'exclude_unless:type,'.UserTypeEnum::Admin->value;

        return [
            'full_name' => ['required', 'string', 'min:2', 'max:255'],
            'type' => ['required', Rule::in($this->user_roles_array)],
            'roles' => [$rolesOnlyForAdmin, 'nullable', 'array'],
            'roles.*' => [$rolesOnlyForAdmin, 'integer', 'distinct', 'exists:roles,id'],
            'password' => $isCreate
                ? ['required', 'string', 'min:8', 'same:confirm_password']
                : ['nullable', 'string', 'min:8', 'same:confirm_password'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'active' => ['sometimes', 'boolean'],
            'is_notify' => ['sometimes', 'boolean'],
            'locale' => ['nullable', 'string', Rule::in($this->language_array)],
            'gender' => ['nullable', Rule::in(['male', 'female'])],
            'birth_date' => ['nullable', 'date', 'before_or_equal:'.now()->subYears(15)->toDateString()],

            'email' => [
                in_array($this->input('type'), UserTypeEnum::adminValues(), true) ? 'required' : 'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],

            'phone' => [
                'required',
                'max:50',
                "regex:/{$phoneRegex}/",
                Rule::unique('users', 'phone')->ignore($userId),
            ],
        ];
    }
}
