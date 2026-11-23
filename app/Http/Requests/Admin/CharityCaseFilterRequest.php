<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * The beneficiary list filter: `search` looks in the name, national ID and
 * phone; `city_id` and `region_id` match the beneficiary's address.
 */
class CharityCaseFilterRequest extends FormRequest
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
            'volunteer_id' => ['nullable', 'integer'],
            'city_id' => ['nullable', 'integer'],
            'region_id' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
