<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * The beneficiary search behind the assistance form's select (`search`), and
 * the duplicate check of the profile form (`national_id`, `phone`,
 * `full_name`, leaving out the case being edited: `ignore_id`).
 */
class CharityCaseLookupRequest extends FormRequest
{
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
            'national_id' => ['nullable', 'string', 'max:14'],
            'phone' => ['nullable', 'string', 'max:30'],
            'full_name' => ['nullable', 'string', 'max:255'],
            'ignore_id' => ['nullable', 'integer'],
        ];
    }
}
