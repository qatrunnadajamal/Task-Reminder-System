document.addEventListener("DOMContentLoaded", function () {

    const editorElement = document.querySelector('#editor');
    const form = document.querySelector('#taskForm');
    const descriptionInput = document.querySelector('#description');

    if (!editorElement || !form || !descriptionInput) {
        console.log("Missing element detected");
        return;
    }

    const submitButton = form.querySelector('button[type="submit"]');

    form.addEventListener('submit', function (event) {
        if (form.dataset.submitting === 'true') {
            event.preventDefault();
            return false;
        }

        form.dataset.submitting = 'true';

        if (submitButton) {
            submitButton.disabled = true;
            submitButton.textContent = 'Saving...';
        }
    });

    const quill = new Quill(editorElement, {
        theme: 'snow',
        placeholder: 'Write your description...',
        modules: {
            toolbar: [
                [{ 'font': [] }],
                ['bold', 'italic', 'underline','strike'],
                [{ header: [1, 2, false] }],
                [{ 'script': 'sub'}, { 'script': 'super' }],   
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['link','image', 'video', 'formula'],
                ['clean']
            ]
        }
    });

    window.taskQuill = quill;

    form.addEventListener('submit', function () {

        const content = quill.root.innerHTML;

        console.log("Quill content:", content);

        descriptionInput.value = content;
    });
            function initQuill() {
            const editorElement = document.querySelector('#editor');
            const form = document.querySelector('#taskForm');
            const descriptionInput = document.querySelector('#description');

            if (!editorElement || !form || !descriptionInput) return;

            const quill = new Quill(editorElement, { theme: 'snow' });

            form.addEventListener('submit', function () {
                descriptionInput.value = quill.root.innerHTML;
            });
        }

        document.addEventListener("DOMContentLoaded", initQuill);

});