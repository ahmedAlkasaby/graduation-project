<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\RegionFilterRequest;
use App\Http\Requests\Admin\RegionRequest;
use App\Models\City;
use App\Models\Region;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Managing regions (the areas inside a governorate) from the Blade admin
 * panel, the twin of Api\V1\Admin\RegionController: an admin sees inactive
 * regions too and can filter on `city_id` and `active`.
 */
class RegionController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->setClass('region');
    }

    public function index(RegionFilterRequest $request): View
    {
        $filters = $request->validated();

        $regions = Region::with('city')
            ->filter($filters)
            ->paginate($this->perPage($filters))
            ->withQueryString();

        return $this->listView('admin.regions.index', $regions, 'regions', [
            'cities' => ['' => __('All')] + City::listForSelect(),
        ]);
    }

    public function create(): View
    {
        return view('admin.regions.create', ['cities' => City::listForSelect()]);
    }

    public function store(RegionRequest $request): RedirectResponse
    {
        Region::create($request->validated());

        return redirect()->route('admin.regions.index')->with('success', __('Created successfully'));
    }

    public function show(Region $region): View
    {
        return view('admin.regions.show', ['region' => $region->load('city')]);
    }

    public function edit(Region $region): View
    {
        return view('admin.regions.edit', [
            'region' => $region,
            'cities' => City::listForSelect(),
        ]);
    }

    public function update(RegionRequest $request, Region $region): RedirectResponse
    {
        $region->update($request->validated());

        return redirect()->route('admin.regions.index')->with('success', __('Updated successfully'));
    }

    /** Flips the region between active and inactive. */
    public function status(Request $request, Region $region): JsonResponse|RedirectResponse
    {
        $region->update(['active' => ! $region->active]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'active' => (bool) $region->active]);
        }

        return back()->with('success', __('Updated successfully'));
    }

    public function destroy(Region $region): RedirectResponse
    {
        $region->delete();

        return redirect()->route('admin.regions.index')->with('success', __('Deleted successfully'));
    }
}
