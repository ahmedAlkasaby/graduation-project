<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\MainController;
use App\Models\Setting;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\View;

/**
 * Base for every admin screen: resolves the signed in user, and holds the
 * handful of responses ($this->pageError() and friends) the modules share.
 */
class AdminController extends MainController
{
    use ValidatesRequests;

    public function __construct()
    {
        parent::__construct();

        $this->middleware('admin');
        $this->middleware(function (Request $request, $next) {
            $this->user = auth()->user();
            $this->language = $this->user->locale;
            $this->type = $this->user->type;

            return $next($request);
        });
    }

    /**
     * The module a screen belongs to. Shared with every view so the sidebar
     * can mark the current entry without each view passing it along.
     */
    protected function setClass($class): void
    {
        $this->class = $class;
        View::share('class', $class);
    }


    protected function perPage($request = null, ?int $default = null): int
    {
        $requested = (int) ($request ?? request())->input('limit', 0);

        return $requested > 0 ? $requested : max(1, $default ?: (int) $this->limit);
    }

    public function pageError()
    {
        return view('admin.errors.404');
    }

    public function pageUnauthorized()
    {
        return view('admin.errors.unauthorized');
    }

    public function getTypeInput($request, $default = 'admin', $type_array = ['admin'], $is_null = false)
    {
        $type = $request->input('type', $default);

        if (! in_array($type, $type_array)) {
            $type = $default;
        }

        if ($is_null && $type === '') {
            $type = null;
        }

        return $type;
    }

    public function getOptimize()
    {
        Artisan::call('optimize');

        return 'done';
    }

    public function getOptimizeClear()
    {
        Artisan::call('optimize:clear');

        return 'done';
    }

    public function developerSetting()
    {
        return view('admin.settings.developer-setting', [
            'setting' => settings()->all(),
            'user' => $this->user,
            'class' => 'developer',
            'table' => 'setting',
            'type' => null,
        ]);
    }

    public function developerSettingStore(Request $request)
    {
        foreach ($request->all() as $key => $value) {
            if (in_array($key, $this->setting_options_array)) {
                Setting::updateSetting($key, strip_tags(trim((string) $value)), 0, 'setting');
            }
        }

        settings()->refresh();

        return redirect()
            ->route('admin.developer.setting')
            ->with('success', __('Setting update successfully'));
    }

    public function imageSetting()
    {
        return view('admin.settings.images-setting', [
            'images' => Setting::where('type', 'image')->where('group', 'setting')->pluck('value', 'key')->toArray(),
            'class' => 'image-setting',
            'table' => 'setting',
            'type' => 'image',
        ]);
    }

    public function imageSettingStore(Request $request)
    {
        $request->validate([
            'key' => 'required|string|in:'.implode(',', array_keys(imageType())),
            'image' => 'required|mimes:jpg,jpeg,png,webp,svg',
        ]);

        $path = $this->uploadImage($request->file('image'), 'settings', 1);

        Setting::updateOrCreate(
            ['key' => $request->key, 'type' => 'image', 'group' => 'setting'],
            ['value' => $path]
        );

        settings()->refresh();

        return redirect()->back()->with('success', __('Image saved successfully'));
    }
}
