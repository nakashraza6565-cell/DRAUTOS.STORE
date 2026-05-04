<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupplierLedger;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class SupplierLedgerController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();
        
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('company_name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('phone', 'LIKE', '%' . $request->search . '%');
            });
        }

        $suppliers = $query->orderBy('name', 'asc')->paginate(5000);
        
        return view('backend.supplier_ledger.index', compact('suppliers'));
    }

    public function show(Supplier $supplier, Request $request)
    {
        $query = SupplierLedger::where('supplier_id', $supplier->id);

        if ($request->date_from) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        $ledger = $query->orderBy('transaction_date', 'asc')->get();
        
        // Prepare Graph Data
        $graphLabels = [];
        $balanceHistory = [];
        $runningBalance = 0;
        
        foreach ($ledger as $item) {
            $graphLabels[] = date('d M', strtotime($item->transaction_date));
            if ($item->type == 'debit') {
                $runningBalance += $item->amount;
            } else {
                $runningBalance -= $item->amount;
            }
            $balanceHistory[] = $runningBalance;
        }

        $ledger = $query->orderBy('transaction_date', 'desc')->orderBy('id', 'desc')->paginate(5000);
        
        return view('backend.supplier_ledger.show', compact('supplier', 'ledger', 'graphLabels', 'balanceHistory'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'transaction_date' => 'required|date',
            'type' => 'required|in:credit,debit',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
            'category' => 'required|in:manual,payment,purchase,return',
            'payment_method' => 'nullable|string',
            'payment_details' => 'nullable|array'
        ]);

        try {
            // Auto-fix database schema if columns are missing
            if (!\Illuminate\Support\Facades\Schema::hasColumn('supplier_ledgers', 'payment_method')) {
                \Illuminate\Support\Facades\Schema::table('supplier_ledgers', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->string('payment_method')->nullable()->after('category');
                    $table->text('payment_details')->nullable()->after('payment_method'); // Using text instead of json for better compatibility
                });
            }

            SupplierLedger::record(
                $request->supplier_id,
                $request->transaction_date,
                $request->type,
                $request->category,
                $request->description,
                $request->amount,
                null,
                $request->payment_method,
                $request->payment_details
            );

            return redirect()->back()->with('success', 'Transaction recorded successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'type' => 'required|in:credit,debit',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
            'category' => 'required|in:manual,payment,purchase,return'
        ]);

        try {
            $transaction = SupplierLedger::findOrFail($id);
            $supplierId = $transaction->supplier_id;
            $transaction->update($validated);
            
            // Recalculate balance
            SupplierLedger::updateBalance($supplierId);

            return redirect()->back()->with('success', 'Transaction updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function generatePDF($supplierId)
    {
        $supplier = Supplier::findOrFail($supplierId);
        $ledger = SupplierLedger::where('supplier_id', $supplierId)->orderBy('transaction_date', 'asc')->get();
        $pdf = \PDF::loadView('backend.supplier_ledger.pdf', compact('supplier', 'ledger'));
        return $pdf->download('ledger-' . $supplier->name . '-' . date('Y-m-d') . '.pdf');
    }

    public function sendWhatsApp(Request $request, $supplierId)
    {
        $supplier = Supplier::findOrFail($supplierId);
        $ledger = SupplierLedger::where('supplier_id', $supplierId)->orderBy('transaction_date', 'asc')->get();
        
        // Generate PDF path
        $pdf = \PDF::loadView('backend.supplier_ledger.pdf', compact('supplier', 'ledger'));
        $fileName = 'ledger-' . str_replace(' ', '_', $supplier->name) . '-' . time() . '.pdf';
        $path = public_path('storage/ledgers/' . $fileName);
        
        if (!file_exists(public_path('storage/ledgers'))) {
            mkdir(public_path('storage/ledgers'), 0777, true);
        }
        
        $pdf->save($path);
        $fileUrl = asset('storage/ledgers/' . $fileName);

        $waService = new \App\Services\WhatsAppService();
        $message = "Hello " . $supplier->name . ", here is our account statement with you from Danyal Autos.\n\nCurrent Payable: Rs. " . number_format($supplier->current_balance, 2);
        
        try {
            $waService->sendMediaMessage($supplier->phone, $fileUrl, $fileName, $message);
            return redirect()->back()->with('success', 'Ledger sent to supplier via WhatsApp');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'WhatsApp Error: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $transaction = SupplierLedger::findOrFail($id);
            $supplierId = $transaction->supplier_id;
            $transaction->delete();
            
            // Recalculate balance
            SupplierLedger::updateBalance($supplierId);

            return redirect()->back()->with('success', 'Transaction deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function thermalPrint($supplierId)
    {
        $supplier = Supplier::findOrFail($supplierId);
        $ledger = SupplierLedger::where('supplier_id', $supplierId)->orderBy('transaction_date', 'asc')->get();
        return view('backend.supplier_ledger.thermal', compact('supplier', 'ledger'));
    }

    public function printTransactionVoucher($id)
    {
        $transaction = SupplierLedger::with('supplier')->findOrFail($id);
        return view('backend.supplier_ledger.thermal-voucher', compact('transaction'));
    }
}
