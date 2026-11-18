
@if ($foldersByProject->isEmpty())
    <p class="text-muted p-4 mb-0">{{ __('No folders yet') }}</p>
@endif

<ul class="list-unstyled mb-0">
    @foreach ($projects as $project)
        @if ($foldersByProject->has($project->id))
            <li class="tree-group">
                <a class="tree-row tree-group-row" data-bs-toggle="collapse"
                   href="#project-{{ $project->id }}" role="button" aria-expanded="true">
                    <i class="ti ti-chevron-down tree-caret"></i>
                    <i class="ti ti-briefcase tree-icon"></i>
                    <span class="tree-label">{{ $project->nameLang() }}</span>
                </a>
                <ul class="list-unstyled collapse show" id="project-{{ $project->id }}">
                    @include('admin.tasks.components.folder-tree', [
                        'folders' => $foldersByProject->get($project->id),
                    ])
                </ul>
            </li>
        @endif
    @endforeach

    @if ($foldersByProject->has(0))
        <li class="tree-group">
            <a class="tree-row tree-group-row" data-bs-toggle="collapse"
               href="#project-none" role="button" aria-expanded="true">
                <i class="ti ti-chevron-down tree-caret"></i>
                <i class="ti ti-folders tree-icon"></i>
                <span class="tree-label">{{ __('No project') }}</span>
            </a>
            <ul class="list-unstyled collapse show" id="project-none">
                @include('admin.tasks.components.folder-tree', [
                    'folders' => $foldersByProject->get(0),
                ])
            </ul>
        </li>
    @endif
</ul>
