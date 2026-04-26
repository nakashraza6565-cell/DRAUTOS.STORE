<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class ManufacturingProduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_number', 'manufacturing_bill_id', 'quantity_produced',
        'production_date', 'actual_cost', 'notes', 'produced_by'
    ];

    protected $casts = [
        'quantity_produced' => 'integer',
        'production_date' => 'date',
        'actual_cost' => 'float',
    ];

    public function manufacturingBill()
    {
        return $this->belongsTo(ManufacturingBill::class);
    }

    public function producer()
    {
        return $this->belongsTo(User::class, 'produced_by');
    }
}
