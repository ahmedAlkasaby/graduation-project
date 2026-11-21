<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CaseCategoryFilterRequest;
use App\Http\Requests\Admin\CaseCategoryRequest;
use App\Models\CaseCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Managing case categories from the Blade admin panel, the twin of
 * Api\V1\Admin\CaseCategoryController: an admin sees inactive categories too
 * and can filter on `active`. A category that still holds assistances cannot be
 * deleted.
 */
class CaseCategoryController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->setClass('case_category');
    }

    public function index(CaseCategoryFilterRequest $request): View
    {
        $filters = $request->validated();

        $categories = CaseCategory::withCount('assistances')
            ->filter($filters)
            ->paginate($this->perPage($filters))
            ->withQueryString();

        return $this->listView('admin.case_categories.index', $categories, 'case_categories');
    }

    public function create(): View
    {
        return view('admin.case_categories.create');
    }

    /** Also answers the assistance form's quick-create dialog with JSON. */
    public function store(CaseCategoryRequest $request): JsonResponse|RedirectResponse
    {
        $category = CaseCategory::create($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Created successfully'),
                'category' => ['id' => $category->id, 'text' => $category->nameLang(app()->getLocale())],
            ], Response::HTTP_CREATED);
        }

        return redirect()->route('admin.case_categories.index')->with('success', __('Created successfully'));
    }

    public function show(CaseCategory $caseCategory): View
    {
        return view('admin.case_categories.show', ['caseCategory' => $caseCategory->loadCount('assistances')]);
    }

    public function edit(CaseCategory $caseCategory): View
    {
        return view('admin.case_categories.edit', ['caseCategory' => $caseCategory]);
    }

    public function update(CaseCategoryRequest $request, CaseCategory $caseCategory): RedirectResponse
    {
        $caseCategory->update($request->validated());

        return redirect()->route('admin.case_categories.index')->with('success', __('Updated successfully'));
    }

    /** Flips the category between active and inactive. */
    public function status(Request $request, CaseCategory $caseCategory): JsonResponse|RedirectResponse
    {
        $caseCategory->update(['active' => ! $caseCategory->active]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'active' => (bool) $caseCategory->active]);
        }

        return back()->with('success', __('Updated successfully'));
    }

    public function destroy(CaseCategory $caseCategory): RedirectResponse
    {
        if ($caseCategory->isInUse()) {
            return redirect()->route('admin.case_categories.index')->with('error', __('This category is used by assistances and cannot be deleted'));
        }

        $caseCategory->delete();

        return redirect()->route('admin.case_categories.index')->with('success', __('Deleted successfully'));
    }
}
