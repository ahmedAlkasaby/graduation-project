{{--
    The "create" button above a list. Pass $table and $create_label; the
    controller decides whether it shows, by passing $route_create.
--}}
<div class="d-flex justify-content-end align-items-baseline">
    <div class="dt-action-buttons d-flex align-items-center mb-3 mb-md-0 pt-0 gap-4 gap-sm-0 flex-sm-row">
        @if (isset($route_create) && $route_create == true)
            @include('admin.layouts.tables.add', ['table' => $table, 'value' => $create_label])
        @endif
    </div>
</div>

<div class="content-header mb-3"></div>
