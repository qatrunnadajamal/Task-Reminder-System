<?php

namespace App\Mail;

use App\Models\Reminder;
use App\Models\TaskInvitation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $task;
    public $assignee;
    public $invitation;

    public function __construct(Reminder $task, User $assignee, ?TaskInvitation $invitation = null)
    {
        $this->task = $task;
        $this->assignee = $assignee;
        $this->invitation = $invitation;
    }

    public function build()
    {
        return $this
            ->subject($this->task->user->name . ' has set a reminder for you')
            ->view('emails.task_assigned');
    }
}