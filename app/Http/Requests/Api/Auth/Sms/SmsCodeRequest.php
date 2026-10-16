<?php

namespace App\Http\Requests\Api\Auth\Sms;

use App\Http\Requests\Api\BaseRequest;
use Propaganistas\LaravelPhone\Rules\Phone as PhoneRule;

class SmsCodeRequest extends BaseRequest
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
        $countryCode = $this->resolveCountryCode();
        $phoneRule = (new PhoneRule())->country($countryCode);

        return [
            'phone' => ['nullable', 'required_without:email', 'max:50', $phoneRule, 'exists:user_codes,phone'],
            'email' => ['nullable', 'required_without:phone', 'max:50', 'exists:user_codes,email'],
            'code'  => ['required', 'max:50'],
            'type'  => ['nullable', 'string', 'in:client,admin'],
            'country_id' => ['nullable', 'integer', 'exists:countries,id'],
        ];
    }
}
