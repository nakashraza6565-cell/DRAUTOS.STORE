@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid p-0">
    <form action="{{route('inventory-incoming.update', $inventoryIncoming->id)}}" method="POST" id="incoming-form">
        @csrf
        @method('PUT')

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mx-4 mt-3" role="alert">
                <strong><i class="fas fa-exclamation-triangle mr-1"></i> Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mx-4 mt-3" role="alert">
                <strong><i class="fas fa-bug mr-1"></i> System Error:</strong>
                <div class="mt-2 small">{{ session('error') }}</div>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        {{-- STICKY TOOLBAR --}}
        <div class="sticky-top bg-white border-bottom shadow-sm mb-4" style="z-index: 1020; top: 0;">
            <div class="container-fluid py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-edit mr-2"></i> Edit Incoming Goods: {{$inventoryIncoming->reference_number}}
                        </h5>
                        <div class="small text-muted mt-1">Update draft details and items.</div>
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
                                <label class="small font-weight-bold text-uppercase text-info">Shipping Cost (Rs.)</label>
                                <input type="number" name="shipping_cost" id="shipping_cost" class="form-control form-control-lg font-weight-bold border-info" value="{{$inventoryIncoming->shipping_cost}}" min="0" step="0.01">
                            </div>
                            <div class="form-group">
                                <label class="small font-weight-bold text-uppercase">Received Date</label>
                                <input type="date" name="received_date" class="form-control" value="{{$inventoryIncoming->received_date->format('Y-m-d')}}" required>
                            </div>
                            <div class="form-group">
                                <label class="small font-weight-bold text-uppercase">Invoice # (Handwritten)</label>
                                <input type="text" name="invoice_number" class="form-control" value="{{$inventoryIncoming->invoice_number}}" placeholder="e.g. INV-2024-001">
                            </div>
                            <div class="form-group mb-0">
                                <label class="small font-weight-bold text-uppercase">Internal Note</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Add any special notes about this shipment...">{{$inventoryIncoming->notes}}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>


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
    // Initialize Select2 for main supplier
    $('#supplier_id').select2({ theme: 'bootstrap4', width: '100%' }).trigger('change');

    // Inject existing items
    @foreach($inventoryIncoming->items as $item)
        addItemRow({
            id: {{ $item->product_id }},
            title: {!! json_encode($item->product->title) !!},
            sku: {!! json_encode($item->product->sku) !!},
            qty: {{ $item->quantity }},
            cost: {{ $item->unit_cost }},
            total: {{ $item->total_cost }},
            pkg_id: "{{ $item->packaging_item_id }}",
            pkg_qty: {{ $item->packaging_quantity }},
            pkg_cost: {{ $item->packaging_cost }}
        });
    @endforeach

    // Add empty row if none
    if ($('#items-container tr').length === 0) {
        addItemRow();
    }

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

    $(document).on('input', '.qty-input, .cost-input, .pkg-cost-input, #shipping_cost', function() {
        updateGrandTotal();
    });
});

function updateGrandTotal() {
    let itemsTotal = 0;
    $('.item-row').each(function() {
        let qty = parseFloat($(this).find('.qty-input').val()) || 0;
        let cost = parseFloat($(this).find('.cost-input').val()) || 0;
        let pkgCost = parseFloat($(this).find('.pkg-cost-input').val()) || 0;
        let total = (qty * cost) + pkgCost;
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
                    <select name="items[${itemIndex}][product_id]" class="form-control select2-ajax product-select" required>
                        ${product ? `<option value="${product.id}" selected>${product.title} (${product.sku})</option>` : '<option value="">Search by Name/SKU/Barcode</option>'}
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
                            <option value="{{$pkg->id}}" ${product && product.pkg_id == "{{$pkg->id}}" ? 'selected' : ''}>{{$pkg->name}}</option>
                        @endforeach
                    </select>
                    <label class="small font-weight-bold">Additional Cost</label>
                    <input type="number" step="0.01" name="items[${itemIndex}][packaging_cost]" class="form-control form-control-sm pkg-cost-input" value="${product ? product.pkg_cost : 0}">
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
    
    $html.find('.select2-ajax').select2({ 
        theme: 'bootstrap4',
        ajax: {
            url: "{{route('inventory-incoming.search-products')}}",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            text: item.title + ' (' + item.sku + ')',
                            id: item.id,
                            cost: item.purchase_price
                        }
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 1,
        placeholder: 'Search by Name/SKU/Barcode'
    }).on('select2:select', function(e) {
        let data = e.params.data;
        let $row = $(this).closest('.item-row');
        if(data.cost) {
            $row.find('.current-cost-info').html('<i class="fas fa-info-circle mr-1"></i> Prev Cost: Rs. ' + parseFloat(data.cost).toFixed(2));
            $row.find('.cost-input').val(data.cost);
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

// Quick Add Handlers for Incoming Goods
$(document).on('submit', '#quickAddSupplierForm', function(e) {
    e.preventDefault();
    let $form = $(this);
    let $btn = $form.find('button[type="submit"]');
    $btn.prop('disabled', true).text('Saving...');

    $.post("{{route('supplier.quick-store')}}", $form.serialize() + "&_token={{csrf_token()}}", function(res) {
        if (res.status == 'success') {
            $('#supplier_id').append(new Option(res.supplier.name + ' (' + res.supplier.phone + ')', res.supplier.id, true, true)).trigger('change');
            $('#addSupplierModal').modal('hide');
            $form[0].reset();
            Swal.fire('Success', 'Supplier added successfully', 'success');
        }
    }).always(function() {
        $btn.prop('disabled', false).text('Create Supplier');
    });
});

$(document).on('submit', '#quickAddProductForm', function(e) {
    e.preventDefault();
    let $form = $(this);
    let $btn = $form.find('button[type="submit"]');
    $btn.prop('disabled', true).text('Creating...');

    $.post("{{route('product.quick-store')}}", $form.serialize() + "&_token={{csrf_token()}}&status=active", function(res) {
        if (res.status == 'success') {
            $('#addProductModal').modal('hide');
            $form[0].reset();
            Swal.fire({
                icon: 'success',
                title: 'Product Created',
                text: res.product.title + ' is now available for entry. Simply type the name in any row.',
                timer: 3000
            });
        }
    }).fail(function(err) {
        let msg = err.responseJSON && err.responseJSON.message ? err.responseJSON.message : 'Error creating product';
        Swal.fire('Error', msg, 'error');
    }).always(function() {
        $btn.prop('disabled', false).text('Create Product');
    });
});
</script>
@endpush
@endsection

@push('modals')
@include('backend.inventory.incoming.modals.quick_add_supplier')
@include('backend.inventory.incoming.modals.quick_add_product')
@endpush
