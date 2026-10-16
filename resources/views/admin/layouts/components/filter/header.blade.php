{{--
    Opens the collapsible filter panel above a list. A module drops its own
    fields between this and filter.footer.

    $filterKeys names the query inputs the panel owns — when any of them is in
    the URL the panel starts open, so a filtered list never looks unfiltered.
    The toggle script lives in admin.layouts.filter (include it in after_foot).
--}}
<div class="card mb-4">
    <div class="card-widget-separator-wrapper">
        <div class="ps-4">
            <div class="card-header" style="cursor: pointer;" onclick="toggleSearchForm()">
                <div class="d-flex align-items-center filter-title">
                    <h5 class="card-title mb-0">{{ __('Filter') }}</h5>
                    <a class="ti ti-arrow-down ms-2" id="toggleArrow"></a>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center row py-1 gap-3 gap-md-0">
                <div id="searchForm"
                    style="display: {{ request()->hasAny($filterKeys ?? ['search']) ? 'block' : 'none' }};">
                    <div class="p-3 p-md-3">
                        @include('admin.layouts.forms.search')
                        <div class="row">
