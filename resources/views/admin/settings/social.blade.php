<div id="social-links" class="content">
    <div class="row g-3">
        {{-- <div class="col-12">
            <h6>2. {{ __('Social Links') }}</h6>
            <hr class="mt-0">
        </div> --}}
        <div class="col-md-6">
            <label class="form-label">{{ __('Facebook') }}</label>
            {!! html()->text('facebook', $setting['facebook'] ?? null)->class('form-control')->attribute('data-parsley-type', 'url') !!}
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('Twitter') }}</label>
            {!! html()->text('twitter', $setting['twitter'] ?? null)->class('form-control')->attribute('data-parsley-type', 'url') !!}
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('Youtube') }}</label>
            {!! html()->text('youtube', $setting['youtube'] ?? null)->class('form-control')->attribute('data-parsley-type', 'url') !!}
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('Instagram') }}</label>
            {!! html()->text('instagram', $setting['instagram'] ?? null)->class('form-control')->attribute('data-parsley-type', 'url') !!}
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('Whatsapp') }}</label>
            {!! html()->text('whatsapp', $setting['whatsapp'] ?? null)->class('form-control') !!}
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('Tiktok') }}</label>
            {!! html()->text('tiktok', $setting['tiktok'] ?? null)->class('form-control')->attribute('data-parsley-type', 'url') !!}
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('Snapchat') }}</label>
            {!! html()->text('snapchat', $setting['snapchat'] ?? null)->class('form-control')->attribute('data-parsley-type', 'url') !!}
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('Apple') }}</label>
            {!! html()->text('apple', $setting['apple'] ?? null)->class('form-control')->attribute('data-parsley-type', 'url') !!}
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('Android') }}</label>
            {!! html()->text('android', $setting['android'] ?? null)->class('form-control')->attribute('data-parsley-type', 'url') !!}
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('Huawei') }}</label>
            {!! html()->text('huawei', $setting['huawei'] ?? null)->class('form-control')->attribute('data-parsley-type', 'url') !!}
        </div>
    </div>
</div>