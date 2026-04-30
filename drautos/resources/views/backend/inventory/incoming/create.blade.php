@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid p-0">
    <form action="{{route('inventory-incoming.store')}}" method="POST" id="incoming-form">
        @csrf

        @include('backend.layouts.notification')
        @if($errors->any())
            <div class="alert alert-danger mx-4 mt-3">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- STICKY TOOLBAR --}}
        <div class="sticky-top bg-white border-bottom shadow-sm mb-4" style="z-index: 1020; top: 0;">
            <div class="container-fluid py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-truck-loading mr-2"></i> Add Incoming Goods
                        </h5>
                        <div class="small text-muted mt-1">Consolidate multiple items and sync to supplier ledger.</div>
                    </div>
                    <div class="d-flex align-items-center" style="gap: 20px;">
                        <div class="text-right">
                            <div class="small text-muted font-weight-bold text-uppercase">Total Entry Value</div>
                            <div id="grand-total-display" class="h4 mb-0 font-weight-bold text-primary">Rs. 0.00</div>
                        </div>
                        <div class="border-left pl-3 ml-3">
                            <button type="submit" class="btn btn-success px-4 py-2 font-weight-bold shadow-sm">
                                <i class="fas fa-save mr-1"></i> SAVE & POST ENTRY
                            </button>
                            <a href="{{route('inventory-incoming.index')}}" class="btn btn-light border ml-2">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-4">
            <div class="row">
                <div class="col-xl-8">
                    {{-- SUPPLIER & BASIC INFO --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="form-group mb-0">
                                        <label class="font-weight-bold small text-uppercase">Supplier <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <select name="supplier_id" id="supplier_id" class="form-control select2" required>
                                                <option value="">--Select Supplier--</option>
                                                @foreach($suppliers as $supplier)
                                                    <option value="{{$supplier->id}}" data-phone="{{$supplier->phone}}" data-balance="{{number_format($supplier->current_balance, 2)}}" data-name="{{$supplier->name}}">
                                                        {{$supplier->name}} ({{$supplier->phone}})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addSupplierModal" title="Quick Add Supplier">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="custom-control custom-switch mt-3">
                                        <input type="checkbox" name="post_to_ledger" class="custom-control-input" id="post_to_ledger" checked>
                                        <label class="custom-control-label font-weight-bold text-primary cursor-pointer" for="post_to_ledger">
                                            <i class="fas fa-file-invoice-dollar mr-1"></i> Automatically Record to Supplier Ledger
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-5" id="supplier-info-card" style="display:none;">
                                    <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="font-weight-bold text-primary h6 mb-1" id="s-name"></div>
                                            <div id="s-phone" class="small text-muted"></div>
                                        </div>
                                        <div class="text-right border-left pl-3">
                                            <div class="small text-muted font-weight-bold text-uppercase">Owed Balance</div>
                                            <div id="s-balance" class="h5 mb-0 font-weight-bold text-danger"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ITEMS GRID --}}
                    <div class="card shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h6 class="m-0 font-weight-bold text-dark">Multiple Item Entry Grid</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="incoming-items-table">
                                    <thead class="bg-light small font-weight-bold text-uppercase">
                                        <tr>
                                            <th style="width: 40%;">Product / Description</th>
                                            <th style="width: 15%;">Quantity</th>
                                            <th style="width: 20%;">Unit Cost (Rs.)</th>
                                            <th style="width: 20%;">Line Total</th>
                                            <th style="width: 5%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="items-container">
                                        <!-- Items will be added here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <button type="button" class="btn btn-outline-primary font-weight-bold" onclick="addItemRow()">
                                <i class="fas fa-plus-circle mr-1"></i> ADD ANOTHER ROW
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    {{-- SHIPMENT DETAILS --}}
                    <div class="card shadow-sm mb-4 border-left-info">
                        <div class="card-header bg-white py-3">
                            <h6 class="m-0 font-weight-bold text-dark text-uppercase small">Shipment & Logistics</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="small font-weight-bold text-uppercase">Destination Warehouse <span class="text-danger">*</span></label>
                                <select name="warehouse_id" class="form-control" required>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="small font-weight-bold text-uppercase text-info">Shipping Cost (Rs.)</label>
                                <input type="number" name="shipping_cost" id="shipping_cost" class="form-control form-control-lg font-weight-bold border-info" value="0" min="0" step="0.01">
                            </div>
                            <div class="form-group">
                                <label class="small font-weight-bold text-uppercase">Received Date</label>
                                <input type="date" name="received_date" class="form-control" value="{{date('Y-m-d')}}" required>
                            </div>
                            <div class="form-group">
                                <label class="small font-weight-bold text-uppercase">Invoice # (Handwritten)</label>
                                <input type="text" name="invoice_number" class="form-control" placeholder="e.g. INV-2024-001">
                            </div>
                            <div class="form-group mb-0">
                                <label class="small font-weight-bold text-uppercase">Internal Note</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Add any special notes about this shipment..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- MODALS --}}
