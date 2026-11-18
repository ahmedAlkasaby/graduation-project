@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title')
    {{ __('Tasks') }}
@stop
@section('after_head')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/jkanban/jkanban.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/app-kanban.css') }}" />
    @include('admin.tasks.css.board')
@stop
@section('content')
    @include('admin.errors.alerts')
    <div class="row g-4 task-board"
         data-tasks-url="{{ route('admin.tasks.folder_tasks', ['id' => '__ID__']) }}"
         data-move-url="{{ route('admin.tasks.move') }}"
         data-board-url="{{ route('admin.tasks.board') }}"
         data-folder-store-url="{{ route('admin.tasks.folder_store') }}"
         data-folder-url="{{ route('admin.tasks.folder_show', ['id' => '__ID__']) }}"
         data-task-store-url="{{ route('admin.tasks.store') }}"
         data-task-url="{{ route('admin.tasks.update', ['task' => '__ID__']) }}"
         data-task-details-url="{{ route('admin.tasks.details', ['id' => '__ID__']) }}"
         data-task-comment-url="{{ route('admin.tasks.comment_store', ['id' => '__ID__']) }}"
         data-task-complete-url="{{ route('admin.tasks.complete', ['id' => '__ID__']) }}"
         data-task-stop-url="{{ route('admin.tasks.stop', ['id' => '__ID__']) }}"
         data-task-return-url="{{ route('admin.tasks.return', ['id' => '__ID__']) }}"
         data-task-reopen-url="{{ route('admin.tasks.reopen', ['id' => '__ID__']) }}"
         data-can-create="{{ $route_create ? 1 : 0 }}"
         data-can-edit="{{ $route_edit ? 1 : 0 }}"
         data-can-delete="{{ $route_delete ? 1 : 0 }}"
         data-selected="{{ $selectedFolder?->id }}"
         data-i18n-error="{{ __('Something went wrong, please try again') }}"
         data-i18n-moved="{{ __('Task moved') }}"
         data-i18n-new-folder="{{ __('New Folder') }}"
         data-i18n-edit-folder="{{ __('Edit Folder') }}"
         data-i18n-new-task="{{ __('New Task') }}"
         data-i18n-edit-task="{{ __('Edit Task') }}"
         data-i18n-confirm-delete="{{ __('Are you sure you want to delete this item?') }}"
         data-i18n-delete="{{ __('Delete') }}"
         data-i18n-yes="{{ __('Yes') }}"
         data-i18n-complete="{{ __('Complete') }}"
         data-i18n-confirm-complete="{{ __('Are you sure you completed this task?') }}"
         data-i18n-return="{{ __('Return') }}"
         data-i18n-confirm-return="{{ __('Return to this task?') }}"
         data-i18n-completed="{{ __('Completed') }}"
         data-i18n-reopen="{{ __('Reopen') }}"
         data-i18n-confirm-reopen="{{ __('Mark this task as not completed?') }}"
         data-i18n-stopped="{{ __('Stopped') }}"
         data-i18n-still-stopped="{{ __('Still stopped') }}"
         data-i18n-minutes="{{ __('minutes') }}"
         data-i18n-stopped-for="{{ __('stopped for') }}"
         data-i18n-over-estimate="{{ __('over the estimate by') }}"
         data-i18n-under-estimate="{{ __('under the estimate by') }}"
         data-i18n-after-due="{{ __('finished after the due date by') }}"
         data-i18n-before-due="{{ __('finished before the due date by') }}"
         data-i18n-no-stops="{{ __('This task was never stopped') }}"
         data-i18n-no-comments="{{ __('No comments yet') }}"
         data-i18n-no-attachments="{{ __('No attachments') }}"
         data-i18n-drop="{{ __('Drag images here or click to upload') }}">

        {{-- Left: folder tree --}}
        <div class="col-lg-3 col-md-4">
            <div class="card task-board-sidebar">
                <div class="card-header d-flex justify-content-between align-items-center py-3">
                    <h5 class="card-title mb-0">{{ __('Folders') }}</h5>
                    @if ($route_create)
                        <button type="button" class="btn btn-sm btn-icon btn-label-primary" id="folder-add-root"
                                title="{{ __('New Folder') }}">
                            <i class="ti ti-folder-plus"></i>
                        </button>
                    @endif
                </div>
                <div class="card-body p-0 folder-tree" id="folder-tree">
                    @include('admin.tasks.components.folder-sidebar')
                </div>
            </div>
        </div>

        {{-- Right: kanban of the selected folder --}}
        <div class="col-lg-9 col-md-8">
            <div class="card task-board-main">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
                    <div>
                        <h5 class="card-title mb-0" id="board-folder-name">
                            {{ $selectedFolder?->nameLang() ?? __('Select a folder') }}
                        </h5>
                        <small class="text-muted" id="board-project-name">{{ $selectedFolder?->project?->nameLang() }}</small>
                    </div>
                    <div class="d-flex gap-2">
                        @if ($route_create)
                            <button type="button" id="board-add-task" class="btn btn-primary btn-sm" {{ $selectedFolder ? '' : 'disabled' }}>
                                <i class="ti ti-plus me-1"></i>{{ __('Create Task') }}
                            </button>
                        @endif
                        <a class="btn btn-label-secondary btn-sm" href="{{ route('admin.tasks.index') }}">
                            <i class="ti ti-list me-1"></i>{{ __('List view') }}
                        </a>
                    </div>
                </div>
                <div class="card-body app-kanban">
                    <div id="board-empty" class="text-center text-muted py-5 {{ $selectedFolder ? 'd-none' : '' }}">
                        <i class="ti ti-folder-open display-4 d-block mb-3"></i>
                        {{ __('Pick a folder on the left to see its tasks') }}
                    </div>
                    <div id="board-loading" class="text-center py-5 d-none">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                    <div class="kanban-wrapper"></div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.tasks.components.folder-modal')
    @include('admin.tasks.components.task-modal')
    @include('admin.tasks.components.task-details')
    @include('admin.tasks.components.stop-modal')
    @include('admin.tasks.components.confirm-modal')
@stop
@section('mainFiles')
    @include('admin.layouts.forms.dropzone.dropzoneJs')
@stop
@section('after_foot')
    <script src="{{ asset('assets/vendor/libs/jkanban/jkanban.js') }}"></script>
    @include('admin.tasks.js.board')
@stop
