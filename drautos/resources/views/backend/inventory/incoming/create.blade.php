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

        @if($purchase_order_id)
            <input type="hidden" name="purchase_order_id" value="{{$purchase_order_id}}">
            <div class="alert alert-info mx-4 mb-4">
                <i class="fas fa-info-circle mr-2"></i> Converting from Purchase Order: <strong>#{{ \App\Models\PurchaseOrder::find($purchase_order_id)->po_number ?? 'PO-N/A' }}</strong>
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
                                                    <option value="{{$supplier->id}}" data-phone="{{$supplier->phone}}" data-balance="{{number_format($supplier->current_balance, 2)}}" data-name="{{$supplier->name}}" {{ (isset($prefill_supplier_id) && $prefill_supplier_id == $supplier->id) ? 'selected' : '' }}>
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
                                <table class="table table-hover mb-0 incoming-goods-card-layout" id="incoming-items-table">
                                    <thead class="bg-light small font-weight-bold text-uppercase">
                                        <tr>
                                            <th style="width: 30%;">Product / Description</th>
                                            <th style="width: 15%;">Avail. Stock</th>
                                            <th style="width: 15%;">New Qty</th>
                                            <th style="width: 15%;">Unit Cost (Rs.)</th>
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">
<style>
    .select2-container--bootstrap4 .select2-selection--single { height: 38px !important; }

    /* Modern Mobile Card view for Add Incoming Goods */
    @media (max-width: 768px) {
        /* Remove table boundaries and force block flow */
        table.incoming-goods-card-layout {
            border: 0 !important;
            display: block !important;
            background: transparent !important;
        }
        table.incoming-goods-card-layout thead {
            display: none !important;
        }
        table.incoming-goods-card-layout tbody {
            display: block !important;
            width: 100% !important;
        }
        
        /* Make table responsive container visible overflow */
        .table-responsive {
            overflow-x: visible !important;
            border: 0 !important;
        }

        /* Each Row becomes a premium Card */
        table.incoming-goods-card-layout tr.item-row {
            display: grid !important;
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 16px 12px !important;
            position: relative !important;
            background: #ffffff !important;
            border: 1px solid rgba(226, 232, 240, 0.8) !important;
            border-radius: 16px !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.04) !important;
            padding: 20px 16px 16px 16px !important;
            margin-bottom: 24px !important;
            transition: all 0.2s ease;
        }
        table.incoming-goods-card-layout tr.item-row:hover,
        table.incoming-goods-card-layout tr.item-row:focus-within {
            border-color: rgba(78, 115, 223, 0.4) !important;
            box-shadow: 0 12px 30px -5px rgba(78, 115, 223, 0.08) !important;
        }

        /* Layout Grid Cell Spans */
        table.incoming-goods-card-layout tr.item-row td {
            display: block !important;
            width: 100% !important;
            padding: 0 !important;
            border: 0 !important;
            margin: 0 !important;
        }
        
        table.incoming-goods-card-layout tr.item-row td[data-title="Product"] {
            grid-column: span 2 !important;
            border-bottom: 1px solid #f1f5f9 !important;
            padding-bottom: 16px !important;
            margin-bottom: 4px !important;
        }
        table.incoming-goods-card-layout tr.item-row td[data-title="Avail. Stock"] {
            grid-column: span 1 !important;
        }
        table.incoming-goods-card-layout tr.item-row td[data-title="New Qty"] {
            grid-column: span 1 !important;
        }
        table.incoming-goods-card-layout tr.item-row td[data-title="Unit Cost"] {
            grid-column: span 2 !important;
        }
        table.incoming-goods-card-layout tr.item-row td[data-title="Line Total"] {
            grid-column: span 2 !important;
        }

        /* Add dynamic uppercase labels using data-title */
        table.incoming-goods-card-layout tr.item-row td::before {
            content: attr(data-title);
            display: block !important;
            font-size: 0.68rem !important;
            font-weight: 800 !important;
            letter-spacing: 0.8px !important;
            color: #64748b !important;
            margin-bottom: 6px !important;
            text-transform: uppercase !important;
        }

        /* Product input alignment */
        table.incoming-goods-card-layout tr.item-row td[data-title="Product"] > div {
            display: flex !important;
            align-items: center !important;
            width: 100% !important;
        }
        table.incoming-goods-card-layout tr.item-row td[data-title="Product"] .select2-container {
            width: 100% !important;
            flex-grow: 1 !important;
        }
        table.incoming-goods-card-layout tr.item-row td[data-title="Product"] .btn-link {
            flex-shrink: 0 !important;
            margin-left: 8px !important;
            background: #f1f5f9 !important;
            border: 1px solid #cbd5e1 !important;
            width: 38px !important;
            height: 38px !important;
            border-radius: 8px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: #4e73df !important;
            text-decoration: none !important;
            transition: all 0.2s ease !important;
        }
        table.incoming-goods-card-layout tr.item-row td[data-title="Product"] .btn-link:hover {
            background: #4e73df !important;
            color: #ffffff !important;
            border-color: #4e73df !important;
        }

        /* Hide packaging and additional cost completely on mobile */
        table.incoming-goods-card-layout tr.item-row td[data-title="Unit Cost"] .btn-link,
        table.incoming-goods-card-layout tr.item-row td[data-title="Unit Cost"] .collapse {
            display: none !important;
        }

        /* Float Trash Button absolutely at Top Right of the card */
        table.incoming-goods-card-layout tr.item-row td[data-title="Action"] {
            position: absolute !important;
            top: 12px !important;
            right: 12px !important;
            z-index: 10 !important;
            width: auto !important;
        }
        table.incoming-goods-card-layout tr.item-row td[data-title="Action"]::before {
            display: none !important;
        }
        table.incoming-goods-card-layout tr.item-row td[data-title="Action"] .remove-row {
            background: #fff5f5 !important;
            color: #e53e3e !important;
            border: 1px solid #fed7d7 !important;
            border-radius: 50% !important;
            width: 36px !important;
            height: 36px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.2s ease !important;
        }
        table.incoming-goods-card-layout tr.item-row td[data-title="Action"] .remove-row:hover {
            background: #e53e3e !important;
            color: #ffffff !important;
            border-color: #e53e3e !important;
        }

        /* Sleek Card Line Total footer */
        table.incoming-goods-card-layout tr.item-row td[data-title="Line Total"] {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            border-top: 1px dashed #e2e8f0 !important;
            padding-top: 14px !important;
            margin-top: 6px !important;
        }
        table.incoming-goods-card-layout tr.item-row td[data-title="Line Total"]::before {
            content: "LINE TOTAL" !important;
            display: inline-block !important;
            font-size: 0.72rem !important;
            font-weight: 800 !important;
            color: #64748b !important;
            margin-bottom: 0 !important;
        }
        table.incoming-goods-card-layout tr.item-row td[data-title="Line Total"] .row-total-display {
            font-size: 1.15rem !important;
            font-weight: 800 !important;
            color: #4e73df !important;
            text-align: right !important;
        }

        /* Large Add Button Target on Mobile */
        .card-footer button.btn-outline-primary {
            width: 100% !important;
            padding: 12px !important;
            font-size: 0.95rem !important;
            border-radius: 10px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-weight: bold !important;
        }

        /* Sticky Top Bar Optimization for Native-App responsiveness */
        .sticky-top {
            padding: 12px 16px !important;
        }
        .sticky-top .container-fluid {
            padding: 0 !important;
        }
        .sticky-top .d-flex.justify-content-between {
            flex-direction: column !important;
            align-items: stretch !important;
        }
        .sticky-top .d-flex.justify-content-between > div:first-child {
            text-align: center !important;
            margin-bottom: 8px !important;
        }
        .sticky-top .d-flex.justify-content-between > div:first-child h5 {
            font-size: 1.05rem !important;
        }
        .sticky-top .d-flex.justify-content-between > div:first-child .small {
            display: none !important; /* hide subtitle to save real estate */
        }
        .sticky-top .d-flex.align-items-center {
            justify-content: space-between !important;
            width: 100% !important;
            gap: 12px !important;
        }
        .sticky-top .text-right {
            text-align: left !important;
            flex-grow: 1 !important;
        }
        .sticky-top #grand-total-display {
            font-size: 1.25rem !important;
        }
        .sticky-top .border-left {
            border-left: 0 !important;
            padding-left: 0 !important;
            margin-left: 0 !important;
            display: flex !important;
            gap: 8px !important;
        }
        .sticky-top .btn-success {
            padding: 8px 16px !important;
            font-size: 0.88rem !important;
            border-radius: 8px !important;
        }
        .sticky-top .btn-light {
            padding: 8px 12px !important;
            font-size: 0.88rem !important;
            border-radius: 8px !important;
        }
        
        /* General form margin tuning */
        .px-4 {
            padding-left: 12px !important;
            padding-right: 12px !important;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let itemIndex = 0;

$(document).ready(function() {
    $('.select2').select2({ theme: 'bootstrap4' });
    
    // Add first row automatically or pre-filled rows
    @if(count($prefill_items) > 0)
        @foreach($prefill_items as $item)
            addItemRow({
                id: {{$item['product_id']}},
                qty: {{$item['quantity']}},
                cost: {{$item['unit_cost']}}
            });
        @endforeach
    @else
        addItemRow();
    @endif

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
            <td class="align-middle border-0" data-title="Product">
                <div class="d-flex align-items-center">
                    <select name="items[${itemIndex}][product_id]" class="form-control select2-dynamic product-select" required>
                        <option value="">Select Product</option>
                        @foreach($products as $p)
                            <option value="{{$p->id}}" data-cost="{{$p->purchase_price}}" data-stock="{{$p->stock}}" ${product && product.id == {{$p->id}} ? 'selected' : ''}>
                                {{$p->title}} | {{ $p->brand->title ?? 'No Brand' }} | Rs. {{ number_format($p->purchase_price, 2) }}
                            </option>
                        @endforeach
                    </select>
                    <button type="button" class="btn btn-link btn-sm text-primary p-0 ml-2" data-toggle="modal" data-target="#addProductModal" title="Quick Add Product">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <div class="current-cost-info mt-1 small text-muted"></div>
            </td>
            <td class="align-middle border-0" data-title="Avail. Stock">
                <input type="number" name="items[${itemIndex}][available_stock]" class="form-control stock-input bg-light font-weight-bold" value="${product ? (product.stock || 0) : 0}" required>
                <small class="text-muted d-block mt-1">Live Correction</small>
            </td>
            <td class="align-middle border-0" data-title="New Qty">
                <input type="number" name="items[${itemIndex}][quantity]" class="form-control qty-input" min="0.01" step="0.01" value="${product ? product.qty : 1}" required>
            </td>
            <td class="align-middle border-0" data-title="Unit Cost">
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
            <td class="align-middle border-0 text-right" data-title="Line Total">
                <div class="row-total-display font-weight-bold text-dark">Rs. 0.00</div>
            </td>
            <td class="align-middle border-0 text-center" data-title="Action">
                <button type="button" class="btn btn-link text-danger remove-row"><i class="fas fa-trash-alt"></i></button>
            </td>
        </tr>
    `;
    
    let $html = $(html);
    $('#items-container').append($html);
    
    $html.find('.select2-dynamic').select2({ theme: 'bootstrap4' });
    
    $html.find('.product-select').on('change', function() {
        let cost = $(this).find(':selected').data('cost');
        let stock = $(this).find(':selected').data('stock');
        let $row = $(this).closest('.item-row');
        if(cost !== undefined) {
            $row.find('.current-cost-info').html('<i class="fas fa-info-circle mr-1"></i> Prev Cost: Rs. ' + parseFloat(cost).toFixed(2));
            $row.find('.cost-input').val(cost);
        } else {
            $row.find('.current-cost-info').empty();
        }
        if(stock !== undefined) {
            $row.find('.stock-input').val(stock);
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

</script>
@endpush
@endsection
