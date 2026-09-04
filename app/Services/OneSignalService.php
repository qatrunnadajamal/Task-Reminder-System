<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Notification;
use Exception;

class OneSignalService
{
    public function sendNotification($title, $message, $userId, $reminder_id = null)
    {
        $apiKey = config('services.onesignal.rest_api_key');
        $appId  = config('services.onesignal.app_id');

        if (!$apiKey || !$appId) {
            throw new Exception('Missing OneSignal configuration.');
        }

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $apiKey,
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
        ])->post('https://api.onesignal.com/notifications', [ 
            'app_id' => $appId,
            // 'included_segments' => ['All'],
            'include_external_user_ids'=>[(string)$userId],

            'headings' => [
                'en' => $title,
            ],
            'contents' => [
                'en' => $message,
            ],
            'url' => 'http://127.0.0.1:8000/task',
            'data' => [
                'reminder_id' => $reminder_id,
            ],
        ]);

        logger()->info('ONESIGNAL RESPONSE', [
            'status' => $response->status(),
            'body'   => $response->json(),
        ]);

        Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'is_read' => false,
            'reminder_id' => $reminder_id,
        ]);

        return $response->successful();
    }
}
