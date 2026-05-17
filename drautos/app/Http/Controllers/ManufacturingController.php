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
        try {
            $products = Product::where('status', 'active')->orderBy('title')->get();
            $factors = \App\Models\ProductionFactor::where('status', 'active')->orderBy('name')->get();
            return view('backend.manufacturing.create', compact('products', 'factors'))->render();
        } catch (\Exception $e) {
            dd('ERROR:', $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());
        }
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
            'components.*.product_id' => 'required|string',
            'components.*.quantity' => 'required|numeric|min:0.01',
            'overheads' => 'nullable|array',
            'overheads.*.type' => 'required|in:machining,labour,packaging,overhead',
            'overheads.*.cost' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $totalMaterialCost = 0;

            // Map Overheads Array
            $machiningCost = 0;
            $labourCost = 0;
            $packagingCost = 0;
            $overheadCost = 0;
            $overheadDetails = [];

            if ($request->has('overheads') && is_array($request->overheads)) {
                foreach ($request->overheads as $ov) {
                    $costVal = (float) ($ov['cost'] ?? 0);
                    $type = $ov['type'] ?? '';
                    
                    $name = '';
                    switch ($type) {
                        case 'machining':
                            $name = 'Machining Cost';
                            $machiningCost += $costVal;
                            break;
                        case 'labour':
                            $name = 'Labour Cost';
                            $labourCost += $costVal;
                            break;
                        case 'packaging':
                            $name = 'Packaging Cost';
                            $packagingCost += $costVal;
                            break;
                        case 'overhead':
                            $name = 'Other Overheads';
                            $overheadCost += $costVal;
                            break;
                        default:
                            $name = ucfirst(str_replace('_', ' ', $type));
                            $overheadCost += $costVal;
                            break;
                    }

                    if ($costVal > 0 || !empty($type)) {
                        $overheadDetails[] = [
                            'type' => $type,
                            'name' => $name,
                            'cost' => $costVal
                        ];
                    }
                }
            }

            // Create BOM
            $bom = new ManufacturingBill();
            $bom->bom_number = $request->bom_number;
            $bom->product_id = $request->product_id;
            $bom->batch_quantity = $request->batch_quantity;
            $bom->machining_cost = $machiningCost;
            $bom->labour_cost = $labourCost;
            $bom->packaging_cost = $packagingCost;
            $bom->overhead_cost = $overheadCost;
            $bom->overhead_details = $overheadDetails;
            $bom->notes = $request->notes;
            $bom->status = 'active';
            $bom->created_by = Auth::id();
            $bom->save();

            // Add Components
            foreach ($request->components as $componentData) {
                $rawId = $componentData['product_id'];
                
                if (str_starts_with($rawId, 'factor_')) {
                    $factorId = (int) str_replace('factor_', '', $rawId);
                    $factor = \App\Models\ProductionFactor::find($factorId);
                    $costPerUnit = $factor->cost_price ?? 0;
                    $unit = $factor->unit ?? 'pcs';
                    $ingredientType = 'App\\Models\\ProductionFactor';
                    $componentProductId = $factorId;
                } else {
                    $productId = (int) str_replace('product_', '', $rawId);
                    $product = Product::find($productId);
                    $costPerUnit = $product->purchase_price ?? ($product->price ?? 0);
                    $unit = 'pcs';
                    $ingredientType = 'App\\Models\\Product';
                    $componentProductId = $productId;
                }

                $totalCost = $costPerUnit * $componentData['quantity'];

                $component = new ManufacturingBillComponent();
                $component->manufacturing_bill_id = $bom->id;
                $component->component_product_id = $componentProductId;
                $component->ingredient_type = $ingredientType;
                $component->quantity_required = $componentData['quantity'];
                $component->unit = $unit;
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
        $factors = \App\Models\ProductionFactor::where('status', 'active')->orderBy('name')->get();
        return view('backend.manufacturing.edit', compact('bom', 'products', 'factors'));
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
            'components.*.product_id' => 'required|string',
            'components.*.quantity' => 'required|numeric|min:0.01',
            'overheads' => 'nullable|array',
            'overheads.*.type' => 'required|in:machining,labour,packaging,overhead',
            'overheads.*.cost' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Map Overheads Array
            $machiningCost = 0;
            $labourCost = 0;
            $packagingCost = 0;
            $overheadCost = 0;
            $overheadDetails = [];

            if ($request->has('overheads') && is_array($request->overheads)) {
                foreach ($request->overheads as $ov) {
                    $costVal = (float) ($ov['cost'] ?? 0);
                    $type = $ov['type'] ?? '';
                    
                    $name = '';
                    switch ($type) {
                        case 'machining':
                            $name = 'Machining Cost';
                            $machiningCost += $costVal;
                            break;
                        case 'labour':
                            $name = 'Labour Cost';
                            $labourCost += $costVal;
                            break;
                        case 'packaging':
                            $name = 'Packaging Cost';
                            $packagingCost += $costVal;
                            break;
                        case 'overhead':
                            $name = 'Other Overheads';
                            $overheadCost += $costVal;
                            break;
                        default:
                            $name = ucfirst(str_replace('_', ' ', $type));
                            $overheadCost += $costVal;
                            break;
                    }

                    if ($costVal > 0 || !empty($type)) {
                        $overheadDetails[] = [
                            'type' => $type,
                            'name' => $name,
                            'cost' => $costVal
                        ];
                    }
                }
            }

            // Update BOM details
            $bom->bom_number = $request->bom_number;
            $bom->product_id = $request->product_id;
            $bom->batch_quantity = $request->batch_quantity;
            $bom->machining_cost = $machiningCost;
            $bom->labour_cost = $labourCost;
            $bom->packaging_cost = $packagingCost;
            $bom->overhead_cost = $overheadCost;
            $bom->overhead_details = $overheadDetails;
            $bom->notes = $request->notes;
            $bom->save();

            // Clear existing components
            $bom->components()->delete();

            $totalMaterialCost = 0;

            foreach ($request->components as $componentData) {
                $rawId = $componentData['product_id'];
                
                if (str_starts_with($rawId, 'factor_')) {
                    $factorId = (int) str_replace('factor_', '', $rawId);
                    $factor = \App\Models\ProductionFactor::find($factorId);
                    $costPerUnit = $factor->cost_price ?? 0;
                    $unit = $factor->unit ?? 'pcs';
                    $ingredientType = 'App\\Models\\ProductionFactor';
                    $componentProductId = $factorId;
                } else {
                    $productId = (int) str_replace('product_', '', $rawId);
                    $product = Product::find($productId);
                    $costPerUnit = $product->purchase_price ?? ($product->price ?? 0);
                    $unit = 'pcs';
                    $ingredientType = 'App\\Models\\Product';
                    $componentProductId = $productId;
                }

                $totalCost = $costPerUnit * $componentData['quantity'];

                $component = new ManufacturingBillComponent();
                $component->manufacturing_bill_id = $bom->id;
                $component->component_product_id = $componentProductId;
                $component->ingredient_type = $ingredientType;
                $component->quantity_required = $componentData['quantity'];
                $component->unit = $unit;
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
        $suppliers = \App\Models\Supplier::where('status', 'active')->orderBy('name')->get();
        return view('backend.manufacturing.production.create', compact('boms', 'selectedBom', 'suppliers'));
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
            'subcontractor_id' => 'nullable|exists:suppliers,id',
        ]);

        $bom = ManufacturingBill::with('components')->findOrFail($request->manufacturing_bill_id);
        $multiplier = $request->quantity_produced / $bom->batch_quantity;

        DB::beginTransaction();
        try {
            // Check Stock Availability first
            foreach ($bom->components as $component) {
                $requiredQty = $component->quantity_required * $multiplier;
                
                if ($component->ingredient_type === 'App\\Models\\ProductionFactor') {
                    $factor = \App\Models\ProductionFactor::find($component->component_product_id);
                    if ($factor && $factor->type == 'material') {
                        if ($factor->stock_quantity < $requiredQty) {
                            throw new \Exception("Insufficient stock for material: {$factor->name}. Required: {$requiredQty}, Available: {$factor->stock_quantity}");
                        }
                    }
                } else {
                    $product = Product::find($component->component_product_id);
                    if ($product) {
                        if ($product->stock < $requiredQty) {
                            throw new \Exception("Insufficient stock for product: {$product->title}. Required: {$requiredQty}, Available: {$product->stock}");
                        }
                    }
                }
            }

            $totalLaborCost = 0;

            // Deduct Stock
            foreach ($bom->components as $component) {
                $requiredQty = $component->quantity_required * $multiplier;
                
                if ($component->ingredient_type === 'App\\Models\\ProductionFactor') {
                    $factor = \App\Models\ProductionFactor::find($component->component_product_id);
                    if ($factor) {
                        if ($factor->type == 'material') {
                            $factor->decrement('stock_quantity', $requiredQty);
                        } elseif ($factor->type == 'labor') {
                            $totalLaborCost += $component->cost_per_unit * $requiredQty;
                        }
                    }
                } else {
                    $product = Product::find($component->component_product_id);
                    if ($product) {
                        $product->decrement('stock', $requiredQty);
                    }
                }
            }

            // Add Finished Goods Stock
            Product::where('id', $bom->product_id)->increment('stock', $request->quantity_produced);

            // Record Production
            $production = new ManufacturingProduction();
            $production->production_number = 'PROD-' . Str::upper(Str::random(8));
            $production->manufacturing_bill_id = $bom->id;
            $production->quantity_produced = $request->quantity_produced;
            $production->production_date = $request->production_date;
            $production->actual_cost = $bom->total_cost_per_unit * $request->quantity_produced;
            $production->notes = $request->notes;
            $production->produced_by = Auth::id();
            $production->save();

            // Subcontractor Ledger Automation Hook
            if ($request->subcontractor_id && $totalLaborCost > 0) {
                \App\Models\SupplierLedger::record(
                    $request->subcontractor_id,
                    $request->production_date,
                    'debit', // Debit because we owe them for their service
                    'purchase',
                    "Labor / Subcontract Service for Production Run {$production->production_number} (produced {$request->quantity_produced} units)",
                    $totalLaborCost,
                    'production_' . $production->id
                );
            }

            DB::commit();
            return redirect()->route('manufacturing.production.index')->with('success', 'Production run recorded successfully. Stock and Ledger updated.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Production Failed: ' . $e->getMessage())->withInput();
        }
    }
}
