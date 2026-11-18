{{--
    One level of the folder tree on the task board. Includes itself for
    sub-folders (parent_id). Pass $folders (a collection from TaskFolder::tree()).
--}}
@foreach ($folders as $folder)
    <li class="tree-node" data-folder-id="{{ $folder->id }}">
        <div class="tree-row {{ isset($selectedFolder) && $selectedFolder?->id === $folder->id ? 'active' : '' }}">
            @if ($folder->children->isNotEmpty())
                <a class="tree-caret-btn collapsed" data-bs-toggle="collapse"
                   href="#folder-{{ $folder->id }}" role="button" aria-expanded="false">
                    <i class="ti ti-chevron-right tree-caret"></i>
                </a>
            @else
                <span class="tree-caret-spacer"></span>
            @endif
            <a class="tree-link folder-link" href="{{ route('admin.tasks.board', ['folder' => $folder->id]) }}"
               data-folder-id="{{ $folder->id }}">
                <i class="ti ti-folder tree-icon"></i>
                <span class="tree-label">{{ $folder->nameLang() }}</span>
                <span class="badge bg-label-secondary rounded-pill tree-count">{{ $folder->tasks_count }}</span>
            </a>
            @if (!empty($route_create) || !empty($route_edit) || !empty($route_delete))
                <div class="dropdown tree-actions">
                    <button type="button" class="btn btn-icon btn-sm btn-text-secondary rounded-pill" data-bs-toggle="dropdown">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        @if (!empty($route_create))
                            <a class="dropdown-item folder-add-child" href="javascript:void(0);"
                               data-folder-id="{{ $folder->id }}" data-project-id="{{ $folder->project_id }}">
                                <i class="ti ti-folder-plus me-1"></i>{{ __('Add sub-folder') }}
                            </a>
                        @endif
                        @if (!empty($route_edit))
                            <a class="dropdown-item folder-edit" href="javascript:void(0);" data-folder-id="{{ $folder->id }}">
                                <i class="ti ti-edit me-1"></i>{{ __('Edit') }}
                            </a>
                        @endif
                        @if (!empty($route_delete))
                            <a class="dropdown-item text-danger folder-delete" href="javascript:void(0);"
                               data-folder-id="{{ $folder->id }}" data-folder-name="{{ $folder->nameLang() }}">
                                <i class="ti ti-trash me-1"></i>{{ __('Delete') }}
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
        @if ($folder->children->isNotEmpty())
            <ul class="list-unstyled collapse tree-children" id="folder-{{ $folder->id }}">
                @include('admin.tasks.components.folder-tree', ['folders' => $folder->children])
            </ul>
        @endif
    </li>
@endforeach
