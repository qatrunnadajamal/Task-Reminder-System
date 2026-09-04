<x-app-layout>

<x-slot name="header">
    <div class="d-flex justify-content-between align-items-center">

        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight m-0">
                Add New Task
            </h2>
            <p class="text-sm text-gray-500 m-0">
                Create and organize your reminders
            </p>
        </div>

        <a href="{{ url('/task') }}"
           class="btn btn-outline-secondary btn-sm shadow-sm rounded-3 px-3">
            Back
        </a>

    </div>
</x-slot>

<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">

    <div class="row justify-content-center">
        <div class="col-md-8 mb-4">

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">

                    <form id="taskForm" action="{{ route('task.store') }}" method="POST">
                        @csrf

                        <x-task.form :users="$users" />
                        <div class="d-flex justify-content-end d-flex gap-3">
                            <button type="submit" class="btn btn-success px-4 shadow-sm">
                                Save
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

</div>

</x-app-layout>
