<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialAccount extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'account_number', 'opening_balance', 'current_balance', 'status'];

    public function transactions()
    {
        return $this->hasMany(AccountTransaction::class);
    }

    public static function updateBalance($accountId)
    {
        $account = self::find($accountId);
        if (!$account) return;

        $in = AccountTransaction::where('financial_account_id', $accountId)->where('type', 'in')->sum('amount');
        $out = AccountTransaction::where('financial_account_id', $accountId)->where('type', 'out')->sum('amount');

        $account->current_balance = $account->opening_balance + $in - $out;
        $account->save();
        
        return $account->current_balance;
    }
}
