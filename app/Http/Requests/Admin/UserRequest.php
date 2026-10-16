<?php

namespace App\Http\Requests\Admin;

use App\Traits\AdminHelperTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    use AdminHelperTrait;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('id');
        $isCreate = $this->isMethod('POST');
        $phoneRegex = settings()->getPhoneRegex();

        return [
            'full_name' => ['required', 'min:2', 'max:255'],
            'type' => ['required', Rule::in($this->user_roles_array)],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', 'exists:roles,id'],
            'password' => $isCreate
                ? ['required', 'same:confirm-password', 'min:8']
                : ['nullable', 'same:confirm-password', 'min:8'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'is_notify' => ['nullable', 'boolean'],
            'locale' => ['nullable', 'string', Rule::in($this->language_array)],
            'gender' => ['nullable', Rule::in(['male', 'female'])],
            'birth_date' => ['nullable', 'date', 'before_or_equal:'.now()->subYears(15)->toDateString()],

            'email' => [
                $this->input('type') === 'client' ? 'nullable' : 'required',
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
