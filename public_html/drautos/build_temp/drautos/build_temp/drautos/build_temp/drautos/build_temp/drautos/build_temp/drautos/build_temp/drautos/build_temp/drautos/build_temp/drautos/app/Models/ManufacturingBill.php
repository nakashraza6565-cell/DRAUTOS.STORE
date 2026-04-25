<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class ManufacturingBill extends Model
{
    use HasFactory;

    protected $fillable = [
        'bom_number', 'product_id', 'batch_quantity', 'material_cost',
        'machining_cost', 'labour_cost', 'packaging_cost', 'overhead_cost',
        'total_cost_per_unit', 'notes', 'status', 'created_by'
    ];

    protected $casts = [
        'batch_quantity' => 'integer',
        'material_cost' => 'float',
        'machining_cost' => 'float',
        'labour_cost' => 'float',
        'packaging_cost' => 'float',
        'overhead_cost' => 'float',
        'total_cost_per_unit' => 'float',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function components()
    {
        return $this->hasMany(ManufacturingBillComponent::class);
    }

    public function productions()
    {
        return $this->hasMany(ManufacturingProduction::class);
    }

    /**
     * Calculate total cost per unit
     */
    public function calculateCost()
    {
        $this->material_cost = $this->components->sum('total_cost');
        $total = $this->material_cost + $this->machining_cost + 
                 $this->labour_cost + $this->packaging_cost + $this->overhead_cost;
        $this->total_cost_per_unit = $this->batch_quantity > 0 ? $total / $this->batch_quantity : 0;
        $this->save();
    }
}
