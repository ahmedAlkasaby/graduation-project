<?php

namespace App\Http\Requests\Api;

use App\Enums\UserTypeEnum;
use App\Models\Country;
use App\Services\AppSettingsService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Propaganistas\LaravelPhone\Rules\Phone as PhoneRule;

/**
 * Base for every API form request: makes a failed validation come back in the
 * same envelope as every other API response, and holds the phone/email rules
 * that vary by audience.
 */
abstract class BaseRequest extends FormRequest
{
    protected function settings(): AppSettingsService
    {
        return app(AppSettingsService::class);
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'validation_error',
            'errors' => $validator->errors(),
        ], 422));
    }

    /**
     * The ISO code ("EG", "KW", …) a phone number should be validated against:
     * whatever the request says, else the configured default country.
     */
    protected function resolveCountryCode(): string
    {
        $countryId = $this->input('country_id') ?: $this->settings()->get('default_country');

        $code = $countryId
            ? Country::where('id', $countryId)->value('code')
            : Country::query()->value('code');

        return strtoupper($code ?? 'EG');
    }

    /**
     * Contact rules per audience. Uniqueness is scoped so an app user and an
     * admin may share a phone number — see
     * AppServiceProvider::registerUserValidators().
     */
    protected function roleContactRules(?string $type = null): array
    {
        $type = $type ?? $this->input('type');

        $phoneRule = (new PhoneRule)->country($this->resolveCountryCode());

        if ($type === UserTypeEnum::Admin->value) {
            return [
                'email' => 'nullable|max:255|email|required_without:phone|uniqueUserEmail',
                'phone' => ['nullable', 'required_without:email', 'max:50', $phoneRule, 'uniqueUserPhone'],
            ];
        }

        return [
            'email' => 'nullable|max:255|email|required_without:phone|uniqueUserClientEmail',
            'phone' => ['nullable', 'required_without:email', 'max:50', $phoneRule, 'uniqueUserClientPhone'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('email')) {
            $this->merge(['email' => strtolower(trim((string) $this->email))]);
        }

        if ($this->has('phone')) {
            $this->merge(['phone' => trim((string) $this->phone)]);
        }
    }
}
