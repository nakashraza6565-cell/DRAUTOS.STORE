<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashRegister;
use App\Models\Order;
use Carbon\Carbon;



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

            // --- CASH INFLOW ---
            // 1. Sales Payments (from customers)
            $posSales = \App\Models\CustomerLedger::whereBetween('created_at', [$opened_at, $now])
                        ->where('financial_account_id', $accountId)
                        ->where('type', 'credit')
                        ->where('category', 'payment')
                        ->sum('amount');

            // --- CASH OUTFLOW ---
            // 2. Expenses
            $expenses = \App\Models\Expense::whereBetween('created_at', [$opened_at, $now])
                        ->where('financial_account_id', $accountId)
                        ->sum('amount');

            // 3. Supplier Payments (from Ledger)
            $supplierPayments = \App\Models\SupplierLedger::whereBetween('created_at', [$opened_at, $now])
                                ->where('financial_account_id', $accountId)
                                ->where('type', 'credit') // Payment made to supplier
                                ->where('category', 'payment')
                                ->sum('amount');

            $totalIn = $posSales;
            $totalOut = $expenses + $supplierPayments;

            $summary = [
                'total_in' => $totalIn,
                'total_out' => $totalOut,
                'opening' => $activeRegister->opening_amount,
                'expected_cash' => $activeRegister->opening_amount + $totalIn - $totalOut,
                'breakdown' => [
                    'sales' => $posSales,
                    'expenses' => $expenses,
                    'supplier_payments' => $supplierPayments,
                    'others' => 0
                ]
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
            'user_id' => auth()->id(),
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
        $posSales = \App\Models\CustomerLedger::whereBetween('created_at', [$opened_at, $now])
                    ->where('financial_account_id', $accountId)
                    ->where('type', 'credit')->where('category', 'payment')
                    ->sum('amount');

        // 2. Expenses
        $expenses = \App\Models\Expense::whereBetween('created_at', [$opened_at, $now])
                    ->where('financial_account_id', $accountId)
                    ->sum('amount');

        // 3. Supplier Payments
        $supplierPayments = \App\Models\SupplierLedger::whereBetween('created_at', [$opened_at, $now])
                            ->where('financial_account_id', $accountId)
                            ->where('type', 'credit')->where('category', 'payment')
                            ->sum('amount');

        $totalIn = $posSales;
        $totalOut = $expenses + $supplierPayments;

        $expected_closing = $register->opening_amount + $totalIn - $totalOut;

        $register->update([
            'closing_amount' => $expected_closing,
            'status' => 'closed',
            'closed_at' => $now,
            'note' => $request->note
        ]);

        return redirect()->back()->with('success', 'Register Closed Successfully at Rs. ' . number_format($expected_closing, 2));
    }
}
