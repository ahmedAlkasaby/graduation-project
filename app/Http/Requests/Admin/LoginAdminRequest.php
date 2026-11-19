<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Signing in to the Blade admin panel. `email` takes an email address or a
 * phone number, like the login form says.
 */
class LoginAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    /** The column the login value is looked up in. */
    public function loginColumn(): string
    {
        return filter_var($this->input('email'), FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
    }
}
