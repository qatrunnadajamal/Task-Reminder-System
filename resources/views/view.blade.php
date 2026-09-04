<x-app-layout>
    @include('components.task.detail-modal', ['task' => $task])

    <script>
        document.addEventListener("DOMContentLoaded", function () {

            const modalEl = document.getElementById('view{{ $task->id }}');

            const modal = new bootstrap.Modal(modalEl);

            modal.show();
        });
    </script>

</x-app-layout>