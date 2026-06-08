<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterialPurchase extends Model
{
    use HasFactory;

    protected $fillable = ['invoice_number', 'supplier_id', 'purchase_date', 'total_amount', 'notes', 'manufacturing_bill_id'];

    public function manufacturingBill()
    {
        return $this->belongsTo(ManufacturingBill::class, 'manufacturing_bill_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(RawMaterialPurchaseItem::class, 'purchase_id');
    }
}
