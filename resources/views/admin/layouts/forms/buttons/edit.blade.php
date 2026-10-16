@php
$route_name = getRouteName($route_type ?? NULL);
@endphp
<div class="col-sm-3">
    <div class="form-group text-center">
        <a href="{{ route("$route_name.$table.edit",$id) }}" class="btn btn-{{ $btn_block ?? 'block' }} btn-{{ $btn_class ?? 'info' }}"> {{ $save ?? __('Edit') }} </a>
    </div>
</div>
<div class="clearfix"></div>
