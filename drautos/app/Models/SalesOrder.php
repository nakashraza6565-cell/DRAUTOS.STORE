<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'staff_id',
        'total_amount',
        'status',
        'is_priority',
        'note'
    ];

    public function items()
    {
        return $this->hasMany(SalesOrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

    public function staff()
    {
        return $this->belongsTo(\App\User::class, 'staff_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'sales_order_id');
    }
}
