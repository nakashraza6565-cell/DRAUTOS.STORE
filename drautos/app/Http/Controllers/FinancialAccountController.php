<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FinancialAccount;
use App\Models\AccountTransaction;

class FinancialAccountController extends Controller
{
    public function index()
    {
        $accounts = FinancialAccount::orderBy('name', 'asc')->get();
        return view('backend.financial_accounts.index', compact('accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string',
            'account_number' => 'nullable|string',
            'opening_balance' => 'nullable|numeric',
        ]);

        $account = FinancialAccount::create($request->all());
        FinancialAccount::updateBalance($account->id);

        request()->session()->flash('success', 'Account created successfully');
        return back();
    }

    public function show($id)
    {
        $account = FinancialAccount::findOrFail($id);
        $transactions = AccountTransaction::where('financial_account_id', $id)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(50);
            
        return view('backend.financial_accounts.show', compact('account', 'transactions'));
    }

    public function update(Request $request, $id)
    {
        $account = FinancialAccount::findOrFail($id);
        $account->update($request->all());
        FinancialAccount::updateBalance($id);
        
        request()->session()->flash('success', 'Account updated successfully');
        return back();
    }

    public function destroy($id)
    {
        $account = FinancialAccount::findOrFail($id);
        $account->delete();
        
        request()->session()->flash('success', 'Account deleted successfully');
        return redirect()->route('financial-accounts.index');
    }
}
