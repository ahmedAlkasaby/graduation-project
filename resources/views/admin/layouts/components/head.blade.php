{{--
    Back-arrow header for a create / edit / show screen.
    Pass $back_url — usually $previous_url ?? route('admin.<table>.index').
--}}
@php
    $dir = ($admin_language ?? 'en') === 'en' ? 'left' : 'right';
@endphp
<div class="row">
    <div class="col-lg-12 margin-tb">
        <a class="btn btn-primary ti ti-arrow-{{ $dir }}" href="{{ $back_url }}"></a>
    </div>
</div>
