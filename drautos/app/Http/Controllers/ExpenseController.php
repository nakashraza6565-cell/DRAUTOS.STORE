<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense; // Assuming you'll create this model shortly or use generic DB
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with('financialAccount')->orderBy('date', 'DESC')->get();
        return view('backend.expense.index', compact('expenses'));
    }

    public function create()
    {
        $accounts = \App\Models\FinancialAccount::where('status', 'active')->get();
        return view('backend.expense.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'financial_account_id' => 'nullable|exists:financial_accounts,id'
        ]);

        $expense = Expense::create([
            'title' => $request->title,
            'amount' => $request->amount,
            'date' => $request->date,
            'description' => $request->description,
            'financial_account_id' => $request->financial_account_id,
            'user_id' => auth()->id()
        ]);

        if ($request->financial_account_id && $request->amount > 0) {
            \App\Models\AccountTransaction::record(
                $request->financial_account_id,
                $request->amount,
                'out',
                'Expense',
                $expense->id,
                'Expense: ' . $request->title,
                $request->date
            );
        }

        return redirect()->route('expenses.index')->with('success', 'Expense created successfully');
    }

    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        
        // Remove linked transaction if exists
        \App\Models\AccountTransaction::where('reference_type', 'Expense')
            ->where('reference_id', $expense->id)
            ->delete();
            
        $expense->delete();

        return back()->with('success', 'Expense deleted');
    }
}
