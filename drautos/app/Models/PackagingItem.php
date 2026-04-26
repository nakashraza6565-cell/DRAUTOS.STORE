<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackagingItem extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'name', 'size', 'supplier_id', 'cost', 'stock'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchases()
    {
        return $this->hasMany(PackagingPurchase::class);
    }

    public function order_usage()
    {
        return $this->hasMany(OrderPackaging::class);
    }
}
