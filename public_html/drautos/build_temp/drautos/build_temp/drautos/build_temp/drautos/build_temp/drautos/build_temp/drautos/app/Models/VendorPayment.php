<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class VendorPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_number', 'supplier_id', 'amount', 'payment_date',
        'payment_method', 'reference_number', 'purchase_order_id',
        'cheque_id', 'notes', 'paid_by'
    ];

    protected $casts = [
        'amount' => 'float',
        'payment_date' => 'date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function cheque()
    {
        return $this->belongsTo(Cheque::class);
    }

    public function payer()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
}
