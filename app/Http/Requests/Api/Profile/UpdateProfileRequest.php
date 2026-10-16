<?php


namespace App\Http\Requests\Api\Profile;

use App\Models\Setting;
use App\Http\Requests\Api\BaseRequest;
use App\Http\Requests\Api\MainApiRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProfileRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $primaryLogin = settings()->get('primary_login');
        $user = auth('sanctum')->user();

        if ($this->boolean('is_phone')) {
            if ($primaryLogin === 'phone') {
                return [];
            }

            return [
                'is_phone' => 'required|boolean',
                'phone' => 'required|string|max:20|unique:users,phone,'.$user->id,
            ];
        }

        $rules = [
            'name_first' => 'required|string',
            'name_last' => 'required|string',
            'gender' => 'nullable|in:male,female',
            'date_of_birth' => 'nullable|date_format:Y-m-d|before_or_equal:'.now()->subYears(15)->toDateString(),
            'address_id' => 'nullable|integer|exists:addresses,id',
        ];

        if ($primaryLogin !== 'email') {
            $rules['email'] = $user->type === 'client'
                ? "nullable|email|max:255|uniqueUserClientEmailUpdate:$user->id"
                : "nullable|email|max:255|uniqueUserEmailUpdate:$user->id";
        }

        if ($primaryLogin !== 'phone') {
            $rules['phone'] = 'nullable|string|max:20';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name_first.required' => __('The first name field is required'),
            'name_last.required' => __('The last name field is required'),

            'gender.in' => __('The gender must be male or female'),

            'date_of_birth.date_format' => __('The date of birth must be in Y-m-d format'),
            'date_of_birth.before_or_equal' => __('You must be at least 15 years old'),

            'address_id.integer' => __('The address id must be an integer'),
            'address_id.exists' => __('The selected address does not exist'),

            'phone.required' => __('Please provide a phone number'),
            'phone.unique' => __('This phone number is already taken'),
            'phone.max' => __('Phone number cannot be longer than 20 characters'),
        ];
    }


    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all(); 

        $response = response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $errors,
        ], 422);

        throw new HttpResponseException($response);
    }
}
