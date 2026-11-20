<?php

namespace App\Http\Requests\Admin;

use App\Enums\AssistanceTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * The assistance list filter: `search` looks in the title and the
 * beneficiary's name, national ID and phone.
 */
class CaseAssistanceFilterRequest extends FormRequest
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
            'case_id' => ['nullable', 'integer'],
            'category_id' => ['nullable', 'integer', 'exists:case_categories,id'],
            'assistance_type' => ['nullable', Rule::enum(AssistanceTypeEnum::class)],
            'all_data' => ['nullable', 'in:0,1'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
