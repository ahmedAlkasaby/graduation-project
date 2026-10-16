<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'array'],
            'name.en' => ['required', 'string', 'max:255'],
            'name.ar' => ['required', 'string', 'max:255'],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'order_id' => ['nullable', 'integer', 'min:1', 'max:127'],
            'active' => ['required', 'boolean'],
            'latitude' => ['nullable', 'string', 'max:191'],
            'longitude' => ['nullable', 'string', 'max:191'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.en.required' => __('validation.required', ['attribute' => __('site.name')]),
            'name.ar.required' => __('validation.required', ['attribute' => __('site.name')]),
            'country_id.required' => __('validation.required', ['attribute' => __('site.country')]),
            'country_id.exists' => __('validation.exists', ['attribute' => __('site.country')]),
        ];
    }
}
