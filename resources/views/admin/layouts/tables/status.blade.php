{{--  @if($active == 0)
<a class=dropdown-item {{ $ajax_class ?? '' }}  {{ $error_class   ?? 'danger' }}"   data-id="{{ $id }}"  data-{{ $data_status ?? 'status' }}="{{ $error_status ?? 1 }}" ><i class="fa-solid fa-circle-xmark" style="color:#dc3535; font-size:1.5rem"></i>
</a>
@else
<a class="dropdown-item @if(!isset($model_is_read)) {{ $ajax_class ?? '' }} @endif  {{ $success_class ?? 'success' }}"  data-id="{{ $id }}"  data-{{ $data_status ?? 'status' }}="{{ $success_status ?? 0 }}" ><i class="fa-solid fa-circle-check" style="color: #19873a; font-size:1.5rem"></i>
</a>
@endif  --}}

<label class="switch switch-{{ $success_class ?? 'success' }}">
    <input type="checkbox" class="switch-input  {{ $ajax_class ?? '' }} " @if($active >= 1) checked @endif @if(isset($is_read) && $active >= 1) disabled @endif  data-id="{{ $id }}" />
    <span class="switch-toggle-slider">
      <span class="switch-on">
        <i class="ti ti-check"></i>
      </span>
      <span class="switch-off">
        <i class="ti ti-x"></i>
      </span>
    </span>
</label>
