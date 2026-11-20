{{--
    The beneficiary profile fields, shared by the create / edit form and the
    assistance form's quick-create dialog. Every field is required. Pass
    $plainSelects inside a modal, where select2 dropdowns do not work.
--}}
<div class="row g-3">
    <div class="col-sm-6">
        @include('admin.layouts.forms.fields.text', [
            'label_req' => true,
            'label_name' => __('Full Name'),
            'text_name' => 'full_name',
            'text_value' => $charityCase->full_name ?? null,
        ])
    </div>
    <div class="col-sm-6">
        @include('admin.layouts.forms.fields.text', [
            'label_req' => true,
            'label_name' => __('National ID'),
            'text_name' => 'national_id',
            'text_value' => $charityCase->national_id ?? null,
            'text_type' => 'digits',
            'placeholder' => __('14 digits'),
        ])
    </div>
    <div class="col-sm-6">
        @include('admin.layouts.forms.fields.text', [
            'label_req' => true,
            'label_name' => __('Phone'),
            'text_name' => 'phone',
            'text_value' => $charityCase->phone ?? null,
            'text_type' => 'digits',
        ])
    </div>
    <div class="col-sm-6">
        @include('admin.layouts.forms.fields.select', [
            'label_req' => true,
            'label' => __('Category'),
            'select_name' => 'category_id',
            'select_value' => $charityCase->category_id ?? null,
            'select_function' => ['' => __('Choose')] + $categories,
            'select_class' => ($plainSelects ?? false) ? 'form-select' : 'form-select form-control select2',
        ])
    </div>
    <div class="col-sm-6">
        @include('admin.layouts.forms.fields.select', [
            'label_req' => true,
            'label' => __('Volunteer'),
            'select_name' => 'volunteer_id',
            'select_value' => $charityCase->volunteer_id ?? null,
            'select_function' => ['' => __('Choose')] + $volunteers,
            'select_class' => ($plainSelects ?? false) ? 'form-select' : 'form-select form-control select2',
        ])
    </div>
</div>
