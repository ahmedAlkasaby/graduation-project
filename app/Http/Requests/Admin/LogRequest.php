<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * The change log filter. See ActivityLogRequest — same reasoning.
 */
class LogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['nullable', 'array'],
            'user_id.*' => ['integer', 'exists:users,id'],
            'model' => ['nullable', 'string', 'max:100'],
            'field_id' => ['nullable', 'integer', 'min:1'],
            'key' => ['nullable', 'string', 'max:100'],
            'date_start' => ['nullable', 'date'],
            'date_end' => ['nullable', 'date', 'after_or_equal:date_start'],
            'user_device_id' => ['nullable', 'integer', 'exists:user_devices,id'],
            'device' => ['nullable', 'string', 'max:100'],
            'platform' => ['nullable', 'string', 'max:100'],
            'browser' => ['nullable', 'string', 'max:100'],
            'is_desktop' => ['nullable', 'in:0,1'],
            'is_phone' => ['nullable', 'in:0,1'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:10000'],
        ];
    }
}
