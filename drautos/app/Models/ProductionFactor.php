<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionFactor extends Model
{
    protected $fillable = [
        'name',
        'type',
        'unit',
        'cost_price',
        'stock_quantity',
        'status',
        'description'
    ];
}
