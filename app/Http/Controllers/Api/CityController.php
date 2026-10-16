<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CityCollection;
use App\Http\Resources\CityResource;
use App\Models\City;
use Exception;
use Illuminate\Http\Request;

class CityController extends ApiHomeController
{
    public function index(Request $request)
    {
        try {
            $query = City::with('country')->filter($request);

            [$cities, $paginated] = $this->getDataWithPagination($query, $request);

            return $this->collectionResponse(new CityCollection($cities), null, null, $paginated);
        } catch (Exception $e) {
            return $this->failedResponse($e);
        }
    }

    public function show($id)
    {
        $city = City::with('country')->find($id);

        if (! $city) {
            return $this->errorResponse();
        }

        return $this->successResponse(['city' => new CityResource($city)]);
    }
}
