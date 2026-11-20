{{--
    The beneficiary profile create / edit form. Opened from the assistance
    form, `return_to` sends the new profile back there, already selected.
--}}
<div class="col-12 mb-4">
    <div class="card">
        <div class="card-body">
            @include('admin.charity_cases.components.fields')
            @if ($returnTo)
                {{ html()->hidden('return_to', $returnTo) }}
            @endif
            <div class="content-header mb-3"></div>
            @include('admin.layouts.forms.buttons.form-save')
        </div>
    </div>
</div>
