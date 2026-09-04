let reminderCalendar = null;

        function initializeReminderCalendar() {
            const calendarElement = document.getElementById('calendar');
            const loadingElement = document.getElementById('calendar-loading');

            if (!calendarElement) {
                return;
            }

            if (typeof FullCalendar === 'undefined') {
                console.error('FullCalendar did not load.');

                if (loadingElement) {
                    loadingElement.innerHTML = `
                        <div class="alert alert-danger">
                            FullCalendar could not be loaded.
                        </div>
                    `;
                }

                return;
            }

            // Prevent duplicate calendar when using Livewire navigation
            if (reminderCalendar) {
                reminderCalendar.destroy();
                reminderCalendar = null;
            }

            reminderCalendar = new FullCalendar.Calendar(calendarElement, {
                initialView: 'dayGridMonth',

                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listWeek'
                },
                eventDisplay: 'block',
                editable: true,
                eventStartEditable: true,
                eventDurationEditable: false,
                buttonText: {
                    today: 'Today',
                    month: 'Month',
                    week: 'Week',
                    list: 'List'
                },

                height: 'auto',
                navLinks: true,
                nowIndicator: true,
                dayMaxEvents: true,

                eventTimeFormat: {
                    hour: 'numeric',
                    minute: '2-digit',
                    meridiem: 'short'
                },

                events: {
                    url: '/calendar/events/',
                    method: 'GET',

                    failure: function (error) {
                        console.error(
                            'Unable to load reminder events:',
                            error
                        );

                        if (loadingElement) {
                            loadingElement.innerHTML = `
                                <div class="alert alert-danger">
                                    Unable to load reminders.
                                    Please check the browser console.
                                </div>
                            `;
                        }
                    }
                },

                loading: function (isLoading) {
                    if (!loadingElement) {
                        return;
                    }

                    loadingElement.style.display =
                        isLoading ? 'block' : 'none';
                },

                eventDrop: function (info) {
                    const event = info.event;

                    if (!event.id) {
                        return;
                    }

                    fetch(`/calendar/events/${event.id}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            due_task: event.start ? event.start.toISOString() : null
                        })
                    })
                    .then((response) => response.json())
                    .then((data) => {
                        if (!data.success) {
                            // console.error('Unable to update reminder date:', data);

                            Swal.fire({
                                icon: 'error',
                                title: 'Unable to reschedule the reminder',
                                text: data.message || 'The due date could not be updated.',
                                confirmButtonText: 'OK'
                            });

                            info.revert();
                        }
                    })
                    .catch((error) => {
                        // console.error('Unable to update reminder date:', error);

                        Swal.fire({
                            icon: 'error',
                            title: 'Unable to reschedule the reminder',
                            text: 'Something went wrong while updating the reminder.',
                            confirmButtonText: 'OK'
                        });

                        info.revert();
                    });
                },

                eventClick: function (info) {
                    const event = info.event;
                    const properties = event.extendedProps;
                    // alert(
                    //     'Task: ' + event.title +
                    //     '\nStatus: ' + properties.status +
                    //     '\nPriority: ' + properties.priority +
                    //     '\nDifficulty: ' + properties.difficulty +
                    //     '\nDue: ' + event.start.toLocaleString()
                    // );
                }
            });

            reminderCalendar.render();
        }

        document.addEventListener(
            'DOMContentLoaded',
            initializeReminderCalendar
        );
        document.addEventListener(
            'livewire:navigated',
            initializeReminderCalendar
        );
  