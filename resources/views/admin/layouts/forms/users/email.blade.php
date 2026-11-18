<div class="form-group">
    <label>{{ __('Email') }}</label>
    @if(!isset($not_req))
    {!! html()->text('email', null)->class('form-control')->required()->attribute('data-parsley-type', "email") !!}
    @else
    {!! html()->text('email', null)->class('form-control')->attribute('data-parsley-type', "email") !!}
    @endif
</div>
