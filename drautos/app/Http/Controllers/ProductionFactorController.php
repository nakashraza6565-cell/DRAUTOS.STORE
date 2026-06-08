<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductionFactor;

class ProductionFactorController extends Controller
{
    public function index()
    {
        $factors = ProductionFactor::orderBy('created_at', 'DESC')->paginate(50);
        $suppliers = \App\Models\Supplier::where('status', 'active')->orderBy('name')->get();
        return view('backend.manufacturing.factors.index', compact('factors', 'suppliers'));
    }

    public function create()
    {
        return view('backend.manufacturing.factors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:material,labor,overhead,service',
            'unit' => 'nullable|string|max:50',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $data = $request->all();
        // default missing values
        $data['cost_price'] = $data['cost_price'] ?? 0;
        $data['stock_quantity'] = $request->stock_quantity ?? 0;

        ProductionFactor::create($data);

        return redirect()->route('manufacturing.production-factors.index')->with('success', 'Factor of Production added successfully.');
    }

    public function quickStore(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'cost_price' => 'nullable|numeric|min:0',
                'unit' => 'nullable|string|max:50',
            ]);

            $factor = ProductionFactor::create([
                'name' => $request->name,
                'type' => 'material',
                'unit' => $request->unit ?? 'piece',
                'cost_price' => $request->cost_price ?? 0,
                'status' => 'active',
                'stock_quantity' => $request->stock ?? 0,
            ]);

            return response()->json([
                'status' => 'success',
                'factor' => $factor
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    public function purchaseForm()
    {
        $suppliers = \App\Models\Supplier::where('status', 'active')->orderBy('name')->get();
        $factors = ProductionFactor::where('status', 'active')->orderBy('name')->get();
        return view('backend.manufacturing.factors.purchase', compact('suppliers', 'factors'));
    }

    public function purchaseStore(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.factor_id' => 'required|exists:production_factors,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.total_cost' => 'required|numeric|min:0',
        ]);

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $grandTotal = 0;
            $descriptionParts = [];

            $purchase = \App\Models\RawMaterialPurchase::create([
                'invoice_number' => 'TMP-' . time(),
                'supplier_id' => $request->supplier_id,
                'purchase_date' => $request->date,
                'total_amount' => 0,
                'notes' => $request->notes ?? null
            ]);
            $purchase->invoice_number = 'RMP-' . date('Ymd', strtotime($request->date)) . '-' . str_pad($purchase->id, 4, '0', STR_PAD_LEFT);

            foreach ($request->items as $item) {
                $factor = ProductionFactor::findOrFail($item['factor_id']);
                
                // Update Stock
                if ($factor->type == 'material') {
                    $factor->stock_quantity += $item['quantity'];
                    $factor->save();
                }

                \App\Models\RawMaterialPurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'factor_id' => $factor->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['total_cost'] / $item['quantity'],
                    'total' => $item['total_cost'],
                ]);

                $grandTotal += $item['total_cost'];
                $descriptionParts[] = $item['quantity'] . ' ' . ($factor->unit ?? 'pcs') . ' of ' . $factor->name;
            }

            $purchase->total_amount = $grandTotal;
            $purchase->save();

            $description = "Purchased (Invoice: {$purchase->invoice_number}): " . implode(', ', $descriptionParts);

            // Update Supplier Ledger (Debit because we owe them)
            \App\Models\SupplierLedger::record(
                $request->supplier_id,
                $request->date,
                'debit',
                'purchase',
                $description,
                $grandTotal,
                null
            );

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->route('manufacturing.production-factors.index')->with('success', 'Purchase logged successfully! Stock and Supplier Ledger have been updated.');
        
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Error logging purchase: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $factor = ProductionFactor::findOrFail($id);
        return view('backend.manufacturing.factors.edit', compact('factor'));
    }

    public function invoices()
    {
        $invoices = \App\Models\RawMaterialPurchase::with('supplier')->orderBy('purchase_date', 'DESC')->orderBy('id', 'DESC')->paginate(20);
        return view('backend.manufacturing.factors.invoices', compact('invoices'));
    }

    public function invoiceShow($id)
    {
        $invoice = \App\Models\RawMaterialPurchase::with(['supplier', 'items.factor'])->findOrFail($id);
        return view('backend.manufacturing.factors.invoice_show', compact('invoice'));
    }

    public function update(Request $request, $id)
    {
        $factor = ProductionFactor::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:material,labor,overhead,service',
            'unit' => 'nullable|string|max:50',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $data = $request->all();
        $data['cost_price'] = $data['cost_price'] ?? 0;
        $data['stock_quantity'] = $data['stock_quantity'] ?? 0;

        $factor->update($data);

        return redirect()->route('manufacturing.production-factors.index')->with('success', 'Factor of Production updated successfully.');
    }

    public function destroy($id)
    {
        $factor = ProductionFactor::findOrFail($id);
        $factor->delete();

        return redirect()->route('manufacturing.production-factors.index')->with('success', 'Factor of Production deleted successfully.');
    }
}
