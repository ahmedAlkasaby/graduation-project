@if ($model->type == 'main')
    <span class="bold-text"> {{ branchName($model->type) }} </span>
@else
    {{ branchName($model->type) }}
@endif
