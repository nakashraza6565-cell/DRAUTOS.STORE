<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryIncomingItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_incoming_id', 'product_id', 'quantity', 'unit_cost',
        'total_cost', 'batch_number', 'barcode_printed',
        'packaging_item_id', 'packaging_quantity', 'packaging_cost'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_cost' => 'float',
        'total_cost' => 'float',
        'barcode_printed' => 'boolean',
        'packaging_quantity' => 'float',
        'packaging_cost' => 'float',
    ];

    public function inventoryIncoming()
    {
        return $this->belongsTo(InventoryIncoming::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function packagingItem()
    {
        return $this->belongsTo(PackagingItem::class);
    }
}
