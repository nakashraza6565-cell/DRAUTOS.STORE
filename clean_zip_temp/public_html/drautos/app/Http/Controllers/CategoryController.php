<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::where('is_parent', 1)->with('child_cat')->orderBy('title', 'ASC')->paginate(5000);
        $all_products = \App\Models\Product::with(['cat_info', 'sub_cat_info'])->orderBy('title', 'ASC')->get();
        return view('backend.category.index', compact('categories', 'all_products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $parent_id = $request->query('parent_id');
        $parent_cats = Category::where('is_parent', 1)->orderBy('title', 'ASC')->get();
        return view('backend.category.create', compact('parent_cats', 'parent_id'));
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
            'summary' => 'nullable|string',
            'photo' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'is_parent' => 'sometimes|in:1',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $slug = \Illuminate\Support\Str::slug($request->title);
        $count = Category::where('slug', $slug)->count();
        if ($count > 0) {
            $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);
        }
        $validatedData['slug'] = $slug;
        $validatedData['is_parent'] = $request->input('is_parent', 0);
        $validatedData['added_by'] = auth()->id();

        $category = Category::create($validatedData);

        $message = $category
            ? 'Category successfully added'
            : 'Error occurred, Please try again!';

        return redirect()->route('category.index')->with(
            $category ? 'success' : 'error',
            $message
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Implement if needed
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $parent_cats = Category::where('is_parent', 1)->get();
        return view('backend.category.edit', compact('category', 'parent_cats'));
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
        $category = Category::findOrFail($id);

        $validatedData = $request->validate([
            'title' => 'required|string',
            'summary' => 'nullable|string',
            'photo' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'is_parent' => 'sometimes|in:1',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $validatedData['is_parent'] = $request->input('is_parent', 0);

        $status = $category->update($validatedData);

        $message = $status
            ? 'Category successfully updated'
            : 'Error occurred, Please try again!';

        return redirect()->route('category.index')->with(
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
        $category = Category::findOrFail($id);
        $child_cat_id = Category::where('parent_id', $id)->pluck('id');

        $status = $category->delete();

        if ($status && $child_cat_id->count() > 0) {
            Category::shiftChild($child_cat_id);
        }

        $message = $status
            ? 'Category successfully deleted'
            : 'Error while deleting category';

        return redirect()->route('category.index')->with(
            $status ? 'success' : 'error',
            $message
        );
    }

    /**
     * Get child categories by parent ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getChildByParent(Request $request)
    {
        $category = Category::findOrFail($request->id);
        $child_cat = Category::getChildByParentID($request->id);

        if ($child_cat->count() <= 0) {
            return response()->json(['status' => false, 'msg' => '', 'data' => null]);
        }

        return response()->json(['status' => true, 'msg' => '', 'data' => $child_cat]);
    }

    public function manageProducts($id)
    {
        $category = Category::findOrFail($id);
        // Get all products. We will show which ones are in this category.
        $products = \App\Models\Product::orderBy('title', 'ASC')->get();
        
        return view('backend.category.manage-products', compact('category', 'products'));
    }

    public function updateProducts(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $product_ids = $request->input('product_ids', []);
        $append = $request->input('append', false);

        if($append) {
            // Drag and Drop logic: just add these products to this category
            if($category->is_parent == 1) {
                \App\Models\Product::whereIn('id', $product_ids)->update(['cat_id' => $id, 'child_cat_id' => null]);
            } else {
                \App\Models\Product::whereIn('id', $product_ids)->update([
                    'cat_id' => $category->parent_id,
                    'child_cat_id' => $id
                ]);
            }
        } else {
            // Bulk Manager logic: replace all
            if($category->is_parent == 1) {
                \App\Models\Product::where('cat_id', $id)->update(['cat_id' => null, 'child_cat_id' => null]);
                if(!empty($product_ids)) {
                    \App\Models\Product::whereIn('id', $product_ids)->update(['cat_id' => $id]);
                }
            } else {
                \App\Models\Product::where('child_cat_id', $id)->update(['child_cat_id' => null]);
                if(!empty($product_ids)) {
                    \App\Models\Product::whereIn('id', $product_ids)->update([
                        'cat_id' => $category->parent_id,
                        'child_cat_id' => $id
                    ]);
                }
            }
        }

        if($request->ajax()) {
            return response()->json(['status' => true, 'msg' => 'Products updated successfully']);
        }

        return redirect()->route('category.index')->with('success', 'Products successfully updated for category ' . $category->title);
    }
    public function printCatalog()
    {
        ini_set('max_execution_time', 300);
        $categories = Category::where('is_parent', 1)
            ->with(['child_cat.products', 'products'])
            ->orderBy('title', 'ASC')
            ->get();
            
        // Clear any previous output buffers to avoid corrupted PDF
        if (ob_get_length()) ob_end_clean();
        
        $pdf = \PDF::loadView('backend.category.pdf', compact('categories'));
        
        return $pdf->download('Digital_Catalog_' . date('Y-m-d') . '.pdf');
    }

    public function quickStore(Request $request)
    {
        \Log::info('QuickStore Request:', $request->all());
        $request->validate([
            'title' => 'required|string',
            'is_parent' => 'nullable',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $data = $request->all();
        
        $slug = \Illuminate\Support\Str::slug($request->title);
        $count = Category::where('slug', $slug)->count();
        if ($count > 0) {
            $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);
        }
        $data['slug'] = $slug;
        
        $data['status'] = 'active';
        $data['is_parent'] = $request->input('is_parent', 0);
        $data['added_by'] = auth()->id();

        $category = Category::create($data);

        if ($category) {
            return response()->json([
                'status' => 'success',
                'category' => $category
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create category'
            ]);
        }
    }
}
