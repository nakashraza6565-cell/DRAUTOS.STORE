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
        $recent_expense_titles = Expense::select('title')
            ->groupBy('title')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(20)
            ->pluck('title');
            
        return view('backend.expense.create', compact('accounts', 'recent_expense_titles'));
    }

    public function store(Request $request)
    {
        // Fallback for missing fields (like from the dashboard quick expense modal)
        if (!$request->filled('title') && $request->filled('description')) {
            $request->merge([
                'title' => mb_substr($request->description, 0, 50) ?: 'Quick Expense'
            ]);
        }
        if (!$request->filled('date')) {
            $request->merge([
                'date' => date('Y-m-d')
            ]);
        }

        $request->validate([
            'title' => 'required|string',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'financial_account_id' => 'nullable|exists:financial_accounts,id'
        ]);

        $user = auth()->user();
        $is_admin = $user->hasRole('admin');

        $expense = Expense::create([
            'title' => $request->title,
            'amount' => $request->amount,
            'date' => $request->date,
            'description' => $request->description,
            'financial_account_id' => $request->financial_account_id,
            'user_id' => $user->id,
            'approval_status' => $is_admin ? 'approved' : 'pending',
            'approved_by' => $is_admin ? $user->id : null
        ]);

        if ($is_admin && $request->financial_account_id && $request->amount > 0) {
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

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Expense created successfully',
                'expense' => $expense
            ]);
        }

        if ($request->header('referer') && str_contains($request->header('referer'), 'expenses/create')) {
            return redirect()->route('expenses.index')->with('success', 'Expense created successfully');
        }

        return redirect()->back()->with('success', 'Expense created successfully');
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
    public function approve($id)
    {
        $expense = Expense::findOrFail($id);
        
        if ($expense->approval_status !== 'approved') {
            $expense->update([
                'approval_status' => 'approved',
                'approved_by' => auth()->id()
            ]);

            if ($expense->financial_account_id && $expense->amount > 0) {
                \App\Models\AccountTransaction::record(
                    $expense->financial_account_id,
                    $expense->amount,
                    'out',
                    'Expense',
                    $expense->id,
                    'Expense: ' . $expense->title,
                    $expense->date
                );
            }
        }
        
        return back()->with('success', 'Expense approved successfully.');
    }
}
