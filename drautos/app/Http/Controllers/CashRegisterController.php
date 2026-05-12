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
        $activeRegister = CashRegister::with(['user', 'financialAccount'])->where('status', 'open')->latest()->first();
        
        $summary = null;
        if($activeRegister) {
            $opened_at = $activeRegister->opened_at;
            $now = Carbon::now();
            $accountId = $activeRegister->financial_account_id;

            // 1. Sales - Initial payments at counter (Cash Only)
            $posSalesQuery = \App\Models\CustomerLedger::whereBetween('created_at', [$opened_at, $now])
                        ->where('type', 'credit')
                        ->where('category', 'payment');
            if($accountId) $posSalesQuery->where('financial_account_id', $accountId);
            else $posSalesQuery->where('description', 'LIKE', '%via CASH%');
            $posSales = $posSalesQuery->sum('amount');

            // 2. Later Payments - Payments received via Ledger/Collections (Cash Only)
            $laterPaymentsQuery = \App\Models\CustomerLedger::whereBetween('created_at', [$opened_at, $now])
                        ->where('type', 'credit')
                        ->where('category', 'payment')
                        ->where('description', 'NOT LIKE', '%Order #%');
            if($accountId) $laterPaymentsQuery->where('financial_account_id', $accountId);
            else $laterPaymentsQuery->where(function($q) { $q->where('description', 'LIKE', '%cash%')->orWhere('description', 'LIKE', '%Cash%'); });
            $laterPayments = $laterPaymentsQuery->sum('amount');

            // 3. Expenses
            $expenses = \App\Models\Expense::whereBetween('created_at', [$opened_at, $now])->sum('amount');

            // 4. Purchase Payments - Initial payments to suppliers
            $purchaseOrderPayments = \App\Models\PurchaseOrder::whereBetween('created_at', [$opened_at, $now])->sum('paid_amount');

            // 5. Packaging Purchases
            $packagingPayments = \App\Models\PackagingPurchase::whereBetween('created_at', [$opened_at, $now])->sum('total_price');

            // 6. Manual Supplier Ledger Payments (Cash Only)
            $supplierLedgerPaymentsQuery = \App\Models\SupplierLedger::whereBetween('created_at', [$opened_at, $now])
                                ->where('type', 'credit') // Payment made to supplier
                                ->where('category', 'payment');
            if($accountId) $supplierLedgerPaymentsQuery->where('financial_account_id', $accountId);
            else $supplierLedgerPaymentsQuery->where(function($q) { $q->where('description', 'LIKE', '%cash%')->orWhere('description', 'LIKE', '%Cash%'); });
            $supplierLedgerPayments = $supplierLedgerPaymentsQuery->sum('amount');

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
        $history = CashRegister::with(['user', 'financialAccount'])->orderBy('id', 'DESC')->get();
        $financialAccounts = \App\Models\FinancialAccount::where('status', 'active')->orderBy('type', 'desc')->get();
        $cashAccounts = \App\Models\FinancialAccount::where('type', 'cash')->where('status', 'active')->get();

        return view('backend.pos.cash-register', compact('activeRegister', 'history', 'summary', 'financialAccounts', 'cashAccounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'opening_amount' => 'nullable|numeric|min:0',
            'financial_account_id' => 'required|exists:financial_accounts,id'
        ]);

        // Check if open register exists for THIS specific account
        $exists = CashRegister::where('status', 'open')->where('financial_account_id', $request->financial_account_id)->exists();
        if($exists) {
            return back()->with('error', 'This register is already open!');
        }

        $account = \App\Models\FinancialAccount::find($request->financial_account_id);

        CashRegister::create([
            'user_id' => Auth::id(),
            'financial_account_id' => $request->financial_account_id,
            'opening_amount' => $request->opening_amount ?? $account->current_balance,
            'status' => 'open',
            'opened_at' => Carbon::now()
        ]);

        return redirect()->back()->with('success', 'Register Opened for ' . $account->name);
    }

    public function close(Request $request, $id)
    {
        $register = CashRegister::findOrFail($id);
        
        $opened_at = $register->opened_at;
        $now = Carbon::now();
        $accountId = $register->financial_account_id;

        // 1. Sales
        $posSalesQuery = \App\Models\CustomerLedger::whereBetween('created_at', [$opened_at, $now])
                    ->where('type', 'credit')->where('category', 'payment');
        if($accountId) $posSalesQuery->where('financial_account_id', $accountId);
        else $posSalesQuery->where('description', 'LIKE', '%via CASH%');
        $posSales = $posSalesQuery->sum('amount');

        // 2. Later Payments
        $laterPaymentsQuery = \App\Models\CustomerLedger::whereBetween('created_at', [$opened_at, $now])
                    ->where('type', 'credit')->where('category', 'payment')->where('description', 'NOT LIKE', '%Order #%');
        if($accountId) $laterPaymentsQuery->where('financial_account_id', $accountId);
        else $laterPaymentsQuery->where(function($q) { $q->where('description', 'LIKE', '%cash%')->orWhere('description', 'LIKE', '%Cash%'); });
        $laterPayments = $laterPaymentsQuery->sum('amount');

        $expenses = \App\Models\Expense::whereBetween('created_at', [$opened_at, $now])->sum('amount');
        $purchaseOrderPayments = \App\Models\PurchaseOrder::whereBetween('created_at', [$opened_at, $now])->sum('paid_amount');
        $packagingPayments = \App\Models\PackagingPurchase::whereBetween('created_at', [$opened_at, $now])->sum('total_price');

        // 6. Manual Supplier Ledger
        $supplierLedgerPaymentsQuery = \App\Models\SupplierLedger::whereBetween('created_at', [$opened_at, $now])
                            ->where('type', 'credit')->where('category', 'payment');
        if($accountId) $supplierLedgerPaymentsQuery->where('financial_account_id', $accountId);
        else $supplierLedgerPaymentsQuery->where(function($q) { $q->where('description', 'LIKE', '%cash%')->orWhere('description', 'LIKE', '%Cash%'); });
        $supplierLedgerPayments = $supplierLedgerPaymentsQuery->sum('amount');

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
