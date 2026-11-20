<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\AssistanceTypeEnum;
use App\Http\Controllers\Api\ApiHomeController;
use App\Http\Requests\Admin\CancelInstallmentRequest;
use App\Http\Requests\Admin\CaseAssistanceFilterRequest;
use App\Http\Requests\Admin\CaseAssistanceRequest;
use App\Http\Requests\Admin\CaseAssistanceUpdateRequest;
use App\Http\Resources\AssistanceInstallmentResource;
use App\Http\Resources\CaseAssistanceCollection;
use App\Http\Resources\CaseAssistanceResource;
use App\Models\AssistanceInstallment;
use App\Models\CaseAssistance;
use App\Models\CaseCategory;
use App\Services\CharityCase\CaseAssistanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * Assistances (support plans) from the dashboard. Creating one generates its
 * installments in the same transaction. After that, only `title` and
 * `description` can be updated: sending an amount, the type, the beneficiary
 * or any schedule field is a 422. A single pending installment can be
 * cancelled; nothing here records a payment.
 */
class CaseAssistanceController extends ApiHomeController
{
    public function __construct(private readonly CaseAssistanceService $assistanceService)
    {
        parent::__construct();
    }

    public function index(CaseAssistanceFilterRequest $request): JsonResponse
    {
        $query = CaseAssistance::query()
            ->with(['charityCase', 'category'])
            ->withInstallmentSummary()
            ->filter($request->validated());

        [$assistances, $paginated] = $this->getDataWithPagination($query, $request);

        return $this->collectionResponse(
            new CaseAssistanceCollection($assistances),
            ['permissions' => $this->listPagePermissions('case_assistances', ['is_store' => 'store', 'is_update' => 'update'])],
            null,
            $paginated,
        );
    }

    /** The types and categories the assistance form and list filter offer. */
    public function formOptions(): JsonResponse
    {
        $categories = CaseCategory::options();

        return $this->successResponse([
            'types' => array_map(
                fn (AssistanceTypeEnum $type): array => ['value' => $type->value, 'label' => $type->label()],
                AssistanceTypeEnum::cases(),
            ),
            'categories' => array_map(
                fn (int $id, string $label): array => ['value' => $id, 'label' => $label],
                array_keys($categories),
                $categories,
            ),
            'min_due_at' => today()->toDateString(),
            'min_month' => today()->format('Y-m'),
            'default_installments' => CaseAssistanceService::DEFAULT_INSTALLMENTS,
            'max_installments' => CaseAssistanceService::MAX_INSTALLMENTS,
            'currency' => settings()->get('currency', 'EGP'),
        ]);
    }

    public function store(CaseAssistanceRequest $request): JsonResponse
    {
        $assistance = $this->assistanceService->create($request->validated(), $request->user());

        return $this->successResponse(
            ['assistance' => new CaseAssistanceResource($this->loadDetails($assistance))],
            'Success',
            Response::HTTP_CREATED,
        );
    }

    public function show(CaseAssistance $caseAssistance): JsonResponse
    {
        return $this->successResponse(['assistance' => new CaseAssistanceResource($this->loadDetails($caseAssistance))]);
    }

    public function update(CaseAssistanceUpdateRequest $request, CaseAssistance $caseAssistance): JsonResponse
    {
        $this->assistanceService->update($caseAssistance, $request->validated());

        return $this->successResponse(['assistance' => new CaseAssistanceResource($this->loadDetails($caseAssistance))]);
    }

    /** Flips the assistance between active and inactive. Only its visibility changes. */
    public function status(CaseAssistance $caseAssistance): JsonResponse
    {
        $caseAssistance->update(['active' => ! $caseAssistance->active]);

        return $this->successResponse(['assistance' => new CaseAssistanceResource($this->loadDetails($caseAssistance))]);
    }

    public function cancelInstallment(
        CancelInstallmentRequest $request,
        CaseAssistance $caseAssistance,
        AssistanceInstallment $installment,
    ): JsonResponse {
        $installment = $this->assistanceService->cancelInstallment($installment, $request->validated('notes'), $request->user());

        return $this->successResponse(['installment' => new AssistanceInstallmentResource($installment)]);
    }

    private function loadDetails(CaseAssistance $assistance): CaseAssistance
    {
        return $assistance->load([
            'charityCase',
            'category',
            'installments' => fn ($query) => $query->orderBy('due_at'),
        ]);
    }
}
