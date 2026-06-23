<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class DieExpense extends Model
{
    protected $table = 'die_expenses';

    protected $fillable = [
        'die_id', 'supplier_id', 'expense_date', 'amount', 'description', 
        'payment_method', 'financial_account_id', 'created_by'
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'float'
    ];

    public function die()
    {
        return $this->belongsTo(DieModel::class, 'die_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function financialAccount()
    {
        return $this->belongsTo(FinancialAccount::class, 'financial_account_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
