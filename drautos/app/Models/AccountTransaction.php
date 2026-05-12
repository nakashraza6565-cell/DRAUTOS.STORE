<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'financial_account_id', 'amount', 'type', 'reference_type', 
        'reference_id', 'description', 'transaction_date'
    ];

    protected $casts = [
        'transaction_date' => 'date',
    ];

    public function account()
    {
        return $this->belongsTo(FinancialAccount::class, 'financial_account_id');
    }

    public static function record($accountId, $amount, $type, $refType = null, $refId = null, $desc = null, $date = null)
    {
        $transaction = self::create([
            'financial_account_id' => $accountId,
            'amount' => $amount,
            'type' => $type,
            'reference_type' => $refType,
            'reference_id' => $refId,
            'description' => $desc,
            'transaction_date' => $date ?: now(),
        ]);

        FinancialAccount::updateBalance($accountId);
        
        return $transaction;
    }
}
