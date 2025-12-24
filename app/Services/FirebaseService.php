<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;

class FirebaseService
{
    protected $client;
    protected $projectId;

    public function __construct()
    {
        $this->client = new GoogleClient();
        $this->client->setAuthConfig(storage_path('app/firebase/credential.json'));
        $this->client->addScope('https://www.googleapis.com/auth/firebase.messaging');

        $json = json_decode(file_get_contents(storage_path('app/firebase/credential.json')), true);
		$this->projectId = $json['project_id'];
    }

    protected function getAccessToken()
    {
        if ($this->client->isAccessTokenExpired()) {
            $this->client->fetchAccessTokenWithAssertion();
        }
        return $this->client->getAccessToken()['access_token'];
    }

    public function sendToTopic($topic, $title, $body, $data = [])
    {
        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $message = [
            "message" => [
                "topic" => $topic,
                "notification" => [
                    "title" => $title,
                    "body"  => $body,
                ],
                "data" => $data,
            ]
        ];

        $response = Http::withToken($this->getAccessToken())
            ->post($url, $message);

        return $response->json();
    }

    public function sendToToken($deviceToken, $title, $body, $data = [])
    {
        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $message = [
            "message" => [
                "token" => $deviceToken,
                "notification" => [
                    "title" => $title,
                    "body"  => $body,
                ],
                "data" => $data,
            ]
        ];

        $response = Http::withToken($this->getAccessToken())
            ->post($url, $message);

        return $response->json();
    }
}
