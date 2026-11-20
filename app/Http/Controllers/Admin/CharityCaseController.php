<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CharityCaseFilterRequest;
use App\Http\Requests\Admin\CharityCaseLookupRequest;
use App\Http\Requests\Admin\CharityCaseRequest;
use App\Models\CharityCase;
use App\Services\CharityCase\CharityCaseService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Beneficiary profiles ("shared cases") in the Blade admin panel, the twin of
 * Api\V1\Admin\CharityCaseController. A profile is who the beneficiary is;
 * what they receive is managed under assistances.
 *
 * `store` also answers the assistance form's quick-create dialog with JSON,
 * and returns to the assistance form when opened from it (`return_to`).
 */
class CharityCaseController extends AdminController
{
    /** The `return_to` value that sends a new profile back to the assistance form. */
    public const RETURN_TO_ASSISTANCE = 'assistance';

    public function __construct(private readonly CharityCaseService $charityCaseService)
    {
        parent::__construct();
        $this->setClass('charity_case');
    }

    /** The accepted filters are listed in CharityCaseFilterRequest. */
    public function index(CharityCaseFilterRequest $request): View
    {
        $filters = $request->validated();

        $cases = CharityCase::query()
            ->with(['category', 'volunteer'])
            ->withCount('assistances')
            ->filter($filters)
            ->paginate($this->perPage($filters))
            ->withQueryString();

        $options = $this->charityCaseService->formOptions();

        return $this->listView('admin.charity_cases.index', $cases, 'charity_cases', [
            'categories' => ['' => __('All')] + $options['categories'],
            'volunteers' => ['' => __('All')] + $options['volunteers'],
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.charity_cases.create', $this->charityCaseService->formOptions() + [
            'returnTo' => $request->query('return_to') === self::RETURN_TO_ASSISTANCE ? self::RETURN_TO_ASSISTANCE : null,
        ]);
    }

    public function store(CharityCaseRequest $request): JsonResponse|RedirectResponse
    {
        $charityCase = $this->charityCaseService->create($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Created successfully'),
                'case' => ['id' => $charityCase->id, 'text' => $charityCase->selectLabel()],
            ], Response::HTTP_CREATED);
        }

        if ($request->input('return_to') === self::RETURN_TO_ASSISTANCE) {
            return redirect()
                ->route('admin.case_assistances.create', ['case_id' => $charityCase->id])
                ->with('success', __('Created successfully'));
        }

        return redirect()->route('admin.charity_cases.show', $charityCase)->with('success', __('Created successfully'));
    }

    public function show(CharityCase $charityCase): View
    {
        $charityCase->load(['category', 'volunteer']);

        $assistances = $charityCase->assistances()
            ->withInstallmentSummary()
            ->latest('id')
            ->get();

        return view('admin.charity_cases.show', [
            'charityCase' => $charityCase,
            'assistances' => $assistances,
            'currency' => settings()->get('currency', 'EGP'),
            'canEdit' => $this->can('charity_cases.update'),
            'canAddAssistance' => $this->can('case_assistances.store'),
            'canShowAssistance' => $this->can('case_assistances.show'),
        ]);
    }

    public function edit(CharityCase $charityCase): View
    {
        return view('admin.charity_cases.edit', [
            'charityCase' => $charityCase,
            'returnTo' => null,
        ] + $this->charityCaseService->formOptions());
    }

    public function update(CharityCaseRequest $request, CharityCase $charityCase): RedirectResponse
    {
        $this->charityCaseService->update($charityCase, $request->validated());

        return redirect()->route('admin.charity_cases.show', $charityCase)->with('success', __('Updated successfully'));
    }

    /** The searchable beneficiary select of the assistance form, in select2's format. */
    public function lookup(CharityCaseLookupRequest $request): JsonResponse
    {
        $results = $this->charityCaseService->lookup($request->validated('search'))
            ->map(fn (CharityCase $charityCase): array => [
                'id' => $charityCase->id,
                'text' => $charityCase->selectLabel(),
            ]);

        return response()->json(['results' => $results]);
    }
}
