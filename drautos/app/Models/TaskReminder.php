<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskReminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id', 'reminder_at', 'sent'
    ];

    protected $casts = [
        'reminder_at' => 'datetime',
        'sent' => 'boolean',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
