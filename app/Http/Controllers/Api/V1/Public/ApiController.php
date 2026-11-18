<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Api\ApiHomeController;
use App\Models\Setting;
use Illuminate\Http\Request;


class ApiController extends ApiHomeController
{
    public function settings(Request $request)
    {
        $data = $this->getSettingApi();

        return $this->successResponse($data);
    }

    public function images(Request $request)
    {
        return $this->successResponse(
            Setting::where('type', 'image')->pluck('value', 'key')
        );
    }
}
