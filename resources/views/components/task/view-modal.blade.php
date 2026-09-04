<div class="modal fade" id="taskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-secondary-subtle text-black">
                <h5 id="taskTitle" class="modal-title fw-bold">
                    <i class="bi bi-journal-text"></i>
                </h5>

                <button type="button"
                    class="btn-close btn-close-black"
                    data-bs-dismiss="modal">
                </button>

            </div>   
            <div class="modal-body p-4">
                <!-- Description -->
                <div class="mb-4">
                    <h6 class="fw-bold text-muted mb-2">Description</h6>
                    <div class="border bg-light rounded-3">
                        <div id="taskDesc" class="ql-editor" style="height: 150px; "></div>
                    </div>
                </div>

                <div class="row g-3">

                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block mb-1">Due</small>
                            <div id="taskDue" class="fw-semibold"></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block mb-1">Priority</small>
                            <span id="taskPrio" class="badge bg-dark px-3 py-2"></span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block mb-1">Difficulty</small>
                            <span id="taskDiff" class="badge bg-dark px-3 py-2"></span>
                        </div>
                    </div>

                    <div class="col-md-6">
                         <div class="border rounded-3 p-3 h-100">
                             <small class="text-muted d-block mb-1">Status</small>
                               <span id="taskStat" ></span>
                                </div>
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