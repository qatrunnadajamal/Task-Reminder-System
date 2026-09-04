async function loadNotifications() {

    let res = await fetch('/notifications');
    let data = await res.json();
    let list = document.getElementById('notifList');

    list.innerHTML = '';

    if (data.length === 0) {
        list.innerHTML = '<li><span class="dropdown-item text-muted">No notifications</span></li>';
        return;
    }

    data.forEach(n => {

        let item = document.createElement('li');

        item.innerHTML = `
        <div class="dropdown-item p-2 rounded-3 notif-item d-flex justify-content-between align-items-start
            ${n.is_read ? '' : 'bg-light border-start border-3 border-primary'}">

            <!-- content -->
            <div class="notif-content" onclick="markAsRead(${n.id})" style="cursor:pointer;">

                <div class="notif-title-row">
                    <div class="notif-title fw-semibold small text-dark">
                        ${n.title}
                    </div>

                    ${n.is_read ? '' : '<span class="badge bg-primary rounded-pill">New</span>'}
                </div>

                <div class="notif-message text-muted small mt-1">
                    ${n.message}
                </div>

            </div>

            <!-- delete -->
            <button class="btn btn-sm btn-light text-danger rounded-circle p-1 ms-2"
                    onclick="deleteNotification(${n.id}, this.closest('.notif-item'), event)"
                    title="Delete">
                <i class="bi bi-trash"></i>
            </button>
        </div>
        `;

        list.appendChild(item);
    });
}

    async function loadUnreadCount() {

        let res = await fetch('/notifications/unread-count');
        let count = await res.json();

        let badge = document.getElementById('notifCount');
        badge.innerText = count;
        badge.style.display = (count == 0) ? 'none' : 'inline-block';
    }

    async function markAsRead(id) {

        await fetch('/notifications/read/' + id, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        loadNotifications();
        loadUnreadCount();
    }

    /* delete */
    async function deleteNotification(id, element) {
        event.stopPropagation();
        element.classList.add('notif-delete');

        // remove frm list
        setTimeout(async () => {

            // remove instantly from DOM
            element.remove();
            // update backend
            await fetch('/notifications/' + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            // update count only (NOT full reload)
            loadUnreadCount();

        }, 200);
    }
loadNotifications();
loadUnreadCount();