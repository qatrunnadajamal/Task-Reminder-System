<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center">
        <!-- title -->
        <h2 class="font-semibold text-xl text-gray-800 leading-tight m-0">
            {{ __('Task Manager') }}
        </h2>
        <div class="ms-auto d-flex align-items-center gap-3 flex-wrap">

            <!-- search  -->
            <form method="GET" action="{{ url('/task') }}" class="d-flex gap-2">
                <input type="text"
                    name="search"
                    id="searchInput"
                    class="form-control form-control-sm shadow-sm rounded-3"
                    placeholder="Search tasks..."
                    value="{{ request('search') }}">

                <button type="submit" class="btn btn-primary">
                    Search
                </button>
            </form>

            <!-- filter  -->
            <div class="dropdown ms-2">

                <button class="btn btn-outline-primary btn-sm dropdown-toggle"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">

                    <i class="bi bi-funnel"></i> Filter
                </button>

                <ul class="dropdown-menu">

                    <li>
                        <a class="dropdown-item {{ !request('filter') ? 'active' : '' }}"
                        href="{{ url('/task') }}">
                            All Tasks
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item {{ request('filter') == 'today' ? 'active' : '' }}"
                        href="{{ url('/task?filter=today') }}">
                            Today
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item {{ request('filter') == 'upcoming' ? 'active' : '' }}"
                        href="{{ url('/task?filter=upcoming') }}">
                            Upcoming
                        </a>
                    </li>

                </ul>
            </div>

            <!-- add  -->
            <div>
                <a href="/add" class="btn btn-primary mt-1">
                    <i class="bi bi-plus-circle me-1"></i>
                    Add Task
                </a>
            </div>
        </div>
    </div>
    </x-slot>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
    <!-- card 1 -->
    <div class="card border-0 shadow-sm rounded-4">

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <!-- TABLE HEADER -->
                    <thead class="table-light">
                        <tr>
                            <th></th>
                            <th>Task</th>
                            <th>Countdown</th>
                            <th>Due</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>

                        </tr>
                    </thead>

                    <!-- TABLE BODY -->
                    <tbody id="pending-tasks">
                        @forelse($pending as $task)
                            @php
                                $dueDate   = \Carbon\Carbon::parse($task->due_task);
                                $isOverdue = $task->status != 'completed' && $dueDate->isPast();
                                $encId     = $task->uuid;
                            @endphp

                            <tr class="task-row" data-id="{{ $task->uuid }}">

                                <!-- checkbox -->
                                <td>
                                    <input type="checkbox"
                                    class="form-check-input task-check action-area"
                                    style="border: 1px solid #868686;"
                                    data-due="{{ $task->due_task }}"
                                    data-id="{{ $task->uuid }}"
                                    >
                                </td>

                                <!-- TASK -->
                                <td>
                                    <div class="fw-semibold">
                                        {{ $task->title }}
                                    </div>
                                </td>

                                <!-- countdown -->
                                <td style="min-width:220px;">

                                    <div
                                        class="countdown fw-bold"
                                        data-id="{{ $task->uuid }}"
                                        data-due="{{ $task->due_task }}"
                                        data-created="{{ $task->created_at }}"
                                        data-status="{{ $task->status }}">
                                    </div>

                                    <div class="progress mt-2" style="height:6px;">
                                        <div class="progress-bar bg-primary-subtle progress-fill"></div>
                                    </div>
                                </td>

                                <!-- DATE -->
                                <td>
                                    <span class="badge bg-light text-dark border px-3 py-2">
                                        <i class="bi bi-calendar-event"></i>
                                        {{ $dueDate->format('d M Y - g:i A') }}
                                    </span>
                                </td>

                                <!-- STATUS -->
                                <td>
                                    <span
                                        id="status-badge-{{ $task->uuid }}"
                                        class="badge
                                        @if($task->status == 'completed')
                                        bg-success
                                        @elseif($isOverdue)
                                        bg-danger
                                        @else
                                        bg-warning text-dark
                                        @endif
                                        px-3 py-2">

                                        @if($task->status == 'completed')
                                        Completed
                                        @elseif($isOverdue)
                                        Overdue
                                        @else
                                        Pending
                                        @endif
                                    </span>
                                </td>

                                <!-- ACTION -->
                                <td class="action-area">

                                    <div class="d-flex justify-content-center align-items-center gap-2 flex-nowrap">
                                        <a href="/task/view/{{ $encId }}"
                                        class="task-view btn btn-sm btn-outline-secondary "
                                        data-id="{{ $encId }}"
                                        >
                                        <i class="bi bi-eye"></i>
                                        </a>

                                        <!-- EDIT -->
                                        <a href="{{ route('edit', $encId) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <!-- DELETE -->
                                        <a
                                            class="delete-modal btn btn-sm btn-outline-danger"
                                            data-title="{{ $task->title }}"
                                            data-id="{{ $encId }}"
                                            >
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>

                                </td>
                                <!-- end of action button  -->
                            </tr>

                        @empty
                        <!-- EMPTY STATE -->
                        <tr class="empty-state-row">

                            <td colspan="6" class="text-center py-5">

                                <i class="bi bi-inbox fs-1 text-muted"></i>

                                <h5 class="mt-3 text-muted">
                                    No tasks available
                                </h5>

                                <p class="text-muted">
                                    Start by creating a new reminder
                                </p>

                                <a href="/add" class="btn btn-primary">

                                    <i class="bi bi-plus-circle"></i>

                                    Add Task

                                </a>

                            </td>

                        </tr>

                        @endforelse

                    </tbody>


                </table>
                {{ $pending->links() }}

            </div>

        </div>

    </div>
    <!-- card 2 -->

    <div class="card border-0 shadow-sm rounded-4 mt-3">

        <div class="card-body">
            <div class="table-responsive">
                <h5 class="mb-3 fw-bold .text-dark">
                Completed Tasks
            </h5>
                <table class="table table-hover align-middle">
                    <!-- TABLE HEADER -->
                    <thead class="table-light">
                        <tr>
                            <th></th>
                            <th>Task</th>
                            <th>Countdown</th>
                            <th>Due</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>

                        </tr>
                    </thead>

                    <!-- TABLE BODY -->
                    <tbody id="completed-tasks">
                        @forelse($completed as $task)
                            @php
                                $dueDate   = \Carbon\Carbon::parse($task->due_task);
                                $isOverdue = $task->status != 'completed' && $dueDate->isPast();
                                $encId     = $task->uuid;
                            @endphp

                            <tr class="task-row" data-id="{{ $task->uuid }}">

                                <!-- checkbox -->
                                <td>
                                    <input type="checkbox"
                                    data-due="{{ $task->due_task }}"
                                    class="form-check-input task-check action-area"
                                    data-id="{{ $task->uuid }}"
                                    @checked($task->status == 'completed')>
                                </td>

                                <!-- TASK -->
                                <td>
                                    <div class="fw-semibold">
                                        {{ $task->title }}
                                    </div>
                                </td>

                                <!-- countdown -->
                                <td style="min-width:220px;">

                                    <div
                                        class="countdown fw-bold"
                                        data-id="{{ $task->uuid }}"
                                        data-due="{{ $task->due_task }}"
                                        data-created="{{ $task->created_at }}"
                                        data-status="{{ $task->status }}">
                                    </div>

                                    <div class="progress mt-2" style="height:6px;">
                                        <div class="progress-bar bg-primary-subtle progress-fill"></div>
                                    </div>
                                </td>

                                <!-- DATE -->
                                <td>
                                    <span class="badge bg-light text-dark border px-3 py-2">
                                        <i class="bi bi-calendar-event"></i>

                                        {{ $dueDate->format('d M Y - g:i A') }}
                                    </span>
                                </td>

                                <!-- STATUS -->
                                <td>
                                    <span
                                        id="status-badge-{{ $task->uuid }}"
                                        class="badge
                                        @if($task->status == 'completed')
                                        bg-success
                                        @elseif($isOverdue)
                                        bg-danger
                                        @else
                                        bg-warning text-dark
                                        @endif
                                        px-3 py-2">

                                        @if($task->status == 'completed')
                                        Completed
                                        @elseif($isOverdue)
                                        Overdue
                                        @else
                                        Pending
                                        @endif
                                    </span>
                                </td>

                                <!-- ACTION -->
                                <td class="action-area">

                                    <div class="d-flex justify-content-center align-items-center gap-2 flex-nowrap">

                                        <a href="/task/view/{{ $encId }}"
                                        class="task-view btn btn-sm btn-outline-secondary "
                                        data-id="{{ $encId }}"
                                        >
                                        <i class="bi bi-eye"></i>
                                        </a>

                                        <!-- EDIT -->
                                        <a href="/edit/{{ $encId }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <!-- DELETE -->
                                        <a
                                            class="delete-modal btn btn-sm btn-outline-danger"
                                            data-title="{{ $task->title }}"
                                            data-id="{{ $encId }}"
                                            >
                                            <i class="bi bi-trash"></i>
                                        </a>

                                    </div>

                                </td>

                            </tr>
                        @empty
                        <!-- EMPTY STATE -->
                        <tr class="empty-state-row">
                            <td colspan="6" class="text-center text-muted py-4">
                                No completed tasks yet
                            </td>
                        </tr>

                        @endforelse

                    </tbody>

                </table>
                 {{ $completed->links() }}
            </div>

        </div>
    </div>

    @include('components.task.viewDelete-modal')
    @include('components.task.view-modal')
</x-app-layout>