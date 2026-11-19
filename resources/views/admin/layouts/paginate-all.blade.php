{{--
    The list footer, shared by every index page: rows per page, how many rows
    are shown, and the page links. $data is a paginator. Include it after the
    .table-responsive wrapper so it does not scroll sideways with a wide table.
--}}
@php
    $perPage = $data->perPage();
    $perPageLinks = collect(adminPerPageOptions($perPage))
        ->mapWithKeys(fn (string $label, int $size): array => [$size => request()->fullUrlWithQuery(['limit' => $size, 'page' => null])]);
@endphp
<div class="admin-pagination border-top px-3 px-md-4 py-3">
    <div class="d-flex flex-column flex-lg-row align-items-center justify-content-between gap-3">
        <div class="d-flex flex-wrap align-items-center justify-content-center gap-3 text-muted small">
            <label class="d-flex align-items-center gap-2 mb-0">
                <span>{{ __('Show') }}</span>
                <select class="form-select form-select-sm w-auto admin-per-page" aria-label="{{ __('Per Page') }}">
                    @foreach ($perPageLinks as $size => $url)
                        <option value="{{ $url }}" @selected($size === $perPage)>{{ $size }}</option>
                    @endforeach
                </select>
                <span>{{ __('per page') }}</span>
            </label>

            <span class="d-none d-sm-inline vr"></span>

            <span>
                @if ($data->total() > 0)
                    {!! __('Showing :from to :to of :total entries', [
                        'from' => '<span class="fw-medium text-heading">'.$data->firstItem().'</span>',
                        'to' => '<span class="fw-medium text-heading">'.$data->lastItem().'</span>',
                        'total' => '<span class="fw-medium text-heading">'.number_format($data->total()).'</span>',
                    ]) !!}
                @else
                    {{ __('No data found') }}
                @endif
            </span>
        </div>

        @if ($data->hasPages())
            <div class="d-flex flex-column flex-sm-row align-items-center gap-2">
                <span class="text-muted small d-sm-none">
                    {{ __('Page :current of :last', ['current' => $data->currentPage(), 'last' => $data->lastPage()]) }}
                </span>
                {{ $data->onEachSide(1)->links('admin.layouts.pagination') }}
            </div>
        @endif
    </div>
</div>

@once
    <style>
        .admin-pagination .pagination { flex-wrap: wrap; justify-content: center; gap: .25rem; }
        .admin-pagination .page-link { min-width: 2.25rem; text-align: center; }
        .admin-pagination .vr { height: 1.25rem; align-self: center; }
        /* Phones: arrows and the current page only, with "Page x of y" beside them. */
        @media (max-width: 575.98px) {
            .admin-pagination .page-item:not(.active):not(.first):not(.prev):not(.next):not(.last) { display: none; }
        }
    </style>
    <script>
        document.addEventListener('change', function (event) {
            if (event.target.classList.contains('admin-per-page')) {
                window.location.href = event.target.value;
            }
        });
    </script>
@endonce
