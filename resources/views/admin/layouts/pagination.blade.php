{{-- The panel's page links: first / previous / numbers / next / last, arrows flipped for RTL. --}}
@if ($paginator->hasPages())
    @php
        $isRtl = ($admin_dir ?? 'ltr') === 'rtl';
        $startIcon = $isRtl ? 'right' : 'left';
        $endIcon = $isRtl ? 'left' : 'right';
    @endphp
    <nav aria-label="{{ __('Page navigation') }}">
        <ul class="pagination pagination-rounded mb-0">
            @if ($paginator->onFirstPage())
                <li class="page-item first disabled"><span class="page-link"><i class="ti ti-chevrons-{{ $startIcon }} ti-xs"></i></span></li>
                <li class="page-item prev disabled"><span class="page-link"><i class="ti ti-chevron-{{ $startIcon }} ti-xs"></i></span></li>
            @else
                <li class="page-item first">
                    <a class="page-link" href="{{ $paginator->url(1) }}" title="{{ __('First') }}"><i class="ti ti-chevrons-{{ $startIcon }} ti-xs"></i></a>
                </li>
                <li class="page-item prev">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" title="{{ __('Previous') }}"><i class="ti ti-chevron-{{ $startIcon }} ti-xs"></i></a>
                </li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li class="page-item next">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" title="{{ __('Next') }}"><i class="ti ti-chevron-{{ $endIcon }} ti-xs"></i></a>
                </li>
                <li class="page-item last">
                    <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}" title="{{ __('Last') }}"><i class="ti ti-chevrons-{{ $endIcon }} ti-xs"></i></a>
                </li>
            @else
                <li class="page-item next disabled"><span class="page-link"><i class="ti ti-chevron-{{ $endIcon }} ti-xs"></i></span></li>
                <li class="page-item last disabled"><span class="page-link"><i class="ti ti-chevrons-{{ $endIcon }} ti-xs"></i></span></li>
            @endif
        </ul>
    </nav>
@endif
