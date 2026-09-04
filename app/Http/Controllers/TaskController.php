<?php
namespace App\Http\Controllers;
use App\Exports\ReminderExport;
use App\Http\Requests\storeReminderRequest;
use App\Models\Reminder;
use App\Models\TaskInvitation;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\File;
use Throwable;

class TaskController extends Controller
{

    public function indexTaskManager(Request $request)
    {
        $query = Reminder::where('user_id', Auth::id());
        $reminders = $query
            ->orderBy('due_task')
            ->orderByDesc('priority_level')
            ->orderByDesc('difficulty')

            //search ,filter 
            ->when($request->filter === 'today', function ($query) {
                $query->whereDate('due_task', today());
            })
            ->when($request->filter === 'upcoming', function ($query) {
                $query->whereDate('due_task', '>', today());
            })

            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('title', 'LIKE', "%{$request->search}%")
                        ->orWhere('description', 'LIKE', "%{$request->search}%");
                });
            });

        // paginate 
        $pending =  (clone $reminders)
            ->whereIn('status', ['pending', 'overdue'])
            ->paginate(5, ['*'], 'pending_task');

        $completed = (clone $reminders)
            ->where('status', 'completed')
            ->paginate(5, ['*'], 'complete_task');

        return view('task', compact('pending', 'completed'));
    }

    //dashboard
    public function dashboard()
    {

        $completedCount = Reminder::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->count();
        $pendingCount = Reminder::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->count();
        $overdueCount = Reminder::where('user_id', Auth::id())
            ->where('status', 'overdue')
            ->count();
        $reminders = Reminder::where('user_id', Auth::id())
            ->latest()
            ->limit(5)
            ->get();

        //trend purpose
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $count = Reminder::query()
                ->where('user_id', Auth::id())
                ->whereDate('created_at', $date)
                ->count();
            $data[] = [
                'date' => $date->format('D'),
                'count' => $count
            ];
        }

        return view('dashboard', compact(
            'reminders',
            'completedCount',
            'pendingCount',
            'overdueCount',
            'data'
        ));
    }

    public function today()
    {
        $reminders = Reminder::query()
            ->where('user_id', Auth::id())
            ->whereDate('due_task', Carbon::today())
            ->orderBy('due_task', 'asc')
            ->orderByDesc('priority_level')
            ->orderByDesc('difficulty')
            ->get();
        return view('today', compact('reminders'));
    }

    public function add()
    {
        $users = User::query()
            ->where('id', '!=', Auth::id())
            ->orderBy('name')
            ->get();
        return view('add', compact('users'));
    }

    public function store(storeReminderRequest $request)
    {
        $data = $request->validated();
        $assigneeIds = null;
        $assignees = collect();


        $emails = $data['assignee_email_input'] ?? [];

        if (! empty($emails)) {
            $assignees = User::query()
                ->whereIn('email', $emails)
                ->where('id', '!=', Auth::id())
                ->get();

            $assigneeIds = $assignees->pluck('id')->all();
            
        }

        $task = Reminder::create([
            'user_id' => Auth::id(),
            'assignee_ids' => $assigneeIds ?? null,
            'title' => $data['title'],
            'description' => $data['description'],
            'due_task' => $data['due_task'],
            'difficulty' => $data['difficulty'],
            'priority_level' => $data['priority_level'],
            'status' => 'pending',
        ]);

        // echo '<pre>';
        // print_r($task);
        // echo '</pre>';
        // die();
        foreach ($assignees as $assignee) {

            $invitation = TaskInvitation::create([
                'reminder_id' => $task->id,
                'user_id' => $assignee->id,
                'token' => Str::random(64),
                'status' => 'pending',
                'expires_at' => now()->addDays(7),
            ]);

            Mail::to($assignee->email)
                ->send(new \App\Mail\TaskAssignedMail($task, $assignee, $invitation));
        }

        return redirect('/task')->with('success', 'Task created successfully!');
    }


    public function acceptInvitation($token)
    {
        $invitation = TaskInvitation::query()
            ->where('token', $token)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->firstOrFail();

        if ($invitation->user_id !== Auth::id()) {
            abort(403, 'Your have no acces for this invitation');
        }

        //TaskInvitation model-copy
        $originalTask = $invitation->reminder;

        Reminder::create([
            'user_id' => Auth::id(),
            'assignee_ids' => null,
            'title' => $originalTask->title,
            'description' => $originalTask->description,  
            'due_task' => $originalTask->due_task,
            'difficulty' => $originalTask->difficulty,
            'priority_level' => $originalTask->priority_level,
            'status' => 'pending',
        ]);

        $invitation->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        return redirect('/task')->with('success', 'Task added to your task list.');
    }


    //edit    
    public function edit($id)
    {
        $task = Reminder::where('id', '!=', Auth::id())
              ->where('uuid', $id)
              ->firstOrFail();

        $users = User::query()
            ->where('id', '!=', Auth::id())
            ->orderBy('name')
            ->get();

        $assignedUsers = User::query()
            ->whereIn('id', $task->assignee_ids ?? [])
            ->pluck('email')
            ->toArray();

        return view('edit', compact('task', 'users', 'assignedUsers'));
    }

    public function update(storeReminderRequest $request, $id)
    {
        $task = Reminder::where('uuid', $id)->firstOrFail();

        $data = $request->validated();
        $emails = $data['assignee_email_input'] ?? [];

        $assigneeIds = [];

        if (! empty($emails)) {
            $assignees = User::whereIn('email', $emails)->get();
            $assigneeIds = $assignees->pluck('id')->all();
        }

        $task->update([
            'user_id' => Auth::id(),
            'title' => $data['title'],
            'description' => $data['description'],
            'due_task' => $data['due_task'],
            'difficulty' => $data['difficulty'],
            'priority_level' => $data['priority_level'],
            'assignee_ids' => $assigneeIds,
            'status' => $data['status'] ?? 'pending',
        ]);

        return redirect('/task')->with('success', 'Task updated successfully !');
    }

    public function markComplete(Request $request, $id)
    {
        $task = Reminder::where('user_id', Auth::id())
            ->where('uuid', $id)
            ->firstOrFail();
        $checked = $request->input('checked');

        if ($checked === 1 || $checked === '1') {
            $task->status = 'completed';
        } else {

            if (Carbon::parse($task->due_task)->isPast()) {
                $task->status = 'overdue';
            } else {
                $task->status = 'pending';
            }
        }
        $task->save();
        return response()->json([
            'success' => true,
            'status' => $task->status
        ]);
    }

    //markoverdue
    public function markOverdue($id)
    {
        $task = Reminder::where('user_id', Auth::id())
            ->where('uuid', $id)
            ->firstOrFail();
        if (
            $task->status !== 'completed' &&
            Carbon::parse($task->due_task)->isPast()
        ) {
            $task->status = 'overdue';
            $task->save();
        }
        return response()->json([
            'success' => true
        ]);
    }

    public function deleteModal($id)
    {
        $dataReminder = Reminder::where('uuid', $id)->firstOrFail();
        return response()->json([
            'title' => $dataReminder->title
        ]);
    }

    // delete
    public function delete($id)
    {
        $task = Reminder::where('user_id', Auth::id())
            ->where('uuid', $id)
            ->firstOrFail();
            
        $task->delete();
        return redirect()->back()->with('success', 'Task deleted successfully !');
    }

    public function view($id)
    {
        $task = Reminder::where('uuid', $id)->firstOrFail();
        return response()->json([
            'title' => $task->title,
            'description' => $task->description,
            'status' => ucfirst($task->status),
            'difficulty' => ucfirst($task->difficulty),
            'priority_level' => ucfirst($task->priority_level),
            'due_task' => \Carbon\Carbon::parse($task->due_task)->format('d M Y g:i A')
        ]);
    }


    //export Excel
    public function exportExcel(Request $request)
    {
        $reminders = Reminder::where('user_id', Auth::id());
        $data = $reminders->get();
        return Excel::download(
            new ReminderExport($data),
            'reminders.xlsx'
        );
    }

    //pdf
    public function exportPDF(Request $request)
    {
        $reminders = Reminder::where('user_id', Auth::id());
        $data = $reminders->get();

        $pdf = PDF::loadView('exports.task-pdf', compact('data'));
        // return $pdf->download('task-list.pdf');
        return $pdf->stream('task-list.pdf');
    }

    //extract 
    public function extractDescriptionText(Request $request): JsonResponse{
        $request->validate([
            'document' => [
                'required',
                File::types([
                    'pdf',
                    'jpg',
                    'jpeg',
                    'png',
                    'webp',
                ])->max('10mb'),
            ],
        ],
                [
                    'document.required' => 'Please choose a PDF or image.',
                    'document.max' => 'The uploaded file must not exceed 10 MB.',
        ]);     

        try {
            $apiKey = config('services.gemini.key');
            $model = config('services.gemini.model');

            if (blank($apiKey)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gemini API key is not configured.',
                ], 500);
            }

            $file = $request->file('document');
            $filePath = $file->getRealPath();

            if (!$filePath || !is_readable($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The uploaded file could not be read.',
                ], 422);
            }

            $fileContents = file_get_contents($filePath);

            if ($fileContents === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to read the uploaded file.',
                ], 422);
            }

            $mimeType = $file->getMimeType();

            $supportedTypes = [
                'application/pdf',
                'image/jpeg',
                'image/png',
                'image/webp',
            ];

            if (!in_array($mimeType, $supportedTypes, true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unsupported file format.',
                ], 422);
            }

            $base64File = base64_encode($fileContents);
            $endpoint =
                "https://generativelanguage.googleapis.com/v1beta/models/"
                . urlencode($model)
                . ":generateContent";

            $response = Http::timeout(120)
                ->withHeaders([
                    'x-goog-api-key' => $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($endpoint, [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                [
                                    'inline_data' => [
                                        'mime_type' => $mimeType,
                                        'data' => $base64File,
                                    ],
                                ],
                                [
                                    'text' => '
                                        Extract all readable text from this file.
 
                                        Rules:
                                        1. Return only the extracted text.
                                        2. Do not summarize.
                                        3. Do not explain.
                                        4. Preserve headings, paragraphs and lists.
                                        5. Preserve names, dates, emails, numbers and table content.
                                        6. If something is unreadable, write [unclear].
                                        7. Do not add information not shown in the file.
                                    ',
                                ],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature' => 0,
                    ],
                ]);

            if ($response->failed()) {
                Log::error('Gemini extraction failed', [
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => data_get(
                        $response->json(),
                        'error.message',
                        'Gemini could not extract the text.'
                    ),
                ], $response->status());
            }

            $parts = data_get(
                $response->json(),
                'candidates.0.content.parts',
                []
            );

            $extractedText = collect($parts)
                ->pluck('text')
                ->filter()
                ->implode("\n");

            $extractedText = trim($extractedText);

            if ($extractedText === '') {
                return response()->json([
                    'success' => false,
                    'message' => 'No readable text was found.',
                ], 422);
            }

            return response()->json([
                'success' => true,
                'text' => $extractedText,
                'filename' => $file->getClientOriginalName(),
            ]);
        } catch (Throwable $exception) {
            Log::error('Task description extraction error', [
                'message' => $exception->getMessage(),
                'line' => $exception->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
            ], 500);
        }
}
}