@include('backend.inventory.incoming.modals.quick_add_supplier')
@include('backend.inventory.incoming.modals.quick_add_product')
@include('backend.product.partials.modals')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
<style>
    .select2-container--bootstrap4 .select2-selection--single { height: 38px !important; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let itemIndex = 0;

$(document).ready(function() {
    $('.select2').select2({ theme: 'bootstrap4' });
    
    // Add first row automatically
    addItemRow();

    $('#supplier_id').on('change', function() {
        let $opt = $(this).find(':selected');
        if(!$(this).val()) {
            $('#supplier-info-card').hide();
            return;
        }
        $('#s-name').text($opt.data('name'));
        $('#s-phone').text($opt.data('phone'));
        $('#s-balance').text('Rs. ' + $opt.data('balance'));
        $('#supplier-info-card').fadeIn();
    });

    $(document).on('input', '.qty-input, .cost-input, #shipping_cost', function() {
        updateGrandTotal();
    });
});

function updateGrandTotal() {
    let itemsTotal = 0;
    $('.item-row').each(function() {
        let qty = parseFloat($(this).find('.qty-input').val()) || 0;
        let cost = parseFloat($(this).find('.cost-input').val()) || 0;
        let total = qty * cost;
        $(this).find('.row-total-display').text('Rs. ' + total.toFixed(2));
        itemsTotal += total;
    });
    
    let shippingCost = parseFloat($('#shipping_cost').val()) || 0;
    let grandTotal = itemsTotal + shippingCost;
    
    $('#grand-total-display').text('Rs. ' + grandTotal.toLocaleString(undefined, {minimumFractionDigits: 2}));
}

function addItemRow(product = null) {
    let html = `
        <tr class="item-row">
            <td class="align-middle border-0">
                <div class="d-flex align-items-center">
                    <select name="items[${itemIndex}][product_id]" class="form-control select2-dynamic product-select" required>
                        <option value="">Select Product</option>
                        @foreach($products as $p)
                            <option value="{{$p->id}}" data-cost="{{$p->purchase_price}}" ${product && product.id == {{$p->id}} ? 'selected' : ''}>
                                {{$p->title}} ({{$p->sku}})
                            </option>
                        @endforeach
                    </select>
                    <button type="button" class="btn btn-link btn-sm text-primary p-0 ml-2" data-toggle="modal" data-target="#addProductModal" title="Quick Add Product">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <div class="current-cost-info mt-1 small text-muted"></div>
            </td>
            <td class="align-middle border-0">
                <input type="number" name="items[${itemIndex}][quantity]" class="form-control qty-input" min="1" value="${product ? product.qty : 1}" required>
            </td>
            <td class="align-middle border-0">
                <input type="number" step="0.01" name="items[${itemIndex}][unit_cost]" class="form-control cost-input" min="0" value="${product ? product.cost : 0}" required>
                <button type="button" class="btn btn-link btn-sm p-0 small mt-1 text-info" data-toggle="collapse" data-target="#pkg-${itemIndex}">
                    <i class="fas fa-box-open mr-1"></i> Packaging
                </button>
                <div class="collapse mt-2 bg-light p-2 rounded" id="pkg-${itemIndex}">
                    <label class="small font-weight-bold">Pkg Material</label>
                    <select name="items[${itemIndex}][packaging_item_id]" class="form-control form-control-sm mb-1">
                        <option value="">None</option>
                        @foreach($packaging_items as $pkg)
                            <option value="{{$pkg->id}}">{{$pkg->name}}</option>
                        @endforeach
                    </select>
                    <label class="small font-weight-bold">Additional Cost</label>
                    <input type="number" step="0.01" name="items[${itemIndex}][packaging_cost]" class="form-control form-control-sm" value="0">
                </div>
            </td>
            <td class="align-middle border-0 text-right">
                <div class="row-total-display font-weight-bold text-dark">Rs. 0.00</div>
            </td>
            <td class="align-middle border-0 text-center">
                <button type="button" class="btn btn-link text-danger remove-row"><i class="fas fa-trash-alt"></i></button>
            </td>
        </tr>
    `;
    
    let $html = $(html);
    $('#items-container').append($html);
    
    $html.find('.select2-dynamic').select2({ theme: 'bootstrap4' });
    
    $html.find('.product-select').on('change', function() {
        let cost = $(this).find(':selected').data('cost');
        let $row = $(this).closest('.item-row');
        if(cost) {
            $row.find('.current-cost-info').html('<i class="fas fa-info-circle mr-1"></i> Prev Cost: Rs. ' + parseFloat(cost).toFixed(2));
            $row.find('.cost-input').val(cost);
        } else {
            $row.find('.current-cost-info').empty();
        }
        updateGrandTotal();
    });

    $html.find('.remove-row').on('click', function() {
        if($('.item-row').length > 1) {
            $(this).closest('.item-row').remove();
            updateGrandTotal();
        } else {
            Swal.fire('Info', 'At least one item row is required.', 'info');
        }
    });
    
    itemIndex++;
    updateGrandTotal();
}

// Sub-Modal AJAX Handlers (For Category, Brand, Model, Unit)
$(document).on('submit', '#quickAddCategoryForm', function(e) {
    e.preventDefault();
    $.post("{{route('category.quick-store')}}", $(this).serialize() + "&_token={{csrf_token()}}&is_parent=1", function(res) {
        if(res.status == 'success') {
            $('#qa-cat-select').append(new Option(res.category.title, res.category.id, false, true)).trigger('change');
            $('#addCategoryModal').modal('hide');
        }
    });
});

$(document).on('submit', '#quickAddBrandForm', function(e) {
    e.preventDefault();
    $.post("{{route('brand.quick-store')}}", $(this).serialize() + "&_token={{csrf_token()}}", function(res) {
        if(res.status == 'success') {
            $('#qa-brand-select').append(new Option(res.brand.title, res.brand.id, false, true)).trigger('change');
            $('#addBrandModal').modal('hide');
        }
    });
});

$(document).on('submit', '#quickAddUnitForm', function(e) {
    e.preventDefault();
    $.post("{{route('product.store-unit')}}", $(this).serialize() + "&_token={{csrf_token()}}", function(res) {
        if(res.status == 'success') {
            $('#qa-unit-select').append(new Option(res.unit.name, res.unit.name, false, true)).trigger('change');
            $('#addUnitModal').modal('hide');
        }
    });
});

$(document).on('submit', '#quickAddModelForm', function(e) {
    e.preventDefault();
    $.post("{{route('product.store-model')}}", $(this).serialize() + "&_token={{csrf_token()}}", function(res) {
        if(res.status == 'success') {
            $('#qa-model-select').append(new Option(res.model.name, res.model.name, false, true)).trigger('change');
            $('#addModelModal').modal('hide');
        }
    });
});

$(document).on('submit', '#quickAddSupplierForm', function(e) {
    e.preventDefault();
    $.post("{{route('supplier.quick-store')}}", $(this).serialize() + "&_token={{csrf_token()}}", function(res) {
        if(res.status == 'success') {
            $('#supplier_id').append(new Option(res.supplier.name + ' (' + (res.supplier.phone || '') + ')', res.supplier.id, false, true)).trigger('change');
            $('#addSupplierModal').modal('hide');
        }
    });
});
</script>
@endpush
@endsection
