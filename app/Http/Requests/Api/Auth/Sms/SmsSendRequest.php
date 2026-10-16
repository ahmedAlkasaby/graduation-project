<?php

namespace App\Http\Requests\Api\Auth\Sms;

use App\Enums\UserTypeEnum;
use App\Http\Requests\Api\BaseRequest;
use Illuminate\Validation\Rule;

class SmsSendRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(
            $this->baseRules(),
            $this->roleContactRules()
        );
    }

    protected function baseRules(): array
    {
        if (! $this->has('code_type')) {
            $this->merge(['code_type' => 'email']);
        }

        if (! $this->has('country_id')) {
            $this->merge(['country_id' => $this->settings()->get('default_country')]);
        }

        return [
            // Must match the `type` the app registers with — the code is checked against it.
            'type' => ['required', Rule::in(UserTypeEnum::appValues())],
            'full_name' => 'nullable|max:255',
            'country_id' => 'nullable|integer|exists:countries,id',
            'code_type' => ['required', 'in:phone,email'],
        ];
    }
}
