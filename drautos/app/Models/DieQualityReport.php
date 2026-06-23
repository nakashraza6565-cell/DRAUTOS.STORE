<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class DieQualityReport extends Model
{
    protected $table = 'die_quality_reports';

    protected $fillable = [
        'die_id', 'quality_status', 'report_date', 'notes', 'reported_by'
    ];

    protected $casts = [
        'report_date' => 'datetime'
    ];

    public function die()
    {
        return $this->belongsTo(DieModel::class, 'die_id');
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}
