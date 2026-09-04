<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                Are you sure you want to delete:
                <strong id="tskTitle"></strong>?
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>
                <form id="deleteTaskForm"
                    action="#"
                    method="POST"
                    class="m-0 p-0">

                    @csrf
                    @method('DELETE')

                    <button type="submit"
                        class="btn btn-danger">

                        Yes, Delete

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>