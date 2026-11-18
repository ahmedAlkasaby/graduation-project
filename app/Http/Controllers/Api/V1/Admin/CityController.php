<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\ApiHomeController;
use App\Http\Requests\Admin\CityRequest;
use App\Http\Resources\CityCollection;
use App\Http\Resources\CityResource;
use App\Models\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Managing cities from the dashboard. Unlike the public listing, an admin sees
 * inactive cities too and can filter on `active`.
 *
 * Access is decided by the route name: "admin.cities.index" needs the
 * "cities.index" permission, and so on (see AutoCheckPermission).
 */
class CityController extends ApiHomeController
{
    public function index(Request $request): JsonResponse
    {
        $query = City::with('country')->filter($request, 'admin');

        [$cities, $paginated] = $this->getDataWithPagination($query, $request);

        return $this->collectionResponse(new CityCollection($cities), null, null, $paginated);
    }

    public function store(CityRequest $request): JsonResponse
    {
        $city = City::create($request->validated())->load('country');

        return $this->successResponse(['city' => new CityResource($city)], 'Success', Response::HTTP_CREATED);
    }

    public function show(City $city): JsonResponse
    {
        return $this->successResponse(['city' => new CityResource($city->load('country'))]);
    }

    public function update(CityRequest $request, City $city): JsonResponse
    {
        $city->update($request->validated());

        return $this->successResponse(['city' => new CityResource($city->load('country'))]);
    }

    /** Flips the city between active and inactive. */
    public function status(City $city): JsonResponse
    {
        $city->update(['active' => ! $city->active]);

        return $this->successResponse(['city' => new CityResource($city->load('country'))]);
    }

    public function destroy(City $city): JsonResponse
    {
        $city->delete();

        return $this->successResponse(null, __('Deleted successfully'));
    }
}
