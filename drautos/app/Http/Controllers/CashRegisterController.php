<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashRegister;
use App\Models\Order;
use Carbon\Carbon;
use Auth;

class CashRegisterController extends Controller
{
    public function index()
    {
        // Check for active register
        $activeRegister = CashRegister::with('user')->where('status', 'open')->latest()->first();
        
        $summary = null;
        if($activeRegister) {
            $opened_at = $activeRegister->opened_at;
            $now = Carbon::now();

            // 1. Sales - Initial payments at counter (Cash Only)
            $posSales = \App\Models\CustomerLedger::whereBetween('created_at', [$opened_at, $now])
                        ->where('type', 'credit')
                        ->where('category', 'payment')
                        ->where('description', 'LIKE', '%via CASH%')
                        ->sum('amount');

            // 2. Later Payments - Payments received via Ledger/Collections (Cash Only)
            $laterPayments = \App\Models\CustomerLedger::whereBetween('created_at', [$opened_at, $now])
                        ->where('type', 'credit')
                        ->where('category', 'payment')
                        ->where('description', 'NOT LIKE', '%Order #%') // Avoid double counting if POS logic differs, but here we explicitly use 'manual' or separate payments
                        ->where(function($q) {
                            $q->where('description', 'LIKE', '%cash%')->orWhere('description', 'LIKE', '%Cash%');
                        })
                        ->sum('amount');

            // 3. Expenses
            $expenses = \App\Models\Expense::whereBetween('created_at', [$opened_at, $now])
                        ->sum('amount');

            // 4. Purchase Payments - Initial payments to suppliers
            $purchaseOrderPayments = \App\Models\PurchaseOrder::whereBetween('created_at', [$opened_at, $now])
                                ->sum('paid_amount');

            // 5. Packaging Purchases
            $packagingPayments = \App\Models\PackagingPurchase::whereBetween('created_at', [$opened_at, $now])
                                ->sum('total_price');

            // 6. Manual Supplier Ledger Payments (Cash Only)
            $supplierLedgerPayments = \App\Models\SupplierLedger::whereBetween('created_at', [$opened_at, $now])
                                ->where('type', 'credit') // Payment made to supplier
                                ->where('category', 'payment')
                                ->where(function($q) {
                                    $q->where('description', 'LIKE', '%cash%')->orWhere('description', 'LIKE', '%Cash%');
                                })
                                ->sum('amount');

            $totalOut = $expenses + $purchaseOrderPayments + $packagingPayments + $supplierLedgerPayments;

            $summary = [
                'pos_sales' => $posSales,
                'collections' => $laterPayments,
                'expenses' => $expenses,
                'purchase_payments' => $purchaseOrderPayments,
                'packaging_payments' => $packagingPayments,
                'supplier_ledger_payments' => $supplierLedgerPayments,
                'total_in' => $posSales + $laterPayments,
                'total_out' => $totalOut,
                'expected_cash' => $activeRegister->opening_amount + ($posSales + $laterPayments) - $totalOut
            ];
        }

        // History
        $history = CashRegister::with('user')->orderBy('id', 'DESC')->get();
        $financialAccounts = \App\Models\FinancialAccount::where('status', 'active')->get();

        return view('backend.pos.cash-register', compact('activeRegister', 'history', 'summary', 'financialAccounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'opening_amount' => 'required|numeric|min:0'
        ]);

        // Check if open register exists
        $exists = CashRegister::where('status', 'open')->exists();
        if($exists) {
            return back()->with('error', 'A register is already open!');
        }

        CashRegister::create([
            'user_id' => Auth::id(),
            'opening_amount' => $request->opening_amount,
            'status' => 'open',
            'opened_at' => Carbon::now()
        ]);

        return redirect()->back()->with('success', 'Register Opened Successfully');
    }

    public function close(Request $request, $id)
    {
        $register = CashRegister::findOrFail($id);
        
        $opened_at = $register->opened_at;
        $now = Carbon::now();

        // Recalculate everything for final record
        $posSales = \App\Models\CustomerLedger::whereBetween('created_at', [$opened_at, $now])
                    ->where('type', 'credit')
                    ->where('category', 'payment')
                    ->where('description', 'LIKE', '%via CASH%')
                    ->sum('amount');

        $laterPayments = \App\Models\CustomerLedger::whereBetween('created_at', [$opened_at, $now])
                    ->where('type', 'credit')
                    ->where('category', 'payment')
                    ->where('description', 'NOT LIKE', '%Order #%')
                    ->where(function($q) {
                        $q->where('description', 'LIKE', '%cash%')->orWhere('description', 'LIKE', '%Cash%');
                    })
                    ->sum('amount');

        $expenses = \App\Models\Expense::whereBetween('created_at', [$opened_at, $now])
                    ->sum('amount');

        $purchaseOrderPayments = \App\Models\PurchaseOrder::whereBetween('created_at', [$opened_at, $now])
                            ->sum('paid_amount');

        $packagingPayments = \App\Models\PackagingPurchase::whereBetween('created_at', [$opened_at, $now])
                            ->sum('total_price');

        $supplierLedgerPayments = \App\Models\SupplierLedger::whereBetween('created_at', [$opened_at, $now])
                            ->where('type', 'credit')
                            ->where('category', 'payment')
                            ->where(function($q) {
                                $q->where('description', 'LIKE', '%cash%')->orWhere('description', 'LIKE', '%Cash%');
                            })
                            ->sum('amount');

        $totalOut = $expenses + $purchaseOrderPayments + $packagingPayments + $supplierLedgerPayments;

        $expected_closing = $register->opening_amount + ($posSales + $laterPayments) - $totalOut;

        $register->update([
            'closing_amount' => $expected_closing,
            'status' => 'closed',
            'closed_at' => $now,
            'note' => $request->note
        ]);

        return redirect()->back()->with('success', 'Register Closed Successfully at Rs. ' . number_format($expected_closing, 2));
    }
}
