<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Editing an assistance: only its title and description. Everything that
 * makes up the financial commitment or the installment schedule is fixed
 * after creation, and sending it is an error, not something silently
 * dropped.
 */
class CaseAssistanceUpdateRequest extends FormRequest
{
    /** Fields that may never be sent on an update. */
    public const LOCKED = [
        'case_id', 'assistance_type', 'requested_amount', 'amount_per_period',
        'due_at', 'start_month', 'end_month', 'installments', 'amount',
        'created_by', 'finished_at', 'cancelled_at',
    ];

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $rules = [
            'title' => ['required', 'array'],
            'title.en' => ['required', 'string', 'max:255'],
            'title.ar' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
        ];

        foreach (self::LOCKED as $field) {
            $rules[$field] = ['prohibited'];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        $messages = [
            'title.en.required' => __('validation.required', ['attribute' => __('English Title')]),
            'title.ar.required' => __('validation.required', ['attribute' => __('Arabic Title')]),
        ];

        foreach (self::LOCKED as $field) {
            $messages["{$field}.prohibited"] = __('This field cannot be changed after the assistance is created.');
        }

        return $messages;
    }
}
