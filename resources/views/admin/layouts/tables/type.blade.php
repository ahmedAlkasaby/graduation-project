<a class="dropdown-item" href="{{ route("admin.$table.$type",$id) }}">
    <i class=" ti ti-{{ $fa_class ?? 'printer' }}"></i>
    <span>{{ $value ?? __('Print') }}</span>
</a>
