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
            'status' => 'required|in:active,inactive',
        ]);

        $data = $request->all();
        // default missing values
        $data['cost_price'] = $data['cost_price'] ?? 0;
        $data['stock_quantity'] = 0; // Stock is added via inventory incoming later if it's a material

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

        $grandTotal = 0;
        $descriptionParts = [];

        foreach ($request->items as $item) {
            $factor = ProductionFactor::findOrFail($item['factor_id']);
            
            // Update Stock
            if ($factor->type == 'material') {
                $factor->stock_quantity += $item['quantity'];
                $factor->save();
            }

            $grandTotal += $item['total_cost'];
            $descriptionParts[] = $item['quantity'] . ' ' . ($factor->unit ?? 'pcs') . ' of ' . $factor->name;
        }

        $description = "Purchased: " . implode(', ', $descriptionParts);

        // Update Supplier Ledger (Debit because we owe them)
        \App\Models\SupplierLedger::record(
            $request->supplier_id,
            $request->date,
            'debit',
            'purchase',
            $description,
            $grandTotal,
            'multi_factor_purchase'
        );

        return redirect()->route('manufacturing.production-factors.index')->with('success', 'Purchase logged successfully! Stock and Supplier Ledger have been updated.');
    }

    public function edit($id)
    {
        $factor = ProductionFactor::findOrFail($id);
        return view('backend.manufacturing.factors.edit', compact('factor'));
    }

    public function update(Request $request, $id)
    {
        $factor = ProductionFactor::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:material,labor,overhead,service',
            'unit' => 'nullable|string|max:50',
            'cost_price' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $data = $request->all();
        $data['cost_price'] = $data['cost_price'] ?? 0;

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
