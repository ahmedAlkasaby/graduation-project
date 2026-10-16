<div class="col-12">
    <h5>Role Permissions</h5>
    <!-- Permission table -->
    <div class="table-responsive">
        <table class="table table-flush-spacing">
            <thead>
                <tr>
                    <th>{{ __('Model') }}</th>
                    <th>{{ __('View') }}</th>
                    <th>{{ __('Create') }}</th>
                    <th>{{ __('Edit') }}</th>
                    <th>{{ __('Show') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Delete') }}</th>
                </tr>
            </thead>
<tbody>
                <tr>
                    <td>
                        <input class="form-check-input" id="selectAll" type="checkbox" onClick="toggleV2(this , 'permissions', 'all')">
                        <label class="form-check-label" for="">{{ __('Select All') }}</label>
                    </td>
                    <td>
                        <input class="form-check-input permission-view-all" type="checkbox" onClick="toggleV2(this , 'permission-view', 'permission-view')">
                        <label class="form-check-label" for="">{{ __('Select All') }}</label>
                    </td>
                    <td>
                        <input class="form-check-input permission-create-all" type="checkbox" onClick="toggleV2(this , 'permission-create', 'permission-create')">
                        <label class="form-check-label" for="">{{ __('Select All') }}</label>
                    </td>
                    <td>
                        <input class="form-check-input permission-edit-all" type="checkbox" onClick="toggleV2(this , 'permission-edit', 'permission-edit')">
                        <label class="form-check-label" for="">{{ __('Select All') }}</label>
                    </td>
                    <td>
                        <input class="form-check-input permission-show-all" type="checkbox" onClick="toggleV2(this , 'permission-show', 'permission-show')">
                        <label class="form-check-label" for="">{{ __('Select All') }}</label>
                    </td>
                    <td>
                        <input class="form-check-input permission-status-all" type="checkbox" onClick="toggleV2(this , 'permission-status', 'permission-status')">
                        <label class="form-check-label" for="">{{ __('Select All') }}</label>
                    </td>
                    <td>
                        <input class="form-check-input permission-delete-all" type="checkbox" onClick="toggleV2(this , 'permission-delete', 'permission-delete')">
                        <label class="form-check-label" for="">{{ __('Select All') }}</label>
                    </td>
                </tr>
                @foreach ($data as $key => $model)
                <tr>
                    <td>
                        {{ $model['category_name'] }}
                    </td>
                    @php
                    @endphp
                    @foreach ($model['permissions'] as $id => $permission)
                    <td>
                        <div class="checkbox">
                            <input class="form-check-input permission-{{ $permission }}" type="checkbox" name="permissions[]" value="{{ $id }}" 
                                {{ in_array($id, $rolePermissions) ? 'checked' : '' }}>
                        </div>
                    </td>
                    @endforeach
                    @php
                    $count = count($model['permissions']);
                    if($count != 6){
                    for($i=$count;$i<6;$i++){
                        echo '<td></td>' ;
                        }
                        }
                    @endphp
                        </tr>
                    @endforeach
        </table>
    </div>
    <!-- Permission table -->
</div>
<div class="col-12 text-center mt-4">
    @include('admin.layouts.forms.buttons.form-save')
</div>