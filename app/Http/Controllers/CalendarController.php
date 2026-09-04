<?php

namespace App\Http\Controllers;
use App\Models\Reminder;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CalendarController extends Controller
{
 
    public function index(): View
    {
        return view('calendar');
    }
    
    public function events(): JsonResponse
    {
        $reminders = Reminder::query()
            ->where(function ($query) {
                $query->where('user_id', Auth::id())
                      ->orWhereJsonContains('assignee_ids', Auth::id());
            })
            ->whereNull('deleted_at')
            ->orderBy('due_task')
            ->get();

        $events = $reminders->map(function ($reminder) {
            return [
                'id' => $reminder->uuid,
                'title' => $reminder->title,
                'start' => $reminder->due_task,
                'allDay' => false,

                // Extra reminder information
                'extendedProps' => [
                    'description' => $reminder->description,
                    'status' => $reminder->status,
                    'difficulty' => $reminder->difficulty,
                    'priority' => $reminder->priority_level,
                ],
            ];
        });

        return response()->json($events);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $reminder = Reminder::query()
            ->where(function ($query) {
                $query->where('user_id', Auth::id())
                      ->orWhereJsonContains('assignee_ids', Auth::id());
            })
            ->whereNull('deleted_at')
            ->where('uuid', $id)
            ->firstOrFail();
        // echo '<pre>';
        // print_r($reminder);
        // echo '</pre>';
        // die();
        $dueTask = $request->input('due_task');

        if (empty($dueTask)) {
            return response()->json([
                'success' => false,
                'message' => 'Due task is required.',
            ], 422);
        }

        try {
            $parsedDueTask = Carbon::parse($dueTask);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'The due date must be a valid date and time.',
            ], 422);
        }

        if ($parsedDueTask->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'The due date must be later than the current date and time.',
            ], 422);
        }

        $reminder->update([
            'due_task' => $parsedDueTask->toDateTimeString(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reminder updated successfully.',
            'due_task' => $reminder->due_task->toDateTimeString(),
        ]);
    }
}