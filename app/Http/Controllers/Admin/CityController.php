<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CityRequest;
use App\Models\City;
use App\Models\Country;
use Illuminate\Http\Request;

class CityController extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->setClass('city');
        $this->table = 'cities';
    }

    public function index(Request $request)
    {
        return view('admin.cities.index', [
            'data' => City::with('country')->filter($request, 'admin')->paginateRequested($request, $this->limit),
            'route_create' => $this->checkPerm($this->table . '.create'),
            'route_edit' => $this->checkPerm($this->table . '.edit'),
        ]);
    }

    public function create()
    {
        return view('admin.cities.create', [
            'countries' => Country::listForSelect(),
        ]);
    }

    public function store(CityRequest $request)
    {
        City::create($request->validated());

        return redirect()->route('admin.cities.index')->with('success', __('City created successfully'));
    }

    public function show(Request $request, $id)
    {
        return $this->edit($id);
    }

    public function edit($id)
    {
        $city = City::find($id);

        if (! $city) {
            return $this->pageError();
        }

        return view('admin.cities.edit', [
            'city' => $city,
            'countries' => Country::listForSelect(),
        ]);
    }

    public function update(CityRequest $request, $id)
    {
        $city = City::find($id);

        if (! $city) {
            return $this->pageError();
        }

        $city->update($request->validated());

        return redirect()->route('admin.cities.index')->with('success', __('City updated successfully'));
    }

    public function destroy($id)
    {
        $city = City::withCount('users')->find($id);

        // A city still pointed at by a user would leave those rows orphaned.
        if (! $city || $city->users_count > 0) {
            return redirect()->route('admin.cities.index')->with('error', __('City deleted failed'));
        }

        $city->delete();

        return redirect()->route('admin.cities.index')->with('success', __('City deleted successfully'));
    }
}
