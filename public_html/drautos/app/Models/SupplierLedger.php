<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierLedger extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id', 'transaction_date', 'type', 'category', 'description', 'amount', 'balance', 'reference_id', 'payment_method', 'payment_details'
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'float',
        'balance' => 'float',
        'payment_details' => 'array',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public static function record($supplierId, $date, $type, $category, $description, $amount, $referenceId = null, $paymentMethod = null, $paymentDetails = null)
    {
        // Ensure columns exist before inserting
        if (!\Illuminate\Support\Facades\Schema::hasColumn('supplier_ledgers', 'payment_method')) {
            \Illuminate\Support\Facades\Schema::table('supplier_ledgers', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->string('payment_method')->nullable()->after('category');
                $table->text('payment_details')->nullable()->after('payment_method');
            });
        }

        $ledger = self::create([
            'supplier_id' => $supplierId,
            'transaction_date' => $date,
            'type' => $type,
            'category' => $category,
            'description' => $description,
            'amount' => $amount,
            'reference_id' => $referenceId,
            'payment_method' => $paymentMethod,
            'payment_details' => $paymentDetails
        ]);
        self::updateBalance($supplierId);
        return $ledger;
    }

    public static function updateBalance($supplierId)
    {
        $transactions = self::where('supplier_id', $supplierId)
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
        
        $supplier = Supplier::find($supplierId);
        if($supplier) {
            $supplier->current_balance = $balance;
            $supplier->save();
        }
        
        return $balance;
    }
}
