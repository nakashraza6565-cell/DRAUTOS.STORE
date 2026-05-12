<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class CashRegister extends Model
{
    protected $fillable = ['user_id', 'financial_account_id', 'opening_amount', 'closing_amount', 'status', 'opened_at', 'closed_at', 'note'];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function user() {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

    public function financialAccount()
    {
        return $this->belongsTo(\App\Models\FinancialAccount::class, 'financial_account_id');
    }
}
