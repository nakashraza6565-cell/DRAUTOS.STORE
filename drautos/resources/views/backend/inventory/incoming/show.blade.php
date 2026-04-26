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
                        @elseif($inventoryIncoming->status == 'verified')
                        <form method="POST" action="{{ route('inventory-incoming.complete', $inventoryIncoming->id) }}">
                            @csrf
                            <button class="btn btn-success btn-block shadow-sm">
                                <i class="fas fa-flag-checkered mr-1"></i> Mark as Completed
                            </button>
                        </form>
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
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($inventoryIncoming->items as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="font-weight-bold text-dark">{{ $item->product->title }}</div>
                                        <div class="text-muted small">SKU: {{ $item->product->sku }}</div>
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
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr class="font-weight-bold">
                                    <td colspan="3" class="text-right">Totals:</td>
                                    <td class="text-center">{{ $inventoryIncoming->items->sum('quantity') }}</td>
                                    <td class="text-right">-</td>
                                    <td class="text-right grand-total-display">PKR {{ number_format($inventoryIncoming->items->sum('total_cost'), 2) }}</td>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    .bg-gray-100 { background-color: #f8f9fc; }
    .text-gray-800 { color: #5a5c69; }
    .editable-field { cursor: pointer; transition: background 0.2s; }
    .editable-field:hover { background-color: #f0f4ff; }
</style>

<script>
$(document).ready(function() {
    // Double click to edit
    $('.editable-field').on('dblclick', function() {
        let td = $(this);
        td.find('.display-value').addClass('d-none');
        td.find('.edit-input').removeClass('d-none').focus().select();
    });

    // Save on Enter, Cancel on Escape
    $('.edit-input').on('keyup', function(e) {
        let input = $(this);
        let td = input.closest('.editable-field');
        let id = td.data('id');
        let field = td.data('field');
        let originalVal = td.find('.display-value').text();

        if (e.which === 13) { // Enter
            saveField(id, td);
        } else if (e.which === 27) { // Escape
            input.addClass('d-none');
            td.find('.display-value').removeClass('d-none');
        }
    });

    // Save on focus out
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
                    // Update current row
                    tr.find('[data-field="quantity"] .display-value').text(qty);
                    tr.find('[data-field="unit_cost"] .display-value').text(parseFloat(cost).toLocaleString(undefined, {minimumFractionDigits: 2}));
                    tr.find('.item-total').text('PKR ' + response.new_total);
                    
                    // Update Grand Totals
                    $('.grand-total-display').text('PKR ' + response.grand_total);
                    
                    // Toast success
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
});
</script>
@endpush
