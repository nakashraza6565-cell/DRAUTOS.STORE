<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Support\Str;
use App\Helpers\helpers;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $brands = Brand::with('products')->orderBy('title', 'ASC')->paginate(5000);
        $all_products = \App\Models\Product::with(['cat_info', 'brand'])->orderBy('title', 'ASC')->get();
        return view('backend.brand.index', compact('brands', 'all_products'));
    }

    public function updateProducts(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);
        $product_ids = $request->input('product_ids', []);
        
        \App\Models\Product::whereIn('id', $product_ids)->update(['brand_id' => $id]);

        if($request->ajax()) {
            return response()->json(['status' => true, 'msg' => 'Brand updated successfully']);
        }

        return redirect()->route('brand.index')->with('success', 'Brand updated for selected products');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.brand.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string',
            'company_name' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $slug = generateUniqueSlug($request->title, Brand::class);
        $validatedData['slug'] = $slug;

        $brand = Brand::create($validatedData);

        $message = $brand
            ? 'Brand successfully created'
            : 'Error, Please try again';

        return redirect()->route('brand.index')->with(
            $brand ? 'success' : 'error',
            $message
        );
    }

    public function quickStore(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'company_name' => 'nullable|string|max:255',
            ]);

            $slug = generateUniqueSlug($request->title, Brand::class);
            
            $brand = Brand::create([
                'title' => $request->title,
                'company_name' => $request->company_name,
                'slug' => $slug,
                'status' => 'active'
            ]);

            return response()->json(['status' => 'success', 'brand' => $brand]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $brand = Brand::find($id);

        if (!$brand) {
            return redirect()->back()->with('error', 'Brand not found');
        }

        return view('backend.brand.edit', compact('brand'));
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $brand = Brand::find($id);

        if (!$brand) {
            return redirect()->back()->with('error', 'Brand not found');
        }

        $validatedData = $request->validate([
            'title' => 'required|string',
            'status' => 'required|in:active,inactive',
        ]);

        $status = $brand->update($validatedData);

        $message = $status
            ? 'Brand successfully updated'
            : 'Error, Please try again';

        return redirect()->route('brand.index')->with(
            $status ? 'success' : 'error',
            $message
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $brand = Brand::find($id);

        if (!$brand) {
            return redirect()->back()->with('error', 'Brand not found');
        }

        $status = $brand->delete();

        $message = $status
            ? 'Brand successfully deleted'
            : 'Error, Please try again';

        return redirect()->route('brand.index')->with(
            $status ? 'success' : 'error',
            $message
        );
    }
}
