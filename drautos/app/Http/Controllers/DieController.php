<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DieModel;
use App\Models\DieCustodyLog;
use App\Models\DieQualityReport;
use App\Models\DieExpense;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\FinancialAccount;
use App\Models\SupplierLedger;
use App\Models\AccountTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DieController extends Controller
{
    public function index()
    {
        $dies = DieModel::with('product')->orderBy('id', 'DESC')->get();
        return view('backend.die.index')->with('dies', $dies);
    }

    public function create()
    {
        $products = Product::where('status', 'active')->orderBy('title')->get();
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        $accounts = FinancialAccount::where('status', 'active')->get();
        return view('backend.die.create', compact('products', 'suppliers', 'accounts'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'string|required',
            'rack_number' => 'string|nullable',
            'maker' => 'string|nullable',
            'maker_phone' => 'string|nullable',
            'die_type' => 'string|nullable',
            'phone_number' => 'string|nullable',
            'custody_of' => 'string|nullable',
            'custody_phone' => 'string|nullable',
            'quality_status' => 'string|nullable',
            'photo' => 'string|nullable',
            'photos' => 'array|nullable',
            'product_id' => 'nullable|exists:products,id',
            'maker_id' => 'nullable|exists:suppliers,id',
            'making_cost' => 'numeric|min:0|nullable',
            'amount_paid' => 'numeric|min:0|nullable',
            'financial_account_id' => 'nullable|exists:financial_accounts,id',
            'status' => 'required|in:active,inactive'
        ]);

        $data = $request->all();
        $data['making_cost'] = $data['making_cost'] ?? 0;
        
        // Handle photos array (gallery)
        if ($request->has('photos') && is_array($request->photos)) {
            $data['photos'] = array_filter($request->photos);
        } else {
            $data['photos'] = [];
        }

        DB::beginTransaction();
        try {
            $die = DieModel::create($data);

            // Record initial quality status in history if set
            if ($die->quality_status) {
                DieQualityReport::create([
                    'die_id' => $die->id,
                    'quality_status' => $die->quality_status,
                    'report_date' => now(),
                    'notes' => 'Initial setup quality status set to: ' . ucfirst(str_replace('_', ' ', $die->quality_status)),
                    'reported_by' => Auth::id()
                ]);
            }

            // Record initial custody log if set
            if ($die->custody_of) {
                DieCustodyLog::create([
                    'die_id' => $die->id,
                    'custody_of' => $die->custody_of,
                    'custody_phone' => $die->custody_phone,
                    'handover_date' => now(),
                    'notes' => 'Initial setup custody assigned to: ' . $die->custody_of,
                    'created_by' => Auth::id()
                ]);
            }

            // Accounting Trigger for Maker Cost / Bill
            if ($die->maker_id && $die->making_cost > 0) {
                // 1. Create debit entry (bill) in Supplier Ledger
                $description = "Purchase of new Die: " . $die->name;
                $ledger = SupplierLedger::record(
                    $die->maker_id,
                    now()->toDateString(),
                    'debit',
                    'purchase',
                    $description,
                    $die->making_cost,
                    $die->id
                );

                // 2. If paid amount > 0, create credit entry (payment) and deduct cash
                $paid = (float)($request->amount_paid ?? 0);
                if ($paid > 0) {
                    $financialAccountId = $request->financial_account_id ?: FinancialAccount::getStaffAccount();
                    
                    SupplierLedger::record(
                        $die->maker_id,
                        now()->toDateString(),
                        'credit',
                        'payment',
                        "Payment/Advance for Die: " . $die->name,
                        $paid,
                        $die->id,
                        'cash',
                        null,
                        $financialAccountId
                    );
                }
            }

            DB::commit();
            request()->session()->flash('success', 'Die successfully added');
            return redirect()->route('die-management.index');

        } catch (\Exception $e) {
            DB::rollBack();
            request()->session()->flash('error', 'Error occurred: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function show($id)
    {
        $die = DieModel::with([
            'product', 
            'makerSupplier', 
            'custodyLogs.creator', 
            'qualityReports.reporter', 
            'expenses.supplier', 
            'expenses.financialAccount',
            'productions.producer'
        ])->findOrFail($id);

        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        $accounts = FinancialAccount::where('status', 'active')->get();
        
        // Secure QR Code url
        $qrCodeUrl = route('die-management.show', $die->id);

        return view('backend.die.show', compact('die', 'suppliers', 'accounts', 'qrCodeUrl'));
    }

    public function edit($id)
    {
        $die = DieModel::findOrFail($id);
        $products = Product::where('status', 'active')->orderBy('title')->get();
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        return view('backend.die.edit', compact('die', 'products', 'suppliers'));
    }

    public function update(Request $request, $id)
    {
        $die = DieModel::findOrFail($id);
        $this->validate($request, [
            'name' => 'string|required',
            'rack_number' => 'string|nullable',
            'maker' => 'string|nullable',
            'maker_phone' => 'string|nullable',
            'die_type' => 'string|nullable',
            'phone_number' => 'string|nullable',
            'custody_of' => 'string|nullable',
            'custody_phone' => 'string|nullable',
            'quality_status' => 'string|nullable',
            'photo' => 'string|nullable',
            'photos' => 'array|nullable',
            'product_id' => 'nullable|exists:products,id',
            'maker_id' => 'nullable|exists:suppliers,id',
            'making_cost' => 'numeric|min:0|nullable',
            'status' => 'required|in:active,inactive'
        ]);

        $data = $request->all();
        $data['making_cost'] = $data['making_cost'] ?? 0;

        // Handle photos array (gallery)
        if ($request->has('photos') && is_array($request->photos)) {
            $data['photos'] = array_filter($request->photos);
        } else {
            $data['photos'] = [];
        }

        DB::beginTransaction();
        try {
            $oldMakerId = $die->maker_id;
            $oldCost = $die->making_cost;

            $die->fill($data)->save();

            // If maker or cost changed, update supplier ledger records
            if (($oldMakerId != $die->maker_id) || ($oldCost != $die->making_cost)) {
                // Delete old initial purchase ledger entry for this die
                SupplierLedger::where('reference_id', $die->id)
                    ->where('category', 'purchase')
                    ->where('description', 'LIKE', 'Purchase of new Die%')
                    ->delete();

                // Re-record if active
                if ($die->maker_id && $die->making_cost > 0) {
                    SupplierLedger::record(
                        $die->maker_id,
                        now()->toDateString(),
                        'debit',
                        'purchase',
                        "Purchase of new Die (Updated): " . $die->name,
                        $die->making_cost,
                        $die->id
                    );
                }

                if ($oldMakerId) {
                    SupplierLedger::updateBalance($oldMakerId);
                }
                if ($die->maker_id) {
                    SupplierLedger::updateBalance($die->maker_id);
                }
            }

            DB::commit();
            request()->session()->flash('success', 'Die successfully updated');
            return redirect()->route('die-management.index');

        } catch (\Exception $e) {
            DB::rollBack();
            request()->session()->flash('error', 'Error occurred: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function destroy($id)
    {
        $die = DieModel::findOrFail($id);

        DB::beginTransaction();
        try {
            // Delete related ledger entries
            $makerId = $die->maker_id;
            SupplierLedger::where('reference_id', $die->id)->delete();
            
            $die->delete();

            if ($makerId) {
                SupplierLedger::updateBalance($makerId);
            }

            DB::commit();
            request()->session()->flash('success', 'Die successfully deleted');
            return redirect()->route('die-management.index');

        } catch (\Exception $e) {
            DB::rollBack();
            request()->session()->flash('error', 'Error: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Record Custody Handover
     */
    public function recordHandover(Request $request, $id)
    {
        $die = DieModel::findOrFail($id);

        $request->validate([
            'custody_of' => 'required|string',
            'custody_phone' => 'nullable|string',
            'handover_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            DieCustodyLog::create([
                'die_id' => $die->id,
                'custody_of' => $request->custody_of,
                'custody_phone' => $request->custody_phone,
                'handover_date' => $request->handover_date,
                'notes' => $request->notes,
                'created_by' => Auth::id()
            ]);

            $die->update([
                'custody_of' => $request->custody_of,
                'custody_phone' => $request->custody_phone
            ]);

            DB::commit();
            return back()->with('success', 'Custody handover recorded successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Handover failed: ' . $e->getMessage());
        }
    }

    /**
     * Record Quality Status Change
     */
    public function recordQualityReport(Request $request, $id)
    {
        $die = DieModel::findOrFail($id);

        $request->validate([
            'quality_status' => 'required|in:good,maintenance_required,damaged',
            'report_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            DieQualityReport::create([
                'die_id' => $die->id,
                'quality_status' => $request->quality_status,
                'report_date' => $request->report_date,
                'notes' => $request->notes,
                'reported_by' => Auth::id()
            ]);

            $die->update([
                'quality_status' => $request->quality_status
            ]);

            DB::commit();
            return back()->with('success', 'Quality status report recorded successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Quality report failed: ' . $e->getMessage());
        }
    }

    /**
     * Record Maintenance Expense
     */
    public function recordExpense(Request $request, $id)
    {
        $die = DieModel::findOrFail($id);

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'description' => 'required|string',
            'financial_account_id' => 'nullable|exists:financial_accounts,id',
            'amount_paid' => 'required|numeric|min:0'
        ]);

        DB::beginTransaction();
        try {
            $financialAccountId = $request->financial_account_id ?: FinancialAccount::getStaffAccount();
            $paid = (float)$request->amount_paid;
            $amount = (float)$request->amount;

            $expense = DieExpense::create([
                'die_id' => $die->id,
                'supplier_id' => $request->supplier_id,
                'expense_date' => $request->expense_date,
                'amount' => $amount,
                'description' => $request->description,
                'payment_method' => $paid > 0 ? 'cash' : null,
                'financial_account_id' => $paid > 0 ? $financialAccountId : null,
                'created_by' => Auth::id()
            ]);

            // Ledger integrations
            if ($request->supplier_id) {
                // 1. Post debit (bill) to Supplier Ledger
                SupplierLedger::record(
                    $request->supplier_id,
                    $request->expense_date,
                    'debit',
                    'purchase',
                    "Maintenance service: " . $request->description . " (Die: " . $die->name . ")",
                    $amount,
                    $expense->id
                );

                // 2. Post credit (payment) to Supplier Ledger if paid > 0, and deduct cash
                if ($paid > 0) {
                    SupplierLedger::record(
                        $request->supplier_id,
                        $request->expense_date,
                        'credit',
                        'payment',
                        "Payment for maintenance: " . $request->description . " (Die: " . $die->name . ")",
                        $paid,
                        $expense->id,
                        'cash',
                        null,
                        $financialAccountId
                    );
                }
            } else {
                // Direct cash payment without a supplier
                if ($paid > 0) {
                    AccountTransaction::record(
                        $financialAccountId,
                        $paid,
                        'out',
                        'DieExpense',
                        $expense->id,
                        "Direct Maintenance Payment: " . $request->description . " (Die: " . $die->name . ")",
                        $request->expense_date
                    );
                }
            }

            DB::commit();
            return back()->with('success', 'Maintenance expense recorded successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Expense recording failed: ' . $e->getMessage());
        }
    }
}
