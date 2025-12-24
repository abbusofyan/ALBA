<?php

// app/Jobs/SendPushNotification.php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\PushNotification;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Log;

class SendPushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected $notification;

    public function __construct(PushNotification $notification)
    {
        $this->notification = $notification;
    }

    public function handle(FirebaseService $fcm)
    {
        try {
            $response = $fcm->sendToTopic(
                "all_user",
                $this->notification->title,
                $this->notification->body,
                ["type" => "broadcast"]
            );

            $this->notification->update([
                'status' => PushNotification::STATUS_SENT,
            ]);
        } catch (\Exception $e) {
            $this->notification->update([
                'status' => PushNotification::STATUS_FAILED,
            ]);
        }
    }
}
