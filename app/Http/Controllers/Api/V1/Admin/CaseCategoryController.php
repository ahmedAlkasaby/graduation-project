<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\ApiHomeController;
use App\Http\Requests\Admin\CaseCategoryFilterRequest;
use App\Http\Requests\Admin\CaseCategoryRequest;
use App\Http\Resources\CaseCategoryCollection;
use App\Http\Resources\CaseCategoryResource;
use App\Models\CaseCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * Managing case categories from the dashboard. An admin sees inactive
 * categories too and can filter on `active`. A category that still holds
 * assistances cannot be deleted (409).
 *
 * Access is decided by the route name: "admin.case_categories.index" needs the
 * "case_categories.index" permission, and so on (see AutoCheckPermission).
 */
class CaseCategoryController extends ApiHomeController
{
    public function index(CaseCategoryFilterRequest $request): JsonResponse
    {
        $query = CaseCategory::withCount('assistances')->filter($request->validated());

        [$categories, $paginated] = $this->getDataWithPagination($query, $request);

        return $this->collectionResponse(new CaseCategoryCollection($categories), null, null, $paginated);
    }

    public function store(CaseCategoryRequest $request): JsonResponse
    {
        $category = CaseCategory::create($request->validated());

        return $this->successResponse(['case_category' => new CaseCategoryResource($category)], 'Success', Response::HTTP_CREATED);
    }

    public function show(CaseCategory $caseCategory): JsonResponse
    {
        return $this->successResponse(['case_category' => new CaseCategoryResource($caseCategory->loadCount('assistances'))]);
    }

    public function update(CaseCategoryRequest $request, CaseCategory $caseCategory): JsonResponse
    {
        $caseCategory->update($request->validated());

        return $this->successResponse(['case_category' => new CaseCategoryResource($caseCategory)]);
    }

    /** Flips the category between active and inactive. */
    public function status(CaseCategory $caseCategory): JsonResponse
    {
        $caseCategory->update(['active' => ! $caseCategory->active]);

        return $this->successResponse(['case_category' => new CaseCategoryResource($caseCategory)]);
    }

    public function destroy(CaseCategory $caseCategory): JsonResponse
    {
        abort_if($caseCategory->isInUse(), Response::HTTP_CONFLICT, __('This category is used by assistances and cannot be deleted'));

        $caseCategory->delete();

        return $this->successResponse(null, __('Deleted successfully'));
    }
}
