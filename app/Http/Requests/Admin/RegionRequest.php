<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RegionRequest extends FormRequest
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
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'order_id' => ['nullable', 'integer', 'min:1', 'max:127'],
            'active' => ['required', 'boolean'],
            'latitude' => ['nullable', 'string', 'max:191'],
            'longitude' => ['nullable', 'string', 'max:191'],
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
            'city_id.required' => __('validation.required', ['attribute' => __('site.city')]),
            'city_id.exists' => __('validation.exists', ['attribute' => __('site.city')]),
        ];
    }
}
