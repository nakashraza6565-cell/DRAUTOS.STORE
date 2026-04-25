<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'order_number', 'sub_total', 'quantity', 'delivery_charge', 'status', 'total_amount', 
        'first_name', 'last_name', 'country', 'post_code', 'address1', 'address2', 'phone', 'email', 
        'payment_method', 'payment_status', 'shipping_id', 'coupon', 'order_type', 'consignment_number', 
        'transport_details', 'expected_delivery_date', 'pending_items_note', 'staff_commission', 'staff_id',
        'courier_company', 'courier_number', 'pinned', 'amount_paid', 'due_date'
    ];

    public function staff() {
        return $this->belongsTo('App\User', 'staff_id');
    }

    public function cart_info(){
        return $this->hasMany('App\Models\Cart','order_id','id');
    }
    public static function getAllOrder($id){
        return Order::with('cart_info')->find($id);
    }
    public static function countActiveOrder(){
        $data=Order::count();
        if($data){
            return $data;
        }
        return 0;
    }
    public function cart(){
        return $this->hasMany(Cart::class);
    }

    public function shipping(){
        return $this->belongsTo(Shipping::class,'shipping_id');
    }
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function packaging()
    {
        return $this->hasMany(OrderPackaging::class);
    }

}
