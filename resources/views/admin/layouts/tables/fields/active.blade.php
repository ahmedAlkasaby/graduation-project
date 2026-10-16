@if ($model->active == 1)
    <p class="operation-status confirmed-status">{{ __('Active') }}</p>
@else
    <p class="operation-status cancelled-status">{{ __('In Active') }}</p>
@endif
