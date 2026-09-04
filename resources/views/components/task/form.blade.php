
@props(['task' => null, 'users' => [], 'assignedUsers' => []])

@php
    $isEdit = $task !== null;
@endphp

<div class="mb-3">
    <label class="form-label fw-semibold">
        <i class="bi bi-pencil-square me-1"></i>
        Task Name
    </label>

    <input
        type="text"
        name="title"
        class="form-control @error('title') is-invalid @enderror"
        placeholder="Enter task name"
        value="{{ old('title', $isEdit ? $task->title : '') }}"
        required>

    @error('title')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<!-- DESCRIPTION -->
<div class="mb-3">
    <label class="form-label fw-semibold">
        <i class="bi bi-card-text me-1"></i>
        Description
    </label>

    <div id="editor" style="height: 200px;">
        {!! old('description', $isEdit ? $task->description : '') !!}
    </div>

    <input
        type="hidden"
        name="description"
        id="description"
        value="{{ old('description', $isEdit ? $task->description : '') }}">

    <div class="mt-3">
        <label class="form-label small text-muted">
            <i class="bi bi-file-earmark-richtext me-1"></i>
            Extract text from PDF or image
        </label>

        <div class="d-flex flex-wrap gap-2">
            <input
                type="file"
                id="extractDescriptionFile"
                class="form-control form-control-sm"
                accept=".pdf,.jpg,.jpeg,.png,.webp,image/*,application/pdf">

            <button type="button" id="extractDescriptionButton" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-magic"></i> Extract text
            </button>
        </div>

        <div id="extractDescriptionStatus" class="form-text mt-2"></div>
    </div>

    @error('description')
        <div class="text-danger small mt-2">{{ $message }}</div>
    @enderror
</div>

<!-- DUE DATETIME -->
<div class="mb-3">
    <label class="form-label fw-semibold">
        <i class="bi bi-calendar-event me-1"></i>
        Due Date & Time
    </label>

    <input
        type="datetime-local"
        name="due_task"
        class="form-control @error('due_task') is-invalid @enderror"
        value="{{ old('due_task', $isEdit ? \Carbon\Carbon::parse($task->due_task)->format('Y-m-d\TH:i') : '') }}"
        required>

    @error('due_task')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<!-- DIFFICULTY -->
<div class="mb-3">
    <label class="form-label fw-semibold">
        <i class="bi bi-bar-chart me-1"></i>
        Difficulty Level
    </label>

    <select name="difficulty" class="form-select border" required>
        <option value="">-- Select Level --</option>

        <option value="Easy" {{ $isEdit && $task->difficulty == 'easy' ? 'selected' : '' }}>Easy</option>
        <option value="Medium" {{ $isEdit && $task->difficulty == 'medium' ? 'selected' : '' }}>Medium</option>
        <option value="Hard" {{ $isEdit && $task->difficulty == 'hard' ? 'selected' : '' }}>Hard</option>
    </select>
</div>

<!-- PRIORITY -->
<div class="mb-3">
    <label class="form-label fw-semibold">
        <i class="bi bi-flag me-1"></i>
        Priority Level
    </label>

    <select name="priority_level" class="form-select border" required>
        <option value="">-- Select Priority --</option>
        <option value="Low" {{ $isEdit && $task->priority_level == 'low' ? 'selected' : '' }}>Low</option>
        <option value="Medium" {{ $isEdit && $task->priority_level == 'medium' ? 'selected' : '' }}>Medium</option>
        <option value="High" {{ $isEdit && $task->priority_level == 'high' ? 'selected' : '' }}>High</option>
    </select>
</div>

<!-- ASSIGN TO -->
<div class="mb-3">
    <label class="form-label fw-semibold">
        <i class="bi bi-person me-1"></i>
        Assign To
        <span class="text-muted">(Optional)</span>
    </label>

    <input
        id="assignee_email"
        name="assignee_email_input"
        class="form-control @if($errors->has('assignee_email_input') || $errors->has('assignee_email_input.*')) is-invalid @endif"
        value="{{ old('assignee_email_input') ? implode(',', (array) old('assignee_email_input')) : ($isEdit ? implode(',', $assignedUsers) : '') }}"
        placeholder="Type an email and press Enter"
        data-users="{{ json_encode($users->pluck('email')->values()->all()) }}">

    @error('assignee_email_input')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror

    @if($errors->has('assignee_email_input.*'))
        @foreach($errors->get('assignee_email_input.*') as $messages)
            @foreach($messages as $message)
                <div class="text-danger small">{{ $message }}</div>
            @endforeach
        @endforeach
    @endif
</div>

<!-- STATUS -->
<input
    type="hidden"
    name="status"
    value="{{ old('status', $isEdit ? $task->status : 'pending') }}">