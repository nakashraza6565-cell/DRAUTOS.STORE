<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class EmployeeCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'order_id', 'sale_amount', 'commission_rate',
        'commission_amount', 'commission_date', 'status', 'payment_id'
    ];

    protected $casts = [
        'sale_amount' => 'float',
        'commission_rate' => 'float',
        'commission_amount' => 'float',
        'commission_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function payment()
    {
        return $this->belongsTo(EmployeePayment::class, 'payment_id');
    }
}
