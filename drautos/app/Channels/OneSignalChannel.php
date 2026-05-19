<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class OneSignalChannel
{
    public function send($notifiable, Notification $notification)
    {
        $appId = env('ONESIGNAL_APP_ID');
        $restKey = env('ONESIGNAL_REST_API_KEY');

        if (!$appId || !$restKey) return;

        // Get the notification data
        $message = $notification->toArray($notifiable);
        
        try {
            Http::withHeaders([
                'Authorization' => 'Basic ' . $restKey,
                'Content-Type' => 'application/json'
            ])->post('https://onesignal.com/api/v1/notifications', [
                'app_id' => $appId,
                'included_segments' => ['All'],
                'headings' => ['en' => 'DRAUTOS App'],
                'contents' => ['en' => $message['title'] ?? 'New Notification'],
                'url' => $message['actionURL'] ?? null,
            ]);
        } catch (\Exception $e) {
            \Log::error('OneSignal Push Error: ' . $e->getMessage());
        }
    }
}
