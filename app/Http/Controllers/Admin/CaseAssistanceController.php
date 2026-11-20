<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AssistanceTypeEnum;
use App\Enums\InstallmentStatusEnum;
use App\Http\Requests\Admin\CancelInstallmentRequest;
use App\Http\Requests\Admin\CaseAssistanceFilterRequest;
use App\Http\Requests\Admin\CaseAssistanceRequest;
use App\Http\Requests\Admin\CaseAssistanceUpdateRequest;
use App\Models\AssistanceInstallment;
use App\Models\CaseAssistance;
use App\Models\CaseCategory;
use App\Models\CharityCase;
use App\Services\CharityCase\CaseAssistanceService;
use App\Services\CharityCase\CharityCaseService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Assistances (support plans) in the Blade admin panel, the twin of
 * Api\V1\Admin\CaseAssistanceController. Creating one generates its
 * installments; editing only changes the title and description; a single
 * pending installment can be cancelled from the details page.
 */
class CaseAssistanceController extends AdminController
{
    public function __construct(
        private readonly CaseAssistanceService $assistanceService,
        private readonly CharityCaseService $charityCaseService,
    ) {
        parent::__construct();
        $this->setClass('case_assistance');
    }

    /** The accepted filters are listed in CaseAssistanceFilterRequest. */
    public function index(CaseAssistanceFilterRequest $request): View
    {
        $filters = $request->validated();

        $assistances = CaseAssistance::query()
            ->with(['charityCase', 'category'])
            ->withInstallmentSummary()
            ->filter($filters)
            ->paginate($this->perPage($filters))
            ->withQueryString();

        return $this->listView('admin.case_assistances.index', $assistances, 'case_assistances', [
            'typeOptions' => AssistanceTypeEnum::optionsWithPlaceholder(),
            'categories' => ['' => __('All')] + CaseCategory::options(),
            'currency' => $this->currency(),
        ]);
    }

    /** `case_id` in the query string preselects the beneficiary. */
    public function create(Request $request): View
    {
        $caseId = old('case_id', $request->query('case_id'));

        return view('admin.case_assistances.create', [
            'selectedCase' => filled($caseId) ? CharityCase::find($caseId) : null,
            'categories' => CaseCategory::options(),
            'types' => AssistanceTypeEnum::cases(),
            'selectedType' => old('assistance_type', AssistanceTypeEnum::OneTime->value),
            'minDueAt' => today()->toDateString(),
            'minMonth' => today()->format('Y-m'),
            'defaultInstallments' => CaseAssistanceService::DEFAULT_INSTALLMENTS,
            'maxInstallments' => CaseAssistanceService::MAX_INSTALLMENTS,
            'canQuickCreateCase' => $this->can('charity_cases.store'),
            'currency' => $this->currency(),
        ] + $this->charityCaseService->formOptions());
    }

    public function store(CaseAssistanceRequest $request): RedirectResponse
    {
        $assistance = $this->assistanceService->create($request->validated(), $this->admin());

        return redirect()->route('admin.case_assistances.show', $assistance)->with('success', __('Created successfully'));
    }

    public function show(CaseAssistance $caseAssistance): View
    {
        $caseAssistance->load([
            'category',
            'charityCase.volunteer',
            'creator',
            'installments' => fn ($query) => $query->orderBy('due_at'),
        ]);

        $installments = $caseAssistance->installments;
        $statuses = $installments->map(fn (AssistanceInstallment $installment) => $installment->status());
        $countOf = fn (InstallmentStatusEnum $status): int => $statuses->filter(fn ($value) => $value === $status)->count();
        $paid = $countOf(InstallmentStatusEnum::Paid);

        return view('admin.case_assistances.show', [
            'assistance' => $caseAssistance,
            'installments' => $installments,
            // A one-time assistance has exactly one installment: its page shows it as a single payment.
            'installment' => $installments->first(),
            'currency' => $this->currency(),
            'stats' => [
                'total' => $installments->count(),
                'paid' => $paid,
                'pending' => $countOf(InstallmentStatusEnum::Pending),
                'overdue' => $countOf(InstallmentStatusEnum::Overdue),
                'cancelled' => $countOf(InstallmentStatusEnum::Cancelled),
                'planned_amount' => $installments->whereNull('cancelled_at')->sum('amount'),
                'paid_amount' => $installments->whereNotNull('paid_at')->sum('amount'),
                'paid_percent' => $installments->isEmpty() ? 0 : (int) round($paid / $installments->count() * 100),
                'first_due_at' => $installments->first()?->due_at,
                'last_due_at' => $installments->last()?->due_at,
            ],
            'canEdit' => $this->can('case_assistances.update'),
            'canCancelInstallment' => $this->can('case_assistances.cancel_installment'),
            'canShowCase' => $this->can('charity_cases.show'),
        ]);
    }

    /** The beneficiary and category stay editable; the amounts and schedule are shown read-only. */
    public function edit(CaseAssistance $caseAssistance): View
    {
        $caseId = old('case_id', $caseAssistance->case_id);

        return view('admin.case_assistances.edit', [
            'assistance' => $caseAssistance->load('charityCase')->loadCount('installments'),
            'selectedCase' => CharityCase::find($caseId),
            'categories' => CaseCategory::options(),
            'canQuickCreateCase' => false,
            'currency' => $this->currency(),
        ]);
    }

    public function update(CaseAssistanceUpdateRequest $request, CaseAssistance $caseAssistance): RedirectResponse
    {
        $this->assistanceService->update($caseAssistance, $request->validated());

        return redirect()->route('admin.case_assistances.show', $caseAssistance)->with('success', __('Updated successfully'));
    }

    /** Flips the assistance between active and inactive. Only its visibility changes. */
    public function status(Request $request, CaseAssistance $caseAssistance): JsonResponse|RedirectResponse
    {
        $caseAssistance->update(['active' => ! $caseAssistance->active]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'active' => (bool) $caseAssistance->active]);
        }

        return back()->with('success', __('Updated successfully'));
    }

    public function cancelInstallment(
        CancelInstallmentRequest $request,
        CaseAssistance $caseAssistance,
        AssistanceInstallment $installment,
    ): RedirectResponse {
        $this->assistanceService->cancelInstallment($installment, $request->validated('notes'), $this->admin());

        return redirect()->route('admin.case_assistances.show', $caseAssistance)->with('success', __('Installment cancelled'));
    }

    private function currency(): string
    {
        return (string) settings()->get('currency', 'EGP');
    }
}
