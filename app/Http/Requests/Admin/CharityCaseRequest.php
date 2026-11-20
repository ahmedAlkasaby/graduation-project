<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Creating or editing a beneficiary profile. The national ID is optional, but
 * when given it is a 14 digit Egyptian ID that no other case holds. The
 * phone may repeat across cases (families share phones).
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
                'nullable',
                'digits:14',
                'regex:/^[23][0-9]{13}$/',
                Rule::unique('charity_cases', 'national_id')->ignore($this->route('charityCase')?->id),
            ],
            'phone' => ['nullable', 'string', 'max:30', "regex:/{$phoneRegex}/"],
            'category_id' => ['required', 'integer', 'exists:case_categories,id'],
            'volunteer_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')
                    ->where('type', UserTypeEnum::Volunteer->value)
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
            'volunteer_id.exists' => __('The selected volunteer is not a volunteer user.'),
        ];
    }
}
