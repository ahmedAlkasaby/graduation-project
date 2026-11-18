<div id="social-links" class="content">
    <div class="row g-3">
        <div class="col-md-6">
            @include('admin.layouts.forms.fields.text', [
                'text_name' => 'facebook',
                'text_value' => $setting['facebook'] ?? null,
                'label_name' => __('Facebook'),
                'not_req' => true,
                'text_type' => 'url',
            ])
        </div>
        <div class="col-md-6">
            @include('admin.layouts.forms.fields.text', [
                'text_name' => 'twitter',
                'text_value' => $setting['twitter'] ?? null,
                'label_name' => __('Twitter'),
                'not_req' => true,
                'text_type' => 'url',
            ])
        </div>
        <div class="col-md-6">
            @include('admin.layouts.forms.fields.text', [
                'text_name' => 'youtube',
                'text_value' => $setting['youtube'] ?? null,
                'label_name' => __('Youtube'),
                'not_req' => true,
                'text_type' => 'url',
            ])
        </div>
        <div class="col-md-6">
            @include('admin.layouts.forms.fields.text', [
                'text_name' => 'instagram',
                'text_value' => $setting['instagram'] ?? null,
                'label_name' => __('Instagram'),
                'not_req' => true,
                'text_type' => 'url',
            ])
        </div>
        <div class="col-md-6">
            @include('admin.layouts.forms.fields.text', [
                'text_name' => 'whatsapp',
                'text_value' => $setting['whatsapp'] ?? null,
                'label_name' => __('Whatsapp'),
                'not_req' => true,
            ])
        </div>
        <div class="col-md-6">
            @include('admin.layouts.forms.fields.text', [
                'text_name' => 'tiktok',
                'text_value' => $setting['tiktok'] ?? null,
                'label_name' => __('Tiktok'),
                'not_req' => true,
                'text_type' => 'url',
            ])
        </div>
        <div class="col-md-6">
            @include('admin.layouts.forms.fields.text', [
                'text_name' => 'snapchat',
                'text_value' => $setting['snapchat'] ?? null,
                'label_name' => __('Snapchat'),
                'not_req' => true,
                'text_type' => 'url',
            ])
        </div>
        <div class="col-md-6">
            @include('admin.layouts.forms.fields.text', [
                'text_name' => 'apple',
                'text_value' => $setting['apple'] ?? null,
                'label_name' => __('Apple'),
                'not_req' => true,
                'text_type' => 'url',
            ])
        </div>
        <div class="col-md-6">
            @include('admin.layouts.forms.fields.text', [
                'text_name' => 'android',
                'text_value' => $setting['android'] ?? null,
                'label_name' => __('Android'),
                'not_req' => true,
                'text_type' => 'url',
            ])
        </div>
        <div class="col-md-6">
            @include('admin.layouts.forms.fields.text', [
                'text_name' => 'huawei',
                'text_value' => $setting['huawei'] ?? null,
                'label_name' => __('Huawei'),
                'not_req' => true,
                'text_type' => 'url',
            ])
        </div>
    </div>
</div>
