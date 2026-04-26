<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DieModel extends Model
{
    protected $table = 'dies';
    protected $fillable = ['name', 'rack_number', 'maker', 'maker_phone', 'die_type', 'phone_number', 'custody_of', 'custody_phone', 'goods_produced', 'quality_status', 'status', 'photo'];
}
