<div class="col-12 mb-4">
    <small class="text-light fw-medium"></small>
    <div class="bs-stepper wizard-numbered mt-2">
        <div class="bs-stepper-content">
            <form onSubmit="return false">
                <div class="row g-3">
                    <div class="col-sm-12">
                        @include('admin.layouts.forms.fields.select', [
                            'label_name' => __('Name'),
                            'select_name' => 'name',
                            'select_function' => userType('yes'),
                        ])
                        <div class="content-header mb-3"></div>
                        @include('admin.layouts.forms.texts.name', [
                            'name' => __('Display Name'),
                            'name_text' => 'display_name',
                            'not_req' => true,
                        ])
                        <div class="content-header mb-3"></div>
                        @include('admin.layouts.forms.fields.textarea', [
                            'text_name' => 'description',
                            'label_name' => __('Description'),
                            'not_req' => true,
                        ])
                        <div class="content-header mb-3"></div>
                    </div>
                </div>
            </form>
            @include('admin.roles.components.permissions-table', ['data' => $permissions])

        </div>
    </div>
</div>



<style>
    .checkbox input[type="checkbox"],
    .checkbox-inline input[type="checkbox"],
    .radio input[type="radio"],
    .radio-inline input[type="radio"] {
        margin-left: 0 !important;
        margin-top: 0 !important;
    }
</style>
