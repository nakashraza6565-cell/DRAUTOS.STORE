<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackagingPurchase extends Model
{
    use HasFactory;

    protected $fillable = ['packaging_item_id', 'supplier_id', 'quantity', 'price', 'total_price', 'invoice_no', 'purchase_date'];

    public function packagingItem()
    {
        return $this->belongsTo(PackagingItem::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
