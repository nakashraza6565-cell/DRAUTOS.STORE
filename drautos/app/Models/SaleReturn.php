<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class SaleReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_number', 'order_id', 'customer_id', 'return_date',
        'total_return_amount', 'refund_method', 'refund_reference',
        'reason', 'status', 'processed_by'
    ];

    protected $casts = [
        'return_date' => 'date',
        'total_return_amount' => 'float',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function items()
    {
        return $this->hasMany(SaleReturnItem::class);
    }
}
