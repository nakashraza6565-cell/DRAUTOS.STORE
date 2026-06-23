<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DieModel extends Model
{
    protected $table = 'dies';
    
    protected $fillable = [
        'name', 'rack_number', 'maker', 'maker_phone', 'die_type', 'phone_number', 
        'custody_of', 'custody_phone', 'goods_produced', 'quality_status', 'status', 
        'photo', 'product_id', 'maker_id', 'making_cost', 'photos'
    ];

    protected $casts = [
        'photos' => 'array',
        'making_cost' => 'float',
        'goods_produced' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function makerSupplier()
    {
        return $this->belongsTo(Supplier::class, 'maker_id');
    }

    public function custodyLogs()
    {
        return $this->hasMany(DieCustodyLog::class, 'die_id')->orderBy('handover_date', 'desc');
    }

    public function qualityReports()
    {
        return $this->hasMany(DieQualityReport::class, 'die_id')->orderBy('report_date', 'desc');
    }

    public function expenses()
    {
        return $this->hasMany(DieExpense::class, 'die_id')->orderBy('expense_date', 'desc');
    }

    public function productions()
    {
        return $this->hasMany(ManufacturingProduction::class, 'die_id')->orderBy('production_date', 'desc');
    }
}
