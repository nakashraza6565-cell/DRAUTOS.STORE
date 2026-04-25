<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense; // Assuming you'll create this model shortly or use generic DB
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function index()
    {
        // Simple model definition inline or use DB if model not made yet (I will make model)
        $expenses = DB::table('expenses')->orderBy('date', 'DESC')->get();
        return view('backend.expense.index', compact('expenses'));
    }

    public function create()
    {
        return view('backend.expense.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'amount' => 'required|numeric',
            'date' => 'required|date'
        ]);

        DB::table('expenses')->insert([
            'title' => $request->title,
            'amount' => $request->amount,
            'date' => $request->date,
            'description' => $request->description,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('expenses.index')->with('success', 'Expense created successfully');
    }

    public function destroy($id)
    {
        DB::table('expenses')->where('id', $id)->delete();
        return back()->with('success', 'Expense deleted');
    }
}
