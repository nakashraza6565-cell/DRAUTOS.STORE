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

        return self::create([
            'user_id' => auth()->id(),
            'log_type' => $type,
            'action' => $action,
            'description' => $description,
            'icon' => $icons[$type] ?? 'fa-info-circle text-muted',
            'link' => $link
        ]);
    }
}
