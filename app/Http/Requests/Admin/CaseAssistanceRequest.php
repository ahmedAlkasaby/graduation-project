<?php

namespace App\Http\Requests\Admin;

use App\Enums\AssistanceTypeEnum;
use App\Services\CharityCase\CaseAssistanceService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Creating an assistance. The type decides which fields apply:
 *
 * - one_time:  `requested_amount` and one `due_at` date.
 * - recurring: `amount_per_period`, a `start_month` and an optional
 *              `end_month` ("Y-m"); every installment falls on the 1st.
 *
 * A field of the other type is prohibited rather than ignored, so a client
 * sending both amounts gets told instead of silently losing one.
 */
class CaseAssistanceRequest extends FormRequest
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
        $oneTime = AssistanceTypeEnum::OneTime->value;
        $recurring = AssistanceTypeEnum::Recurring->value;
        $amount = ['nullable', 'numeric', 'gt:0', 'decimal:0,2', 'max:9999999999.99'];

        return [
            'case_id' => ['required', 'integer', 'exists:charity_cases,id'],
            'assistance_type' => ['required', Rule::enum(AssistanceTypeEnum::class)],
            'title' => ['required', 'array'],
            'title.en' => ['required', 'string', 'max:255'],
            'title.ar' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],

            'requested_amount' => ["required_if:assistance_type,{$oneTime}", "prohibited_unless:assistance_type,{$oneTime}", ...$amount],
            'due_at' => ["required_if:assistance_type,{$oneTime}", "prohibited_unless:assistance_type,{$oneTime}", 'nullable', 'date_format:Y-m-d'],

            'amount_per_period' => ["required_if:assistance_type,{$recurring}", "prohibited_unless:assistance_type,{$recurring}", ...$amount],
            'start_month' => ["required_if:assistance_type,{$recurring}", "prohibited_unless:assistance_type,{$recurring}", 'nullable', 'date_format:Y-m'],
            'end_month' => ["prohibited_unless:assistance_type,{$recurring}", 'nullable', 'date_format:Y-m', 'after_or_equal:start_month'],
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $start = $this->input('start_month');
                $end = $this->input('end_month');

                if ($validator->errors()->hasAny(['start_month', 'end_month']) || blank($start) || blank($end)) {
                    return;
                }

                $months = app(CaseAssistanceService::class)->monthsBetween($start, $end);

                if ($months > CaseAssistanceService::MAX_INSTALLMENTS) {
                    $validator->errors()->add('end_month', __('A monthly assistance can have at most :count installments.', [
                        'count' => CaseAssistanceService::MAX_INSTALLMENTS,
                    ]));
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.en.required' => __('validation.required', ['attribute' => __('English Title')]),
            'title.ar.required' => __('validation.required', ['attribute' => __('Arabic Title')]),
            'requested_amount.required_if' => __('The amount is required for a one-time assistance.'),
            'due_at.required_if' => __('The due date is required for a one-time assistance.'),
            'amount_per_period.required_if' => __('The monthly amount is required for a monthly assistance.'),
            'start_month.required_if' => __('The start month is required for a monthly assistance.'),
            'end_month.after_or_equal' => __('The end month must be the start month or later.'),
            'requested_amount.prohibited_unless' => __('This field only applies to a one-time assistance.'),
            'due_at.prohibited_unless' => __('This field only applies to a one-time assistance.'),
            'amount_per_period.prohibited_unless' => __('This field only applies to a monthly assistance.'),
            'start_month.prohibited_unless' => __('This field only applies to a monthly assistance.'),
            'end_month.prohibited_unless' => __('This field only applies to a monthly assistance.'),
        ];
    }
}
