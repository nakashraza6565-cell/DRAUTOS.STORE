<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bundle;
use App\Models\BundleItem;
use App\Models\Product;
use DB;
use PDF;

class BundleController extends Controller
{
    public function index()
    {
        $bundles = Bundle::withCount('items')->orderBy('id', 'DESC')->get();
        return view('backend.bundle.index')->with('bundles', $bundles);
    }

    public function create()
    {
        $products = Product::where('status', 'active')->get();

        // Generate Auto SKU: BNDL-001
        $lastBundle = Bundle::orderBy('id', 'desc')->first();
        $lastId = $lastBundle ? $lastBundle->id : 0;
        $nextId = $lastId + 1;
        $auto_sku = 'BNDL-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

        return view('backend.bundle.create', compact('products', 'auto_sku'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'sku' => 'required|string|unique:bundles,sku',
            'price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'retail_price' => 'nullable|numeric|min:0',
            'walkin_price' => 'nullable|numeric|min:0',
            'salesman_price' => 'nullable|numeric|min:0',
            'product_id' => 'required|array',
            'product_id.*' => 'required|exists:products,id',
            'quantity' => 'required|array',
            'quantity.*' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $bundle = Bundle::create([
                'name' => $request->name,
                'sku' => $request->sku,
                'price' => $request->price,
                'wholesale_price' => $request->wholesale_price,
                'retail_price' => $request->retail_price,
                'walkin_price' => $request->walkin_price,
                'salesman_price' => $request->salesman_price,
                'description' => $request->description,
                'status' => 'active',
            ]);

            foreach ($request->product_id as $key => $pid) {
                BundleItem::create([
                    'bundle_id' => $bundle->id,
                    'product_id' => $pid,
                    'quantity' => $request->quantity[$key],
                ]);
            }

            DB::commit();
            request()->session()->flash('success', 'Bundle successfully created');
            return redirect()->route('bundles.index');

        } catch (\Exception $e) {
            DB::rollback();
            request()->session()->flash('error', 'Error occurred: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        $bundle = Bundle::with('items')->findOrFail($id);
        $products = Product::where('status', 'active')->get();
        return view('backend.bundle.edit', compact('bundle', 'products'));
    }

    public function update(Request $request, $id)
    {
        $bundle = Bundle::findOrFail($id);

        $this->validate($request, [
            'name' => 'required|string',
            'sku' => 'required|string|unique:bundles,sku,'.$id,
            'price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'retail_price' => 'nullable|numeric|min:0',
            'walkin_price' => 'nullable|numeric|min:0',
            'salesman_price' => 'nullable|numeric|min:0',
            'product_id' => 'required|array',
            'product_id.*' => 'required|exists:products,id',
            'quantity' => 'required|array',
            'quantity.*' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $bundle->update([
                'name' => $request->name,
                'sku' => $request->sku,
                'price' => $request->price,
                'wholesale_price' => $request->wholesale_price,
                'retail_price' => $request->retail_price,
                'walkin_price' => $request->walkin_price,
                'salesman_price' => $request->salesman_price,
                'description' => $request->description,
            ]);

            // Sync items (delete old, add new)
            // A smarter way is to update existing, but simple way is delete all and recreate
            $bundle->items()->delete();

            foreach ($request->product_id as $key => $pid) {
                BundleItem::create([
                    'bundle_id' => $bundle->id,
                    'product_id' => $pid,
                    'quantity' => $request->quantity[$key],
                ]);
            }

            DB::commit();
            request()->session()->flash('success', 'Bundle successfully updated');
            return redirect()->route('bundles.index');

        } catch (\Exception $e) {
            DB::rollback();
            request()->session()->flash('error', 'Error occurred: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function destroy($id)
    {
        $bundle = Bundle::findOrFail($id);
        $bundle->delete();
        request()->session()->flash('success', 'Bundle deleted');
        return redirect()->route('bundles.index');
    }

    public function generatePDF($id)
    {
        $bundle = Bundle::with('items.product')->findOrFail($id);
        $fileName = 'Bundle-' . $bundle->sku . '.pdf';
        
        $pdf = PDF::loadView('backend.bundle.pdf', compact('bundle'));
        return $pdf->download($fileName);
    }
}
