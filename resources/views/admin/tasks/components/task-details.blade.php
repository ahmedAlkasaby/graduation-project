{{-- Task details panel --}}
<div class="offcanvas offcanvas-end task-details" tabindex="-1" id="taskDetails" aria-hidden="true">
    <div class="offcanvas-header border-bottom align-items-start">
        <div class="me-2">
            <h5 class="offcanvas-title mb-1" id="details-title"></h5>
            <div class="d-flex flex-wrap gap-2" id="details-badges"></div>
        </div>
        <div class="d-flex align-items-center gap-1">
            @if ($route_edit)
                <button type="button" class="btn btn-sm btn-label-primary" id="details-edit">
                    <i class="ti ti-edit me-1"></i>{{ __('Edit') }}
                </button>
            @endif
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
    </div>
    <div class="offcanvas-body">
        <div id="details-loading" class="text-center py-5 d-none">
            <div class="spinner-border text-primary" role="status"></div>
        </div>
        <div id="details-content">
            @if ($route_edit)
                <div class="d-flex flex-wrap gap-2 mb-4 details-actions">
                    <button type="button" class="btn btn-sm btn-success d-none" id="details-complete">
                        <i class="ti ti-circle-check me-1"></i>{{ __('Complete') }}
                    </button>
                    <button type="button" class="btn btn-sm btn-label-warning d-none" id="details-stop">
                        <i class="ti ti-player-pause me-1"></i>{{ __('Stop') }}
                    </button>
                    <button type="button" class="btn btn-sm btn-label-info d-none" id="details-return">
                        <i class="ti ti-player-play me-1"></i>{{ __('Return') }}
                    </button>
                    <button type="button" class="btn btn-sm btn-label-secondary d-none" id="details-reopen">
                        <i class="ti ti-rotate-2 me-1"></i>{{ __('Reopen') }}
                    </button>
                </div>
            @endif
            <dl class="row details-meta mb-4">
                <dt class="col-5">{{ __('Due Date') }}</dt><dd class="col-7" id="details-due"></dd>
                <dt class="col-5">{{ __('Assignees') }}</dt><dd class="col-7" id="details-assignee"></dd>
                <dt class="col-5">{{ __('Estimated Minutes') }}</dt><dd class="col-7" id="details-estimated"></dd>
                <dt class="col-5">{{ __('Started At') }}</dt><dd class="col-7" id="details-started"></dd>
                <dt class="col-5">{{ __('Completed At') }}</dt><dd class="col-7" id="details-ended"></dd>
                <dt class="col-5">{{ __('Time Tracked') }}</dt><dd class="col-7" id="details-tracked"></dd>
                <dt class="col-5">{{ __('Project') }}</dt><dd class="col-7" id="details-project"></dd>
                <dt class="col-5">{{ __('Folder') }}</dt><dd class="col-7" id="details-folder"></dd>
                <dt class="col-5">{{ __('Created By') }}</dt><dd class="col-7" id="details-creator"></dd>
            </dl>

            <h6 class="text-uppercase small text-muted">{{ __('Description') }}</h6>
            <p class="details-description mb-4" id="details-description"></p>

            <h6 class="text-uppercase small text-muted">{{ __('Attachments') }}</h6>
            <div class="details-attachments d-flex flex-wrap gap-2 mb-4" id="details-attachments"></div>

            <h6 class="text-uppercase small text-muted">{{ __('Stop History') }} <span class="badge bg-label-secondary rounded-pill" id="details-stops-count">0</span></h6>
            <div class="details-stops mb-4" id="details-stops"></div>

            <h6 class="text-uppercase small text-muted">{{ __('Comments') }} <span class="badge bg-label-secondary rounded-pill" id="details-comments-count">0</span></h6>
            <div class="details-comments mb-3" id="details-comments"></div>

            <form id="commentForm" class="details-comment-form">
                @include('admin.layouts.forms.fields.hidden', ['hidden' => 'task_id'])
                @include('admin.layouts.forms.fields.textarea', [
                    'text_name' => 'body',
                    'text_id' => 'comment-body',
                    'label_show' => false,
                    'rows' => 2,
                    'placeholder' => __('Write a comment...'),
                ])
                <div class="text-end mt-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="ti ti-send me-1"></i>{{ __('Comment') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
