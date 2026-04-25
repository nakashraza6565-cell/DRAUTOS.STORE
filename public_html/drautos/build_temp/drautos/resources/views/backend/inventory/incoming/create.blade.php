@extends('backend.layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Add Incoming Goods Entry</h6>
    </div>
    <div class="card-body">
        <form action="{{route('inventory-incoming.store')}}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Supplier</label>
                        <select name="supplier_id" class="form-control">
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Warehouse</label>
                        <select name="warehouse_id" class="form-control">
                            <option value="">Select Warehouse</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Received Date <span class="text-danger">*</span></label>
                        <input type="date" name="received_date" class="form-control" value="{{date('Y-m-d')}}" required>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Invoice Number</label>
                        <input type="text" name="invoice_number" class="form-control" placeholder="Handwritten invoice #">
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" class="form-control" rows="2"></textarea>
            </div>
            
            <hr>
            
            <h5>Items<span class="text-muted small ml-2">(Search and add products)</span></h5>
            
            <div class="form-group">
                <input type="text" id="product-search" class="form-control" placeholder="Search product by name, SKU or barcode...">
            </div>
            
            <div id="items-container" class="mb-3">
                <!-- Items will be added here dynamically -->
            </div>
            
            <button type="button" class="btn btn-secondary btn-sm mb-3" onclick="addItemRow()">
                <i class="fas fa-plus"></i> Add Item Manually
            </button>
            
            <div class="form-group mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Entry
                </button>
                <a href="{{route('inventory-incoming.index')}}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
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
<script>
let itemIndex = 0;

// Initialize Select2 on existing selects
$(document).ready(function() {
    $('.search-select').select2({ theme: 'bootstrap4' });
    addItemRow();
});

function addItemRow(product = null) {
    let html = `
        <div class="card mb-2 item-row">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-md-4">
                        <label>Product</label>
                        <select name="items[${itemIndex}][product_id]" class="form-control select2-dynamic" required>
                            <option value="">Select Product</option>
                            @foreach($products as $p)
                                <option value="{{$p->id}}" ${product && product.id == {{$p->id}} ? 'selected' : ''}>{{$p->title}} ({{$p->sku}})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Quantity</label>
                        <input type="number" name="items[${itemIndex}][quantity]" class="form-control" min="1" value="${product ? product.qty : 1}" required>
                    </div>
                    <div class="col-md-2">
                        <label>Unit Cost</label>
                        <input type="number" step="0.01" name="items[${itemIndex}][unit_cost]" class="form-control" min="0" value="${product ? product.cost : 0}" required>
                    </div>
                    <div class="col-md-3">
                        <label>Batch Number</label>
                        <input type="text" name="items[${itemIndex}][batch_number]" class="form-control">
                    </div>
                    <div class="col-md-1">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-sm d-block" onclick="$(this).closest('.item-row').remove()">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>

                <!-- NEW: Packaging Information Section -->
                <div class="row mt-3" style="background: #fdfaf9; padding: 10px; border: 1px dashed #e2e8f0; border-radius: 5px; margin-left: 0; margin-right: 0;">
                    <div class="col-md-4">
                        <label class="small font-weight-bold text-info"><i class="fas fa-sticky-note"></i> Packaging Material (Optional)</label>
                        <select name="items[${itemIndex}][packaging_item_id]" class="form-control form-control-sm">
                            <option value="">-- No Packaging --</option>
                            @foreach($packaging_items as $pkg)
                                <option value="{{$pkg->id}}">{{strtoupper($pkg->type)}}: {{$pkg->name}} ({{$pkg->size ?? 'N/A'}})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="small font-weight-bold text-info">Pkg Quantity Used</label>
                        <input type="number" step="0.01" name="items[${itemIndex}][packaging_quantity]" class="form-control form-control-sm" placeholder="e.g. 1">
                    </div>
                    <div class="col-md-4">
                        <label class="small font-weight-bold text-info">Pkg Cost (Additional)</label>
                        <input type="number" step="0.01" name="items[${itemIndex}][packaging_cost]" class="form-control form-control-sm" placeholder="0.00">
                    </div>
                </div>
            </div>
        </div>
    `;
    
    let $html = $(html);
    $('#items-container').append($html);
    
    // Initialize Select2 for the new row
    $html.find('.select2-dynamic').select2({
        theme: 'bootstrap4',
        placeholder: 'Search product...'
    });
    
    itemIndex++;
}

// Product search logic
$('#product-search').on('keyup', function() {
    // ...
});
</script>
@endpush
@endsection
