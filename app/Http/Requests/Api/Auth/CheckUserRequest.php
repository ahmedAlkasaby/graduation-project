<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\BaseRequest;
use App\Services\AppSettingsService;
use Illuminate\Foundation\Http\FormRequest;

class CheckUserRequest extends BaseRequest
{
    
    public function authorize(): bool
    {
        return true;
    }


    public function rules()
    {
        return $this->roleContactRules();
    }

   
}
