<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;

use Illuminate\Support\Str;
use PDF;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $products = Product::with(['cat_info', 'sub_cat_info', 'brand', 'supplier'])
            ->when($request->title, function ($q) use ($request) {
                return $q->where('title', 'LIKE', "%{$request->title}%");
            })
            ->when($request->price, function ($q) use ($request) {
                return $q->where('price', '<=', $request->price);
            })
            ->when($request->cat_id, function ($q) use ($request) {
                return $q->where('cat_id', $request->cat_id);
            })
            ->when($request->brand_id, function ($q) use ($request) {
                return $q->where('brand_id', $request->brand_id);
            })
            ->when($request->stock == 'low', function ($q) {
                return $q->whereRaw('stock <= COALESCE(low_stock_threshold, 0)');
            })
            ->orderBy('id', 'desc')
            ->paginate(5000);

        $categories = Category::where('is_parent', 1)->get();
        return view('backend.product.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $brands = Brand::get();
        $categories = Category::where('is_parent', 1)->get();
        $suppliers = \App\Models\Supplier::where('status', 'active')->get();
        $warehouses = \App\Models\Warehouse::where('status', 'active')->get();
        $units = \App\Models\Unit::all();
        $product_models = \App\Models\ProductModel::all();
        return view('backend.product.create', compact('categories', 'brands', 'suppliers', 'warehouses', 'units', 'product_models'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title'               => 'required|string|max:255',
            'summary'             => 'nullable|string',
            'description'         => 'nullable|string',
            'photo'               => 'nullable|string',
            'size'                => 'nullable|array',
            'size.*'              => 'nullable|string',
            'stock'               => 'required|numeric',
            'cat_id'              => 'required|exists:categories,id',
            'brand_id'            => 'nullable|exists:brands,id',
            'model'               => 'nullable|string|max:255',
            'child_cat_id'        => 'nullable|exists:categories,id',
            'is_featured'         => 'sometimes|in:1',
            'status'              => 'nullable|in:active,inactive',
            'condition'           => 'nullable|in:default,new,hot',
            'price'               => 'required|numeric|min:0',
            'purchase_price'      => 'nullable|numeric|min:0',
            'packaging_cost'      => 'nullable|numeric|min:0',
            'discount'            => 'nullable|numeric|min:0|max:100',
            'sku'                 => 'nullable|string|max:100|unique:products,sku',
            'barcode'             => 'nullable|string|max:255',
            'low_stock_threshold' => 'required|numeric|min:0',
            'suppliers'           => 'required|array',
            'suppliers.*'         => 'exists:suppliers,id',
            'warehouse_id'        => 'nullable|exists:warehouses,id',
            'weight'              => 'nullable|numeric|min:0',
            'batch_number'        => 'nullable|string|max:255',
            'wholesale_price'     => 'nullable|numeric|min:0',
            'retail_price'        => 'nullable|numeric|min:0',
            'walkin_price'        => 'nullable|numeric|min:0',
            'salesman_price'      => 'nullable|numeric|min:0',
            'rack_number'         => 'nullable|string|max:255',
            'shelf_number'        => 'nullable|string|max:255',
            'color'               => 'nullable|string|max:255',
            'type'                => 'nullable|string|max:255',
            'unit'                => 'nullable|string|max:255',
        ]);

        $validatedData['status'] = $request->input('status') ?? 'active';
        $validatedData['condition'] = $request->input('condition') ?? 'default';
        $validatedData['summary'] = $request->input('summary') ?? '';
        $validatedData['photo'] = $request->input('photo') ?? '';
        $validatedData['purchase_price'] = $request->input('purchase_price') ?? 0;
        $validatedData['packaging_cost'] = $request->input('packaging_cost') ?? 0;
        $validatedData['discount'] = $request->input('discount') ?? 0;
        $validatedData['unit_type'] = $request->input('unit_type') ?? 'piece';

        $slug = Str::slug($request->title);
        $count = Product::where('slug', $slug)->count();
        if ($count > 0) {
            $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);
        }
        $validatedData['slug'] = $slug;
        $validatedData['is_featured'] = $request->input('is_featured', 0);

        if ($request->has('size')) {
            $validatedData['size'] = is_array($request->input('size')) ? implode(',', $request->input('size')) : $request->input('size');
        } else {
            $validatedData['size'] = '';
        }

        $product = Product::create($validatedData);

        // Auto-generate SKU & Barcode linked to product ID (Serial Number)
        // Format: 202185-XXX (where XXX is padded ID)
        $serial = str_pad($product->id, 3, '0', STR_PAD_LEFT);
        $newCode = '202185-' . $serial;

        if (empty($product->sku)) {
            $product->sku = $newCode;
        }

        if (empty($product->barcode)) {
            $product->barcode = $newCode;
        }

        if ($product->isDirty()) {
            $product->save();
        }

        if ($request->suppliers) {
            $product->suppliers()->sync($request->suppliers);
        }

        return redirect()->route('product.index')->with('success', 'Product Successfully added');
    }

    public function quickStore(Request $request)
    {
        try {
            $this->validate($request, [
                'title' => 'string|required',
                'cat_id' => 'required|exists:categories,id',
                'price' => 'numeric|required',
                'stock' => 'numeric|required',
                'brand_id' => 'nullable|exists:brands,id',
                'purchase_price' => 'nullable|numeric',
                'unit' => 'nullable|string',
            ]);

            $data = $request->all();
            // Sanitize nullable fields to prevent DB errors
            $data['purchase_price'] = $request->input('purchase_price') ?: 0;
            $data['brand_id'] = $request->input('brand_id') ?: null;
            $data['model'] = $request->input('model') ?: null;
            $data['unit'] = $request->input('unit') ?: 'piece';
            
            // Add defaults for required DB fields not in quick form to prevent SQL errors
            $data['summary'] = '';
            $data['description'] = '';
            $data['photo'] = 'backend/img/thumbnail-default.jpg';
            $data['packaging_cost'] = 0;
            $data['discount'] = 0;
            $data['is_featured'] = 0;
            $data['status'] = 'active';
            $data['condition'] = 'default';
            
            $slug = Str::slug($request->title);
            $count = Product::where('slug', $slug)->count();
            if ($count > 0) {
                $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);
            }
            $data['slug'] = $slug;

            $product = Product::create($data);

            // Sync suppliers if provided
            if ($request->suppliers) {
                $product->suppliers()->sync($request->suppliers);
            }

            // Auto SKU/Barcode
            $serial = str_pad($product->id, 3, '0', STR_PAD_LEFT);
            $newCode = '202185-' . $serial;
            if (empty($product->sku)) $product->sku = $newCode;
            if (empty($product->barcode)) $product->barcode = $newCode;
            $product->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Product added successfully',
                'product' => $product
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    public function searchSimple(Request $request)
    {
        $query = $request->get('q');
        $products = Product::where('title', 'LIKE', "%{$query}%")
            ->orWhere('sku', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get();

        $results = [];
        foreach ($products as $product) {
            $results[] = [
                'id' => $product->title, // Return title as ID so tags:true works correctly for new items
                'text' => $product->title . ' (' . $product->sku . ')',
                'is_existing' => true
            ];
        }

        return response()->json($results);
    }

    public function edit($id)
    {
        $brands = Brand::get();
        $product = Product::with('suppliers')->findOrFail($id);
        $categories = Category::where('is_parent', 1)->get();
        $suppliers = \App\Models\Supplier::where('status', 'active')->get();
        $warehouses = \App\Models\Warehouse::where('status', 'active')->get();
        $units = \App\Models\Unit::all();
        $product_models = \App\Models\ProductModel::all();

        return view('backend.product.edit', compact('product', 'brands', 'categories', 'suppliers', 'warehouses', 'units', 'product_models'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validatedData = $request->validate([
            'title'               => 'required|string|max:255',
            'summary'             => 'nullable|string',
            'description'         => 'nullable|string',
            'photo'               => 'nullable|string',
            'size'                => 'nullable|array',
            'size.*'              => 'nullable|string',
            'stock'               => 'required|numeric',
            'cat_id'              => 'required|exists:categories,id',
            'child_cat_id'        => 'nullable|exists:categories,id',
            'is_featured'         => 'sometimes|in:1',
            'brand_id'            => 'nullable|exists:brands,id',
            'model'               => 'nullable|string|max:255',
            'status'              => 'nullable|in:active,inactive',
            'condition'           => 'nullable|in:default,new,hot',
            'price'               => 'required|numeric|min:0',
            'purchase_price'      => 'nullable|numeric|min:0',
            'packaging_cost'      => 'nullable|numeric|min:0',
            'discount'            => 'nullable|numeric|min:0|max:100',
            'sku'                 => 'nullable|string|max:100|unique:products,sku,' . $id,
            'barcode'             => 'nullable|string|max:255',
            'low_stock_threshold' => 'required|numeric|min:0',
            'suppliers'           => 'required|array',
            'suppliers.*'         => 'exists:suppliers,id',
            'warehouse_id'        => 'nullable|exists:warehouses,id',
            'weight'              => 'nullable|numeric|min:0',
            'batch_number'        => 'nullable|string|max:255',
            'wholesale_price'     => 'nullable|numeric|min:0',
            'retail_price'        => 'nullable|numeric|min:0',
            'walkin_price'        => 'nullable|numeric|min:0',
            'salesman_price'      => 'nullable|numeric|min:0',
            'rack_number'         => 'nullable|string|max:255',
            'shelf_number'        => 'nullable|string|max:255',
            'color'               => 'nullable|string|max:255',
            'type'                => 'nullable|string|max:255',
            'unit'                => 'nullable|string|max:255',
        ]);

        $validatedData['status'] = $request->input('status') ?? $product->status;
        $validatedData['condition'] = $request->input('condition') ?? $product->condition;
        $validatedData['summary'] = $request->input('summary') ?? $product->summary ?? '';
        $validatedData['photo'] = $request->input('photo') ?? $product->photo ?? '';
        $validatedData['purchase_price'] = $request->input('purchase_price') ?? $product->purchase_price ?? 0;
        $validatedData['packaging_cost'] = $request->input('packaging_cost') ?? $product->packaging_cost ?? 0;
        $validatedData['discount'] = $request->input('discount') ?? $product->discount ?? 0;
        $validatedData['unit_type'] = $request->input('unit_type') ?? $product->unit_type ?? 'piece';
        $validatedData['unit'] = $request->input('unit') ?? $product->unit ?? null;

        $validatedData['is_featured'] = $request->input('is_featured', 0);

        if ($request->has('size')) {
            $validatedData['size'] = is_array($request->input('size')) ? implode(',', $request->input('size')) : $request->input('size');
        } else {
            $validatedData['size'] = '';
        }
        // Generate Unique 8-digit SKU if cleared/empty
        if (empty($validatedData['sku'])) {
            do {
                $sku = str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);
            } while (Product::where('sku', $sku)->where('id', '!=', $id)->exists());
            $validatedData['sku'] = $sku;
        }

        // Auto-generate Barcode linked to product ID if cleared/empty
        if (empty($validatedData['barcode'])) {
            $validatedData['barcode'] = str_pad($id, 8, '0', STR_PAD_LEFT);
        }

        $status = $product->update($validatedData);
        if ($request->has('suppliers')) {
            $product->suppliers()->sync($request->suppliers);
        }

        return redirect()->route('product.index')->with('success', 'Product Successfully updated');
    }

    public function priceList(Request $request)
    {
        $products = Product::with(['cat_info', 'brand', 'suppliers'])
            ->when($request->title, function ($q) use ($request) {
                return $q->where('title', 'LIKE', "%{$request->title}%");
            })
            ->when($request->cat_id, function ($q) use ($request) {
                return $q->where('cat_id', $request->cat_id);
            })
            ->when($request->supplier_id, function ($q) use ($request) {
                return $q->whereHas('suppliers', function ($sub) use ($request) {
                    $sub->where('suppliers.id', $request->supplier_id);
                });
            })
            ->orderBy('title', 'asc')
            ->paginate(5000);

        $categories = \App\Models\Category::where('is_parent', 1)->get();
        $suppliers = \App\Models\Supplier::where('status', 'active')->orderBy('name', 'asc')->get();
        return view('backend.product.price-list', compact('products', 'categories', 'suppliers'));
    }

    public function lowStock()
    {
        // Treat null threshold as 0
        $products = Product::with(['cat_info', 'sub_cat_info', 'brand', 'supplier'])
            ->whereRaw('stock <= COALESCE(low_stock_threshold, 0)')
            ->orderBy('stock', 'ASC')
            ->paginate(5000);
        return view('backend.product.index', compact('products'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $status = $product->delete();

        $message = $status
            ? 'Product successfully deleted'
            : 'Error while deleting product';

        return redirect()->route('product.index')->with(
            $status ? 'success' : 'error',
            $message
        );
    }

    public function storeUnit(Request $request)
    {
        try {
            $request->validate(['name' => 'required|string|max:255']);
            $unit = \App\Models\Unit::create(['name' => $request->name]);
            return response()->json(['status' => 'success', 'unit' => $unit]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    public function storeModel(Request $request)
    {
        try {
            $request->validate(['name' => 'required|string|max:255']);
            $model = \App\Models\ProductModel::create(['name' => $request->name]);
            return response()->json(['status' => 'success', 'model' => $model]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    public function updatePhoto(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);
            $request->validate([
                'photo' => 'required|string' // This application uses Laravel File Manager URLs
            ]);

            $product->photo = $request->photo;
            $product->save();

            return response()->json(['status' => 'success', 'photo' => $product->photo]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Inline price update from product list table.
     * Accepts price_type (selling|wholesale|retail|walkin|salesman) and value.
     */
    public function updatePrice(Request $request, $id)
    {
        try {
            $allowed = ['price', 'purchase_price', 'wholesale_price', 'retail_price', 'walkin_price', 'salesman_price'];

            $request->validate([
                'price_type' => 'required|in:' . implode(',', $allowed),
                'value'      => 'required|numeric|min:0',
            ]);

            $product = Product::findOrFail($id);
            $field   = $request->price_type;
            $product->$field = $request->value;
            $product->save();

            return response()->json([
                'status'  => 'success',
                'field'   => $field,
                'value'   => $product->$field,
                'message' => 'Price updated successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    public function updateTitle(Request $request, $id)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
            ]);

            $product = Product::findOrFail($id);
            $product->title = $request->title;
            
            // Update slug
            $slug = Str::slug($request->title);
            $count = Product::where('slug', $slug)->where('id', '!=', $id)->count();
            if ($count > 0) {
                $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);
            }
            $product->slug = $slug;
            
            $product->save();

            return response()->json([
                'status'  => 'success',
                'title'   => $product->title,
                'message' => 'Title updated successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    public function priceListPDF(Request $request)
    {
        $products = Product::with(['cat_info', 'brand', 'suppliers'])
            ->when($request->title, function ($q) use ($request) {
                return $q->where('title', 'LIKE', "%{$request->title}%");
            })
            ->when($request->cat_id, function ($q) use ($request) {
                return $q->where('cat_id', $request->cat_id);
            })
            ->when($request->supplier_id, function ($q) use ($request) {
                return $q->whereHas('suppliers', function ($sub) use ($request) {
                    $sub->where('suppliers.id', $request->supplier_id);
                });
            })
            ->orderBy('title', 'asc')
            ->get(); // Get all for PDF, don't paginate

        $pdf = PDF::loadView('backend.product.price-list-pdf', compact('products'));
        return $pdf->download('Price-List-' . date('Y-m-d') . '.pdf');
    }
}
