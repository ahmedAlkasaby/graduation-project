<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Creating or editing a beneficiary profile. The name, national ID and phone
 * are required. The national ID is a 14 digit Egyptian ID that no other case
 * holds; the phone may repeat across cases (families share phones).
 *
 * The volunteer is optional; when sent, it must be an active volunteer user.
 *
 * The address is optional too, but once any part of it is sent the city is
 * required. The region must be an active region of that city, and the map
 * point (latitude and longitude) is sent as a pair.
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
            'volunteer_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')
                    ->where('type', UserTypeEnum::Volunteer->value)
                    ->where('active', 1)
                    ->whereNull('deleted_at'),
            ],

            'address' => ['nullable', 'array'],
            'address.city_id' => [
                'nullable',
                'required_with:address.region_id,address.address,address.latitude,address.longitude',
                'integer',
                Rule::exists('cities', 'id')->where('active', 1),
            ],
            'address.region_id' => [
                'nullable',
                'integer',
                Rule::exists('regions', 'id')
                    ->where('active', 1)
                    ->where('city_id', $this->input('address.city_id')),
            ],
            'address.address' => ['nullable', 'string', 'max:500'],
            'address.latitude' => ['nullable', 'required_with:address.longitude', 'numeric', 'between:-90,90'],
            'address.longitude' => ['nullable', 'required_with:address.latitude', 'numeric', 'between:-180,180'],
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
            'address.city_id.required_with' => __('The city is required when an address is entered.'),
            'address.city_id.exists' => __('The selected city is not available.'),
            'address.region_id.exists' => __('The selected region is not an active region of the selected city.'),
            'address.latitude.required_with' => __('Latitude and longitude must be entered together.'),
            'address.longitude.required_with' => __('Latitude and longitude must be entered together.'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'address.city_id' => __('City'),
            'address.region_id' => __('Region'),
            'address.address' => __('Address'),
            'address.latitude' => __('Latitude'),
            'address.longitude' => __('Longitude'),
        ];
    }
}
