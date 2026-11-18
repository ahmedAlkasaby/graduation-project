@php
    $route_name = getRouteName($route_type ?? null);
    $add_class = $form_class ?? '';
    $route_method = $form_method ?? 'POST';
    $autocomplete = $form_autocomplete ?? 'off';
    $is_validate = $form_validate ?? 'yes';
    $route_status = $form_status ?? 'store';

    $form_attributes = [
        'class' => 'systemira-form ' . $add_class,
        'autocomplete' => $autocomplete,
        // images post as real files, so every form has to be multipart
        'enctype' => 'multipart/form-data',
    ];

    if ($is_validate === 'yes') {
        $form_attributes['data-parsley-validate'] = 'parsley';
    }

    if (isset($model_id) && isset($table_parent)) {
        $form_route = $route_name . ".$table_parent$table.$route_status";
        $form_params = $model_id;
    } elseif (isset($model_id) && !isset($table_parent)) {
        $form_route = $route_name . ".$table.$route_status";
        $form_params = $model_id;
    } else {
        $form_route = $route_name . ".$table.$route_status";
        $form_params = [];
    }
@endphp

{!! html()->form($route_method)->route($form_route, $form_params)->attributes($form_attributes)->open() !!}
