<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class EmployeePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'payment_date', 'payment_type', 'amount',
        'payment_method', 'reference_number', 'month_year', 'notes', 'paid_by'
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'float',
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function payer()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
}
