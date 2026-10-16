@extends('admin.layouts.app')
@section('title') {{ __('SEO') }}
@stop
@section('content')
    @include('admin.errors.alerts')

    {!! html()->form('POST', route('admin.settings.meta.store'))->attribute('data-parsley-validate', '')->attribute('enctype', 'multipart/form-data')->open() !!}

    <div class="row">
        <div class="col-sm-12">
            <div class="box">
                <div class="box-body">
                    <div class="form-group">
                        <label>{{ __('Logo Image') }}</label><br>
                        <input id="logo_image" name="logo_image" type="hidden" value="{{ $logo_image }}">
                        <img src="{{ $logo_image }}" @if ($logo_image == null) style="display:none;" @endif />
                        <a href="{{ URL::asset('filemanager/dialog.php?type=1&akey=admin_panel&field_id=logo_image') }}"
                            class="btn iframe-btn btn-success fa fa-download" type="button"></a>
                        <a href="#" class="btn btn-danger fa fa-remove  remove_image_link" type="button"></a>
                    </div>
                    <div class="form-group">
                        <label>{{ __('Share Image') }}</label><br>
                        <input id="share_image" name="share_image" type="hidden" value="{{ $share_image }}">
                        <img src="{{ $share_image }}" width="40%" height="auto"
                            @if ($share_image == null) style="display:none;" @endif />
                        <a href="{{ URL::asset('filemanager/dialog.php?type=1&akey=admin_panel&field_id=share_image') }}"
                            class="btn iframe-btn btn-success fa fa-download" type="button"></a>
                        <a href="#" class="btn btn-danger fa fa-remove  remove_image_link" type="button"></a>
                    </div>
                    <div class="form-group">
                        <label>{{ __('Default Image') }}</label><br>
                        <input id="default_image" name="default_image" type="hidden" value="{{ $default_image }}">
                        <img src="{{ $default_image }}" width="40%" height="auto"
                            @if ($default_image == null) style="display:none;" @endif />
                        <a href="{{ URL::asset('filemanager/dialog.php?type=1&akey=admin_panel&field_id=default_image') }}"
                            class="btn iframe-btn btn-success fa fa-download" type="button"></a>
                        <a href="#" class="btn btn-danger fa fa-remove  remove_image_link" type="button"></a>
                    </div>
                    <div class="form-group">
                        <label>{{ __('Description') }}</label>
                        {!! html()->textarea('description', $description ?? null)->class('form-control')->rows(5) !!}
                    </div>
                    <div class="form-group">
                        <label>{{ __('Keywords') }}</label>
                        {!! html()->textarea('keywords', $keywords ?? null)->class('form-control')->rows(5) !!}
                    </div>
                    <div class="form-group">
                        <label>{{ __('Facebook Pixel') }}</label>
                        {!! html()->textarea('facebook_pixel', $facebook_pixel ?? null)->class('form-control')->rows(5) !!}
                    </div>
                    <div class="form-group">
                        <label>{{ __('Google Analytic') }}</label>
                        {!! html()->textarea('google_analytic', $google_analytic ?? null)->class('form-control')->rows(5) !!}
                    </div>
                    @include('admin.layouts.forms.buttons.save')
                </div>
            </div>
        </div>

    </div>


    @include('admin.layouts.forms.close')

@stop
@section('after_foot')
    <script type="text/javascript">
        $('body').on('click', '.remove_image_link', function() {
            $(this).prev().prev().prev().val('');
            $(this).prev().prev().attr('src', '').hide();
        });

        $('.iframe-btn').fancybox({
            'type': 'iframe',
            maxWidth: 900,
            maxHeight: 600,
            fitToView: true,
            width: '100%',
            height: '100%',
            autoSize: false,
            closeClick: true,
            closeBtn: true
        });

        function responsive_filemanager_callback(field_id) {
            //        alert(field_id);
            $('#' + field_id).next().attr('src', document.getElementById(field_id).value).show();
            parent.$.fancybox.close();

        }
    </script>

@stop
