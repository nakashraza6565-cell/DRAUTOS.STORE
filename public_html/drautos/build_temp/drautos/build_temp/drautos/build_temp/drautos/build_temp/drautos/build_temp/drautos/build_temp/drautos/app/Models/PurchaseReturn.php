<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class PurchaseReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_number', 'purchase_order_id', 'supplier_id', 'return_date',
        'total_return_amount', 'refund_method', 'refund_reference',
        'reason', 'status', 'processed_by'
    ];

    protected $casts = [
        'return_date' => 'date',
        'total_return_amount' => 'float',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function items()
    {
        return $this->hasMany(PurchaseReturnItem::class);
    }
}
