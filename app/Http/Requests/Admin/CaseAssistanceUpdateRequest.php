<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Editing an assistance: its beneficiary, category, title, description,
 * order and status. Everything that makes up the financial commitment or the
 * installment schedule is fixed after creation, and sending it is an error,
 * not something silently dropped.
 */
class CaseAssistanceUpdateRequest extends FormRequest
{
    /** Fields that may never be sent on an update. */
    public const LOCKED = [
        'assistance_type', 'requested_amount', 'amount_per_period',
        'due_at', 'start_month', 'end_month', 'installments', 'amount',
        'created_by', 'finished_at', 'cancelled_at', 'cancelled_by', 'paid_at', 'paid_by',
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
            'case_id' => ['required', 'integer', Rule::exists('charity_cases', 'id')->whereNull('deleted_at')],
            'category_id' => ['required', 'integer', Rule::exists('case_categories', 'id')->where('active', 1)->whereNull('deleted_at')],
            'title' => ['required', 'array'],
            'title.en' => ['required', 'string', 'max:255'],
            'title.ar' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'array'],
            'description.en' => ['nullable', 'string', 'max:5000'],
            'description.ar' => ['nullable', 'string', 'max:5000'],
            'order_id' => ['nullable', 'integer', 'min:1', 'max:127'],
            'active' => ['required', 'boolean'],
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
