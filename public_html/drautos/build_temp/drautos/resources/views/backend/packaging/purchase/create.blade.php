@extends('backend.layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Record Packaging Purchase</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="{{route('packaging.purchases.store')}}">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="packaging_item_id">Material <span class="text-danger">*</span></label>
                        <select name="packaging_item_id" class="form-control" required id="packaging_item_id">
                            <option value="">-- Select Material --</option>
                            @foreach($items as $item)
                            <option value="{{$item->id}}" data-cost="{{$item->cost}}">
                                {{strtoupper($item->type)}}: {{$item->name}} ({{$item->size ?? 'N/A'}})
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
                            <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="quantity">Quantity <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="quantity" id="quantity" class="form-control" required placeholder="e.g. 1000">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="price">Unit Price (at time of purchase) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="price" id="price" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Total Estimated</label>
                        <input type="text" id="total_display" class="form-control" readonly style="background-color: #f8f9fc;">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="purchase_date">Purchase Date <span class="text-danger">*</span></label>
                        <input type="date" name="purchase_date" class="form-control" value="{{date('Y-m-d')}}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="invoice_no">Invoice # (Optional)</label>
                        <input type="text" name="invoice_no" class="form-control" placeholder="Leave blank for auto-generate">
                    </div>
                </div>
            </div>

            <div class="form-group mb-3">
                <button class="btn btn-success px-4" type="submit">Complete Purchase</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#packaging_item_id').change(function() {
            let cost = $(this).find(':selected').data('cost');
            if(cost) {
                $('#price').val(cost);
            }
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
