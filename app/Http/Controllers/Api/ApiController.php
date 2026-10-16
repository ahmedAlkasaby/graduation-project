<?php

namespace App\Http\Controllers\Api;

use App\Models\Setting;
use Illuminate\Http\Request;

/**
 * Endpoints an app calls before it has anything of its own to show — the
 * settings bundle it needs at startup.
 *
 * A project's own "home" endpoint belongs here.
 */
class ApiController extends ApiHomeController
{
    public function settings(Request $request)
    {
        $data = $this->getSettingApi();
        $data['notification_count'] = $request->user()?->unreadNotifications()->count() ?? 0;

        return $this->successResponse($data);
    }

    public function images(Request $request)
    {
        return $this->successResponse(
            Setting::where('type', 'image')->pluck('value', 'key')
        );
    }
}
