<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\ApiHomeController;
use App\Http\Requests\Admin\CharityCaseFilterRequest;
use App\Http\Requests\Admin\CharityCaseLookupRequest;
use App\Http\Requests\Admin\CharityCaseRequest;
use App\Http\Resources\CharityCaseCollection;
use App\Http\Resources\CharityCaseResource;
use App\Models\CharityCase;
use App\Services\CharityCase\CharityCaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * Beneficiary profiles ("shared cases") from the dashboard. A profile is who
 * the beneficiary is; their support plans live under case-assistances.
 *
 * Access is decided by the route name: "charity_cases.index" needs the
 * "charity_cases.index" permission, and so on (see AutoCheckPermission).
 */
class CharityCaseController extends ApiHomeController
{
    public function __construct(private readonly CharityCaseService $charityCaseService)
    {
        parent::__construct();
    }

    public function index(CharityCaseFilterRequest $request): JsonResponse
    {
        $query = CharityCase::query()
            ->with(['category', 'volunteer'])
            ->withCount('assistances')
            ->filter($request->validated());

        [$cases, $paginated] = $this->getDataWithPagination($query, $request);

        return $this->collectionResponse(
            new CharityCaseCollection($cases),
            ['permissions' => $this->listPagePermissions('charity_cases', ['is_store' => 'store', 'is_update' => 'update'])],
            null,
            $paginated,
        );
    }

    /** The category and volunteer lists the profile form needs. */
    public function formOptions(): JsonResponse
    {
        $options = $this->charityCaseService->formOptions();

        return $this->successResponse([
            'categories' => $this->asOptions($options['categories']),
            'volunteers' => $this->asOptions($options['volunteers']),
        ]);
    }

    /** Beneficiaries matching `search` (name, national ID or phone), for a searchable select. */
    public function lookup(CharityCaseLookupRequest $request): JsonResponse
    {
        $cases = $this->charityCaseService->lookup($request->validated('search'));

        return $this->successResponse(['cases' => CharityCaseResource::collection($cases)]);
    }

    public function store(CharityCaseRequest $request): JsonResponse
    {
        $charityCase = $this->charityCaseService->create($request->validated());

        return $this->successResponse(
            ['case' => new CharityCaseResource($charityCase->load(['category', 'volunteer']))],
            'Success',
            Response::HTTP_CREATED,
        );
    }

    public function show(CharityCase $charityCase): JsonResponse
    {
        $charityCase->load([
            'category',
            'volunteer',
            'assistances' => fn ($query) => $query->withInstallmentSummary()->latest('id'),
        ]);

        return $this->successResponse(['case' => new CharityCaseResource($charityCase)]);
    }

    public function update(CharityCaseRequest $request, CharityCase $charityCase): JsonResponse
    {
        $this->charityCaseService->update($charityCase, $request->validated());

        return $this->successResponse(['case' => new CharityCaseResource($charityCase->load(['category', 'volunteer']))]);
    }

    /**
     * @param  array<int|string, string>  $options
     * @return list<array{value: int|string, label: string}>
     */
    private function asOptions(array $options): array
    {
        return array_map(
            fn (int|string $value, string $label): array => ['value' => $value, 'label' => $label],
            array_keys($options),
            $options,
        );
    }
}
