<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roleId = $this->route('role');

        return [
            'name' => [
                'required', 'string', 'max:255',
                'regex:/^[a-z0-9_]+$/',
                Rule::unique('roles', 'name')->ignore($roleId),
            ],
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.required', ['attribute' => __('site.name')]),
            'name.regex' => __('validation.regex', ['attribute' => __('site.name')]),
            'name.unique' => __('validation.unique', ['attribute' => __('site.name')]),
            'name.max' => __('validation.max.string', ['attribute' => __('site.name'), 'max' => 255]),
            'display_name.required' => __('validation.required', ['attribute' => __('site.display_name')]),
            'display_name.max' => __('validation.max.string', ['attribute' => __('site.display_name'), 'max' => 255]),
            'permissions.array' => __('validation.array', ['attribute' => __('site.permissions')]),
            'permissions.*.exists' => __('validation.exists', ['attribute' => __('site.permissions')]),
        ];
    }
}
