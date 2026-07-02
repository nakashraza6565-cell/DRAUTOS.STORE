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
        // Auto-fix missing Raw Material Purchases due to previous update bug
        $purchases = \App\Models\RawMaterialPurchase::with('items')->get();
        foreach ($purchases as $purchase) {
            if ($purchase->manufacturing_bill_id) {
                // Remove duplicate standalone Subcontract Service ledger if an RMP already exists for this exact supplier & BOM
                \App\Models\SupplierLedger::where('category', 'purchase')
                    ->where('reference_id', $purchase->manufacturing_bill_id)
                    ->where('supplier_id', $purchase->supplier_id)
                    ->where('description', 'LIKE', 'Subcontract Service%')
                    ->delete();

                $exists = \App\Models\SupplierLedger::where('category', 'purchase')
                    ->where('reference_id', $purchase->manufacturing_bill_id)
                    ->where('supplier_id', $purchase->supplier_id)
                    ->where('amount', $purchase->total_amount)
                    ->exists();
                if (!$exists) {
                    $descriptions = [];
                    foreach ($purchase->items as $item) {
                        $descriptions[] = $item->quantity . ' pcs of ' . $item->item_name;
                    }
                    $description = 'Purchased (Invoice: ' . $purchase->invoice_number . '): ' . implode(', ', $descriptions);
                    \App\Models\SupplierLedger::record(
                        $purchase->supplier_id, $purchase->purchase_date, 'debit', 'purchase',
                        $description, $purchase->total_amount, $purchase->manufacturing_bill_id
                    );
                }
            }
        }

        $query = Supplier::query();
        
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('company_name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('phone', 'LIKE', '%' . $request->search . '%');
            });
        }

        $suppliers = $query->with('latestPayment')->orderBy('updated_at', 'desc')->paginate(5000);
        
        return view('backend.supplier_ledger.index', compact('suppliers'));
    }

    public function show(Supplier $supplier, Request $request)
    {
        // Auto-fix missing Raw Material Purchases due to previous update bug
        $purchases = \App\Models\RawMaterialPurchase::with('items')->where('supplier_id', $supplier->id)->get();
        foreach ($purchases as $purchase) {
            if ($purchase->manufacturing_bill_id) {
                // Remove duplicate standalone Subcontract Service ledger if an RMP already exists for this exact supplier & BOM
                \App\Models\SupplierLedger::where('category', 'purchase')
                    ->where('reference_id', $purchase->manufacturing_bill_id)
                    ->where('supplier_id', $purchase->supplier_id)
                    ->where('description', 'LIKE', 'Subcontract Service%')
                    ->delete();

                $exists = \App\Models\SupplierLedger::where('category', 'purchase')
                    ->where('reference_id', $purchase->manufacturing_bill_id)
                    ->where('supplier_id', $purchase->supplier_id)
                    ->where('amount', $purchase->total_amount)
                    ->exists();
                if (!$exists) {
                    $descriptions = [];
                    foreach ($purchase->items as $item) {
                        $descriptions[] = $item->quantity . ' pcs of ' . $item->item_name;
                    }
                    $description = 'Purchased (Invoice: ' . $purchase->invoice_number . '): ' . implode(', ', $descriptions);
                    \App\Models\SupplierLedger::record(
                        $purchase->supplier_id, $purchase->purchase_date, 'debit', 'purchase',
                        $description, $purchase->total_amount, $purchase->manufacturing_bill_id
                    );
                }
            }
        }

        // Recalculate balances after any auto-fixes
        \App\Models\SupplierLedger::updateBalance($supplier->id);
        $supplier = $supplier->fresh();

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
        $accounts = \App\Models\FinancialAccount::where('status', 'active')->get();
        
        return view('backend.supplier_ledger.show', compact('supplier', 'ledger', 'graphLabels', 'balanceHistory', 'accounts'));
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
            'payment_details' => 'nullable|array',
            'financial_account_id' => 'nullable|exists:financial_accounts,id'
        ]);

        try {
            DB::beginTransaction();

            // Auto-fix database schema if columns are missing
            if (!\Illuminate\Support\Facades\Schema::hasColumn('supplier_ledgers', 'payment_method')) {
                \Illuminate\Support\Facades\Schema::table('supplier_ledgers', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->string('payment_method')->nullable()->after('category');
                    $table->text('payment_details')->nullable()->after('payment_method');
                });
            }

            $paymentMethod = $request->payment_method;
            $paymentDetails = $request->payment_details;
            $referenceId = null;

            // Handle Customer Cheque transfer (Multiple)
            if ($paymentMethod === 'customer_cheque' && isset($paymentDetails['cheque_ids'])) {
                $chequeIds = $paymentDetails['cheque_ids'];
                $chequeDetails = [];
                
                foreach ($chequeIds as $id) {
                    $cheque = \App\Models\Cheque::findOrFail($id);
                    $cheque->update([
                        'status' => 'transferred', 
                        'transferred_to_id' => $request->supplier_id,
                        'notes' => ($cheque->notes ? $cheque->notes . "\n" : "") . "Transferred to Supplier ID: " . $request->supplier_id
                    ]);
                    $chequeDetails[] = "#" . $cheque->cheque_number;
                }
                
                $referenceId = !empty($chequeIds) ? $chequeIds[0] : null;
                $paymentDetails['transferred_cheques'] = $chequeDetails;
                $paymentDetails['all_cheque_ids'] = $chequeIds;
            }

            $financialAccountId = $validated['financial_account_id'] ?? null;
            $description = $validated['description'];

            // Auto-detect active register if no account selected for cash transactions
            if (!$financialAccountId && $validated['category'] === 'payment') {
                $financialAccountId = \App\Models\FinancialAccount::getStaffAccount();
                
                if ($financialAccountId) {
                    $description .= ' (Staff Cash Account)';
                } else {
                    $description .= ' (via CASH)';
                }
            }

            SupplierLedger::record(
                $validated['supplier_id'],
                $validated['transaction_date'],
                $validated['type'],
                $validated['category'],
                $description,
                $validated['amount'],
                $referenceId,
                $paymentMethod,
                $paymentDetails,
                $financialAccountId
            );

            DB::commit();
            return redirect()->back()->with('success', 'Transaction recorded successfully');
        } catch (\Exception $e) {
            DB::rollBack();
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

    public function print($supplierId)
    {
        $supplier = Supplier::findOrFail($supplierId);
        $ledger = SupplierLedger::where('supplier_id', $supplierId)->orderBy('transaction_date', 'asc')->get();
        return view('backend.supplier_ledger.pdf', compact('supplier', 'ledger'));
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
            
            // Delete related account transactions
            \App\Models\AccountTransaction::where(function($query) {
                $query->where('reference_type', 'SupplierLedger')
                      ->orWhere('reference_type', 'App\Models\SupplierLedger')
                      ->orWhere('reference_type', 'App\SupplierLedger');
            })->where('reference_id', $id)->delete();

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
        
        $incoming = null;
        if ($transaction->category === 'purchase' && $transaction->reference_id && str_starts_with($transaction->description, 'Incoming Goods Record #')) {
            $incoming = \App\Models\InventoryIncoming::with(['items.product', 'receiver'])->find($transaction->reference_id);
        }
        
        return view('backend.supplier_ledger.thermal-voucher', compact('transaction', 'incoming'));
    }

    public function syncIncomings()
    {
        $incomings = \App\Models\InventoryIncoming::whereIn('status', ['verified', 'completed'])
            ->whereNotNull('supplier_id')
            ->get();

        $fixedCount = 0;
        $details = [];

        foreach ($incomings as $inc) {
            if ($inc->total_cost <= 0) continue;

            $exists = SupplierLedger::where('supplier_id', $inc->supplier_id)
                ->where('reference_id', $inc->id)
                ->where('category', 'purchase')
                ->exists();

            if (!$exists) {
                $ledger = SupplierLedger::record(
                    $inc->supplier_id,
                    $inc->received_date,
                    'debit',
                    'purchase',
                    'Incoming Goods Record #' . $inc->reference_number . ( $inc->invoice_number ? ' (Inv: '.$inc->invoice_number.')' : '' ),
                    $inc->total_cost,
                    $inc->id
                );
                $details[] = "INC-{$inc->reference_number}";
                $fixedCount++;
            }
        }

        $message = "Recalculated balances. Processed " . count($incomings) . " entries. Created {$fixedCount} missing ledger posts.";
        if ($fixedCount > 0) {
            $message .= " Affected entries: " . implode(', ', $details);
        }

        session()->flash('success', $message);
        return redirect()->route('admin.supplier-ledger.index');
    }
}
