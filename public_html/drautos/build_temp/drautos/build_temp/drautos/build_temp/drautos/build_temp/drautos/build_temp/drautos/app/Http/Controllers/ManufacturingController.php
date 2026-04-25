<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ManufacturingBill;
use App\Models\ManufacturingBillComponent;
use App\Models\ManufacturingProduction;
use App\Models\Product;
use Illuminate\Support\Str;
use Auth;
use DB;

class ManufacturingController extends Controller
{
    /**
     * Display a listing of the Manufacturing Bills (BOMs).
     */
    public function index()
    {
        $boms = ManufacturingBill::with('product')->orderBy('created_at', 'DESC')->paginate(5000);
        return view('backend.manufacturing.index', compact('boms'));
    }

    /**
     * Show the form for creating a new BOM.
     */
    public function create()
    {
        // Products that can be manufactured (Finished Goods)
        // Ideally filter by type if you have 'manufactured' vs 'raw', but for now all active products.
        $products = Product::where('status', 'active')->orderBy('title')->get();
        return view('backend.manufacturing.create', compact('products'));
    }

    /**
     * Store a newly created BOM in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'bom_number' => 'required|string|unique:manufacturing_bills,bom_number',
            'product_id' => 'required|exists:products,id',
            'batch_quantity' => 'required|integer|min:1',
            'components' => 'required|array|min:1',
            'components.*.product_id' => 'required|exists:products,id',
            'components.*.quantity' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();
        try {
            $totalMaterialCost = 0;

            // Create BOM
            $bom = new ManufacturingBill();
            $bom->bom_number = $request->bom_number;
            $bom->product_id = $request->product_id;
            $bom->batch_quantity = $request->batch_quantity;
            $bom->machining_cost = $request->machining_cost ?? 0;
            $bom->labour_cost = $request->labour_cost ?? 0;
            $bom->packaging_cost = $request->packaging_cost ?? 0;
            $bom->overhead_cost = $request->overhead_cost ?? 0;
            $bom->notes = $request->notes;
            $bom->status = 'active';
            $bom->created_by = Auth::id();
            $bom->save();

            // Add Components
            foreach ($request->components as $componentData) {
                $product = Product::find($componentData['product_id']);
                $costPerUnit = $product->purchase_price ?? ($product->price ?? 0); // Fallback to selling price if purchase price not set
                $totalCost = $costPerUnit * $componentData['quantity'];

                $component = new ManufacturingBillComponent();
                $component->manufacturing_bill_id = $bom->id;
                $component->component_product_id = $componentData['product_id'];
                $component->quantity_required = $componentData['quantity'];
                $component->unit = 'item'; // Defaulting for now
                $component->cost_per_unit = $costPerUnit;
                $component->total_cost = $totalCost;
                $component->save();

                $totalMaterialCost += $totalCost;
            }

            // Update BOM Costs
            $bom->material_cost = $totalMaterialCost;
            $bom->calculateCost(); // This saves the model

            DB::commit();
            return redirect()->route('manufacturing.index')->with('success', 'Manufacturing Bill created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error creating BOM: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified BOM.
     */
    public function show($id)
    {
        $bom = ManufacturingBill::with(['product', 'components.componentProduct', 'productions'])->findOrFail($id);
        return view('backend.manufacturing.show', compact('bom'));
    }

    /**
     * Show the form for editing the specified BOM.
     */
    public function edit($id)
    {
        $bom = ManufacturingBill::with('components')->findOrFail($id);
        $products = Product::where('status', 'active')->orderBy('title')->get();
        return view('backend.manufacturing.edit', compact('bom', 'products'));
    }

