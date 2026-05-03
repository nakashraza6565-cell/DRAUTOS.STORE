@extends('backend.layouts.master')

@section('main-content')
<style>
    .sleek-input {
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 4px 8px;
        font-weight: 700;
        color: #1e293b;
        background: #f8fafc;
        box-shadow: none;
        transition: all 0.2s;
    }
    .sleek-input:focus {
        border-color: #4e73df;
        background: #fff;
        outline: none;
        box-shadow: 0 0 0 2px rgba(78, 115, 223, 0.2);
    }
    .sleek-input-group {
        display: flex;
        align-items: center;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        overflow: hidden;
        transition: all 0.2s;
    }
    .sleek-input-group:focus-within {
        border-color: #4e73df;
        background: #fff;
        box-shadow: 0 0 0 2px rgba(78, 115, 223, 0.2);
    }
    .sleek-input-group .prefix {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        padding: 4px 8px;
    }
    .sleek-input-group input {
        border: none;
        background: transparent;
        font-weight: 700;
        color: #1e293b;
        padding: 4px 8px 4px 0;
        width: 100%;
    }
    .sleek-input-group input:focus {
        outline: none;
    }
</style>

<div class="row">
    <div class="col-md-12">
        @include('backend.layouts.notification')
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex align-items-center justify-content-between flex-wrap" style="gap:8px;">
        <h6 class="m-0 font-weight-bold text-primary">
            @if($salesOrder->is_priority)
                <i class="fas fa-star text-warning mr-1"></i>
            @endif
            Sale Order: {{$salesOrder->order_number}}
        </h6>
        <div class="d-flex" style="gap:6px; flex-wrap:wrap;">
            <a href="{{route('sales-orders.thermal', $salesOrder->id)}}" target="_blank" class="btn btn-info btn-sm shadow-sm">
                <i class="fas fa-print mr-1"></i> Print
            </a>
            <a href="{{route('sales-orders.index')}}" class="btn btn-secondary btn-sm">Back</a>
        </div>
    </div>

    <div class="card-body">

        {{-- Top Info Row --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="p-3 border rounded bg-light h-100">
                    <h6 class="font-weight-bold mb-2 small text-uppercase text-muted">Customer</h6>
                    <div class="font-weight-bold">{{$salesOrder->user->name ?? 'Guest'}}</div>
                    <div class="text-muted small">{{$salesOrder->user->phone ?? 'N/A'}}</div>
                    @if($salesOrder->user && $salesOrder->user->city)
                        <div class="mt-1"><i class="fas fa-map-marker-alt text-danger mr-1"></i><small>{{$salesOrder->user->city}}</small></div>
                    @endif
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 border rounded bg-light h-100">
                    <h6 class="font-weight-bold mb-2 small text-uppercase text-muted">Order Info</h6>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small text-muted">Status</span>
                        @if($salesOrder->status=='pending') <span class="badge badge-warning">Pending</span>
                        @elseif($salesOrder->status=='partially_delivered') <span class="badge badge-info">Partial</span>
                        @elseif($salesOrder->status=='delivered') <span class="badge badge-success">Delivered</span>
                        @else <span class="badge badge-secondary">{{$salesOrder->status}}</span>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small text-muted">Date</span>
                        <span class="small">{{$salesOrder->created_at->format('d M Y, h:i A')}}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="small text-muted font-weight-bold">Assign Staff</span>
                        <form action="{{route('sales-orders.assign-staff', $salesOrder->id)}}" method="POST">
                            @csrf
                            <select name="staff_id" class="form-control form-control-sm" onchange="this.form.submit()" style="min-width:140px;">
                                @foreach($allStaff as $staff)
                                    <option value="{{$staff->id}}" {{$salesOrder->staff_id == $staff->id ? 'selected' : ''}}>
                                        {{$staff->name}} ({{ucfirst($staff->role)}})
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- ADD ITEM FORM --}}
        <div class="card border-0 bg-light mb-3">
            <div class="card-body py-3">
                <h6 class="font-weight-bold mb-3">
                    <i class="fas fa-plus-circle text-primary mr-1"></i> Add Item to Order
                </h6>
                <form id="add-item-form" action="{{route('sales-orders.add-item', $salesOrder->id)}}" method="POST">
                    @csrf
                    <div class="row align-items-end">
                        <div class="col-md-5">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="small font-weight-bold mb-0">Product</label>
                                <button type="button" class="btn btn-link btn-sm p-0 text-primary font-weight-bold" data-toggle="modal" data-target="#addProductModal">
                                    <i class="fas fa-plus-circle"></i> New Product
                                </button>
                            </div>
                            <select name="product_id" id="add-product-select" class="form-control select2-product" required>
                                <option value="">-- Select Product --</option>
                                @foreach($products as $product)
                                    <option value="{{$product->id}}" data-price="{{$product->price}}">
                                        {{$product->title}} 
                                        @if($product->brand) | {{$product->brand->title}} @endif
                                        @if($product->model) | {{$product->model}} @endif
                                        @if($product->sku) ({{$product->sku}}) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="small font-weight-bold">Qty</label>
                            <input type="number" name="quantity" id="add-qty" class="form-control" value="1" min="0.01" step="any" required>
                        </div>
                        <div class="col-md-3">
                            <label class="small font-weight-bold">Price</label>
                            <input type="number" name="price" id="add-price" class="form-control" value="0" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-plus mr-1"></i> Add
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ITEMS TABLE --}}
        <form id="fulfill-form" action="{{route('sales-orders.fulfill', $salesOrder->id)}}" method="POST">
            @csrf
            <!-- Global Select All / Header -->
            <div class="d-flex justify-content-between align-items-center mb-3 px-2 mt-2">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="select-all">
                    <label class="custom-control-label font-weight-bold text-primary" style="cursor: pointer;" for="select-all">Select All for Fulfillment</label>
                </div>
                <div class="font-weight-bold text-dark text-right" style="font-size: 1.1rem;">
                    Total: <span id="grand-total-display" class="text-success">Rs. {{number_format($salesOrder->total_amount, 2)}}</span>
                </div>
            </div>

            <div id="items-table-container">
                <div class="row m-0">
                    @forelse($salesOrder->items as $item)
                        @php $remaining = $item->quantity - $item->delivered_quantity; @endphp
                        <div class="col-12 col-lg-6 col-xl-4 mb-3 px-2">
                            <div class="card shadow-sm border-0 h-100" style="border-radius: 12px; overflow: hidden; {{ $remaining <= 0 ? 'background-color: #f0fdf4; opacity:0.8;' : 'background-color: #fff; border: 1px solid #e2e8f0 !important;' }}">
                                <!-- Card Header -->
                                <div class="d-flex justify-content-between align-items-center p-3 border-bottom {{ $remaining <= 0 ? 'border-success' : 'border-light' }}">
                                    <div class="d-flex align-items-center" style="gap: 12px; width: calc(100% - 40px);">
                                        @if($remaining > 0)
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input item-checkbox" name="selected_items[]" value="{{$item->id}}" id="check-{{$item->id}}">
                                                <label class="custom-control-label" style="cursor: pointer;" for="check-{{$item->id}}"></label>
                                            </div>
                                        @else
                                            <i class="fas fa-check-circle text-success" style="font-size: 1.2rem;"></i>
                                        @endif
                                        <div class="text-truncate">
                                            <div class="font-weight-bold text-dark text-truncate" style="font-size: 0.95rem;" title="{{$item->product->title ?? 'Deleted Product'}}">{{$item->product->title ?? 'Deleted Product'}}</div>
                                            <div class="small text-muted text-truncate">
                                                SKU: {{$item->product->sku ?? 'N/A'}}
                                                @if($item->product && $item->product->brand)
                                                    | {{$item->product->brand->title}}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @if($remaining > 0)
                                        <button type="button" class="btn btn-sm btn-outline-danger shadow-sm flex-shrink-0" 
                                                style="border-radius: 8px; padding: 4px 8px;"
                                                onclick="removeItem('{{route('sales-orders.remove-item', [$salesOrder->id, $item->id])}}')"
                                                title="Remove item">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    @endif
                                </div>
                                
                                <!-- Card Body -->
                                <div class="card-body p-3">
                                    <!-- Quantities row -->
                                    <div class="d-flex justify-content-between mb-3 text-center bg-light p-2 rounded shadow-sm" style="border: 1px solid #f1f5f9;">
                                        <div class="flex-fill border-right">
                                            <div class="small text-muted font-weight-bold text-uppercase" style="font-size: 10px;">Ordered</div>
                                            <div class="font-weight-bold text-dark">{{$item->quantity}}</div>
                                        </div>
                                        <div class="flex-fill border-right">
                                            <div class="small text-muted font-weight-bold text-uppercase" style="font-size: 10px;">Delivered</div>
                                            <div class="font-weight-bold text-info">{{$item->delivered_quantity}}</div>
                                        </div>
                                        <div class="flex-fill">
                                            <div class="small text-muted font-weight-bold text-uppercase" style="font-size: 10px;">Remaining</div>
                                            <div class="font-weight-bold {{ $remaining > 0 ? 'text-danger' : 'text-success' }}">{{$remaining}}</div>
                                        </div>
                                    </div>

                                    <!-- Inputs row -->
                                    <div class="row align-items-center mt-2">
                                        <div class="col-5 pr-1">
                                            <div class="small text-muted font-weight-bold mb-1" style="font-size: 11px;">Fulfill Qty</div>
                                            @if($remaining > 0)
                                                <input type="number" name="deliver[{{$item->id}}]" class="sleek-input deliver-qty text-center w-100" value="{{$remaining}}" min="0" step="any" style="font-size: 13px;">
                                            @else
                                                <div class="badge badge-success w-100 py-2 d-flex align-items-center justify-content-center" style="font-size: 12px; height: 32px;"><i class="fas fa-check mr-1"></i> FULFILLED</div>
                                            @endif
                                        </div>
                                        <div class="col-7 pl-1">
                                            <div class="small text-muted font-weight-bold mb-1" style="font-size: 11px;">Unit Price</div>
                                            <div class="sleek-input-group w-100">
                                                <span class="prefix">Rs</span>
                                                <input type="number" step="0.01" class="text-right item-price-input" data-id="{{$item->id}}" value="{{$item->price}}" style="font-size: 13px;">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Total row -->
                                    <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center">
                                        <span class="small font-weight-bold text-muted text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Item Total</span>
                                        <span class="font-weight-bold text-primary" style="font-size: 1.1rem;" id="item-total-{{$item->id}}">Rs. {{number_format($item->price * $item->quantity, 2)}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5 text-muted bg-white rounded border shadow-sm">
                            <i class="fas fa-box-open fa-3x mb-3 d-block opacity-50"></i>
                            <h5 class="font-weight-bold">No items in this order yet.</h5>
                            <p class="small mb-0">Use the "Add Item" form above to build this order.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            @if($salesOrder->status != 'delivered' && $salesOrder->status != 'cancelled')
            <div class="mt-3 p-3 border rounded bg-light d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="mb-1 font-weight-bold">Fulfillment Action</h6>
                    <p class="small text-muted mb-0">Check items above and send to POS for delivery.</p>
                </div>
                <button type="submit" class="btn btn-primary btn-lg px-5 shadow" id="fulfill-btn" disabled>
                    <i class="fas fa-desktop mr-2"></i> SEND SELECTED TO POS
                </button>
            </div>
            @endif
        </form>

        @if($salesOrder->note)
        <div class="mt-4">
            <h6 class="font-weight-bold">Order Note:</h6>
            <div class="p-3 bg-light border rounded">{{$salesOrder->note}}</div>
        </div>
        @endif

        {{-- Linked POS Bills --}}
        <div class="mt-4" id="linked-bills-container">
            <h6 class="font-weight-bold text-primary"><i class="fas fa-file-invoice-dollar mr-1"></i> Linked POS Bills (Fulfillment History)</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered bg-light">
                    <thead>
                        <tr>
                            <th>Bill Number</th>
                            <th>Date</th>
                            <th>Total Amount</th>
                            <th>Payment Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salesOrder->orders as $linkedOrder)
                        <tr>
                            <td><span class="font-weight-bold">{{$linkedOrder->order_number}}</span></td>
                            <td>{{$linkedOrder->created_at->format('d M Y, h:i A')}}</td>
                            <td>Rs. {{number_format($linkedOrder->total_amount, 2)}}</td>
                            <td>
                                @if($linkedOrder->payment_status == 'paid') <span class="badge badge-success">Paid</span>
                                @elseif($linkedOrder->payment_status == 'partial') <span class="badge badge-warning">Partial</span>
                                @else <span class="badge badge-danger">Unpaid</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{route('order.show', $linkedOrder->id)}}" class="btn btn-primary btn-sm px-3" target="_blank">
                                    <i class="fas fa-eye mr-1"></i> View Bill
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted small py-3">No bills generated for this sale order yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

