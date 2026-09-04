<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Reminder;
use App\Models\Notification;
use App\Services\OneSignalService;

class CheckDueTasks extends Command
{
    protected $signature = 'tasks:check-due';
    protected $description = 'Send notification when task is due';

    public function handle()
    {
        $now = now();
        $reminderWindow = $now->copy()->addMinutes(5);
        //data for 5 min b4 overdue
        $upcomingTasks = Reminder::where('status', '!=', 'completed')
        ->get()
        ->filter(fn($task) => Carbon::parse($task->due_task)->isSameMinute($reminderWindow));
        //data for overdue
        $overdueTasks = Reminder::where('status', '!=', 'completed')
        ->get()
        ->filter(fn($task) => Carbon::parse($task->due_task)->isSameMinute($now));

        $this->processNotifications($upcomingTasks, 'Task Reminder', fn($t) => "{$t->title} — is due soon!");
        $this->processNotifications($overdueTasks, 'Task Overdue', fn($t) => "{$t->title} — is overdue!");
    }
    private function processNotifications($tasks, string $notifTitle, callable $messageFn)
    {
        foreach ($tasks as $task) {
            try {
                $alreadySent = Notification::where('reminder_id', $task->id)
                    ->where('user_id', $task->user_id)
                    ->where('title', $notifTitle) 
                    ->whereNotNull('sent_at')
                    ->exists();

                if ($alreadySent) {
                    continue;
                }

                $message = $messageFn($task);
                $success = app(OneSignalService::class)->sendNotification(
                    $notifTitle,
                    $message,
                    $task->user_id,
                    $task->id
                );

                if ($success) {
                    Notification::updateOrCreate(
                        [
                            'reminder_id' => $task->id,
                            'user_id' => $task->user_id,
                            'title' => $notifTitle, 
                        ],
                        [
                            'message' => $message,
                            'sent_at' => now(),
                        ]
                    );
                }

            } catch (\Exception $e) {
                logger()->error('Notification Error', [
                    'task_id' => $task->id,
                    'user_id' => $task->user_id,
                    'title' => $notifTitle,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}