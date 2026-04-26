<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Cart;
class Product extends Model
{
    protected $fillable = [
        'title', 'slug', 'summary', 'description', 'cat_id', 'child_cat_id', 'price', 'purchase_price', 
        'packaging_cost', 'brand_id', 'model', 'discount', 'status', 'photo', 'size', 'stock', 'is_featured', 
        'condition', 'sku', 'barcode', 'low_stock_threshold', 'supplier_id', 'warehouse_id', 'weight', 'batch_number',
        'wholesale_price', 'retail_price', 'walkin_price', 'salesman_price', 'rack_number', 'shelf_number', 'color', 'type', 'unit'
    ];

    public function suppliers() {
        return $this->belongsToMany('App\Models\Supplier', 'product_supplier');
    }

    public function supplier() {
        return $this->belongsTo('App\Models\Supplier', 'supplier_id');
    }

    public function warehouse() {
        return $this->belongsTo('App\Models\Warehouse', 'warehouse_id');
    }

    public function variants() {
        return $this->hasMany('App\Models\ProductVariant', 'product_id');
    }

    public function cat_info(){
        return $this->hasOne('App\Models\Category','id','cat_id');
    }
    public function sub_cat_info(){
        return $this->hasOne('App\Models\Category','id','child_cat_id');
    }
    public static function getAllProduct(){
        return Product::with(['cat_info','sub_cat_info'])->orderBy('id','desc')->paginate(5000);
    }
    public function rel_prods(){
        return $this->hasMany('App\Models\Product','cat_id','cat_id')->where('status','active')->orderBy('id','DESC')->limit(8);
    }
    public function getReview(){
        return $this->hasMany('App\Models\ProductReview','product_id','id')->with('user_info')->where('status','active')->orderBy('id','DESC');
    }
    public static function getProductBySlug($slug){
        return Product::with(['cat_info','rel_prods','getReview'])->where('slug',$slug)->first();
    }
    public static function countActiveProduct(){
        $data=Product::where('status','active')->count();
        if($data){
            return $data;
        }
        return 0;
    }

    public function carts(){
        return $this->hasMany(Cart::class)->whereNotNull('order_id');
    }

    public function wishlists(){
        return $this->hasMany(Wishlist::class)->whereNotNull('cart_id');
    }

    public function purchaseOrderItems() {
        return $this->hasMany('App\Models\PurchaseOrderItem');
    }

    public function getLatestSupplierAttribute() {
        // Try to get from last purchase order
        $lastItem = $this->purchaseOrderItems()->latest()->first();
        if ($lastItem && $lastItem->purchaseOrder && $lastItem->purchaseOrder->supplier) {
             return $lastItem->purchaseOrder->supplier;
        }
        
        // Fallback to directly assigned supplier
        return $this->supplier;
    }

    public function brand(){
        return $this->hasOne(Brand::class,'id','brand_id');
    }

}