{{-- Add Product Quick Modal --}}
<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index:9999;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold">Add Quick Product</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body bg-light">
                <form id="add-product-form">
                    @csrf
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="small font-weight-bold">Product Title <span class="text-danger">*</span></label>
                                <select name="title" id="pos-title-select" class="form-control" required></select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">Category <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <select name="cat_id" id="qp-cat-select" class="form-control" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $cat)
                                            <option value="{{$cat->id}}">{{$cat->title}}</option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addCategoryModal"><i class="fas fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Brand</label>
                                <div class="input-group">
                                    <select name="brand_id" id="qp-brand-select" class="form-control">
                                        <option value="">Select Brand</option>
                                        @foreach($brands as $brand)
                                            <option value="{{$brand->id}}">{{$brand->title}}</option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#addBrandModal"><i class="fas fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Model</label>
                                <div class="input-group">
                                    <select name="model" id="qp-model-select" class="form-control">
                                        <option value="">Select Model</option>
                                        @foreach($product_models as $m)
                                            <option value="{{$m->name}}">{{$m->name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#addModelModal"><i class="fas fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Unit</label>
                                <div class="input-group">
                                    <select name="unit" id="qp-unit-select" class="form-control">
                                        <option value="piece">Piece</option>
                                        @foreach($units as $u)
                                            <option value="{{$u->name}}">{{$u->name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addUnitModal"><i class="fas fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Initial Stock <span class="text-danger">*</span></label>
                                <input type="number" name="stock" class="form-control" required value="0">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">Purchase Price</label>
                                <input type="number" name="purchase_price" class="form-control" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">Selling Price <span class="text-danger">*</span></label>
                                <input type="number" name="price" id="qp-price" class="form-control" required placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">Primary Supplier</label>
                                <div class="input-group">
                                    <select name="suppliers[]" id="qp-supplier-select" class="form-control" multiple>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#addSupplierModal"><i class="fas fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary px-4 shadow" id="save-product-btn">
                    <i class="fas fa-save mr-1"></i> SAVE PRODUCT
                </button>
            </div>
        </div>
    </div>
</div>

@include('backend.product.partials.modals')

@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Move modals to body to avoid z-index issues
    $('#addProductModal, #addCategoryModal, #addBrandModal, #addSupplierModal, #addUnitModal, #addModelModal').appendTo('body');

    // Select2 for add item dropdown
    $('.select2-product').select2({ placeholder: 'Search product...', width: '100%' });

    // Auto-fill price when product selected
    $('#add-product-select').on('change', function() {
        let price = $(this).find(':selected').data('price') || 0;
        $('#add-price').val(price);
    });

    // Fulfill checkboxes logic
    const updateFulfillButton = () => {
        const checkedCount = $('.item-checkbox:checked').length;
        $('#fulfill-btn').prop('disabled', checkedCount === 0);
    };

    // Use event delegation for items that might be added/refreshed via AJAX
    $(document).on('change', '.item-checkbox', function() {
        updateFulfillButton();
    });

    $(document).on('change', '#select-all', function() {
        $('.item-checkbox').prop('checked', $(this).prop('checked')).trigger('change');
    });

    // Run once on load to set initial state
    updateFulfillButton();

    $(document).on('input', '.deliver-qty', function() {
        let val = parseFloat($(this).val());
        if (val < 0) $(this).val(0);
    });

    // Init modal Select2 when opened
    $('#addProductModal').on('shown.bs.modal', function () {
        $('#qp-model-select, #qp-unit-select, #qp-cat-select, #qp-brand-select').select2({ tags: true, width: '100%', allowClear: true, dropdownParent: $('#addProductModal') });
        $('#qp-supplier-select').select2({ width: '100%', allowClear: true, dropdownParent: $('#addProductModal') });
        $('#pos-title-select').select2({
            placeholder: 'Search or Enter Product Name',
            allowClear: true, tags: true, width: '100%',
            dropdownParent: $('#addProductModal'),
            minimumInputLength: 2,
            ajax: {
                url: "{{route('admin.product.search-simple')}}",
                dataType: 'json', delay: 250,
                data: function(p) { return { q: p.term }; },
                processResults: function(data) { return { results: data }; },
                cache: true
            }
        });
    });

    // Sub-modal AJAX handlers
    $(document).on('submit', '#quickAddCategoryForm', function(e) {
        e.preventDefault();
        $.post("{{route('category.quick-store')}}", $(this).serialize() + "&_token={{csrf_token()}}&is_parent=1", function(res) {
            if(res.status == 'success') {
                $('#qp-cat-select').append(new Option(res.category.title, res.category.id, false, true)).trigger('change');
                $('#addCategoryModal').modal('hide');
            }
        });
    });

    $(document).on('submit', '#quickAddSupplierForm', function(e) {
        e.preventDefault();
        $.post("{{route('supplier.quick-store')}}", $(this).serialize() + "&_token={{csrf_token()}}", function(res) {
            if(res.status == 'success') {
                $('#qp-supplier-select').append(new Option(res.supplier.name + ' (' + (res.supplier.company_name || '') + ')', res.supplier.id, false, true)).trigger('change');
                $('#addSupplierModal').modal('hide');
            }
        });
    });

    $(document).on('submit', '#quickAddBrandForm', function(e) {
        e.preventDefault();
        $.post("{{route('brand.quick-store')}}", $(this).serialize() + "&_token={{csrf_token()}}", function(res) {
            if(res.status == 'success') {
                $('#qp-brand-select').append(new Option(res.brand.title, res.brand.id, false, true)).trigger('change');
                $('#addBrandModal').modal('hide');
            }
        });
    });

    $(document).on('submit', '#quickAddUnitForm', function(e) {
        e.preventDefault();
        $.post("{{route('product.store-unit')}}", $(this).serialize() + "&_token={{csrf_token()}}", function(res) {
            if(res.status == 'success') {
                $('#qp-unit-select').append(new Option(res.unit.name, res.unit.name, false, true)).trigger('change');
                $('#addUnitModal').modal('hide');
            }
        });
    });

    $(document).on('submit', '#quickAddModelForm', function(e) {
        e.preventDefault();
        $.post("{{route('product.store-model')}}", $(this).serialize() + "&_token={{csrf_token()}}", function(res) {
            if(res.status == 'success') {
                $('#qp-model-select').append(new Option(res.model.name, res.model.name, false, true)).trigger('change');
                $('#addModelModal').modal('hide');
            }
        });
    });

    // Update Item Price AJAX
    $(document).on('change', '.item-price-input', function() {
        let $input = $(this);
        let itemId = $input.data('id');
        let newPrice = $input.val();
        let $row = $input.closest('tr');

        $input.addClass('border-warning');

        $.ajax({
            url: "/admin/sales-orders/item/" + itemId + "/update-price",
            type: 'POST',
            data: {
                _token: "{{csrf_token()}}",
                price: newPrice
            },
            success: function(res) {
                if (res.status === 'success') {
                    $input.removeClass('border-warning').addClass('border-success');
                    setTimeout(() => $input.removeClass('border-success'), 2000);
                    
                    // Update item total display
                    $('#item-total-' + itemId).text('Rs. ' + parseFloat(res.item_total).toLocaleString(undefined, {minimumFractionDigits: 2}));
                    
                    // Update grand total display
                    $('#grand-total-display').text('Rs. ' + parseFloat(res.total_amount).toLocaleString(undefined, {minimumFractionDigits: 2}));
                    
                    Swal.fire({
                        icon: 'success', title: 'Price Updated', toast: true,
                        position: 'top-end', showConfirmButton: false, timer: 2000
                    });
                }
            },
            error: function(err) {
                $input.removeClass('border-warning').addClass('border-danger');
                let msg = err.responseJSON ? err.responseJSON.message : 'Error updating price';
                Swal.fire('Error', msg, 'error');
            }
        });
    });

    // Save new product
    $('#save-product-btn').on('click', function() {
        let $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> SAVING...');
        $.ajax({
            url: "{{route('product.quick-store')}}",
            type: 'POST',
            data: $('#add-product-form').serialize(),
            success: function(res) {
                if (res.status === 'success') {
                    $('#addProductModal').modal('hide');
                    $('#add-product-form')[0].reset();
                    Swal.fire('Success', 'Product added!', 'success');
                    // Add to the add-item dropdown
                    let opt = new Option(res.product.title + ' (' + (res.product.sku || 'N/A') + ')', res.product.id, true, true);
                    $(opt).attr('data-price', res.product.price);
                    $('#add-product-select').append(opt).trigger('change');
                    $('#add-price').val(res.product.price);
                }
                $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> SAVE PRODUCT');
            },
            error: function(err) {
                let msg = err.responseJSON ? err.responseJSON.message : 'Error saving product';
                Swal.fire('Error', msg, 'error');
                $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> SAVE PRODUCT');
            }
        });
    });

    // AJAX Add Item
    $('#add-item-form').on('submit', function(e) {
        e.preventDefault();
        let $form = $(this);
        let $btn = $form.find('button[type="submit"]');
        let originalHtml = $btn.html();

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> ADDING...');

        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: $form.serialize(),
            success: function(res) {
                if (res.status === 'success') {
                    // Refresh only the table container
                    let fetchUrl = window.location.href.split('#')[0];
                    $('#items-table-container').load(fetchUrl + ' #items-table-container > *', function() {
                        // Reset fulfillment button state based on new content
                        updateFulfillButton();
                        $('#select-all').prop('checked', false);
                        
                        // Show success ONLY after the table has actually updated on screen
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: res.message,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    });
                    
                    // Refresh linked POS bills history too
                    $('#linked-bills-container').load(fetchUrl + ' #linked-bills-container > *');

                    // Update Grand Total in view
                    $('#grand-total-display').text('Rs. ' + parseFloat(res.total_amount).toLocaleString(undefined, {minimumFractionDigits: 2}));
                    
                    // Reset form select
                    $('#add-product-select').val('').trigger('change');
                    $('#add-qty').val(1);
                    $('#add-price').val(0);
                }
            },
            error: function(err) {
                let msg = err.responseJSON ? err.responseJSON.message : 'Error adding item';
                Swal.fire('Error', msg, 'error');
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalHtml);
            }
        });
    });
});

function removeItem(url) {
    if (confirm('Remove this item from the order?')) {
        $('#remove-item-form').attr('action', url).submit();
    }
}
</script>

<form id="remove-item-form" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>
@endpush
