<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeAdvanceRepayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'advance_id', 'amount', 'repayment_date', 'repayment_method', 'notes'
    ];

    protected $casts = [
        'amount' => 'float',
        'repayment_date' => 'date',
    ];

    public function advance()
    {
        return $this->belongsTo(EmployeeAdvance::class);
    }
}
