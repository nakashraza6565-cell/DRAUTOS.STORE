@extends('backend.layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Edit Packaging Purchase: {{ $purchase->invoice_no }}</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('packaging.purchases.update', $purchase->id) }}">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="packaging_item_id">Material <span class="text-danger">*</span></label>
                        <select name="packaging_item_id" class="form-control" required id="packaging_item_id">
                            @foreach($items as $item)
                            <option value="{{ $item->id }}" data-cost="{{ $item->cost }}" {{ $purchase->packaging_item_id == $item->id ? 'selected' : '' }}>
                                {{ strtoupper($item->type) }}: {{ $item->name }} ({{ $item->size ?? 'N/A' }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="supplier_id">Supplier</label>
                        <select name="supplier_id" class="form-control">
                            <option value="">-- Select Supplier --</option>
                            @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ $purchase->supplier_id == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="quantity">Quantity <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="quantity" id="quantity" class="form-control" value="{{ $purchase->quantity }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="price">Unit Price <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="price" id="price" class="form-control" value="{{ $purchase->price }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Total Price (Calculated)</label>
                        <input type="text" id="total_display" class="form-control" readonly style="background-color: #f8f9fc;" value="Rs. {{ number_format($purchase->total_price, 2) }}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="purchase_date">Purchase Date <span class="text-danger">*</span></label>
                        <input type="date" name="purchase_date" class="form-control" value="{{ $purchase->purchase_date }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="invoice_no">Invoice #</label>
                        <input type="text" class="form-control" value="{{ $purchase->invoice_no }}" readonly style="background-color: #f8f9fc;">
                        <small class="text-muted">Invoice number cannot be changed.</small>
                    </div>
                </div>
            </div>

            <div class="form-group mb-3">
                <button class="btn btn-success px-4" type="submit">Update Purchase Record</button>
                <a href="{{ route('packaging.purchases.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#packaging_item_id').change(function() {
            calculateTotal();
        });

        $('#quantity, #price').on('input', function() {
            calculateTotal();
        });

        function calculateTotal() {
            let qty = parseFloat($('#quantity').val()) || 0;
            let price = parseFloat($('#price').val()) || 0;
            let total = qty * price;
            $('#total_display').val('Rs. ' + total.toLocaleString(undefined, {minimumFractionDigits: 2}));
        }
    });
</script>
@endpush
