<script>

'use strict';
(function () {
  const root = document.querySelector('.task-board');
  if (!root) return;

  const wrapper = root.querySelector('.kanban-wrapper'),
    emptyEl = byId('board-empty'),
    loadingEl = byId('board-loading'),
    addTaskBtn = byId('board-add-task'),
    canEdit = root.dataset.canEdit === '1',
    csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    folderForm = byId('folderForm'),
    taskForm = byId('taskForm'),
    taskDeleteBtn = byId('task-delete'),
    commentForm = byId('commentForm'),
    stopForm = byId('stopForm'),
    confirmEl = byId('confirmModal');

  const folderModal = bootstrap.Modal.getOrCreateInstance(byId('folderModal')),
    taskModal = bootstrap.Modal.getOrCreateInstance(byId('taskModal')),
    stopModal = bootstrap.Modal.getOrCreateInstance(byId('stopModal')),
    confirmModal = bootstrap.Modal.getOrCreateInstance(confirmEl),
    detailsPanel = bootstrap.Offcanvas.getOrCreateInstance(byId('taskDetails'));

  let kanban = null,
    currentFolder = null, // {id, name, project, project_id}
    currentStatuses = [], // the open folder's statuses: [{id, name, color, is_completed, ...}]
    tasksById = {},
    detailsTaskId = null, // task shown in the details panel
    lastDragEnd = 0; // the browser fires a click on the card right after a drop

  $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' } });

  // ---- small helpers ------------------------------------------------------

  function byId(id) {
    return document.getElementById(id);
  }

  function setText(id, value) {
    byId(id).textContent = value ?? '';
  }

  function url(name, id) {
    return root.dataset[name].replace('__ID__', id);
  }

  function escapeHtml(str) {
    return String(str ?? '').replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }

  // resources send "Y-m-d H:i:s"; the board shows it without seconds
  function fmtDate(value) {
    return value ? String(value).slice(0, 16) : '';
  }

  function notify(type, message) {
    if (window.toastr) toastr[type](message);
    else console.log(type, message);
  }

  function ajaxError(xhr) {
    notify('error', xhr.responseJSON?.message || root.dataset.i18nError);
  }

  function goToFolder(folderId) {
    location.href = root.dataset.boardUrl + (folderId ? '?folder=' + folderId : '');
  }

  // ---- confirm modal ------------------------------------------------------
  // Replaces window.confirm(): resolves true when the user clicks the action
  // button, false when the modal is closed any other way.

  function confirmAction(opts) {
    return new Promise(function (resolve) {
      const okBtn = byId('confirm-ok');
      let ok = false;

      setText('confirm-title', opts.title);
      setText('confirm-body', opts.body);
      okBtn.textContent = opts.button || root.dataset.i18nYes;
      okBtn.className = 'btn ' + (opts.buttonClass || 'btn-danger');

      const onOk = function () {
        ok = true;
        confirmModal.hide();
      };
      okBtn.addEventListener('click', onOk);
      confirmEl.addEventListener(
        'hidden.bs.modal',
        function () {
          okBtn.removeEventListener('click', onOk);
          resolve(ok);
        },
        { once: true }
      );
      confirmModal.show();
    });
  }

  function confirmDelete() {
    return confirmAction({ title: root.dataset.i18nDelete, body: root.dataset.i18nConfirmDelete });
  }

  // ---- form helpers -------------------------------------------------------

  function showErrors(form, xhr) {
    const box = form.querySelector('.form-errors');
    let items = [];
    if (xhr.status === 422 && xhr.responseJSON?.errors) {
      items = Object.values(xhr.responseJSON.errors).flat();
    } else {
      items.push(xhr.responseJSON?.message || root.dataset.i18nError);
    }
    box.innerHTML = '<ul class="mb-0">' + items.map(m => '<li>' + escapeHtml(m) + '</li>').join('') + '</ul>';
    box.classList.remove('d-none');
  }

  function resetForm(form) {
    form.reset();
    form.querySelector('.form-errors').classList.add('d-none');
    form.querySelectorAll('input[type=hidden]').forEach(i => (i.value = ''));
    $(form).find('select.select2').trigger('change');
    form.querySelectorAll('.flatpickr-date, .flatpickr-datetime').forEach(el => el._flatpickr && el._flatpickr.clear());
  }

  function setField(form, name, value) {
    form.querySelector('[name="' + name + '"]').value = value ?? '';
  }

  // select2 selects and flatpickr dates need their widget told about a new value
  function setSelect(form, name, value) {
    $(form).find('[name="' + name + '"]').val(value ?? '').trigger('change');
  }

  function setDate(form, name, value) {
    const el = form.querySelector('[name="' + name + '"]');
    if (el._flatpickr) el._flatpickr.setDate(value || null, false);
    else el.value = value || '';
  }

  function fillSelect(select, options, selected) {
    const first = select.querySelector('option[value=""]');
    select.innerHTML = '';
    if (first) select.appendChild(first);
    Object.keys(options).forEach(function (id) {
      select.add(new Option(options[id], id));
    });
    $(select).val(selected ?? '').trigger('change');
  }

  function setBusy(form, busy) {
    form.querySelectorAll('button[type=submit]').forEach(b => (b.disabled = busy));
  }

  // ---- kanban -------------------------------------------------------------

  function renderCard(task) {
    let meta = '';
    if (task.due_date) {
      meta +=
        '<span class="' + (task.overdue ? 'text-danger' : 'text-muted') + ' small me-3">' +
        '<i class="ti ti-calendar-event me-1"></i>' + escapeHtml(fmtDate(task.due_date)) + '</span>';
    }
    if (task.assignee) {
      meta += '<span class="text-muted small me-3"><i class="ti ti-users me-1"></i>' + escapeHtml(task.assignee) + '</span>';
    }
    if (task.attachments_count) {
      meta += '<span class="text-muted small me-2"><i class="ti ti-paperclip me-1"></i>' + task.attachments_count + '</span>';
    }
    if (task.comments_count) {
      meta += '<span class="text-muted small"><i class="ti ti-message-2 me-1"></i>' + task.comments_count + '</span>';
    }
    let state = '';
    if (task.is_completed) state = '<i class="ti ti-circle-check text-success me-1" title="' + escapeHtml(root.dataset.i18nCompleted) + '"></i>';
    else if (task.is_stopped) state = '<i class="ti ti-player-pause text-warning me-1" title="' + escapeHtml(root.dataset.i18nStopped) + '"></i>';
    return (
      '<div class="d-flex justify-content-between align-items-start mb-2">' +
      '<span><span class="badge bg-label-' + escapeHtml(task.priority_color) + ' rounded-pill me-1">' + escapeHtml(task.priority_name) + '</span>' + state + '</span>' +
      (canEdit ? '<i class="ti ti-edit text-muted task-edit-icon"></i>' : '') +
      '</div>' +
      '<span class="kanban-text d-block mb-2">' + escapeHtml(task.title) + '</span>' +
      '<div class="d-flex flex-wrap align-items-center">' + meta + '</div>'
    );
  }

  // the colored dot + name of a folder status
  function statusDot(status) {
    return (
      '<span class="rounded-circle d-inline-block me-2" style="width:10px;height:10px;background:' +
      escapeHtml(status.color) + '"></span>'
    );
  }

  // one column per folder status
  function buildBoards(data) {
    return data.statuses.map(function (status) {
      const tasks = data.tasks[status.id] || [];
      return {
        id: String(status.id),
        title:
          statusDot(status) +
          '<span class="me-2">' + escapeHtml(status.name) + '</span>' +
          '<span class="badge bg-label-secondary rounded-pill board-count">' + tasks.length + '</span>',
        class: 'status-' + status.id,
        item: tasks.map(task => ({ id: String(task.id), title: renderCard(task) }))
      };
    });
  }

  function refreshCounts() {
    wrapper.querySelectorAll('.kanban-board').forEach(function (board) {
      const badge = board.querySelector('.board-count');
      if (badge) badge.textContent = board.querySelectorAll('.kanban-item').length;
    });
  }

  function moveTask(el, target, source) {
    lastDragEnd = Date.now();
    const id = el.getAttribute('data-eid'),
      taskStatusId = target.parentElement.getAttribute('data-id'),
      changedColumn = target !== source,
      order = [].slice.call(target.querySelectorAll('.kanban-item')).map(i => i.getAttribute('data-eid'));

    refreshCounts();
    $.post(root.dataset.moveUrl, { id: id, task_status_id: taskStatusId, order: order })
      .done(function () {
        // dropping onto / off the Complete column completes / reopens the task
        if (changedColumn) {
          notify('success', root.dataset.i18nMoved);
          loadFolder();
        }
      })
      .fail(function (xhr) {
        ajaxError(xhr);
        source.appendChild(el);
        refreshCounts();
      });
  }

  function renderBoard(data) {
    wrapper.innerHTML = '';
    tasksById = {};
    Object.values(data.tasks).forEach(list => list.forEach(t => (tasksById[t.id] = t)));

    kanban = new jKanban({
      element: '.kanban-wrapper',
      gutter: '15px',
      widthBoard: '260px',
      dragItems: canEdit,
      dragBoards: false,
      boards: buildBoards(data),
      click: function (el) {
        if (Date.now() - lastDragEnd < 400) return; // that was the end of a drag, not a click
        const task = tasksById[el.getAttribute('data-eid')];
        if (task) openTaskDetails(task.id);
      },
      dragendEl: function () {
        lastDragEnd = Date.now();
      },
      dropEl: moveTask
    });
  }

  // ---- loading a folder ---------------------------------------------------
  // Clicking a folder in the tree is a normal page load (?folder=ID). This
  // only fetches the tasks of the folder the page opened with.

  function loadFolder() {
    const folderId = root.dataset.selected;
    if (!folderId) return;

    emptyEl.classList.add('d-none');
    loadingEl.classList.remove('d-none');
    wrapper.innerHTML = '';

    $.getJSON(url('tasksUrl', folderId))
      .done(function (data) {
        currentFolder = data.folder;
        currentStatuses = data.statuses;
        setText('board-folder-name', data.folder.name);
        setText('board-project-name', data.folder.project);
        if (addTaskBtn) addTaskBtn.disabled = false;
        renderBoard(data);

        // task count badge next to the folder in the tree
        const count = root.querySelector('.folder-link[data-folder-id="' + folderId + '"] .tree-count');
        if (count) count.textContent = Object.keys(tasksById).length;
      })
      .fail(function (xhr) {
        emptyEl.classList.remove('d-none');
        ajaxError(xhr);
      })
      .always(() => loadingEl.classList.add('d-none'));
  }

  // open the collapsed parents of the active folder so it is visible
  function showActiveFolder() {
    let parent = root.querySelector('.tree-row.active')?.closest('.tree-children');
    while (parent) {
      bootstrap.Collapse.getOrCreateInstance(parent, { toggle: false }).show();
      parent = parent.parentElement.closest('.tree-children');
    }
  }

  // ---- folder modal -------------------------------------------------------
  // After any folder change the page reloads, so the tree is always fresh.

  const parentSelect = folderForm.querySelector('[name=parent_id]'),
    allParents = parentSelect.innerHTML; // editFolder() narrows the list, so keep the full one

  function openFolderModal(folder, defaults) {
    resetForm(folderForm);
    const isEdit = !!folder;
    setText('folderModalTitle', isEdit ? root.dataset.i18nEditFolder : root.dataset.i18nNewFolder);
    if (!isEdit) parentSelect.innerHTML = allParents;

    if (isEdit) {
      setField(folderForm, 'id', folder.id);
      setField(folderForm, 'name', folder.name);
    }
    setSelect(folderForm, 'project_id', isEdit ? folder.project_id : defaults?.project_id);
    setSelect(folderForm, 'parent_id', isEdit ? folder.parent_id : defaults?.parent_id);
    folderModal.show();
  }

  function editFolder(id) {
    $.getJSON(url('folderUrl', id))
      .done(function (data) {
        // this folder and its descendants are not valid parents
        fillSelect(parentSelect, data.parents, data.folder.parent_id);
        openFolderModal(data.folder);
      })
      .fail(ajaxError);
  }

  function deleteFolder(id) {
    confirmDelete().then(function (ok) {
      if (!ok) return;
      $.ajax({ url: url('folderUrl', id), method: 'DELETE' })
        .done(function () {
          const selected = root.dataset.selected;
          goToFolder(selected === String(id) ? null : selected);
        })
        .fail(ajaxError);
    });
  }

  folderForm.addEventListener('submit', function (e) {
    e.preventDefault();
    const id = folderForm.querySelector('[name=id]').value,
      data = $(folderForm).serialize() + (id ? '&_method=PUT' : ''),
      saveUrl = id ? url('folderUrl', id) : root.dataset.folderStoreUrl;

    setBusy(folderForm, true);
    $.post(saveUrl, data)
      .done(res => goToFolder(res.folder.id))
      .fail(function (xhr) {
        showErrors(folderForm, xhr);
        setBusy(folderForm, false);
      });
  });

  // ---- task details panel -------------------------------------------------

  function attachmentHtml(a) {
    return (
      '<a href="' + escapeHtml(a.file_url) + '" target="_blank" class="details-thumb" title="' + escapeHtml(a.file_name) + '">' +
      '<img src="' + escapeHtml(a.file_url) + '" alt="' + escapeHtml(a.file_name) + '"></a>'
    );
  }

  function commentHtml(c) {
    const avatar = c.author_image
      ? '<img src="' + escapeHtml(c.author_image) + '" alt="" class="rounded-circle">'
      : '<span class="avatar-initial rounded-circle bg-label-primary">' + escapeHtml((c.author_name || '?').charAt(0).toUpperCase()) + '</span>';
    return (
      '<div class="d-flex mb-3 details-comment">' +
      '<div class="avatar avatar-sm me-3 flex-shrink-0">' + avatar + '</div>' +
      '<div class="flex-grow-1">' +
      '<div class="d-flex justify-content-between align-items-baseline">' +
      '<span class="fw-medium">' + escapeHtml(c.author_name) + '</span>' +
      '<small class="text-muted">' + escapeHtml(c.created_at_human) + '</small></div>' +
      '<p class="mb-0 details-comment-body' + (c.is_system ? ' text-danger' : '') + '">' + escapeHtml(c.body) + '</p>' +
      '</div></div>'
    );
  }

  function stopHtml(s) {
    const returned = s.is_open
      ? '<span class="text-warning">' + escapeHtml(root.dataset.i18nStillStopped) + '</span>'
      : escapeHtml(fmtDate(s.returned_at)) + ' · ' + s.minutes + ' ' + escapeHtml(root.dataset.i18nMinutes);
    return (
      '<div class="details-stop mb-3">' +
      '<div class="d-flex justify-content-between align-items-baseline">' +
      '<span class="fw-medium">' + escapeHtml(s.actor || '-') + '</span>' +
      '<small class="text-muted">' + escapeHtml(fmtDate(s.stopped_at)) + '</small></div>' +
      '<small class="text-muted d-block mb-1"><i class="ti ti-player-play me-1"></i>' + returned + '</small>' +
      '<p class="mb-0 details-stop-feedback">' + escapeHtml(s.feedback) + '</p>' +
      '</div>'
    );
  }

  function renderList(id, list, itemHtml, emptyText) {
    byId(id).innerHTML = list.length
      ? list.map(itemHtml).join('')
      : '<span class="text-muted small">' + escapeHtml(emptyText) + '</span>';
  }

  // "2h 10m"
  function fmtMinutes(m) {
    m = Math.abs(parseInt(m, 10) || 0);
    const h = Math.floor(m / 60);
    return (h ? h + 'h ' : '') + (m % 60) + 'm';
  }

  // tracked time vs. the estimate, and how the finish compares to the deadline
  function trackedHtml(t) {
    if (!t.is_completed) return '-';
    let html = '<span class="fw-medium">' + fmtMinutes(t.time_tracked_minutes) + '</span>';
    if (t.stopped_minutes) html += ' <small class="text-muted">(' + escapeHtml(root.dataset.i18nStoppedFor) + ' ' + fmtMinutes(t.stopped_minutes) + ')</small>';
    if (t.estimated_minutes) {
      const over = t.time_tracked_minutes - t.estimated_minutes;
      html += '<br><small class="' + (over > 0 ? 'text-danger' : 'text-success') + '">' +
        escapeHtml(over > 0 ? root.dataset.i18nOverEstimate : root.dataset.i18nUnderEstimate) + ' ' + fmtMinutes(over) + '</small>';
    }
    if (t.due_diff_minutes !== null && t.due_diff_minutes !== undefined) {
      const late = t.due_diff_minutes > 0;
      html += '<br><small class="' + (late ? 'text-danger' : 'text-success') + '">' +
        escapeHtml(late ? root.dataset.i18nAfterDue : root.dataset.i18nBeforeDue) + ' ' + fmtMinutes(t.due_diff_minutes) + '</small>';
    }
    return html;
  }

  function stateBadges(t) {
    let html = '';
    if (t.is_completed) html += '<span class="badge bg-label-success">' + escapeHtml(root.dataset.i18nCompleted) + '</span>';
    else if (t.is_stopped) html += '<span class="badge bg-label-warning">' + escapeHtml(root.dataset.i18nStopped) + '</span>';
    return html;
  }

  // Complete + Stop while the task is running, Return while it is stopped,
  // Reopen once it is completed
  function renderActions(t, canEdit) {
    byId('details-complete')?.classList.toggle('d-none', !canEdit || t.is_completed);
    byId('details-stop')?.classList.toggle('d-none', !canEdit || t.is_completed || t.is_stopped);
    byId('details-return')?.classList.toggle('d-none', !canEdit || t.is_completed || !t.is_stopped);
    byId('details-reopen')?.classList.toggle('d-none', !canEdit || !t.is_completed);
  }

  function renderDetails(data) {
    const t = data.task;
    detailsTaskId = t.id;
    setText('details-title', t.title);
    byId('details-badges').innerHTML =
      (t.task_status
        ? '<span class="badge bg-label-secondary d-inline-flex align-items-center">' + statusDot(t.task_status) + escapeHtml(t.task_status.name) + '</span>'
        : '') +
      '<span class="badge bg-label-' + escapeHtml(t.priority_color) + '">' + escapeHtml(t.priority_name) + '</span>' +
      stateBadges(t);
    renderActions(t, data.can_edit);
    setText('details-due', fmtDate(t.due_date) || '-');
    byId('details-due').classList.toggle('text-danger', !!t.overdue);
    setText('details-assignee', t.assignee || '-');
    setText('details-estimated', t.estimated_minutes ?? 0);
    setText('details-started', fmtDate(t.started_at) || '-');
    setText('details-ended', fmtDate(t.ended_at) || '-');
    byId('details-tracked').innerHTML = trackedHtml(t);
    setText('details-project', t.project?.name || '-');
    setText('details-folder', t.folder?.name || '-');
    setText('details-creator', (t.creator || '-') + (t.created_at ? ' · ' + fmtDate(t.created_at) : ''));
    setText('details-description', t.description || '-');
    renderList('details-attachments', t.attachments || [], attachmentHtml, root.dataset.i18nNoAttachments);
    renderList('details-stops', t.time_entries || [], stopHtml, root.dataset.i18nNoStops);
    setText('details-stops-count', (t.time_entries || []).length);
    renderList('details-comments', t.comments || [], commentHtml, root.dataset.i18nNoComments);
    setText('details-comments-count', (t.comments || []).length);
    byId('details-edit')?.classList.toggle('d-none', !data.can_edit);
    commentForm.reset();
    setField(commentForm, 'task_id', t.id);
  }

  function openTaskDetails(id) {
    byId('details-content').classList.add('d-none');
    byId('details-loading').classList.remove('d-none');
    detailsPanel.show();
    $.getJSON(url('taskDetailsUrl', id))
      .done(function (data) {
        renderDetails(data);
        byId('details-content').classList.remove('d-none');
      })
      .fail(function (xhr) {
        detailsPanel.hide();
        ajaxError(xhr);
      })
      .always(() => byId('details-loading').classList.add('d-none'));
  }

  commentForm.addEventListener('submit', function (e) {
    e.preventDefault();
    const id = commentForm.querySelector('[name=task_id]').value,
      body = commentForm.querySelector('[name=body]').value.trim();
    if (!body) return;

    setBusy(commentForm, true);
    $.post(url('taskCommentUrl', id), { body: body })
      .done(function (res) {
        const box = byId('details-comments');
        if (!box.querySelector('.details-comment')) box.innerHTML = '';
        box.insertAdjacentHTML('beforeend', commentHtml(res.comment));
        setText('details-comments-count', box.querySelectorAll('.details-comment').length);
        setField(commentForm, 'body', '');

        // keep the count on the card in sync
        const task = tasksById[id],
          card = wrapper.querySelector('.kanban-item[data-eid="' + id + '"]');
        if (task && card) {
          task.comments_count = (task.comments_count || 0) + 1;
          card.innerHTML = renderCard(task);
        }
      })
      .fail(ajaxError)
      .always(() => setBusy(commentForm, false));
  });

  byId('details-edit')?.addEventListener('click', function () {
    detailsPanel.hide();
    editTask(detailsTaskId);
  });

  // ---- complete / stop / return -------------------------------------------
  // The details panel steps aside for the modal; after the change the board
  // and the panel are both reloaded so badges and buttons stay in sync.

  function runTaskAction(id, urlName, data) {
    return $.post(url(urlName, id), data || {}).done(function (res) {
      notify('success', res.message);
      loadFolder();
      openTaskDetails(id);
    });
  }

  // ask the user in the confirm modal, then run the action; "No" brings the panel back
  function confirmTaskAction(opts, urlName) {
    const id = detailsTaskId;
    detailsPanel.hide();
    confirmAction(opts).then(function (ok) {
      if (!ok) return detailsPanel.show();
      runTaskAction(id, urlName).fail(function (xhr) {
        ajaxError(xhr);
        openTaskDetails(id);
      });
    });
  }

  byId('details-complete')?.addEventListener('click', function () {
    confirmTaskAction(
      {
        title: root.dataset.i18nComplete,
        body: root.dataset.i18nConfirmComplete,
        button: root.dataset.i18nComplete,
        buttonClass: 'btn-success'
      },
      'taskCompleteUrl'
    );
  });

  byId('details-return')?.addEventListener('click', function () {
    confirmTaskAction(
      {
        title: root.dataset.i18nReturn,
        body: root.dataset.i18nConfirmReturn,
        button: root.dataset.i18nReturn,
        buttonClass: 'btn-info'
      },
      'taskReturnUrl'
    );
  });

  byId('details-reopen')?.addEventListener('click', function () {
    confirmTaskAction(
      {
        title: root.dataset.i18nReopen,
        body: root.dataset.i18nConfirmReopen,
        button: root.dataset.i18nReopen,
        buttonClass: 'btn-secondary'
      },
      'taskReopenUrl'
    );
  });

  let stopSubmitted = false; // closing the stop modal without submitting reopens the panel

  byId('details-stop')?.addEventListener('click', function () {
    resetForm(stopForm);
    setField(stopForm, 'task_id', detailsTaskId);
    stopSubmitted = false;
    detailsPanel.hide();
    stopModal.show();
  });

  byId('stopModal').addEventListener('hidden.bs.modal', function () {
    if (!stopSubmitted) detailsPanel.show();
  });

  stopForm.addEventListener('submit', function (e) {
    e.preventDefault();
    const id = stopForm.querySelector('[name=task_id]').value,
      feedback = stopForm.querySelector('[name=feedback]').value.trim();
    if (!feedback) return;

    setBusy(stopForm, true);
    runTaskAction(id, 'taskStopUrl', { feedback: feedback })
      .done(function () {
        stopSubmitted = true;
        stopModal.hide();
      })
      .fail(xhr => showErrors(stopForm, xhr))
      .always(() => setBusy(stopForm, false));
  });

  // ---- task modal ---------------------------------------------------------

  // dropzone shows the stored images as previews; when one is removed it
  // drops its id from the hidden attachments_existing input
  function fillDropzone(attachments) {
    const el = document.querySelector('.dropzone[data-dropzone-multi="attachments"]'),
      dz = el && window.Dropzone ? Dropzone.forElement(el) : null;
    if (!dz) return;
    dz.removeAllFiles(true);
    attachments.forEach(function (att) {
      const mock = { name: att.file_name, size: att.file_size, accepted: true, existingPath: String(att.id) };
      dz.emit('addedfile', mock);
      dz.emit('thumbnail', mock, att.file_url);
      dz.emit('complete', mock);
      dz.files.push(mock);
    });
    setField(taskForm, 'attachments_existing', attachments.map(a => a.id).join(','));
  }

  // task = null for a new task, otherwise the task from the details endpoint
  function openTaskModal(task, attachments) {
    if (!currentFolder) return;
    resetForm(taskForm);
    setText('taskModalTitle', task ? root.dataset.i18nEditTask : root.dataset.i18nNewTask);
    setField(taskForm, 'folder_id', currentFolder.id);
    setField(taskForm, 'project_id', currentFolder.project_id);
    taskDeleteBtn?.classList.toggle('d-none', !task);

    // the status list is the open folder's statuses; a new task starts in the first one
    const statusOptions = {};
    currentStatuses.forEach(s => (statusOptions[s.id] = s.name));
    fillSelect(taskForm.querySelector('[name=task_status_id]'), statusOptions, null);

    if (task) {
      setField(taskForm, 'id', task.id);
      setField(taskForm, 'title', task.title);
      setField(taskForm, 'description', task.description);
      setField(taskForm, 'estimated_minutes', task.estimated_minutes ?? 0);
      setSelect(taskForm, 'task_status_id', task.task_status_id);
      setSelect(taskForm, 'priority', task.priority);
      setDate(taskForm, 'due_date', fmtDate(task.due_date));
      setSelect(taskForm, 'assignee_ids[]', task.assignee_ids || []);
    } else {
      setField(taskForm, 'estimated_minutes', 0);
      setSelect(taskForm, 'task_status_id', (currentStatuses.find(s => !s.is_completed) || currentStatuses[0])?.id);
      setSelect(taskForm, 'priority', 'normal');
      setSelect(taskForm, 'assignee_ids[]', []);
    }
    fillDropzone(attachments || []);
    taskModal.show();
  }

  function editTask(id) {
    $.getJSON(url('taskDetailsUrl', id))
      .done(data => openTaskModal(data.task, data.task.attachments))
      .fail(ajaxError);
  }

  taskForm.addEventListener('submit', function (e) {
    e.preventDefault();
    const id = taskForm.querySelector('[name=id]').value,
      data = new FormData(taskForm), // files are already in attachments[]
      saveUrl = id ? url('taskUrl', id) : root.dataset.taskStoreUrl;
    if (id) data.append('_method', 'PUT');

    setBusy(taskForm, true);
    $.ajax({ url: saveUrl, method: 'POST', data: data, processData: false, contentType: false })
      .done(function (res) {
        taskModal.hide();
        notify('success', res.message);
        loadFolder();
      })
      .fail(xhr => showErrors(taskForm, xhr))
      .always(() => setBusy(taskForm, false));
  });

  // the task modal steps aside for the confirm modal and comes back on "No"
  taskDeleteBtn?.addEventListener('click', function () {
    const id = taskForm.querySelector('[name=id]').value;
    taskModal.hide();
    confirmDelete().then(function (ok) {
      if (!ok) return taskModal.show();
      $.ajax({ url: url('taskUrl', id), method: 'DELETE' })
        .done(function (res) {
          notify('success', res.message);
          loadFolder();
        })
        .fail(ajaxError);
    });
  });

  // ---- clicks -------------------------------------------------------------

  // the pencil on a card edits; anywhere else on the card opens the details
  wrapper.addEventListener(
    'click',
    function (e) {
      const icon = e.target.closest('.task-edit-icon');
      if (!icon) return;
      e.stopPropagation();
      editTask(icon.closest('.kanban-item').getAttribute('data-eid'));
    },
    true
  );

  // folder tree dropdown actions
  root.addEventListener('click', function (e) {
    const btn = e.target.closest('.folder-add-child, .folder-edit, .folder-delete');
    if (!btn) return;
    const id = btn.dataset.folderId;
    if (btn.matches('.folder-add-child')) openFolderModal(null, { parent_id: id, project_id: btn.dataset.projectId });
    if (btn.matches('.folder-edit')) editFolder(id);
    if (btn.matches('.folder-delete')) deleteFolder(id);
  });

  byId('folder-add-root')?.addEventListener('click', () => openFolderModal(null));
  addTaskBtn?.addEventListener('click', () => openTaskModal(null));

  showActiveFolder();
  loadFolder();
})();
</script>
