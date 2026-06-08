<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterialPurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = ['purchase_id', 'factor_id', 'quantity', 'unit_price', 'total'];

    public function purchase()
    {
        return $this->belongsTo(RawMaterialPurchase::class, 'purchase_id');
    }

    public function factor()
    {
        return $this->belongsTo(ProductionFactor::class, 'factor_id');
    }
}
