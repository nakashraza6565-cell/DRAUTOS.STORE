<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = ['title', 'description', 'amount', 'date', 'status', 'user_id', 'financial_account_id'];

    public function user() {
        return $this->belongsTo(\App\User::class);
    }

    public function financialAccount()
    {
        return $this->belongsTo(FinancialAccount::class);
    }
}
