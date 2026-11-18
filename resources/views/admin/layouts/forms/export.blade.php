@php
    $route_name = getRouteName($route_type ?? null);
    $form_route = [$route_name . ".$serivce_model.export"];
@endphp

{!! html()->form('POST')->route($form_route)->attribute('target', '_blank')->style('display: inline-block;')->open() !!}
{!! html()->csrf() !!}