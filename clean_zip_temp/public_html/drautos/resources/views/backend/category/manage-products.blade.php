@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Manage Products for Category: <span class="text-dark">{{ $category->title }}</span></h6>
            <a href="{{ route('category.index') }}" class="btn btn-sm btn-secondary shadow-sm"><i class="fas fa-arrow-left fa-sm"></i> Back to Categories</a>
        </div>
        <div class="card-body">
            <div class="alert alert-info border-left-info shadow-sm">
                <i class="fas fa-info-circle mr-2"></i> Use the search bar to find products and the checkboxes to add or remove them from this category.
                @if($category->is_parent != 1)
                    <br><small>Note: Assigning a product to this subcategory will also automatically set its parent category to <strong>{{ $category->parent_info->title ?? 'N/A' }}</strong>.</small>
                @endif
            </div>

            <form action="{{ route('category.products.update', $category->id) }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="productManagerTable" width="100%" cellspacing="0">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th style="width: 50px;">Select</th>
                                <th>Product Name</th>
                                <th>SKU</th>
                                <th>Current Category</th>
                                <th>Current Subcategory</th>
                                <th>Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                            @php
                                $isSelected = ($category->is_parent == 1) 
                                    ? ($product->cat_id == $category->id) 
                                    : ($product->child_cat_id == $category->id);
                            @endphp
                            <tr class="{{ $isSelected ? 'table-primary font-weight-bold' : '' }}">
                                <td class="text-center">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" class="custom-control-input" id="check{{ $product->id }}" {{ $isSelected ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="check{{ $product->id }}"></label>
                                    </div>
                                </td>
                                <td>{{ $product->title }}</td>
                                <td><code class="text-primary">{{ $product->sku ?? 'N/A' }}</code></td>
                                <td>
                                    @if($product->cat_id)
                                        <span class="badge {{ $product->cat_id == ($category->is_parent == 1 ? $category->id : ($category->parent_id ?? 0)) ? 'badge-primary' : 'badge-light border' }}">
                                            {{ $product->cat_info->title ?? 'N/A' }}
                                        </span>
                                    @else
                                        <span class="text-muted small italic">None</span>
                                    @endif
                                </td>
                                <td>
                                    @if($product->child_cat_id)
                                        <span class="badge {{ $product->child_cat_id == ($category->is_parent != 1 ? $category->id : 0) ? 'badge-info' : 'badge-light border' }}">
                                            {{ $product->sub_cat_info->title ?? 'N/A' }}
                                        </span>
                                    @else
                                        <span class="text-muted small italic">None</span>
                                    @endif
                                </td>
                                <td>
                                    @if($product->stock > 0)
                                        <span class="text-success font-weight-bold">{{ $product->stock }}</span>
                                    @else
                                        <span class="text-danger font-weight-bold">Out</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4 border-top pt-4 text-right">
                    <button type="submit" class="btn btn-success btn-lg px-5 shadow"><i class="fas fa-save mr-2"></i> Update Product Assignments</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
<style>
    .table-primary { background-color: rgba(78, 115, 223, 0.05) !important; }
    .custom-control-label { cursor: pointer; }
    #productManagerTable tr { transition: background-color 0.2s; }
    #productManagerTable tr:hover { background-color: rgba(0,0,0,0.02); }
</style>
@endpush

@push('scripts')
<script src="{{asset('backend/vendor/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
<script>
    $(document).ready(function() {
        $('#productManagerTable').DataTable({
            "pageLength": 25,
            "order": [[0, "desc"]], // Show selected ones (checked) first if possible? No, checkbox order is weird.
            "columnDefs": [
                { "orderable": false, "targets": [0] }
            ]
        });

        // Highlight row when checkbox changes
        $('.custom-control-input').change(function() {
            if($(this).is(':checked')) {
                $(this).closest('tr').addClass('table-primary font-weight-bold');
            } else {
                $(this).closest('tr').removeClass('table-primary font-weight-bold');
            }
        });
    });
</script>
@endpush
