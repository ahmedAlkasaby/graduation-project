<?php

namespace App\Http\Requests\Api;

use App\Enums\UserTypeEnum;
use App\Models\Country;
use App\Services\AppSettingsService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Propaganistas\LaravelPhone\Rules\Phone as PhoneRule;


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
