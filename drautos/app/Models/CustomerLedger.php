<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class CustomerLedger extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'financial_account_id', 'transaction_date', 'type', 'category', 'description', 'amount', 'balance', 'reference_id', 'payment_method', 'payment_details'
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'float',
        'balance' => 'float',
        'payment_details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function financialAccount()
    {
        return $this->belongsTo(FinancialAccount::class);
    }

    public function saleReturn()
    {
        return $this->belongsTo(SaleReturn::class, 'reference_id');
    }

    public static function record($userId, $date, $type, $category, $description, $amount, $referenceId = null, $paymentMethod = null, $paymentDetails = null, $financialAccountId = null)
    {
        // Ensure columns exist before inserting
        if (!\Illuminate\Support\Facades\Schema::hasColumn('customer_ledgers', 'payment_method')) {
            \Illuminate\Support\Facades\Schema::table('customer_ledgers', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->string('payment_method')->nullable()->after('category');
                $table->text('payment_details')->nullable()->after('payment_method');
            });
        }

        $ledger = self::create([
            'user_id' => $userId,
            'financial_account_id' => $financialAccountId,
            'transaction_date' => $date,
            'type' => $type,
            'category' => $category,
            'description' => $description,
            'amount' => $amount,
            'reference_id' => $referenceId,
            'payment_method' => $paymentMethod,
            'payment_details' => $paymentDetails
        ]);
        self::updateBalance($userId);
        
        // Record Account Transaction if financial account is provided
        if ($financialAccountId && $amount > 0) {
            $accType = ($type == 'credit') ? 'in' : 'out'; // For customer: credit = payment received (IN)
            \App\Models\AccountTransaction::record(
                $financialAccountId, 
                $amount, 
                $accType, 
                'CustomerLedger', 
                $ledger->id, 
                $description, 
                $date
            );
        }
        
        return $ledger;
    }

    public static function updateBalance($userId)
    {
        $transactions = self::where('user_id', $userId)
            ->orderBy('transaction_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $balance = 0;
        foreach ($transactions as $t) {
            if ($t->type == 'debit') {
                $balance += $t->amount;
            } else {
                $balance -= $t->amount;
            }
            $t->update(['balance' => $balance]);
        }
        
        // Update user current balance if needed
        $user = User::find($userId);
        if($user) {
            $user->current_balance = $balance;
            $user->save();
        }
        
        return $balance;
    }
}
