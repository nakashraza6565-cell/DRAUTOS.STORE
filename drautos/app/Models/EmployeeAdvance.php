<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class EmployeeAdvance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'amount', 'advance_date', 'status', 'repaid_amount',
        'balance', 'installments', 'installment_amount', 'reason', 'notes', 'approved_by'
    ];

    protected $casts = [
        'amount' => 'float',
        'advance_date' => 'date',
        'repaid_amount' => 'float',
        'balance' => 'float',
        'installment_amount' => 'float',
        'installments' => 'integer',
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function repayments()
    {
        return $this->hasMany(EmployeeAdvanceRepayment::class, 'advance_id');
    }

    public function updateBalance()
    {
        $this->repaid_amount = $this->repayments->sum('amount');
        $this->balance = $this->amount - $this->repaid_amount;
        
        if ($this->balance <= 0) {
            $this->status = 'fully_repaid';
        } elseif ($this->repaid_amount > 0) {
            $this->status = 'partially_repaid';
        }
        
        $this->save();
    }
}
