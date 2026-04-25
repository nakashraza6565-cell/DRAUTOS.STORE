<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPackaging extends Model
{
    use HasFactory;

    protected $table = 'order_packaging';

    protected $fillable = ['order_id', 'packaging_item_id', 'quantity'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function packagingItem()
    {
        return $this->belongsTo(PackagingItem::class);
    }
}
