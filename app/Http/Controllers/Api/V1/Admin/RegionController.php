<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\ApiHomeController;
use App\Http\Requests\Admin\RegionFilterRequest;
use App\Http\Requests\Admin\RegionRequest;
use App\Http\Resources\RegionCollection;
use App\Http\Resources\RegionResource;
use App\Models\Region;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * Managing regions (the areas inside a governorate) from the dashboard. An
 * admin sees inactive regions too and can filter on `city_id` and `active`.
 *
 * Access is decided by the route name: "admin.regions.index" needs the
 * "regions.index" permission, and so on (see AutoCheckPermission).
 */
class RegionController extends ApiHomeController
{
    public function index(RegionFilterRequest $request): JsonResponse
    {
        $query = Region::with('city')->filter($request->validated());

        [$regions, $paginated] = $this->getDataWithPagination($query, $request);

        return $this->collectionResponse(new RegionCollection($regions), null, null, $paginated);
    }

    public function store(RegionRequest $request): JsonResponse
    {
        $region = Region::create($request->validated())->load('city');

        return $this->successResponse(['region' => new RegionResource($region)], 'Success', Response::HTTP_CREATED);
    }

    public function show(Region $region): JsonResponse
    {
        return $this->successResponse(['region' => new RegionResource($region->load('city'))]);
    }

    public function update(RegionRequest $request, Region $region): JsonResponse
    {
        $region->update($request->validated());

        return $this->successResponse(['region' => new RegionResource($region->load('city'))]);
    }

    /** Flips the region between active and inactive. */
    public function status(Region $region): JsonResponse
    {
        $region->update(['active' => ! $region->active]);

        return $this->successResponse(['region' => new RegionResource($region->load('city'))]);
    }

    public function destroy(Region $region): JsonResponse
    {
        $region->delete();

        return $this->successResponse(null, __('Deleted successfully'));
    }
}
