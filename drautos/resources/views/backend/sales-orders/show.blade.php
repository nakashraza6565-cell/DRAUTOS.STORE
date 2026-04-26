@extends('backend.layouts.master')

@section('main-content')
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
            <div class="table-responsive" id="items-table-container">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th width="40"><input type="checkbox" id="select-all"></th>
                            <th>Product</th>
                            <th class="text-center" width="90">Ordered</th>
                            <th class="text-center" width="90">Delivered</th>
                            <th class="text-center" width="90">Remaining</th>
                            <th class="text-center" width="130">Deliver Qty</th>
                            <th class="text-right" width="110">Price</th>
                            <th class="text-right" width="110">Total</th>
                            <th width="50"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salesOrder->items as $item)
                        @php $remaining = $item->quantity - $item->delivered_quantity; @endphp
                        <tr class="{{ $remaining <= 0 ? 'table-success' : '' }}" style="{{ $remaining <= 0 ? 'opacity:0.7;' : '' }}">
                            <td class="text-center align-middle">
                                @if($remaining > 0)
                                    <input type="checkbox" class="item-checkbox" name="selected_items[]" value="{{$item->id}}">
                                @else
                                    <i class="fas fa-check-circle text-success"></i>
                                @endif
                            </td>
                            <td>
                                <div class="font-weight-bold">{{$item->product->title ?? 'Deleted Product'}}</div>
                                <small class="text-muted">
                                    SKU: {{$item->product->sku ?? 'N/A'}}
                                    @if($item->product && $item->product->brand)
                                        | {{$item->product->brand->title}}
                                    @endif
                                </small>
                            </td>
                            <td class="text-center align-middle">{{$item->quantity}}</td>
                            <td class="text-center align-middle">{{$item->delivered_quantity}}</td>
                            <td class="text-center align-middle font-weight-bold {{ $remaining > 0 ? 'text-primary' : 'text-success' }}">{{$remaining}}</td>
                            <td class="text-center align-middle">
                                @if($remaining > 0)
                                    <input type="number" name="deliver[{{$item->id}}]" class="form-control form-control-sm deliver-qty" value="{{$remaining}}" min="0" step="any">
                                @else
                                    <span class="badge badge-success">FULFILLED</span>
                                @endif
                            </td>
                            <td class="text-right align-middle">Rs. {{number_format($item->price, 2)}}</td>
                            <td class="text-right align-middle font-weight-bold">Rs. {{number_format($item->price * $item->quantity, 2)}}</td>
                            <td class="text-center align-middle">
                                @if($remaining > 0)
                                    <button type="button" class="btn btn-danger btn-sm" 
                                            style="width:28px;height:28px;padding:0;border-radius:50%;" 
                                            onclick="removeItem('{{route('sales-orders.remove-item', [$salesOrder->id, $item->id])}}')"
                                            title="Remove item">
                                        <i class="fas fa-times" style="font-size:10px;"></i>
                                    </button>
                                @else
                                <span class="text-muted small" title="Fully delivered — cannot delete">
                                    <i class="fas fa-lock"></i>
                                </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>No items in this order yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold">
                            <td colspan="7" class="text-right">Grand Total:</td>
                            <td class="text-right" id="grand-total-display">Rs. {{number_format($salesOrder->total_amount, 2)}}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
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
                    $('#items-table-container').load(window.location.href + ' #items-table-container > *', function() {
                        // Reset fulfillment button state based on new content
                        updateFulfillButton();
                        $('#select-all').prop('checked', false);
                    });
                    
                    // Refresh linked POS bills history too
                    $('#linked-bills-container').load(window.location.href + ' #linked-bills-container > *');

                    // Update Grand Total in view
                    $('#grand-total-display').text('Rs. ' + parseFloat(res.total_amount).toLocaleString(undefined, {minimumFractionDigits: 2}));
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: res.message,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    
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
