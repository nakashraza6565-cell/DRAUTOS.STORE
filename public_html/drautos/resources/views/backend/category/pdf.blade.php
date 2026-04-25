<!DOCTYPE html>
<html>
<head>
    <title>Digital Catalog</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #4e73df; padding-bottom: 10px; }
        .header h1 { color: #4e73df; margin-bottom: 5px; }
        .category-block { margin-bottom: 25px; page-break-inside: avoid; }
        .parent-title { background: #f8fafc; color: #1e293b; padding: 8px 12px; border-left: 4px solid #4e73df; font-weight: bold; font-size: 16px; margin-bottom: 10px; }
        .subcategory-block { margin-left: 20px; margin-bottom: 15px; }
        .sub-title { color: #4e73df; font-weight: bold; font-size: 14px; margin-bottom: 5px; border-bottom: 1px solid #e2e8f0; }
        .product-list { margin-left: 15px; list-style-type: none; padding-left: 0; }
        .product-item { padding: 3px 0; border-bottom: 1px dotted #cbd5e1; display: block; }
        .product-name { font-weight: normal; }
        .no-items { color: #94a3b8; font-style: italic; font-size: 11px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #94a3b8; padding: 10px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Digital Catalog</h1>
        <p>Product Listing by Category Hierarchy</p>
        <p><small>Generated on: {{ date('F d, Y') }}</small></p>
    </div>

    @foreach($categories as $category)
        <div class="category-block">
            <div class="parent-title">{{ $category->title }}</div>
            
            {{-- Products directly under parent --}}
            @if($category->products->count() > 0)
                <div class="product-list" style="margin-bottom: 10px;">
                    @foreach($category->products as $product)
                        <div class="product-item">
                            <span class="product-name">- {{ $product->title }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Subcategories --}}
            @if($category->child_cat->count() > 0)
                @foreach($category->child_cat as $child)
                    <div class="subcategory-block">
                        <div class="sub-title">{{ $child->title }}</div>
                        @if($child->products->count() > 0)
                            <div class="product-list">
                                @foreach($child->products as $product)
                                    <div class="product-item">
                                        <span class="product-name">  * {{ $product->title }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="no-items">No products assigned to this subcategory.</div>
                        @endif
                    </div>
                @endforeach
            @endif

            @if($category->products->count() == 0 && $category->child_cat->count() == 0)
                <div class="no-items" style="margin-left: 20px;">No products or subcategories found in this category.</div>
            @endif
        </div>
    @endforeach

    <div class="footer">
        &copy; {{ date('Y') }} Danyal Auto Store - All Rights Reserved.
    </div>
</body>
</html>
