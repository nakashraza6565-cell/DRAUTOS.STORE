<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'address', 'company_name', 'rating', 'status', 'current_balance'];

    public function ledger() {
        return $this->hasMany(SupplierLedger::class);
    }

    public function products() {
        return $this->hasMany(Product::class);
    }

    public function purchaseOrders() {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function purchaseReturns() {
        return $this->hasMany(PurchaseReturn::class);
    }

    public function incomingGoods() {
        return $this->hasMany(InventoryIncoming::class);
    }

    public function latestPurchaseOrder() {
        return $this->hasOne(PurchaseOrder::class)->latest('order_date');
    }

    public function latestIncomingGoods() {
        return $this->hasOne(InventoryIncoming::class)->latest('received_date');
    }
}
