 @props(['task'])
 <div class="modal fade"
    id="view{{ $task->uuid }}"
     tabindex="-1"
     aria-hidden="true">
     <div class="modal-dialog modal-lg modal-dialog-centered">

         <div class="modal-content border-0 shadow-lg rounded-4">

             <div class="modal-header bg-secondary-subtle text-black">

                 <h5 class="modal-title fw-bold">
                     <i class="bi bi-journal-text"></i>
                     {{ $task->title }}
                 </h5>

                 <button type="button"
                     class="btn-close btn-close-black"
                     data-bs-dismiss="modal">
                 </button>

             </div>

             <div class="modal-body p-4">

                 <div class="mb-4">
                     <h6 class="fw-bold text-muted mb-2">Description</h6>
                     <div class="border bg-light rounded-3 ">
                            <div class="ql-editor">
                                {!! $task->description !!}
                            </div>
                     </div>
                 </div>

                 <div class="row g-3">

                     <div class="col-md-6">
                         <div class="border rounded-3 p-3 h-100">
                             <small class="text-muted d-block mb-1">Due</small>
                             <div class="fw-semibold">
                                 <i class="bi bi-calendar-event"></i>
                                 {{ \Carbon\Carbon::parse($task->due_task)->format('d M Y') }}
                                 <span class="ms-2">
                                     <i class="bi bi-clock"></i>
                                     {{ \Carbon\Carbon::parse($task->due_task)->format('g:i A') }}
                                 </span>
                             </div>
                         </div>
                     </div>

                     <div class="col-md-6">
                         <div class="border rounded-3 p-3 h-100">
                             <small class="text-muted d-block mb-1">Priority</small>
                             <span class="badge bg-dark px-3 py-2">
                                 {{ ucfirst($task->priority_level) }}
                             </span>
                         </div>
                     </div>

                     <div class="col-md-6">
                         <div class="border rounded-3 p-3 h-100">
                             <small class="text-muted d-block mb-1">Difficulty</small>
                             <span class="badge bg-dark px-3 py-2">
                                 {{ ucfirst($task->difficulty) }}
                             </span>
                         </div>
                     </div>

                     <div class="col-md-6">
                         <div class="border rounded-3 p-3 h-100">
                             <small class="text-muted d-block mb-1">Status</small>
                             <span id="modal-status-badge-{{ $task->uuid }}"
                                    class="badge
                                    @if($task->status == 'completed')
                                        bg-success
                                    @elseif($task->status == 'overdue')
                                        bg-danger
                                    @else
                                        bg-warning text-dark
                                    @endif
                                    px-3 py-2">
                                    {{ ucfirst($task->status) }}
                                </span>
                                </div>
                     </div>

                 </div>

                 <div class="mt-4">
                     <small class="fw-bold text-muted">Remaining Time</small>

                     <div class="countdown-modal fw-bold fs-8 text-primary"
                                    data-id="{{ $task->uuid }}"
                         data-due="{{ \Carbon\Carbon::parse($task->due_task)->format('Y-m-d H:i:s') }}"
                         data-status="{{ $task->status }}">
                     </div>
                 </div>

             </div>

             <div class="modal-footer">
                 <button type="button"
                     class="btn btn-secondary"
                     data-bs-dismiss="modal">
                     Close
                 </button>
             </div>

         </div>

     </div>
 </div>