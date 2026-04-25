<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class InventoryIncoming extends Model
{
    use HasFactory;

    protected $table = 'inventory_incoming';

    protected $fillable = [
        'reference_number', 'supplier_id', 'warehouse_id', 'received_date',
        'invoice_number', 'received_by', 'notes', 'status', 'shipping_cost'
    ];

    protected $casts = [
        'received_date' => 'date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function items()
    {
        return $this->hasMany(InventoryIncomingItem::class);
    }

    public function getTotalCostAttribute()
    {
        return $this->items->sum('total_cost') + ($this->shipping_cost ?? 0);
    }
}
