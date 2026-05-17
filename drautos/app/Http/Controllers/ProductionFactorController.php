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
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    public function purchase(Request $request, $id)
    {
        $factor = ProductionFactor::findOrFail($id);

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'quantity' => 'required|numeric|min:0.01',
            'total_cost' => 'required|numeric|min:0',
            'date' => 'required|date'
        ]);

        // Update Stock (only for materials)
        if ($factor->type == 'material') {
            $factor->stock_quantity += $request->quantity;
        }
        
        // Optionally update the default cost price if they want the moving average, but for now just leave default cost.
        $factor->save();

        // Update Supplier Ledger (Debit because we owe them)
        \App\Models\SupplierLedger::record(
            $request->supplier_id,
            $request->date,
            'debit',
            'purchase',
            "Purchased {$request->quantity} {$factor->unit} of {$factor->name}",
            $request->total_cost,
            'factor_'.$factor->id
        );

        return redirect()->back()->with('success', 'Purchase logged successfully! Stock and Supplier Ledger have been updated.');
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
