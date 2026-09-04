<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col">
        <!-- title -->
        <h2 class="font-semibold text-xl text-gray-800 leading-tight m-0">
            {{ __('Today Reminders') }}
        </h2>
        <b class="text-muted mt-1">
            {{ \Carbon\Carbon::now()->format('l, d F Y') }}
        </b>
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
                    <tbody>
                        @forelse($reminders as $task)
                             @php
                                $dueDate   = \Carbon\Carbon::parse($task->due_task);
                                $isOverdue = $task->status != 'completed' && $dueDate->isPast();
                                $encId     = $task->uuid;
                            @endphp
                            <tr id="task-row-{{ $task->uuid }}"
                                class="task-row"
                                data-id="{{ $task->uuid }}"
                                style="cursor:pointer;">

                                <!-- checkbox -->
                                 <td>
                                    <input type="checkbox"
                                    class="form-check-input task-check action-area"
                                    style="border: 1px solid #868686;"
                                    data-id="{{ $task->uuid }}">
                                 </td>
                            <!-- TASK -->
                            <td>
                                <div class="fw-semibold">
                                    {{ $task->title }}
                                </div>
                            </td>

                            <!-- countdownt -->
                            <td style="min-width:220px;">

                                <div
                                    class="countdown fw-bold"
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
                                @php
                                $isOverdue =
                                $task->status != 'completed' &&
                               \Carbon\Carbon::parse($task->due_task)->isPast();
                                @endphp

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
                                    <a href="/task/view/{{ $task->uuid }}"
                                        class="task-view btn btn-sm btn-outline-secondary "
                                        data-id="{{ $task->uuid }}">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <!-- EDIT -->
                                    <a href="/edit/{{ $task->uuid }}"
                                        class="btn btn-sm btn-outline-primary"
                                        id="edit-btn-{{ $task->id }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    <!-- DELETE -->
                                    <a
                                        class="delete-modal btn btn-sm btn-outline-danger"
                                        data-title="{{ $task->title }}"
                                        data-id="{{ $encId }}">
                                        <i class="bi bi-trash"></i>
                                    </a>

                                </div>

                            </td>
                            <!-- end of action button  -->
                        </tr>
                        <!-- delete modal -->
                        @include('components.task.viewDelete-modal')
                        <!-- details modal -->
                        @include('components.task.view-modal')

                        @empty
                        <!-- EMPTY STATE -->
                        <tr>

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

            </div>

        </div>

    </div>
    </div>
</x-app-layout>