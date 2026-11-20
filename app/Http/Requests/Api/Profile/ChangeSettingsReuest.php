<?php

namespace App\Http\Requests\Api\Profile;

use App\Http\Requests\Api\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class ChangeSettingsReuest extends BaseRequest
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
        return [
            'is_stock' => 'required|in:0,1',
            'is_tracking' => 'required|in:0,1',
            'is_offer' => 'required|in:0,1',
        ];
    }
    public function messages(): array
    {
        return [
            'is_stock.required' => __('The is stock field is required.'),
            'is_tracking.required' => __('The is tracking field is required.'),
            'is_offer.required' => __('The is offer field is required.'),
        ];
    }
}
