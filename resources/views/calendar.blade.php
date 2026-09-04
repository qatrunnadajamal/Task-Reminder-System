<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight m-0">
                {{ __('Calendar') }}
            </h2>
            <b class="text-muted mt-1">
                Drag to reschedule the due date
            </b>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
        <div class="card border-0 shadow-sm rounded-4 mt-2">
            <div class="card-body">
                <div wire:ignore id="calendar"></div>
            </div>
        </div>
    </div>
</x-app-layout>