{{--  <a  data-repeater-delete class="btn btn-{{ $btn_class ?? 'danger' }} fa fa-{{ $fa_class ?? 'trash' }}"> <span>{{ $delete ?? '' }}</span></a>  --}}
<a class="btn btn-label-danger" href="javascript:void(0);" data-repeater-delete>
    <i class="ti ti-x ti-xs me-1"></i>
    <span class="align-middle">{{ $delete ?? __("Delete") }}</span>
</a>


