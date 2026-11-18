<?php

namespace App\Http\Requests\Admin;

use App\Traits\AdminHelperTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * The dashboard's user list filter. A bad value is rejected with a 422
 * instead of silently returning a wrong or empty list.
 *
 *  - search  : looks in full name, email and phone
 *  - type    : one user type, or "all" (the default) for every type
 *  - active  : 1 active, 0 inactive; left out lists both
 *  - trashed : "only" deleted users alone, "with" deleted and not deleted
 */
class UserFilterRequest extends FormRequest
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
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', Rule::in([...$this->user_roles_array, 'all'])],
            'active' => ['nullable', 'boolean'],
            'trashed' => ['nullable', Rule::in(['with', 'only'])],
            'date_start' => ['nullable', 'date'],
            'date_end' => ['nullable', 'date', 'after_or_equal:date_start'],
            'all_data' => ['nullable', Rule::in(['0', '1'])],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
