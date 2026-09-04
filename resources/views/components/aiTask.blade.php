<a
    data-bs-toggle="modal"
    data-bs-target="#AiModal"
    class="floating-chat-button d-print-none"
    role="button"
    title="Open AI Task Assistant"
    aria-label="Open AI Task Assistant">

    <i class="bi bi-calendar4-week"
        style="font-size: 27px;"></i>

    <span class="floating-chat-badge bg-light text-dark border">
        Quick Reminder
    </span>
</a>

<div class="modal fade"
    id="AiModal"
    tabindex="-1"
    aria-labelledby="aiModalLabel"
    aria-hidden="true"
    data-bs-backdrop="false">

    <div class="modal-dialog ai-chat-dialog">

        <div class="modal-content ai-chat-modal">

            <!-- Header -->
            <div class="modal-header ai-chat-header">

                <div class="d-flex align-items-center gap-2">

                    <div class="ai-chat-avatar">
                        <i class="bi bi-calendar4-week"></i>
                    </div>

                    <div>
                        <h5 class="modal-title mb-0"
                            id="aiModalLabel">
                            Quick Task Reminder
                        </h5>

                        <small class="text-muted">
                            Describe your task naturally
                        </small>
                    </div>

                </div>

                <button type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>

            </div>


            <!-- Chat Messages -->
            <div class="modal-body ai-chat-body"
                id="aiChatMessages">

                <div class="chat-message assistant-message">

                    <div class="chat-message-avatar">
                        <i class="bi bi-calendar4-week"></i>
                    </div>

                    <div class="chat-message-content">

                        <p class="mb-1">
                            Hi! Describe the reminder you want to create.
                        </p>

                        <small class="text-muted">
                            Example: Submit my internship report tomorrow
                            at 3 PM with high priority.
                        </small>

                    </div>

                </div>

            </div>


            <!-- Chat Input -->
            <div class="modal-footer ai-chat-footer">

                <form id="aiTaskChatForm"
                    class="w-100">

                    <div class="input-group">

                        <textarea
                            id="aiTaskPrompt"
                            name="prompt"
                            class="form-control ai-chat-input"
                            rows="1"
                            maxlength="1000"
                            placeholder="Type your task here..."
                            required></textarea>

                        <button type="submit"
                            id="aiTaskSendButton"
                            class="btn btn-primary">

                            <i class="bi bi-send-fill"></i>

                        </button>

                    </div>

                    <div class="form-text mt-2">
                        AI will generate an editable reminder preview.
                    </div>

                </form>

            </div>

        </div>

    </div>

</div>