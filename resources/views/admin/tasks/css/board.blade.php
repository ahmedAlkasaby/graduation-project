{{-- Task board styles: folder tree, kanban, modals, details panel --}}
<style>
/* Task board: folder tree (left) + kanban (right) */

.task-board .task-board-sidebar,
.task-board .task-board-main {
  min-height: calc(100vh - 220px);
}

.folder-tree {
  overflow-y: auto;
  max-height: calc(100vh - 280px);
}

.folder-tree .tree-row {
  display: flex;
  align-items: center;
  padding: 0.45rem 0.75rem;
  color: var(--bs-heading-color);
  text-decoration: none;
  border-radius: var(--bs-border-radius);
  margin: 0.1rem 0.5rem;
}

.folder-tree .tree-group-row {
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.03em;
  color: var(--bs-secondary-color);
}

.folder-tree .tree-row:hover {
  background: rgba(var(--bs-primary-rgb), 0.08);
}

.folder-tree .tree-row.active {
  background: rgba(var(--bs-primary-rgb), 0.16);
  color: var(--bs-primary);
}

.folder-tree .tree-link {
  display: flex;
  align-items: center;
  flex: 1 1 auto;
  min-width: 0;
  color: inherit;
  text-decoration: none;
}

.folder-tree .tree-label {
  flex: 1 1 auto;
  min-width: 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.folder-tree .tree-icon {
  margin-inline-end: 0.5rem;
  font-size: 1.1rem;
  flex-shrink: 0;
}

.folder-tree .tree-caret {
  font-size: 1rem;
  transition: transform 0.2s;
}

.folder-tree .tree-caret-btn,
.folder-tree .tree-caret-spacer {
  width: 1.4rem;
  flex-shrink: 0;
  color: inherit;
  display: inline-flex;
  justify-content: center;
}

/* folder chevron (ti-chevron-right): right when collapsed, down when open */
.folder-tree .tree-caret-btn:not(.collapsed) .tree-caret {
  transform: rotate(90deg);
}
[dir='rtl'] .folder-tree .tree-caret-btn.collapsed .tree-caret {
  transform: rotate(180deg);
}

/* project chevron (ti-chevron-down): down when open, sideways when collapsed */
.folder-tree .tree-group-row.collapsed .tree-caret {
  transform: rotate(-90deg);
}
[dir='rtl'] .folder-tree .tree-group-row.collapsed .tree-caret {
  transform: rotate(90deg);
}

/* each nesting level shifts inward */
.folder-tree .tree-children {
  padding-inline-start: 1.25rem;
}

.folder-tree .tree-count {
  margin-inline-start: 0.5rem;
  font-size: 0.7rem;
}

/* kanban inside the card */
.task-board .kanban-wrapper {
  overflow-x: auto;
  padding-bottom: 0.5rem;
}

.task-board .kanban-item {
  cursor: grab;
  user-select: none;
}
.task-board .kanban-item:active {
  cursor: grabbing;
}

/* the card that follows the mouse while dragging */
.task-board .kanban-item.gu-mirror,
body > .kanban-item.gu-mirror {
  cursor: grabbing;
  opacity: 0.95;
  transform: rotate(2deg);
  box-shadow: 0 0.5rem 1.25rem rgba(0, 0, 0, 0.2);
  pointer-events: none;
}

/* the gap left behind in the column */
.task-board .kanban-item.gu-transit {
  opacity: 0.35;
  border: 1px dashed var(--bs-primary);
}

.task-board .kanban-item .task-edit-link {
  font-size: 1rem;
}

.task-board .kanban-board .kanban-drag {
  min-height: 120px;
}

/* per-folder actions appear on hover */
.folder-tree .tree-actions {
  flex-shrink: 0;
  opacity: 0;
  transition: opacity 0.15s;
}
.folder-tree .tree-row:hover .tree-actions,
.folder-tree .tree-row.active .tree-actions,
.folder-tree .tree-actions.show {
  opacity: 1;
}
.folder-tree .tree-actions .btn {
  padding: 0.15rem 0.35rem;
}
.task-board .kanban-item .task-edit-icon {
  font-size: 1rem;
}

/* task details panel */
.task-details {
  width: 520px;
  max-width: 100vw;
}
.task-details .details-meta dt {
  font-weight: 500;
  color: var(--bs-secondary-color);
}
.task-details .details-meta dd {
  margin-bottom: 0.35rem;
}
.task-details .details-description {
  white-space: pre-wrap;
}
.task-details .details-thumb {
  display: block;
  width: 84px;
  height: 84px;
  border-radius: var(--bs-border-radius);
  overflow: hidden;
  border: 1px solid var(--bs-border-color);
}
.task-details .details-thumb img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.task-details .details-comments {
  max-height: 320px;
  overflow-y: auto;
}
.task-details .details-comment-body {
  white-space: pre-wrap;
}

/* dropzone inside the task modal */
#taskModal .dropzone {
  min-height: 220px;
  border: 2px dashed var(--bs-border-color);
  border-radius: var(--bs-border-radius);
  background: transparent;
  padding: 1rem;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: center;
}
#taskModal .dropzone .dz-preview {
  margin: 0.5rem;
}
#taskModal .dropzone .dz-preview .dz-image {
  width: 120px;
  height: 120px;
  border-radius: var(--bs-border-radius);
}
#taskModal .dropzone .dz-message {
  margin: 2.5rem 0;
  width: 100%;
  text-align: center;
}
</style>
