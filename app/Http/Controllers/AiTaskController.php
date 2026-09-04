<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiTaskController extends Controller
{
    public function create(Request $request): JsonResponse
    {

        // echo '<pre>';
        // print_r($request->all());
        // echo '</pre>';
        // die();
        $validated = $request->validate([
            'prompt' => ['required', 'string', 'max:1000'],
        ]);

        $apiKey = config('services.gemini.key');
        $model = config('services.gemini.model');

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Gemini API key is missing.',
            ], 500);
        }

        if (!$model) {
            return response()->json([
                'success' => false,
                'message' => 'Gemini model configuration is missing.',
            ], 500);
        }

        try {
            $response = Http::timeout(60)->withHeaders([
                'x-goog-api-key' => $apiKey,
                'Content-Type' => 'application/json',
            ])
                ->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent",
                    [
                        'contents' => [
                            [
                                'role' => 'user',
                                'parts' => [
                                    [
                                        'text' => $this->buildPrompt(
                                            $validated['prompt']
                                        ),
                                    ],
                                ],
                            ],
                        ],

                        'generationConfig' => [
                            'temperature' => 0.2,
                            'responseMimeType' => 'application/json',

                            'responseSchema' => [
                                'type' => 'OBJECT',

                                'properties' => [
                                    'title' => [
                                        'type' => 'STRING',
                                    ],

                                    'description' => [
                                        'type' => 'STRING',
                                    ],

                                    'due_task' => [
                                        'type' => 'STRING',
                                    ],

                                    'difficulty' => [
                                        'type' => 'STRING',
                                        'enum' => [
                                            'easy',
                                            'medium',
                                            'hard',
                                        ],
                                    ],

                                    'priority_level' => [
                                        'type' => 'STRING',
                                        'enum' => [
                                            'low',
                                            'medium',
                                            'high',
                                        ],
                                    ],
                                ],

                                'required' => [
                                    'title',
                                    'description',
                                    'due_task',
                                    'difficulty',
                                    'priority_level',
                                ],
                            ],
                        ],
                    ]
                );

            if ($response->failed()) {
                Log::error('Gemini request failed', [
                    'status' => $response->status(),
                    'response' => $response->json(),
                    'body' => $response->body(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Gemini could not generate the task.',
                    'api_error' => $response->json('error.message'),
                ], 502);
            }

            $jsonText = $response->json(
                'candidates.0.content.parts.0.text'
            );

            if (!$jsonText) {
                Log::error('Gemini response text is missing', [
                    'response' => $response->json(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Gemini returned an empty response.',
                ], 422);
            }

            $task = json_decode($jsonText, true);

            if (!is_array($task)) {
                Log::error('Gemini returned invalid JSON', [
                    'json_text' => $jsonText,
                    'json_error' => json_last_error_msg(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Gemini returned an invalid response.',
                ], 422);
            }

            return response()->json([
                'success' => true,
                'task' => [
                    'title' => trim($task['title'] ?? ''),
                    'description' => trim($task['description'] ?? ''),
                    'due_task' => trim($task['due_task'] ?? ''),
                    'difficulty' => $task['difficulty'] ?? 'medium',
                    'priority_level' => $task['priority_level'] ?? 'medium',
                ],
            ]);
        } catch (\Throwable $exception) {
            Log::error('Gemini task error', [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
            ], 500);
        }
    }

    public function prepareForm(Request $request): JsonResponse
    {
        // echo '<pre>';
        // print_r($request->all());
        // echo '</pre>';
        // die();
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'required',
                'string',
            ],
            'due_task' => [
                'required',
                'date_format:Y-m-d\TH:i'
            ],
            'difficulty' => [
                'required',
                'in:easy,medium,hard',
            ],
            'priority_level' => [
                'required',
                'in:low,medium,high',
            ],
        ]);

        $task = Reminder::create([
            'user_id' => Auth::id(),
            'assignee_ids' => $assigneeIds ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'due_task' => $validated['due_task'],
            'difficulty' => $validated['difficulty'],
            'priority_level' => $validated['priority_level'],
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'task_id' => $task->id,
            'redirect_url' => url('/task'),
        ]);
    }

    private function buildPrompt(string $userPrompt): string
    {
        $currentDateTime = now('Asia/Kuala_Lumpur')
            ->format('Y-m-d H:i:s');

        return <<<PROMPT
                You generate task form data for a Laravel Task Reminder System.

                Current date and time:
                {$currentDateTime}

                Timezone:
                Asia/Kuala_Lumpur

                User instruction:
                {$userPrompt}
   
                Rules:
                - Create a short and clear task title.
                - Write a simple task description.
                - due_task must use YYYY-MM-DDTHH:MM format.
                - Convert words such as today, tomorrow, and next Friday into an exact date.
                - Subtract 30 minutes from the stated time when the word "BEFORE" is used.
                - If no time is given, use 12:00 pm.
                - If difficulty is not mentioned, set "difficulty" to an empty string ("").
                - If priority is not mentioned, set "priority" to an empty string ("").
                - Do not create a task for casual chat, greetings, opinions, unclear, meaningless or conversational replies. If no clear actionable item is detected, return Null for the task.
                PROMPT;
    }
    }

