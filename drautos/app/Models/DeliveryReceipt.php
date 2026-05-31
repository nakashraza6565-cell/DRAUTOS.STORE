<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_number', 'date', 'sender_name', 'customer_id', 'receiver_name',
        'courier_company', 'address', 'city', 'no_of_cartons', 'no_of_bags', 'total_parcels'
    ];

    public function customer()
    {
        return $this->belongsTo('App\User', 'customer_id');
    }
}
