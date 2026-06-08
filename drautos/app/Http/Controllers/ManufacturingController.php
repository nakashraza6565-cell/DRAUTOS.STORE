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
            $suppliers = \App\Models\Supplier::where('status', 'active')->orderBy('name')->get();
            return view('backend.manufacturing.create', compact('products', 'factors', 'suppliers'))->render();
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
            'overheads.*.type' => 'required|string',
            'overheads.*.subcontractor_id' => 'nullable|exists:suppliers,id',
            'overheads.*.per_piece_cost' => 'nullable|numeric|min:0',
            'overheads.*.cost' => 'required|numeric|min:0',
            'status' => 'required|in:wip,completed,inactive',
            'subcontractor_id' => 'nullable',
        ]);

        DB::beginTransaction();
        try {
            // Check Stock Availability first if status is completed
            if ($request->status === 'completed') {
                foreach ($request->components as $componentData) {
                    $rawId = $componentData['product_id'];
                    $qty = (float) $componentData['quantity'];
                    
                    if (str_starts_with($rawId, 'factor_')) {
                        $factorId = (int) str_replace('factor_', '', $rawId);
                        $factor = \App\Models\ProductionFactor::find($factorId);
                        if ($factor && $factor->type === 'material') {
                            if ($factor->stock_quantity < $qty) {
                                throw new \Exception("Insufficient stock for material: {$factor->name}. Required: {$qty}, Available: {$factor->stock_quantity}");
                            }
                        }
                    } else {
                        $productId = (int) str_replace('product_', '', $rawId);
                        $product = Product::find($productId);
                        if ($product) {
                            if ($product->stock < $qty) {
                                throw new \Exception("Insufficient stock for product: {$product->title}. Required: {$qty}, Available: {$product->stock}");
                            }
                        }
                    }
                }
            }

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
                            'cost' => $costVal,
                            'per_piece_cost' => !empty($ov['per_piece_cost']) ? (float) $ov['per_piece_cost'] : 0,
                            'subcontractor_id' => !empty($ov['subcontractor_id']) ? (int) $ov['subcontractor_id'] : null
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
            $bom->subcontractor_id = $request->subcontractor_id;
            $bom->notes = $request->notes;
            $bom->status = $request->status ?? 'wip';
            $bom->created_by = Auth::id();
            $bom->save();

            // Add Components
            $purchasesBySupplier = [];
            foreach ($request->components as $componentData) {
                $rawId = $componentData['product_id'];
                
                if (str_starts_with($rawId, 'factor_')) {
                    $factorId = (int) str_replace('factor_', '', $rawId);
                    $factor = \App\Models\ProductionFactor::find($factorId);
                    $costPerUnit = $factor->cost_price ?? 0;
                    $unit = $factor->unit ?? 'pcs';
                    $ingredientType = 'App\\Models\\ProductionFactor';
                    $componentProductId = $factorId;

                    $purchaseSupplierId = $componentData['purchase_supplier_id'] ?? null;
                    if ($purchaseSupplierId) {
                        if (!isset($purchasesBySupplier[$purchaseSupplierId])) {
                            $purchasesBySupplier[$purchaseSupplierId] = ['total' => 0, 'items' => [], 'descriptions' => []];
                        }
                        $purchasesBySupplier[$purchaseSupplierId]['items'][] = [
                            'factor_id' => $factorId,
                            'item_name' => null,
                            'quantity' => $componentData['quantity'],
                            'unit_price' => $costPerUnit,
                            'total' => $costPerUnit * $componentData['quantity']
                        ];
                        $purchasesBySupplier[$purchaseSupplierId]['total'] += ($costPerUnit * $componentData['quantity']);
                        $purchasesBySupplier[$purchaseSupplierId]['descriptions'][] = $componentData['quantity'] . ' ' . $unit . ' of ' . $factor->name;
                        
                        // Immediately increment stock so the BOM consumption matches it perfectly
                        $factor->increment('stock_quantity', $componentData['quantity']);
                    }
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

            // If completed, deduct stock and add finished product
            if ($bom->status === 'completed') {
                $totalMaterialIngredientsCost = 0;

                foreach ($bom->components as $component) {
                    if ($component->ingredient_type === 'App\\Models\\ProductionFactor') {
                        $factor = \App\Models\ProductionFactor::find($component->component_product_id);
                        if ($factor) {
                            if ($factor->type === 'material') {
                                $factor->decrement('stock_quantity', $component->quantity_required);
                                $totalMaterialIngredientsCost += $component->cost_per_unit * $component->quantity_required;
                            } elseif ($factor->type === 'labor') {
                                // Labor factors cost
                                $totalMaterialIngredientsCost += $component->cost_per_unit * $component->quantity_required;
                            }
                        }
                    } else {
                        $product = Product::find($component->component_product_id);
                        if ($product) {
                            $product->decrement('stock', $component->quantity_required);
                            $totalMaterialIngredientsCost += $component->cost_per_unit * $component->quantity_required;
                        }
                    }
                }

            }

            // Decoupled Multi-Subcontractor Ledger Hook & Invoicing
            $details = $bom->overhead_details ?? [];
            $recalcSupplierIds = [];
            foreach ($details as $ov) {
                $subId = $ov['subcontractor_id'] ?? null;
                $costVal = (float) ($ov['cost'] ?? 0);
                if ($subId && $costVal > 0) {
                    $recalcSupplierIds[] = (int) $subId;
                    $overheadName = $ov['name'] ?? ucfirst(str_replace('_', ' ', $ov['type'] ?? 'overhead'));

                    if (!isset($purchasesBySupplier[$subId])) {
                        $purchasesBySupplier[$subId] = ['total' => 0, 'items' => [], 'descriptions' => []];
                    }
                    $purchasesBySupplier[$subId]['items'][] = [
                        'factor_id' => null,
                        'item_name' => "Subcontract Service: " . $overheadName,
                        'quantity' => 1,
                        'unit_price' => $costVal,
                        'total' => $costVal
                    ];
                    $purchasesBySupplier[$subId]['total'] += $costVal;
                    $purchasesBySupplier[$subId]['descriptions'][] = "Subcontract Service ({$overheadName})";
                }
            }

            // Generate Invoices for grouped purchases
            foreach ($purchasesBySupplier as $suppId => $data) {
                if ($data['total'] > 0) {
                    $recalcSupplierIds[] = (int) $suppId;
                    
                    $purchase = \App\Models\RawMaterialPurchase::create([
                        'invoice_number' => 'TMP-' . time() . rand(100, 999),
                        'supplier_id' => $suppId,
                        'purchase_date' => now()->toDateString(),
                        'total_amount' => $data['total'],
                        'notes' => "Auto-generated from BOM {$bom->bom_number}",
                        'manufacturing_bill_id' => $bom->id
                    ]);
                    $purchase->invoice_number = 'RMP-' . date('Ymd') . '-' . str_pad($purchase->id, 4, '0', STR_PAD_LEFT);
                    $purchase->save();

                    foreach ($data['items'] as $item) {
                        \App\Models\RawMaterialPurchaseItem::create([
                            'purchase_id' => $purchase->id,
                            'factor_id' => $item['factor_id'],
                            'item_name' => $item['item_name'],
                            'quantity' => $item['quantity'],
                            'unit_price' => $item['unit_price'],
                            'total' => $item['total']
                        ]);
                    }

                    $description = "Purchased (Invoice: {$purchase->invoice_number}): " . implode(', ', $data['descriptions']);
                    
                    \App\Models\SupplierLedger::record(
                        $suppId,
                        now()->toDateString(),
                        'debit',
                        'purchase',
                        $description,
                        $data['total'],
                        $bom->id
                    );
                }
            }
            foreach (array_unique($recalcSupplierIds) as $supplierId) {
                if ($supplierId) {
                    \App\Models\SupplierLedger::updateBalance($supplierId);
                }
            }

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
        $suppliers = \App\Models\Supplier::where('status', 'active')->orderBy('name')->get();
        return view('backend.manufacturing.edit', compact('bom', 'products', 'factors', 'suppliers'));
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
            'overheads.*.type' => 'required|string',
            'overheads.*.subcontractor_id' => 'nullable|exists:suppliers,id',
            'overheads.*.per_piece_cost' => 'nullable|numeric|min:0',
            'overheads.*.cost' => 'required|numeric|min:0',
            'status' => 'required|in:wip,completed,inactive',
            'subcontractor_id' => 'nullable',
        ]);

        $wasCompleted = $bom->status === 'completed';
        $isCompleted = $request->status === 'completed';

        DB::beginTransaction();
        try {
            // Check Stock Availability first if transitioning to completed
            if (!$wasCompleted && $isCompleted) {
                foreach ($request->components as $componentData) {
                    $rawId = $componentData['product_id'];
                    $qty = (float) $componentData['quantity'];
                    
                    if (str_starts_with($rawId, 'factor_')) {
                        $factorId = (int) str_replace('factor_', '', $rawId);
                        $factor = \App\Models\ProductionFactor::find($factorId);
                        if ($factor && $factor->type === 'material') {
                            if ($factor->stock_quantity < $qty) {
                                throw new \Exception("Insufficient stock for material: {$factor->name}. Required: {$qty}, Available: {$factor->stock_quantity}");
                            }
                        }
                    } else {
                        $productId = (int) str_replace('product_', '', $rawId);
                        $product = Product::find($productId);
                        if ($product) {
                            if ($product->stock < $qty) {
                                throw new \Exception("Insufficient stock for product: {$product->title}. Required: {$qty}, Available: {$product->stock}");
                            }
                        }
                    }
                }
            }

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
                            'cost' => $costVal,
                            'per_piece_cost' => !empty($ov['per_piece_cost']) ? (float) $ov['per_piece_cost'] : 0,
                            'subcontractor_id' => !empty($ov['subcontractor_id']) ? (int) $ov['subcontractor_id'] : null
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
            $bom->subcontractor_id = $request->subcontractor_id;
            $bom->notes = $request->notes;
            $bom->status = $request->status ?? 'wip';
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

            // Perform stock actions if transitioning to completed
            if (!$wasCompleted && $isCompleted) {
                $totalMaterialIngredientsCost = 0;

                foreach ($bom->components as $component) {
                    if ($component->ingredient_type === 'App\\Models\\ProductionFactor') {
                        $factor = \App\Models\ProductionFactor::find($component->component_product_id);
                        if ($factor) {
                            if ($factor->type === 'material') {
                                $factor->decrement('stock_quantity', $component->quantity_required);
                                $totalMaterialIngredientsCost += $component->cost_per_unit * $component->quantity_required;
                            } elseif ($factor->type === 'labor') {
                                $totalMaterialIngredientsCost += $component->cost_per_unit * $component->quantity_required;
                            }
                        }
                    } else {
                        $product = Product::find($component->component_product_id);
                        if ($product) {
                            $product->decrement('stock', $component->quantity_required);
                            $totalMaterialIngredientsCost += $component->cost_per_unit * $component->quantity_required;
                        }
                    }
                }

            }

            // Decoupled Multi-Subcontractor Ledger Hook (Always post/update ledger immediately regardless of status)
            $recalcSupplierIds = [];

            // 1. Find and delete all old ledger entries for this BOM
            $oldLedgers = \App\Models\SupplierLedger::where('reference_id', $bom->id)
                ->where('category', 'purchase')
                ->get();
            foreach ($oldLedgers as $ol) {
                $recalcSupplierIds[] = $ol->supplier_id;
                $ol->delete();
            }

            // 2. Record new ledger entries for each overhead line with a subcontractor selected
            $details = $bom->overhead_details ?? [];
            foreach ($details as $ov) {
                $subId = $ov['subcontractor_id'] ?? null;
                $costVal = (float) ($ov['cost'] ?? 0);
                if ($subId && $costVal > 0) {
                    $recalcSupplierIds[] = (int) $subId;
                    $overheadName = $ov['name'] ?? ucfirst(str_replace('_', ' ', $ov['type'] ?? 'overhead'));
                    
                    \App\Models\SupplierLedger::record(
                        $subId,
                        now()->toDateString(),
                        'debit',
                        'purchase',
                        "Subcontract Service ({$overheadName}) for BOM {$bom->bom_number} (produced {$bom->batch_quantity} units)",
                        $costVal,
                        $bom->id
                    );
                }
            }

            // 3. Recalculate running balance for all unique affected subcontractors
            foreach (array_unique($recalcSupplierIds) as $supplierId) {
                if ($supplierId) {
                    \App\Models\SupplierLedger::updateBalance($supplierId);
                }
            }

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

    /**
     * Clone an existing recipe to start a new WIP batch.
     */
    public function cloneRecipe($id)
    {
        $bom = ManufacturingBill::with('components')->findOrFail($id);
        
        $newBom = new ManufacturingBill();
        $newBom->bom_number = 'BOM-' . strtoupper(uniqid());
        $newBom->product_id = $bom->product_id;
        $newBom->batch_quantity = $bom->batch_quantity;
        $newBom->machining_cost = $bom->machining_cost;
        $newBom->labour_cost = $bom->labour_cost;
        $newBom->packaging_cost = $bom->packaging_cost;
        $newBom->overhead_cost = $bom->overhead_cost;
        $newBom->overhead_details = $bom->overhead_details;
        $newBom->subcontractor_id = $bom->subcontractor_id;
        $newBom->notes = "Cloned from {$bom->bom_number}. " . $bom->notes;
        $newBom->status = 'wip';
        $newBom->created_by = Auth::id();
        $newBom->save();

        foreach ($bom->components as $component) {
            $newComp = new ManufacturingBillComponent();
            $newComp->manufacturing_bill_id = $newBom->id;
            $newComp->component_product_id = $component->component_product_id;
            $newComp->ingredient_type = $component->ingredient_type;
            $newComp->quantity_required = $component->quantity_required;
            $newComp->unit = $component->unit;
            $newComp->cost_per_unit = $component->cost_per_unit;
            $newComp->total_cost = $component->total_cost;
            $newComp->save();
        }

        $newBom->material_cost = $bom->material_cost;
        $newBom->total_cost_per_unit = $bom->total_cost_per_unit;
        $newBom->save();

        return redirect()->route('manufacturing.index')->with('success', "Recipe cloned successfully as {$newBom->bom_number} (WIP). Ready to edit!");
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
                    $production->id
                );
            }

            DB::commit();
            return redirect()->route('manufacturing.production.index')->with('success', 'Production run recorded successfully. Stock and Ledger updated.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Production Failed: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Fetch the most recent BOM for a specific product via AJAX.
     */
    public function getPreviousBom($product_id)
    {
        $bom = ManufacturingBill::with(['components'])->where('product_id', $product_id)->latest()->first();
        if ($bom) {
            return response()->json(['status' => 'success', 'bom' => $bom]);
        }
        return response()->json(['status' => 'error', 'message' => 'No previous BOM found.']);
    }
}
