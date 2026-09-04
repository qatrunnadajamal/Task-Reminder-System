<?php
namespace App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class storeReminderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth::check();
    }

    protected function prepareForValidation(): void
    {
        $emails = $this->input('assignee_email_input', []);

        if (! is_array($emails)) {
            $emails = is_string($emails)
                ? preg_split('/[\s,;]+/', $emails) ?: []
                : [$emails];
        }

        $emails = collect($emails)
            ->map(fn ($email) => strtolower(trim((string) $email)))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $this->merge([
            'title' => trim((string) $this->input('title')),
            'assignee_email_input' => $emails,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_task' =>'required|date|after:now',
            'difficulty' => 'required|in:Easy,Medium,Hard',
            'priority_level' => 'required|in:Low,Medium,High',
            'assignee_email_input' => 'nullable|array',
            'assignee_email_input.*'=>'required|email:rfc|distinct|exists:users,email'
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Please enter a reminder title.',
            'title.max' => 'The reminder title must not exceed 255 characters.',
            'due_task.required' => 'Please provide a valid due date and time.',
            'due_task.after' => 'The due date and time must be later than the current date and time.',
            'difficulty.required' => 'Please select a difficulty level.',
            'difficulty.in' => 'The selected difficulty level is invalid.',
            'priority_level.required' => 'Please select a priority level.',
            'priority_level.in' => 'The selected priority level is invalid.',
            'assignee_email_input.array' => 'The assignee email list format is invalid.',
            'assignee_email_input.*.email' => 'One or more assignee email addresses are invalid.',
            'assignee_email_input.*.exists' => 'One or more assignee email addresses are not registered.',
        ];
    }
}
