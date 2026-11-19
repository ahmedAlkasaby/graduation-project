<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * The navbar's theme and language switches. They save on the signed in
 * admin, so the choice follows them to every device.
 */
class PreferenceController extends AdminController
{
    public function theme(Request $request): JsonResponse
    {
        $data = $request->validate([
            'theme' => ['required', Rule::in(['light', 'dark', 'semi-dark'])],
        ]);

        $this->admin()->update(['theme' => $data['theme']]);

        return response()->json(['success' => true]);
    }

    public function language(Request $request): JsonResponse
    {
        $data = $request->validate([
            'lang' => ['required', Rule::in($this->language_array)],
        ]);

        $this->admin()->update(['locale' => $data['lang']]);

        return response()->json(['success' => true]);
    }
}
