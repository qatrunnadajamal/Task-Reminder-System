<?php

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $task;
    public $taskUrl;
    public $recipient;

    public function __construct($task = null, $recipient = null)
    {
        $this->task = $task;
        $this->taskUrl = route('dashboard');
        $this->recipient = $recipient;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Task Reminder Mail',
        );
    }

    public function content(): Content
    {
        $title = $this->task?->title ?? 'Task';

        return new Content(
            view: 'emails.task_reminder',
            with: [
                'task' => $this->task,
                'title' => $title,
                'taskUrl' => $this->taskUrl,
                'recipient' => $this->recipient,
                'recipientName' => $this->recipient?->name,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
