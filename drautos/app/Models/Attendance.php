<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Attendance extends Model
{
    protected $fillable = ['user_id', 'date', 'clock_in', 'clock_out', 'lat_in', 'lng_in', 'status', 'total_hours', 'overtime_hours', 'is_manual', 'notes'];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
