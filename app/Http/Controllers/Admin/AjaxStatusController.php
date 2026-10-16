<?php

namespace App\Http\Controllers\Admin;

use App\Models\City;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * The toggles in the list views. Each module adds one method here, named after
 * its route so `users.status` maps to userStatus().
 */
class AjaxStatusController extends AdminController
{
    public function userStatus(Request $request)
    {
        $input = $this->validated($request);

        // The seeded owner accounts cannot be switched off from a list row.
        if (in_array((int) $input['id'], $this->access_all_id)) {
            return response()->json(false);
        }

        return response()->json(
            User::updateActive($input['id'], $input['status'])
        );
    }

    public function cityStatus(Request $request)
    {
        $input = $this->validated($request);

        return response()->json(
            City::updateActive($input['id'], $input['status'])
        );
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'id' => ['required', 'integer'],
            'status' => ['required', 'boolean'],
        ]);
    }
}
