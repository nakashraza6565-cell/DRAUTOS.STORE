@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            @include('backend.layouts.notification')
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Incoming Goods Details: {{ $inventoryIncoming->reference_number }}</h6>
            <div>
                <a href="{{ route('inventory-incoming.print-barcodes', $inventoryIncoming->id) }}" class="btn btn-secondary btn-sm" target="_blank">
                    <i class="fas fa-barcode"></i> Print Barcodes
                </a>
                <a href="{{ route('admin.supplier-ledger.thermal', $inventoryIncoming->supplier_id) }}" class="btn btn-info btn-sm" target="_blank">
                    <i class="fas fa-file-invoice-dollar"></i> Print Supplier Ledger
                </a>
                <a href="{{ route('inventory-incoming.thermal', $inventoryIncoming->id) }}" class="btn btn-warning btn-sm" target="_blank">
                    <i class="fas fa-print"></i> Thermal Print
                </a>
                <a href="{{ route('inventory-incoming.index') }}" class="btn btn-dark btn-sm">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="border rounded p-3 bg-light">
                        <label class="text-xs font-weight-bold text-uppercase text-muted mb-1 d-block">Reference Number</label>
                        <div class="h5 mb-3 font-weight-bold text-gray-800">{{ $inventoryIncoming->reference_number }}</div>
                        
                        <label class="text-xs font-weight-bold text-uppercase text-muted mb-1 d-block">Received Date</label>
                        <div class="h6 mb-3 font-weight-bold text-gray-800">{{ $inventoryIncoming->received_date->format('d M Y') }}</div>
                        
                        <label class="text-xs font-weight-bold text-uppercase text-muted mb-1 d-block">Status</label>
                        <div>
                            @if($inventoryIncoming->status == 'pending')
                                <span class="badge badge-warning px-3 py-2">Pending</span>
                            @elseif($inventoryIncoming->status == 'verified')
                                <span class="badge badge-info px-3 py-2">Verified</span>
                            @else
                                <span class="badge badge-success px-3 py-2">Completed</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3">
                        <label class="text-xs font-weight-bold text-uppercase text-muted mb-1 d-block">Supplier Details</label>
                        <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $inventoryIncoming->supplier->name ?? 'N/A' }}</div>
                        <div class="text-gray-600 small mb-3">{{ $inventoryIncoming->supplier->company ?? '' }}</div>
                        
                        <label class="text-xs font-weight-bold text-uppercase text-muted mb-1 d-block">Warehouse / Location</label>
                        <div class="h6 mb-3 font-weight-bold text-gray-800">{{ $inventoryIncoming->warehouse->name ?? 'Default' }}</div>
                        
                        <label class="text-xs font-weight-bold text-uppercase text-muted mb-1 d-block">Invoice Number</label>
                        <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $inventoryIncoming->invoice_number ?: 'N/A' }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3">
                        <label class="text-xs font-weight-bold text-uppercase text-muted mb-1 d-block">Received By</label>
                        <div class="h6 mb-3 font-weight-bold text-gray-800">{{ $inventoryIncoming->receiver->name ?? 'N/A' }}</div>
                        
                        <label class="text-xs font-weight-bold text-uppercase text-muted mb-1 d-block">Total Cost</label>
                        <div class="h5 mb-3 font-weight-bold text-success grand-total-display">PKR {{ number_format($inventoryIncoming->items->sum('total_cost'), 2) }}</div>
                        
                        @if($inventoryIncoming->status == 'pending')
                            <form method="POST" action="{{ route('inventory-incoming.verify', $inventoryIncoming->id) }}">
                                @csrf
                                <button class="btn btn-primary btn-block shadow-sm">
                                    <i class="fas fa-check-circle mr-1"></i> Verify Batch
                                </button>
                            </form>
                        @else
                            @if(!$ledgerExists && $inventoryIncoming->supplier_id && $inventoryIncoming->items->sum('total_cost') > 0)
                                <form method="POST" action="{{ route('inventory-incoming.verify', $inventoryIncoming->id) }}" class="mb-2">
                                    @csrf
                                    <button class="btn btn-danger btn-block shadow-sm">
                                        <i class="fas fa-file-invoice-dollar mr-1"></i> Post to Supplier Ledger
                                    </button>
                                </form>
                            @endif

                            @if($inventoryIncoming->status == 'verified')
                                <form method="POST" action="{{ route('inventory-incoming.complete', $inventoryIncoming->id) }}">
                                    @csrf
                                    <button class="btn btn-success btn-block shadow-sm">
                                        <i class="fas fa-flag-checkered mr-1"></i> Mark as Completed
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">Items in this Batch</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="items-table">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th>#</th>
                                    <th>Item Name / SKU</th>
                                    <th>Batch #</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-right">Unit Cost</th>
                                    <th class="text-right">Total Cost</th>
                                    <th class="text-center">Barcodes</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($inventoryIncoming->items as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="editable-product-field" data-id="{{ $item->id }}" data-field="product_id" title="Double click to change product">
                                        <div class="display-value">
                                            <div class="font-weight-bold text-dark product-title-text">{{ $item->product->title }}</div>
                                            <div class="text-muted small product-sku-text">SKU: {{ $item->product->sku }}</div>
                                        </div>
                                        <div class="edit-input-wrapper d-none">
                                            <select class="form-control form-control-sm select2 product-edit-dropdown" style="width: 100%;">
                                                @foreach($products as $p)
                                                    <option value="{{ $p->id }}" {{ $p->id == $item->product_id ? 'selected' : '' }} data-sku="{{ $p->sku }}">{{ $p->title }} (SKU: {{ $p->sku }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @if($item->packaging_item_id)
                                        <div class="mt-1">
                                            <span class="badge badge-info shadow-sm" style="font-size: 10px;">
                                                <i class="fas fa-box-open mr-1"></i> 
                                                Pkg: {{ $item->packagingItem->name }} ({{ number_format($item->packaging_quantity, 2) }})
                                                @if($item->packaging_cost > 0)
                                                 - Cost: PKR {{ number_format($item->packaging_cost, 2) }}
                                                @endif
                                            </span>
                                        </div>
                                        @endif
                                    </td>
                                    <td>{{ $item->batch_number ?: '-' }}</td>
                                    <td class="text-center font-weight-bold text-primary editable-field" data-id="{{$item->id}}" data-field="quantity" title="Double click to edit">
                                        <span class="display-value">{{ $item->quantity }}</span>
                                        <input type="number" class="form-control form-control-sm d-none edit-input" value="{{ $item->quantity }}" style="width: 80px; margin: 0 auto;">
                                    </td>
                                    <td class="text-right editable-field" data-id="{{$item->id}}" data-field="unit_cost" title="Double click to edit">
                                        <span class="display-value">{{ number_format($item->unit_cost, 2) }}</span>
                                        <input type="number" step="0.01" class="form-control form-control-sm d-none edit-input text-right" value="{{ $item->unit_cost }}" style="width: 100px; margin-left: auto;">
                                    </td>
                                    <td class="text-right font-weight-bold item-total">PKR {{ number_format($item->total_cost, 2) }}</td>
                                    <td class="text-center">
                                        @if($item->barcode_printed)
                                            <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Printed</span>
                                        @else
                                            <span class="badge badge-light text-muted border">Not Printed</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-danger btn-sm delete-item-btn" data-id="{{ $item->id }}" title="Delete from batch">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach

                                <!-- Inline Add Product Row -->
                                <tr class="bg-light add-product-row">
                                    <td class="text-center"><i class="fas fa-plus text-primary"></i></td>
                                    <td>
                                        <select id="new-product-select" class="form-control select2" style="width: 100%;">
                                            <option value="">-- Add Product to Batch --</option>
                                            @foreach($products as $p)
                                                <option value="{{ $p->id }}" data-cost="{{ $p->purchase_price }}">{{ $p->title }} (SKU: {{ $p->sku }})</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" id="new-product-batch" class="form-control form-control-sm" placeholder="Batch #">
                                    </td>
                                    <td class="text-center">
                                        <input type="number" id="new-product-qty" class="form-control form-control-sm text-center" placeholder="Qty" style="width: 80px; margin: 0 auto;" min="0.01" step="any">
                                    </td>
                                    <td class="text-right">
                                        <input type="number" id="new-product-cost" class="form-control form-control-sm text-right" placeholder="Cost" style="width: 100px; margin-left: auto;" min="0" step="0.01">
                                    </td>
                                    <td class="text-right font-weight-bold text-dark" id="new-product-total-display">PKR 0.00</td>
                                    <td class="text-center">
                                        <span class="badge badge-light text-muted border">Pending</span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" id="add-new-item-btn" class="btn btn-primary btn-sm btn-block">
                                            <i class="fas fa-plus"></i> Add
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-light">
                                <tr class="font-weight-bold">
                                    <td colspan="3" class="text-right">Totals:</td>
                                    <td class="text-center">{{ $inventoryIncoming->items->sum('quantity') }}</td>
                                    <td class="text-right">-</td>
                                    <td class="text-right grand-total-display">PKR {{ number_format($inventoryIncoming->items->sum('total_cost'), 2) }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            @if($inventoryIncoming->notes)
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="alert alert-light border">
                        <h6 class="font-weight-bold"><i class="fas fa-sticky-note mr-2 text-warning"></i>Notes:</h6>
                        <p class="mb-0 text-gray-700 italic">{{ $inventoryIncoming->notes }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    .bg-gray-100 { background-color: #f8f9fc; }
    .text-gray-800 { color: #5a5c69; }
    .editable-field, .editable-product-field { cursor: pointer; transition: background 0.2s; }
    .editable-field:hover, .editable-product-field:hover { background-color: #f0f4ff; }
</style>

<script>
$(document).ready(function() {
    // Initialize standard select2 dropdowns
    $('.select2').select2({ width: '100%' });

    // Double click to edit qty/cost
    $('.editable-field').on('dblclick', function() {
        let td = $(this);
        td.find('.display-value').addClass('d-none');
        td.find('.edit-input').removeClass('d-none').focus().select();
    });

    // Save qty/cost on Enter, Cancel on Escape
    $('.edit-input').on('keyup', function(e) {
        let input = $(this);
        let td = input.closest('.editable-field');
        let id = td.data('id');
        let field = td.data('field');

        if (e.which === 13) { // Enter
            saveField(id, td);
        } else if (e.which === 27) { // Escape
            input.addClass('d-none');
            td.find('.display-value').removeClass('d-none');
        }
    });

    // Save qty/cost on focus out
    $('.edit-input').on('blur', function() {
        let input = $(this);
        let td = input.closest('.editable-field');
        let id = td.data('id');
        saveField(id, td);
    });

    function saveField(id, td) {
        let input = td.find('.edit-input');
        if (input.hasClass('d-none')) return;

        let tr = td.closest('tr');
        let qty = tr.find('[data-field="quantity"] .edit-input').val();
        let cost = tr.find('[data-field="unit_cost"] .edit-input').val();

        $.ajax({
            url: "/admin/inventory-incoming/item/" + id + "/update",
            type: "POST",
            data: {
                quantity: qty,
                unit_cost: cost,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response.success) {
                    tr.find('[data-field="quantity"] .display-value').text(qty);
                    tr.find('[data-field="unit_cost"] .display-value').text(parseFloat(cost).toLocaleString(undefined, {minimumFractionDigits: 2}));
                    tr.find('.item-total').text('PKR ' + response.new_total);
                    $('.grand-total-display').text('PKR ' + response.grand_total);
                    
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000
                    });
                    Toast.fire({ icon: 'success', title: response.message });
                }
                
                input.addClass('d-none');
                td.find('.display-value').removeClass('d-none');
            },
            error: function(err) {
                Swal.fire('Error', err.responseJSON?.message || 'Something went wrong', 'error');
                input.addClass('d-none');
                td.find('.display-value').removeClass('d-none');
            }
        });
    }

    // Double click to edit product name (dropdown)
    $('.editable-product-field').on('dblclick', function() {
        let td = $(this);
        td.find('.display-value').addClass('d-none');
        let wrapper = td.find('.edit-input-wrapper');
        wrapper.removeClass('d-none');
        let select = wrapper.find('.product-edit-dropdown');
        
        if (!select.hasClass('select2-hidden-accessible')) {
            select.select2({ width: '100%' });
        }
        select.select2('open');
    });

    // Save product change on dropdown change
    $(document).on('change', '.product-edit-dropdown', function() {
        let select = $(this);
        let td = select.closest('.editable-product-field');
        let id = td.data('id');
        let val = select.val();
        if (!val) return;
        
        let tr = td.closest('tr');
        let qty = tr.find('[data-field="quantity"] .edit-input').val();
        let cost = tr.find('[data-field="unit_cost"] .edit-input').val();
        
        $.ajax({
            url: "/admin/inventory-incoming/item/" + id + "/update",
            type: "POST",
            data: {
                product_id: val,
                quantity: qty,
                unit_cost: cost,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response.success) {
                    let opt = select.find('option:selected');
                    td.find('.product-title-text').text(opt.text().replace(/\s*\(SKU:.*\)/g, ''));
                    td.find('.product-sku-text').text('SKU: ' + opt.data('sku'));
                    $('.grand-total-display').text('PKR ' + response.grand_total);
                    
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000
                    });
                    Toast.fire({ icon: 'success', title: 'Product changed successfully.' });
                }
                
                td.find('.edit-input-wrapper').addClass('d-none');
                td.find('.display-value').removeClass('d-none');
            },
            error: function(err) {
                Swal.fire('Error', err.responseJSON?.message || 'Something went wrong', 'error');
                td.find('.edit-input-wrapper').addClass('d-none');
                td.find('.display-value').removeClass('d-none');
            }
        });
    });

    // Reset dropdown on close without select
    $(document).on('select2:close', '.product-edit-dropdown', function() {
        let select = $(this);
        setTimeout(function() {
            let td = select.closest('.editable-product-field');
            td.find('.edit-input-wrapper').addClass('d-none');
            td.find('.display-value').removeClass('d-none');
        }, 200);
    });

    // Delete item from batch
    $(document).on('click', '.delete-item-btn', function() {
        let btn = $(this);
        let id = btn.data('id');
        let tr = btn.closest('tr');
        
        Swal.fire({
            title: 'Are you sure?',
            text: 'This will delete this product from the batch and adjust its stock!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b',
            cancelButtonColor: '#858796',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "/admin/inventory-incoming/item/" + id + "/delete",
                    type: "DELETE",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            tr.fadeOut(400, function() {
                                tr.remove();
                                $('.grand-total-display').text('PKR ' + response.grand_total);
                                recalculateFooterTotals();
                            });
                            Swal.fire('Deleted!', response.message, 'success');
                        }
                    },
                    error: function(err) {
                        Swal.fire('Error', err.responseJSON?.message || 'Something went wrong', 'error');
                    }
                });
            }
        });
    });

    function recalculateFooterTotals() {
        let totalQty = 0;
        let totalCost = 0;
        
        $('#items-table tbody tr').not('.add-product-row').each(function() {
            let row = $(this);
            let qtyVal = parseFloat(row.find('[data-field="quantity"] .edit-input').val()) || 0;
            totalQty += qtyVal;
            
            let itemTotalText = row.find('.item-total').text().replace(/[^\d.]/g, '');
            totalCost += parseFloat(itemTotalText) || 0;
        });
        
        $('#items-table tfoot tr td').eq(1).text(totalQty);
    }

    // Inline Add Product Row handlers
    $('#new-product-select').on('change', function() {
        let select = $(this);
        let cost = select.find('option:selected').data('cost') || 0;
        $('#new-product-cost').val(parseFloat(cost).toFixed(2));
        calculateNewItemTotal();
    });

    $('#new-product-qty, #new-product-cost').on('input', function() {
        calculateNewItemTotal();
    });

    function calculateNewItemTotal() {
        let qty = parseFloat($('#new-product-qty').val()) || 0;
        let cost = parseFloat($('#new-product-cost').val()) || 0;
        let total = qty * cost;
        $('#new-product-total-display').text('PKR ' + total.toLocaleString(undefined, {minimumFractionDigits: 2}));
    }

    $('#add-new-item-btn').on('click', function() {
        let productId = $('#new-product-select').val();
        let qty = $('#new-product-qty').val();
        let cost = $('#new-product-cost').val();
        let batchNum = $('#new-product-batch').val();
        
        if (!productId) {
            Swal.fire('Warning', 'Please select a product.', 'warning');
            return;
        }
        if (!qty || parseFloat(qty) <= 0) {
            Swal.fire('Warning', 'Please enter a valid quantity.', 'warning');
            return;
        }
        if (cost === '' || parseFloat(cost) < 0) {
            Swal.fire('Warning', 'Please enter a valid cost.', 'warning');
            return;
        }
        
        $.ajax({
            url: "{{ route('inventory-incoming.item.add', $inventoryIncoming->id) }}",
            type: "POST",
            data: {
                product_id: productId,
                quantity: qty,
                unit_cost: cost,
                batch_number: batchNum,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire('Added!', response.message, 'success').then(() => {
                        window.location.reload();
                    });
                }
            },
            error: function(err) {
                Swal.fire('Error', err.responseJSON?.message || 'Something went wrong', 'error');
            }
        });
    });
});
</script>
@endpush
