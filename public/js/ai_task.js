document.addEventListener('DOMContentLoaded', function () {
    const chatForm = document.getElementById('aiTaskChatForm');
    const promptInput = document.getElementById('aiTaskPrompt');
    const sendButton = document.getElementById('aiTaskSendButton');
    const chatMessages = document.getElementById('aiChatMessages');

    //to prevent error
    if (!chatForm || !promptInput || !sendButton || !chatMessages) {
        return;
    }

    chatForm.addEventListener('submit', async function (event) {
        event.preventDefault();
        const prompt = promptInput.value.trim();

        if (!prompt) {
            return;
        }

        appendUserMessage(prompt);
        promptInput.value = '';
        promptInput.disabled = true;
        sendButton.disabled = true;

        const loadingMessage = appendLoadingMessage();

        try {
            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute('content');

            if (!csrfToken) {
                throw new Error('CSRF token was not found.');
            }

            const response = await fetch('/ai/generate-task', {
                method: 'POST',

                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },

                body: JSON.stringify({
                    prompt: prompt,
                }),
            });

            const data = await response.json();

            loadingMessage.remove();

            if (!response.ok || !data.success) {
                throw new Error(data.api_error || data.message ||
                    'AI could not generate the reminder.'
                );
            }

            appendGeneratedTask(data.task);

        } catch (error) {
            loadingMessage.remove();

            appendAssistantMessage(
                error.message || 'An unexpected error occurred.'
            );

            console.error('AI task error:', error);
        } finally {
            promptInput.disabled = false;
            sendButton.disabled = false;
            promptInput.focus();
        }
    });

    function appendUserMessage(message) {
        const row = document.createElement('div');

        row.className = 'chat-message user-message';

        row.innerHTML = `
            <div class="chat-message-content">
                ${escapeHtml(message)}
            </div>
        `;

        chatMessages.appendChild(row);
        scrollToBottom();
    }

    function appendAssistantMessage(message) {
        const row = document.createElement('div');

        row.className = 'chat-message assistant-message';

        row.innerHTML = `
            <div class="chat-message-avatar">
                <i class="bi bi-calendar4-week"></i>
            </div>

            <div class="chat-message-content">
                ${escapeHtml(message)}
            </div>
        `;

        chatMessages.appendChild(row);
        scrollToBottom();

        return row;
    }


    function appendLoadingMessage() {
        const row = document.createElement('div');

        row.className = 'chat-message assistant-message';

        row.innerHTML = `
            <div class="chat-message-avatar">
                <i class="bi bi-calendar4-week"></i>
            </div>

            <div class="chat-message-content">
                <span class="spinner-border spinner-border-sm me-2"></span>
                Generating your reminder...
            </div>
        `;
        chatMessages.appendChild(row);
        scrollToBottom();

        return row;
    }

    //form 
    function appendGeneratedTask(task) {
        const card = document.createElement('div');

        card.className = 'generated-task-card';

        card.innerHTML = `
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-stars text-primary me-2"></i>
                <strong>Generated Reminder</strong>
            </div>

            <div class="mb-2">
                <label class="form-label fw-semibold">Title</label>
                <input type="text" class="form-control form-control-sm ai-generated-title" value="${escapeHtml(task.title)}">
            </div>

            <div class="mb-2">
                <label class="form-label fw-semibold">
                    Description
                </label>

                <textarea
                    class="form-control form-control-sm ai-generated-description"
                    rows="3">${escapeHtml(task.description)}</textarea>
            </div>

            <div class="mb-2">
                <label class="form-label fw-semibold">
                    Due Date
                </label>

                <input type="datetime-local"
                       class="form-control form-control-sm ai-generated-due"
                       value="${escapeHtml(task.due_task)}">
            </div>

            <div class="row g-2 mb-3">
                <div class="col-6">
                    <label class="form-label fw-semibold">
                        Difficulty
                    </label>

                    <select
                        class="form-select form-select-sm ai-generated-difficulty">
                        <option value="" selected>- Select Difficulty -</option>
                        <option value="easy">Easy</option>
                        <option value="medium">Medium</option>
                        <option value="hard">Hard</option>
                    </select>
                </div>

                <div class="col-6">
                    <label class="form-label fw-semibold">
                        Priority
                    </label>

                    <select class="form-select form-select-sm ai-generated-priority">
                       <option value="" selected>- Select Priority -</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
            </div>

            <button type="button" class="btn btn-primary btn-sm w-100 ai-generate-task-button">
                <i class="bi bi-magic me-1"></i>
                Generate Task
            </button>
        `;

        const difficultySelect = card.querySelector(
            '.ai-generated-difficulty'
        );

        const prioritySelect = card.querySelector(
            '.ai-generated-priority'
        );

        difficultySelect.value = task.difficulty || 'medium';
        prioritySelect.value = task.priority_level || 'medium';

        const generateButton = card.querySelector(
            '.ai-generate-task-button'
        );

        generateButton.addEventListener('click', function () {
            const editedTask = {
                title: card
                    .querySelector('.ai-generated-title')
                    .value
                    .trim(),

                description: card
                    .querySelector('.ai-generated-description')
                    .value
                    .trim(),

                due_task: card
                    .querySelector('.ai-generated-due')
                    .value,

                difficulty: card
                    .querySelector('.ai-generated-difficulty')
                    .value,

                priority_level: card
                    .querySelector('.ai-generated-priority')
                    .value,
            };

            if (
                !editedTask.title || !editedTask.description || !editedTask.due_task) {
                appendAssistantMessage(
                    'Please complete the title, description, and due date.'
                );

                return;
            }

            prepareTaskForm(
                editedTask,
                generateButton
            );
        });

        chatMessages.appendChild(card);
        scrollToBottom();
    }

    async function prepareTaskForm(task, button) {
        const originalButtonContent = button.innerHTML;

        button.disabled = true;
        button.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2"></span>
            Preparing form...
        `;

        try {
            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute('content');

            if (!csrfToken) {
                throw new Error('CSRF token was not found.');
            }

            const response = await fetch('/ai/prepare-task-form', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(task),
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                const message = data.message
                    || (data.errors ? Object.values(data.errors).flat().join(' ') : null)
                    || 'Something went wrong while creating the task.';
                throw new Error(message);
            }

            button.disabled = false;
            button.innerHTML = originalButtonContent;

            await Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Task created successfully!',
                width: 400,
                height: 350,
                timer: 4000,
                showConfirmButton: false,
            });

            window.location.href = data.redirect_url;

        } catch (error) {
            appendAssistantMessage(error.message || 'An unexpected error occurred.');
            console.error('Prepare task form error:', error);

            button.disabled = false;
            button.innerHTML = originalButtonContent;
        }
    }

    function escapeHtml(value) {
        const element = document.createElement('div');

        element.textContent = value || '';

        return element.innerHTML;
    }

    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
});