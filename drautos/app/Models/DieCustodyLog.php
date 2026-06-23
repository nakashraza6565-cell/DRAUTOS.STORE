<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class DieCustodyLog extends Model
{
    protected $table = 'die_custody_logs';

    protected $fillable = [
        'die_id', 'custody_of', 'custody_phone', 'handover_date', 'notes', 'created_by'
    ];

    protected $casts = [
        'handover_date' => 'datetime'
    ];

    public function die()
    {
        return $this->belongsTo(DieModel::class, 'die_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
