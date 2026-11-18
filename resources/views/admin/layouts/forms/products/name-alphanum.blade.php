<div class="form-group">
    <label>{{ __('Name') }}</label>
    {!! html()->text('name', null)->class('form-control')->required()->attribute('data-parsley-type', 'alphanum') !!}
</div>
