<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use App\Services\ImageHandlerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Site settings. Which keys are allowed to be written is declared in
 * AdminHelperTrait rather than taken from the form, so a crafted post cannot
 * introduce settings of its own.
 */
class SettingController extends AdminController
{
    public function __construct(protected ImageHandlerService $imageService)
    {
        parent::__construct();

        $this->setClass('setting');
    }

    public function setting()
    {
        $setting = settings()->all();
        $logo = $setting['logo_image'] ?? null;

        return view('admin.settings.setting', [
            'setting' => $setting,
            'image' => $logo,
            'imagesize' => $logo ? $this->getFileSize($logo) : null,
        ]);
    }

    public function settingStore(Request $request)
    {
        $request->validate([
            'site_title' => 'required|string|max:255',
            'site_phone' => 'required|string|max:50',
            'site_email' => 'required|email|max:255',
            'table_limit' => 'required|numeric|min:1|max:10000',
        ]);

        $input = $request->all();

        $current = (object) ['image' => settings()->get('logo_image')];
        $input['logo_image'] = $this->imageService->editImage($request, $current, 'uploads/settings', field: 'image');

        unset($input['image'], $input['image_removed'], $input['_token'], $input['_method']);

        foreach ($input as $key => $value) {
            if (in_array($key, $this->setting_array) || in_array($key, $this->setting_options_array)) {
                Setting::updateSetting($key, $this->clean($value), 0, 'setting');
            }

            if (in_array($key, $this->setting_social_array)) {
                Setting::updateSetting($key, $this->clean($value), 1, 'social');
            }
        }

        settings()->refresh();

        return redirect()->route('admin.settings.index')->with('success', __('Setting update successfully'));
    }

    public function social()
    {
        return view('admin.settings.social', [
            'setting' => settings()->all(),
            'class' => 'social',
        ]);
    }

    public function socialStore(Request $request)
    {
        foreach ($request->all() as $key => $value) {
            if (in_array($key, $this->setting_social_array)) {
                Setting::updateSetting($key, $this->clean($value), 1, 'social');
            }
        }

        settings()->refresh();

        return redirect()->route('admin.settings.social')->with('success', __('Social update successfully'));
    }

    public function changeTheme(Request $request)
    {
        $request->validate(['theme' => 'required|string|in:light,dark,semi-dark']);

        $user = Auth::user();

        if (! $user) {
            return response()->json(['success' => false, 'message' => __('Unauthenticated')], 401);
        }

        $user->update(['theme' => $request->theme]);

        return response()->json(['success' => true]);
    }

    public function changeLang(Request $request)
    {
        $request->validate(['lang' => 'required|string|in:' . implode(',', $this->language_array)]);

        $user = Auth::user();

        if (! $user) {
            return response()->json(['success' => false, 'message' => __('Unauthenticated')], 401);
        }

        $user->update(['locale' => $request->lang]);

        return response()->json(['success' => true]);
    }

    /**
     * FILTER_SANITIZE_STRING was removed in PHP 8.1; strip_tags is what it did.
     */
    private function clean($value): string
    {
        return stripslashes(trim(strip_tags((string) $value)));
    }
}
