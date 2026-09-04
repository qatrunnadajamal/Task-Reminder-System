<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center d-print-none">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <div class="ms-auto d-flex align-items-center gap-1 flex-wrap">
                <!-- Export Excel-->
                <div>
                    <a href="/task/export/excel" class="btn btn-primary mt-1">
                        <i class="bi bi-file-excel"></i>
                        Export Excel
                    </a>
                </div>
                <div>
                    <a href="exports/task-pdf" class="btn btn-primary mt-1">
                        <i class="bi bi-file-pdf"></i>
                        PDF
                    </a>
                </div>
                <div>
                    <button onclick="window.print()" class="btn btn-primary mt-1">
                        <i class="bi bi-printer"></i>
                        Print
                    </button>
                </div>
                <!-- <div>
                    <button id="loadTasks" class="btn btn-primary mt-1">
                        TEST API LAYER METHOD
                    </button>
                </div> -->


            </div>
        </div>
    </x-slot>

    <!-- layout default -->
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="row g-3 mb-3">
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body">
                            <div class="card bg-light mb-4 border-0 border-bottom border-2 shadow-sm ">
                                <h5 class="fw-bold px-3 py-2 mb-0">
                                    Task Summary
                                </h5>
                            </div>
                            <div class="row g-3">
                                <!-- Completed -->
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center p-3 rounded-3 bg-success text-white">
                                        <div>
                                            <i class="bi bi-check-circle-fill me-1"></i>
                                            Completed
                                        </div>
                                        <h4 class="mb-0 fw-bold">
                                            {{ $completedCount ?? 0 }}
                                        </h4>
                                    </div>
                                </div>

                                <!-- Pending -->
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center p-3 rounded-3 bg-warning text-dark">
                                        <div>
                                            <i class="bi bi-hourglass-split me-1"></i>
                                            Pending
                                        </div>
                                        <h4 class="mb-0 fw-bold">
                                            {{ $pendingCount ?? 0 }}
                                        </h4>
                                    </div>
                                </div>

                                <!-- Overdue -->
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center p-3 rounded-3 bg-danger text-white">
                                        <div>
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                            Overdue
                                        </div>
                                        <h4 class="mb-0 fw-bold">
                                            {{ $overdueCount ?? 0 }}
                                        </h4>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                </div>

                <div class="col-12 col-lg-6">
                    <div class="card p-3 shadow-sm rounded-4 h-100">
                        <div class="card mb-4 border-0 border-bottom border-1 rounded-1">
                            <h5 class="fw-bold px-3 py-2 mb-0">
                                Reminder Trend for Last 7 Days
                            </h5>
                        </div>
                        <canvas id="taskChart"></canvas>

                        <div id="chart-data" data-chart='@json($data)'></div>
                    </div>
                </div>

            </div>

            <!-- card for list task  -->
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
                                    <th class="text-center"></th>

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
                                            data-id="{{ $task->uuid }}"
                                            style="border: 1px solid #868686;"
                                            @checked($task->status == 'completed')>
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
                                            data-due="{{ \Carbon\Carbon::parse($task->due_task)->format('Y-m-d H:i:s') }}"
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

                                            {{ \Carbon\Carbon::parse($task->due_task)->format('d M Y - g:i A') }}
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

                                            <!-- <a href="/edit/{{ $task->uuid }}"
                                                class="btn btn-sm btn-outline-primary"
                                                id="edit-btn-{{ $task->id }}">
                                                <i class="bi bi-pencil-square"></i>
                                            </a> -->

                                            <!-- DELETE -->
                                            <!-- <a
                                                class="delete-modal btn btn-sm btn-outline-danger"
                                                data-title="{{ $task->title }}"
                                                data-id="{{ $encId }}">
                                                <i class="bi bi-trash"></i>
                                            </a> -->

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

    </div>
    <!-- <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 card border-0 shadow-sm rounded-4 " id="output"> </div> -->
</x-app-layout>