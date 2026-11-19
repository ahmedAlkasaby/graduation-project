<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CityFilterRequest;
use App\Http\Requests\Admin\CityRequest;
use App\Models\City;
use App\Models\Country;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Managing cities from the Blade admin panel, the twin of
 * Api\V1\Admin\CityController: an admin sees inactive cities too and can
 * filter on `country_id` and `active`.
 */
class CityController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->setClass('city');
    }

    public function index(CityFilterRequest $request): View
    {
        $filters = $request->validated();

        $cities = City::with('country')
            ->filter($filters)
            ->paginate($this->perPage($filters))
            ->withQueryString();

        return $this->listView('admin.cities.index', $cities, 'cities', [
            'countries' => ['' => __('All')] + Country::listForSelect(),
        ]);
    }

    public function create(): View
    {
        return view('admin.cities.create', ['countries' => Country::listForSelect()]);
    }

    public function store(CityRequest $request): RedirectResponse
    {
        City::create($request->validated());

        return redirect()->route('admin.cities.index')->with('success', __('Created successfully'));
    }

    public function show(City $city): View
    {
        return view('admin.cities.show', ['city' => $city->load('country')]);
    }

    public function edit(City $city): View
    {
        return view('admin.cities.edit', [
            'city' => $city,
            'countries' => Country::listForSelect(),
        ]);
    }

    public function update(CityRequest $request, City $city): RedirectResponse
    {
        $city->update($request->validated());

        return redirect()->route('admin.cities.index')->with('success', __('Updated successfully'));
    }

    /** Flips the city between active and inactive. */
    public function status(Request $request, City $city): JsonResponse|RedirectResponse
    {
        $city->update(['active' => ! $city->active]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'active' => (bool) $city->active]);
        }

        return back()->with('success', __('Updated successfully'));
    }

    public function destroy(City $city): RedirectResponse
    {
        $city->delete();

        return redirect()->route('admin.cities.index')->with('success', __('Deleted successfully'));
    }
}
