<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class ActivityLog extends Model
{
    protected $fillable = ['user_id', 'log_type', 'action', 'description', 'icon', 'link'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function log($type, $action, $description, $link = null)
    {
        $icons = [
            'sale' => 'fa-shopping-cart text-success',
            'inventory' => 'fa-box text-primary',
            'price' => 'fa-tag text-warning',
            'system' => 'fa-cog text-secondary',
            'customer' => 'fa-user text-info',
            'supplier' => 'fa-truck text-dark',
            'ledger' => 'fa-book text-danger'
        ];

        $log = self::create([
            'user_id' => auth()->id(),
            'log_type' => $type,
            'action' => $action,
            'description' => $description,
            'icon' => $icons[$type] ?? 'fa-info-circle text-muted',
            'link' => $link
        ]);

        // Send OneSignal Push Notification for this activity
        $appId = env('ONESIGNAL_APP_ID');
        $restKey = env('ONESIGNAL_REST_API_KEY');

        if ($appId && $restKey) {
            try {
                \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => 'Basic ' . $restKey,
                    'Content-Type' => 'application/json'
                ])->post('https://onesignal.com/api/v1/notifications', [
                    'app_id' => $appId,
                    'included_segments' => ['All'],
                    'headings' => ['en' => 'DRAUTOS: ' . $action],
                    'contents' => ['en' => $description],
                    'url' => $link ? (filter_var($link, FILTER_VALIDATE_URL) ? $link : url($link)) : null,
                ]);
            } catch (\Exception $e) {
                \Log::error('OneSignal Activity Push Error: ' . $e->getMessage());
            }
        }

        return $log;
    }
}
