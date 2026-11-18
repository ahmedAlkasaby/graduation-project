@php $row_table = $model->getTable(); @endphp
<div class="dropdown">
    <button type="button" class="btn p-0 dropdown-toggle hide-arrow show" data-bs-toggle="dropdown" aria-expanded="true">
        <i class="ti ti-dots-vertical"></i>
    </button>
    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-icons" aria-labelledby="dropdownMenuButton">
        @if ($model->deleted_at == null)

            @if (($show ?? false) && auth()->user()->can($row_table . '.show'))
                @include('admin.layouts.tables.show', ['table' => $row_table, 'id' => $model->id])
            @endif

            @if (($showEdit ?? false) && isset($route_edit) && $route_edit == true)
                @include('admin.layouts.tables.edit', ['table' => $row_table, 'id' => $model->id])
            @endif

            @if (($showDelete ?? false)
                && in_array($row_table, $admin_delete_models ?? [])
                && auth()->user()->can($row_table . '.destroy'))
                @include('admin.layouts.tables.delete-form', [
                    'btn_class' => 'dropdown-item',
                    'table' => $row_table,
                    'id' => $model->id,
                ])
            @endif

        @else

            @if (($showRestore ?? false)
                && in_array($row_table, $admin_restore_models ?? [])
                && auth()->user()->can($row_table . '.destroy'))
                @include('admin.layouts.tables.restore', [
                    'btn_class' => 'btn btn-primary',
                    'table' => $row_table,
                    'id' => $model->id,
                ])
            @endif

        @endif
    </div>
</div>
