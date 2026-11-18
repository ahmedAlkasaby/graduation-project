<img
    class="user-picture
@if (isset($model->lastDayShift) && $model->lastDayShift->day_name == date('D')) @if ($model->lastDayShift->time_end != null)
        completed
    @else
        opening @endif
@else
cancel
@endif
"src="{{ asset('assets/images/user-picture.png') }}">{{ $model->name }}
