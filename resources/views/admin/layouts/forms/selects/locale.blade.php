<div class="form-group">
    @if(!isset($label_show))
    @include('admin.layouts.forms.fields.label',['label_default'=>__("Language")])
    @endif
    {!! html()->select($locale_text ?? 'locale', languageType(), $locale_value ?? null)->class('select2')->required() !!}
</div>
