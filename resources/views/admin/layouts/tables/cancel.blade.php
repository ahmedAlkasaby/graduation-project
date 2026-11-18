{{-- <a id="cancel" data-id="{{ $id }}" class="btn-width btn btn-{{ $btn_class ?? 'danger' }} fa fa-{{ $fa_class ?? 'window-close' }}"><span>{{ $cancel ?? '' }}</span></a> --}}
@php
    $btn_class = $btn_class ?? 'btn rounded-pill btn-danger waves-effect waves-light';
@endphp

{!! html()->form('POST', route("admin.$table.cancel", $id))->style('display:inline')->id('cancel-form')->open() !!}
<button type="submit" class="{{ $btn_class }}" data-cancel-id="{{ $id }}">
    {{ $cancel ?? 'Cancel' }}
</button>

@include('admin.layouts.forms.close')
