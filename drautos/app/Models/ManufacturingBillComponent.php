<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManufacturingBillComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'manufacturing_bill_id', 'component_product_id', 'quantity_required',
        'unit', 'cost_per_unit', 'total_cost'
    ];

    protected $casts = [
        'quantity_required' => 'float',
        'cost_per_unit' => 'float',
        'total_cost' => 'float',
    ];

    public function manufacturingBill()
    {
        return $this->belongsTo(ManufacturingBill::class);
    }

    public function componentProduct()
    {
        return $this->belongsTo(Product::class, 'component_product_id');
    }
}
