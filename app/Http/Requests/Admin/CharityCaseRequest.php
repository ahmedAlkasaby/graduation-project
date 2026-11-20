<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Creating or editing a beneficiary profile. Every field is required. The
 * national ID is a 14 digit Egyptian ID that no other case holds; the phone
 * may repeat across cases (families share phones). The volunteer must be an
 * active volunteer user.
 */
class CharityCaseRequest extends FormRequest
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
        $phoneRegex = settings()->getPhoneRegex();

        return [
            'full_name' => ['required', 'string', 'min:3', 'max:255'],
            'national_id' => [
                'required',
                'digits:14',
                'regex:/^[23][0-9]{13}$/',
                Rule::unique('charity_cases', 'national_id')->ignore($this->route('charityCase')?->id),
            ],
            'phone' => ['required', 'string', 'max:30', "regex:/{$phoneRegex}/"],
            'category_id' => ['required', 'integer', Rule::exists('case_categories', 'id')->where('active', 1)->whereNull('deleted_at')],
            'volunteer_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')
                    ->where('type', UserTypeEnum::Volunteer->value)
                    ->where('active', 1)
                    ->whereNull('deleted_at'),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'national_id.unique' => __('A case with this national ID already exists.'),
            'national_id.digits' => __('The national ID must be 14 digits.'),
            'national_id.regex' => __('The national ID is not a valid Egyptian national ID.'),
            'volunteer_id.exists' => __('The selected volunteer is not an active volunteer.'),
        ];
    }
}
