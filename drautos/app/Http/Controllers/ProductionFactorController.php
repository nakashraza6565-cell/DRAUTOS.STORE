<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductionFactor;

class ProductionFactorController extends Controller
{
    public function index()
    {
        $factors = ProductionFactor::orderBy('created_at', 'DESC')->paginate(50);
        return view('backend.manufacturing.factors.index', compact('factors'));
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

        return redirect()->route('production-factors.index')->with('success', 'Factor of Production added successfully.');
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

        return redirect()->route('production-factors.index')->with('success', 'Factor of Production updated successfully.');
    }

    public function destroy($id)
    {
        $factor = ProductionFactor::findOrFail($id);
        $factor->delete();

        return redirect()->route('production-factors.index')->with('success', 'Factor of Production deleted successfully.');
    }
}
