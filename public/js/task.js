$(document).ready(function () {
    //to display empty statement when no task 
    function syncEmptyState(container) {
        const tbody = $(container);

        if (!tbody.length) {
            return;
        }

        tbody.find('.empty-state-row').remove();

        if (tbody.find('tr.task-row').length === 0) {
            const isCompleted = tbody.attr('id') === 'completed-tasks';
            const emptyMarkup = isCompleted
                ? '<tr class="empty-state-row"><td colspan="6" class="text-center text-muted py-4">No completed tasks yet</td></tr>'
                : '<tr class="empty-state-row"><td colspan="6" class="text-center py-5"><i class="bi bi-inbox fs-1 text-muted"></i><h5 class="mt-3 text-muted">No tasks available</h5><p class="text-muted">Start by creating a new reminder</p><a href="/add" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add Task</a></td></tr>';

            tbody.append(emptyMarkup);
        }
    }

    $(document).on('change', '.task-check', function () {
        let checkbox = $(this);
        let taskId = checkbox.data('id');
        let isChecked = checkbox.is(':checked');

        $.ajax({
            url: '/task/complete/' + taskId,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                checked: isChecked ? 1 : 0
            },
            success: function (response) {

                let status = response.status;
                let dueDate = new Date(checkbox.data('due')).getTime();
                let now = new Date().getTime();

                if (!isChecked && now > dueDate) {
                    status = 'overdue';
                }

                let countdownEl = $('#status-badge-' + taskId)
                    .closest('tr')
                    .find('.countdown');

                let row = $('tr[data-id="' + taskId + '"]');
                let sourceTbody = row.closest('tbody');
                let targetTbody = status === 'completed' ? $('#completed-tasks') : $('#pending-tasks');

                let modalBadge = $('#taskModal' + taskId)
                    .find('#status-badge-' + taskId + ', .status-badge, .modal-status-badge');

                // update badge
                if (status === 'completed') {

                    $('#status-badge-' + taskId)
                        .removeClass('bg-warning bg-danger text-dark')
                        .addClass('bg-success px-3 py-2')
                        .html('Completed');

                    // modal
                    modalBadge
                        .removeClass('bg-warning bg-danger text-dark')
                        .addClass('bg-success px-3 py-2')
                        .html('Completed');

                    //add at top list 
                    targetTbody.find('.empty-state-row').remove();
                    row.detach();
                    targetTbody.prepend(row);
                    syncEmptyState(sourceTbody);

                } else if (status === 'overdue') {

                    $('#status-badge-' + taskId)
                        .removeClass('bg-success bg-warning text-dark')
                        .addClass('bg-dange px-3 py-2')
                        .html('Overdue');

                    // modal sync
                    modalBadge
                        .removeClass('bg-success bg-warning text-dark')
                        .addClass('bg-danger px-3 py-2')
                        .html('Overdue');

                    targetTbody.find('.empty-state-row').remove();
                    row.detach();
                    targetTbody.prepend(row);
                    syncEmptyState(sourceTbody);

                } else {

                    $('#status-badge-' + taskId)
                        .removeClass('bg-success bg-danger')
                        .addClass('bg-warning text-dark px-3 py-2')
                        .html('Pending');

                    // modal sync
                    modalBadge
                        .removeClass('bg-success bg-danger')
                        .addClass('bg-warning text-dark px-3 py-2')
                        .html('Pending');

                    targetTbody.find('.empty-state-row').remove();
                    row.detach();
                    targetTbody.prepend(row);
                    syncEmptyState(sourceTbody);
                }

                // update countdown
                countdownEl.attr('data-status', status);
            }
        });
    });

    function updateCountdowns() {

        $('.countdown, .countdown-modal').each(function () {

            let countdownEl = $(this);
            let status = countdownEl.attr('data-status');

            let progressBar = countdownEl
                .closest('td')
                .find('.progress-fill');

            // complete
            if (status === 'completed') {

                countdownEl.html(`<span class="text-success"><i class="bi bi-check-circle-fill"></i>Completed </span>`);
                progressBar
                    .css('width', '100%')
                    .removeClass('bg-danger bg-warning bg-primary-subtle')
                    .addClass('bg-success  px-3 py-2');
                return;
            }

            // not conplete-norml
            let dueDate = new Date(countdownEl.data('due')).getTime();
            let now = new Date().getTime();
            let distance = dueDate - now;

            // overdue
            if (distance <= 0) {

                let taskId = countdownEl.data('id');

                
                if (status === 'pending' && countdownEl.attr('data-overdue-sent') !== '1') {

                    countdownEl.attr('data-overdue-sent', '1');

                    $.ajax({
                        url: '/task/overdue/' + taskId,
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function () {

                            countdownEl.attr('data-status', 'overdue');

                            // update all countdown elements with same task id
                            $('.countdown[data-id="' + taskId + '"], .countdown-modal[data-id="' + taskId + '"]')
                                .attr('data-status', 'overdue');

                            $('#status-badge-' + taskId)
                                .removeClass('bg-success bg-warning text-dark')
                                .addClass('bg-danger px-3 py-2')
                                .html('Overdue');

                            $('#modal-status-badge-' + taskId)
                                .removeClass('bg-success bg-warning text-dark')
                                .addClass('bg-danger px-3 py-2')
                                .html('Overdue');
                        },
                        error: function (xhr) {
                            console.error('Overdue update failed:', xhr.status, xhr.responseText);

                            // do not retry every second
                            countdownEl.attr('data-status', 'overdue');
                        }
                    });
                }

                countdownEl.html(`
                    <span class="text-danger">
                        <i class="bi bi-exclamation-circle-fill"></i> Overdue
                    </span>
                `);

                progressBar
                    .css('width', '100%')
                    .removeClass('bg-primary-subtle bg-warning bg-success')
                    .addClass('bg-danger px-3 py-2');

                return;
            }

            // countdown
            let days = Math.floor(distance / (1000 * 60 * 60 * 24));
            let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            let seconds = Math.floor((distance % (1000 * 60)) / 1000);

            countdownEl.html(`
            <span class="text-primary">${days}d ${hours}h ${minutes}m ${seconds}s</span>
        `);

            let createdDate = new Date(countdownEl.data('created')).getTime();
            let totalDuration = dueDate - createdDate;
            let elapsed = now - createdDate;
            let percent = (elapsed / totalDuration) * 100;
            percent = Math.min(100, Math.max(0, percent));

            progressBar
                .css('width', percent + '%')
                .removeClass('bg-danger bg-warning bg-success')
                .addClass('bg-primary-subtle');
        });
    }

    updateCountdowns();
    setInterval(updateCountdowns, 5000);

    //testing logic -view 
    $(document).on('click', '.task-view', function (e) {
        e.preventDefault();
        let encryptedId = $(this).data('id');
        $.ajax({
            url: '/task/view/' + encryptedId,
            type: 'GET',
            data: {
                id: encryptedId
            },

            success: function (response) {
                //fill
                $('#taskTitle').html('<i class="bi bi-journal-text"></i> ' + response.title);
                $('#taskDesc').html(response.description);
                const dueDate = new Date(response.due_task);
                const formattedDate = dueDate.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
                const formattedTime = dueDate.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', hour12: true });
                $('#taskDue').html(`<i class="bi bi-calendar-event"></i> ${formattedDate}&nbsp;
                <i class="bi bi-clock"></i> ${formattedTime}`);

                $('#taskPrio')
                    .text(response.priority_level)
                    .removeClass()
                    .addClass('badge bg-dark px-3 py-2');

                $('#taskDiff')
                    .text(response.difficulty)
                    .removeClass()
                    .addClass('badge bg-dark px-3 py-2');

                $('#taskStat')
                    .text(response.status)
                    .removeClass()
                    .addClass('badge bg-dark px-3 py-2');

                // open bootstrap5 
                const modal = new bootstrap.Modal(document.getElementById('taskModal'));
                modal.show();
            },
        });

    });
        $(document).on('click', '.delete-modal', function (e) {
        e.preventDefault();

        let encryptId = $(this).data('id');
        let title = $(this).data('title');

        console.log(title);

        Swal.fire({
            title: 'Are you sure?',
            html: 'Delete <b>' + title + '</b> ?',
            width: 400,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {

            if (result.isConfirmed) {
                $('#deleteTaskForm')
                    .attr('action', '/delete/' + encryptId)
                    .submit();
            }

        });
    });


    //test API Layer
    const loadTasksButton = document.getElementById("loadTasks");
    if (loadTasksButton) {
        loadTasksButton.addEventListener("click", function () {

            fetch('/dashboard/fetch')
                .then(response => response.json())
                .then(data => {
                    console.log(data);

                    let html = "";

                    data.forEach(reminder => {
                        html += `<p>${reminder.title}</p>`;
                    });

                    document.getElementById("output").innerHTML = html;
                })
                .catch(error => {
                    console.error("Error:", error);
                });
        });
    }
    //emai form validate 
    const input = document.querySelector('#assignee_email');

    if (input) {
        const users = JSON.parse(input.dataset.users || '[]');

        const tagify = new Tagify(input, {
            whitelist: users,
            enforceWhitelist: false,
            originalInputValueFormat: values => values.map(item => item.value).join(','),
            dropdown: {
                maxItems: 10,
                enabled: 0,
                closeOnSelect: false
            }
        });

        const form = input.closest('form');
        if (form) {
            form.addEventListener('submit', function () {
                form.querySelectorAll('input[name="assignee_email[]"]').forEach(hiddenInput => hiddenInput.remove());

                (tagify.value || []).forEach((item, index) => {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = `assignee_email[${index}]`;
                    hiddenInput.value = item.value;
                    form.appendChild(hiddenInput);
                });
            });
        }
    }
    const csrfToken = $('meta[name="csrf-token"]').attr('content') || '';
    const extractDescriptionUrl = '/task/extract-description';

    function setDescriptionContent(text) {
        const hiddenInput = document.getElementById('description');
        if (hiddenInput) {
            hiddenInput.value = text || '';
        }

        if (window.taskQuill) {
            try {
                window.taskQuill.setText('');
                window.taskQuill.clipboard.dangerouslyPasteHTML(text || '');
            } catch (error) {
                console.error('Unable to update Quill editor:', error);
                const editor = document.querySelector('#editor .ql-editor');
                if (editor) {
                    editor.innerHTML = text || '';
                }
            }
            return;
        } 

        const editor = document.querySelector('#editor .ql-editor');
        if (editor) {
            editor.innerHTML = text || '';
        }
    }

    function showExtractStatus(message, type = 'info') {
        const statusEl = document.getElementById('extractDescriptionStatus');
        if (!statusEl) {
            return;
        }

        statusEl.className = `form-text mt-2 text-${type === 'danger' ? 'danger' : type === 'success' ? 'success' : 'muted'}`;
        statusEl.textContent = message;
    }

    $(document).on('click', '#extractDescriptionButton', function () {
        const input = document.getElementById('extractDescriptionFile');
        const file = input?.files?.[0];

        if (!file) {
            showExtractStatus('Please choose a PDF or image first.', 'danger');
            return;
        }

        const button = $(this);
        const formData = new FormData();
        formData.append('document', file);

        showExtractStatus('Extracting text...', 'info');
        button.prop('disabled', true).html('<i class="bi bi-arrow-repeat"></i> Extracting...');

        fetch(extractDescriptionUrl, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        })
            .then(async response => {
                const data = await response.json().catch(() => ({}));

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Unable to extract text.');
                }

                setDescriptionContent(data.text || '');
                showExtractStatus(`Extracted text from ${data.filename || 'file'}.`, 'success');
            })
            .catch(error => {
                console.error('Description extraction failed:', error);
                showExtractStatus(error.message || 'Description extraction failed.', 'danger');
            })
            .finally(() => {
                button.prop('disabled', false).html('<i class="bi bi-magic"></i> Extract text');
            });
    });

});

