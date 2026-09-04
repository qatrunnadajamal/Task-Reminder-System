<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\Reminder;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class CheckUpcomingEmails extends Command
{
    protected $signature = 'tasks:check-upcoming-emails';
    protected $description = 'Send email reminder 2 hours before task due time';

    public function handle()
    {

        $now = now();
        $tasks = Reminder::where('status', '!=', 'completed')
            ->get()
            ->filter(function ($task) use ($now) {

                $emailTime = \Carbon\Carbon::parse($task->due_task)
                    ->subHours(2);
                return $emailTime->isSameMinute($now);
            });

        foreach ($tasks as $task) {

            // creator email
            $emails = collect([
                $task->user->email
            ]);

            // got assignee emails 
            $assigneeEmails = User::whereIn('id',$task->assignee_ids ?? [])
            ->pluck('email');

            // creator + assignee
            $emails = $emails
                ->merge($assigneeEmails)
                ->unique()
                ->values();

            foreach ($emails as $email) {
                $recipient = User::where('email', $email)->first();

                Mail::to($email)
                    ->send(new \App\Mail\TaskReminderMail($task, $recipient));
            }
        }
    }
}