    /**
     * Update the specified BOM in storage.
     */
    public function update(Request $request, $id)
    {
        $bom = ManufacturingBill::findOrFail($id);

        $request->validate([
            'bom_number' => 'required|string|unique:manufacturing_bills,bom_number,'.$id,
            'product_id' => 'required|exists:products,id',
            'batch_quantity' => 'required|integer|min:1',
            'components' => 'required|array|min:1',
            'components.*.product_id' => 'required|exists:products,id',
            'components.*.quantity' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();
        try {
            // Update BOM details
            $bom->bom_number = $request->bom_number;
            $bom->product_id = $request->product_id;
            $bom->batch_quantity = $request->batch_quantity;
            $bom->machining_cost = $request->machining_cost ?? 0;
            $bom->labour_cost = $request->labour_cost ?? 0;
            $bom->packaging_cost = $request->packaging_cost ?? 0;
            $bom->overhead_cost = $request->overhead_cost ?? 0;
            $bom->notes = $request->notes;
            $bom->save();

            // Clear existing components (simple strategy: delete and recreate)
            // Ideally we should sync, but this is cleaner for BOM logic
            $bom->components()->delete();

            $totalMaterialCost = 0;

            foreach ($request->components as $componentData) {
                $product = Product::find($componentData['product_id']);
                $costPerUnit = $product->purchase_price ?? ($product->price ?? 0);
                $totalCost = $costPerUnit * $componentData['quantity'];

                $component = new ManufacturingBillComponent();
                $component->manufacturing_bill_id = $bom->id;
                $component->component_product_id = $componentData['product_id'];
                $component->quantity_required = $componentData['quantity'];
                $component->unit = 'item';
                $component->cost_per_unit = $costPerUnit;
                $component->total_cost = $totalCost;
                $component->save();

                $totalMaterialCost += $totalCost;
            }

            $bom->material_cost = $totalMaterialCost;
            $bom->calculateCost();

            DB::commit();
            return redirect()->route('manufacturing.index')->with('success', 'Manufacturing Bill updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error updating BOM: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified BOM from storage.
     */
    public function destroy($id)
    {
        $bom = ManufacturingBill::findOrFail($id);
        $bom->delete();
        return redirect()->route('manufacturing.index')->with('success', 'Manufacturing Bill deleted successfully.');
    }

    // --- Production Logic ---

    /**
     * Display a listing of Production Runs.
     */
    public function productionIndex()
    {
        $productions = ManufacturingProduction::with(['manufacturingBill.product', 'creator'])->orderBy('created_at', 'DESC')->paginate(5000);
        return view('backend.manufacturing.production.index', compact('productions'));
    }

    /**
     * Show form to create a new Production Run.
     */
    public function productionCreate(Request $request)
    {
        $boms = ManufacturingBill::with('product')->where('status', 'active')->get();
        $selectedBom = null;
        if($request->has('bom_id')) {
            $selectedBom = ManufacturingBill::with('components.componentProduct')->find($request->bom_id);
        }
        return view('backend.manufacturing.production.create', compact('boms', 'selectedBom'));
    }

    /**
     * Store new Production Run (Execute Manufacturing).
     */
    public function productionStore(Request $request)
    {
        $request->validate([
            'manufacturing_bill_id' => 'required|exists:manufacturing_bills,id',
            'quantity_produced' => 'required|integer|min:1',
            'production_date' => 'required|date',
        ]);

        $bom = ManufacturingBill::with('components')->findOrFail($request->manufacturing_bill_id);
        $multiplier = $request->quantity_produced / $bom->batch_quantity;

        DB::beginTransaction();
        try {
            // Check Stock Availability first
            foreach ($bom->components as $component) {
                $requiredQty = $component->quantity_required * $multiplier;
                $material = Product::find($component->component_product_id);
                
                if ($material->stock < $requiredQty) {
                    throw new \Exception("Insufficient stock for material: {$material->title}. Required: {$requiredQty}, Available: {$material->stock}");
                }
            }

            // Deduct Stock
            foreach ($bom->components as $component) {
                $requiredQty = $component->quantity_required * $multiplier;
                Product::where('id', $component->component_product_id)->decrement('stock', $requiredQty);
            }

            // Add Finished Goods Stock
            Product::where('id', $bom->product_id)->increment('stock', $request->quantity_produced);

            // Record Production
            $production = new ManufacturingProduction();
            $production->production_number = 'PROD-' . Str::upper(Str::random(8));
            $production->manufacturing_bill_id = $bom->id;
            $production->quantity_produced = $request->quantity_produced;
            $production->production_date = $request->production_date;
            $production->actual_cost = $bom->total_cost_per_unit * $request->quantity_produced; // Simplified: using BOM standard cost
            $production->notes = $request->notes;
            $production->produced_by = Auth::id(); // Changed from user_id to produced_by based on migration
            $production->save();

            DB::commit();
            return redirect()->route('manufacturing.production.index')->with('success', 'Production run recorded successfully. Stock updated.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Production Failed: ' . $e->getMessage())->withInput();
        }
    }
}
