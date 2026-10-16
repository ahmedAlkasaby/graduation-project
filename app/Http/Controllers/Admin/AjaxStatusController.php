<?php

namespace App\Http\Controllers\Admin;

use App\Models\City;
use Illuminate\Http\Request;

/**
 * The toggles in the list views. Each module adds one method here, named after
 * its route so `cities.status` maps to cityStatus().
 */
class AjaxStatusController extends AdminController
{
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
