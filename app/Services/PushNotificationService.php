<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PushNotificationService
{
    public function sendToTokens(array $tokens, string $title, string $body, array $data = [])
    {
        return Http::withToken(env('FCM_SERVER_KEY'))
            ->post('https://fcm.googleapis.com/fcm/send', [
                'registration_ids' => $tokens, // multiple tokens
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                ],
                'data' => $data,
            ])->json();
    }

    public function sendToTopic(string $topic, string $title, string $body, array $data = [])
    {
        return Http::withToken(env('FCM_SERVER_KEY'))
            ->post('https://fcm.googleapis.com/fcm/send', [
                'to' => "/topics/{$topic}",
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                ],
                'data' => $data,
            ])->json();
    }
}
