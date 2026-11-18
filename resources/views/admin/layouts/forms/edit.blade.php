@php
    $route_name = getRouteName($route_type ?? null); 
    $add_class = $form_class ?? '';
    $route_method = $form_method ?? 'PATCH';
    $autocomplete = $form_autocomplete ?? 'off';
    $is_validate = $form_validate ?? 'yes';
    $route_status = $form_status ?? 'update';
    $form_control = [
        'method' => $route_method,
        'class' => 'systemira-form ' . $add_class,
        'autocomplete' => $autocomplete,
        // images post as real files, so every form has to be multipart
        'enctype' => 'multipart/form-data',
    ];

    if ($is_validate === 'yes') {
        $form_control['data-parsley-validate'] = 'parsley';
    }

    if (isset($parent_id) && isset($table_parent)) {
        $form_control['route'] = [
            "$route_name.$table_parent.$table.$route_status",
            [$parent_id, $model->id], 
        ];
    } else {
        $form_control['route'] = ["$route_name.$table.$route_status", $model->id];
    }
@endphp

{!! html()->modelForm(
        $model,
        $form_control['method'] ?? 'POST',
        is_array($form_control['route'])
            ? route($form_control['route'][0], $form_control['route'][1])
            : route($form_control['route']),
    )->class($form_control['class'] ?? '')->id($form_control['id'] ?? '')->attribute('autocomplete', $form_control['autocomplete'] ?? 'off')->attributes(
        collect($form_control)->except(['method', 'route', 'class', 'id', 'autocomplete'])->toArray(),
    )->open() !!}
