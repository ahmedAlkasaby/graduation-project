<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/** Creating or editing a case category: a name in both languages, its order and status. */
class CaseCategoryRequest extends FormRequest
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
            'name' => ['required', 'array'],
            'name.en' => ['required', 'string', 'max:255'],
            'name.ar' => ['required', 'string', 'max:255'],
            'order_id' => ['nullable', 'integer', 'min:1', 'max:127'],
            'active' => ['required', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.en.required' => __('validation.required', ['attribute' => __('site.name')]),
            'name.ar.required' => __('validation.required', ['attribute' => __('site.name')]),
        ];
    }
}
