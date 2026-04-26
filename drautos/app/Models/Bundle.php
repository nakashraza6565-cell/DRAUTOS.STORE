<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bundle extends Model
{
    protected $fillable = ['name', 'sku', 'price', 'description', 'status', 'wholesale_price', 'retail_price', 'walkin_price', 'salesman_price'];

    public function items()
    {
        return $this->hasMany(BundleItem::class);
    }
}
