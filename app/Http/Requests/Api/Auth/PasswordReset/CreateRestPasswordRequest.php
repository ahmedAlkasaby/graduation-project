<?php

namespace App\Http\Requests\Api\Auth\PasswordReset;

use App\Http\Requests\Api\BaseRequest;

class CreateRestPasswordRequest extends BaseRequest
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
        if(! $this->has('sms_type')){
            $this->merge(['sms_type'=>'email']);
        }
        if(! $this->has('country_id')){
            $this->merge(['country_id'=>1]);
        }
        $rules =  [
            'email' => 'required|string',
            'country_id' => 'nullable|integer|exists:countries,id',
            'code_type' => 'required|in:phone,email',
            'type' => ['nullable', Rule::enum(UserTypeEnum::class)],
            'sms_type' => 'nullable|in:sms,firebase,email',
        ];
        return $rules;
    }
}
