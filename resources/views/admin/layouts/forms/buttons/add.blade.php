{{--  <div class="content-header mb-3"></div>
<a  data-repeater-create class="btn btn-{{ $btn_class ?? 'success' }} fa fa-{{ $fa_class ?? 'plus' }} btn btn-primary waves-effect waves-light"> <span>{{ $add ?? '' }}</span></a>  --}}
<a class="btn btn-primary" href="javascript:void(0);" data-repeater-create>
    <i class="ti ti-plus me-1"></i>
    <span class="align-middle">{{ $add ?? __("Add") }}</span>
</a>
