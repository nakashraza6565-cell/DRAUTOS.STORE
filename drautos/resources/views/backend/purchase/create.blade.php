@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3" style="background: #f8fafc;">
            <h6 class="m-0 font-weight-bold text-primary">Create Purchase Order</h6>
        </div>
        <div class="card-body">
            <form method="post" action="{{route('purchase-orders.store')}}">
                {{csrf_field()}}
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="supplier_id" class="col-form-label font-weight-bold">Supplier <span class="text-danger">*</span></label>
                            <select name="supplier_id" id="supplier_id" class="form-control selectpicker" data-live-search="true" required>
                                <option value="">-- Select Supplier --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{$supplier->id}}">{{$supplier->name}} ({{$supplier->company_name}})</option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="order_date" class="col-form-label font-weight-bold">Order Date <span class="text-danger">*</span></label>
                            <input id="order_date" type="date" name="order_date" value="{{date('Y-m-d')}}" class="form-control" required>
                            @error('order_date')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="expected_delivery_date" class="col-form-label font-weight-bold">Expected Delivery</label>
                            <input id="expected_delivery_date" type="date" name="expected_delivery_date" class="form-control">
                            @error('expected_delivery_date')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr class="my-4">
                <h6 class="font-weight-bold text-secondary mb-3">Order Items</h6>

                <div class="table-responsive">
                    <table class="table table-bordered" id="items-table">
                        <thead class="bg-light text-dark">
                            <tr>
                                <th style="width: 40%;">Product</th>
                                <th>Quantity</th>
                                <th>Unit Price (Rs.)</th>
                                <th>Subtotal (Rs.)</th>
                                <th style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="items-body">
                            <tr>
                                <td>
                                    <select name="product_id[]" class="form-control selectpicker product-select" data-live-search="true" data-container="body" required>
                                        <option value="">-- Select Product --</option>
                                        @foreach($products as $product)
                                            <option value="{{$product->id}}" data-price="{{$product->purchase_price}}">{{$product->title}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="quantity[]" class="form-control qty-input" min="1" value="1" required>
                                </td>
                                <td>
                                    <input type="number" name="unit_price[]" class="form-control price-input" min="0" step="0.01" required>
                                </td>
                                <td>
                                    <input type="number" class="form-control subtotal-input" readonly value="0.00">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-item rounded-circle"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right font-weight-bold">Grand Total:</td>
                                <td colspan="2"><h5 class="m-0 font-weight-bold text-primary">Rs. <span id="grand-total">0.00</span></h5></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="mb-4">
                    <button type="button" id="add-item" class="btn btn-info btn-sm rounded-pill"><i class="fas fa-plus mr-1"></i> Add Another Product</button>
                </div>

                <hr class="my-4 border-top">
                <h6 class="font-weight-bold text-info mb-3"><i class="fas fa-money-bill-wave mr-2"></i>Payment Information</h6>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="paid_amount" class="col-form-label font-weight-bold">Amount Paid (Initial)</label>
                            <input id="paid_amount" type="number" name="paid_amount" value="0" step="0.01" class="form-control" placeholder="PKR 0.00">
                            <small class="text-muted">Enter 0 if no initial payment is made.</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="col-form-label font-weight-bold">Pending Balance</label>
                            <input id="pending_display" type="text" class="form-control" readonly value="0.00">
                            <small class="text-muted">Remaining amount to be paid.</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="due_date" class="col-form-label font-weight-bold">Balance Due Date</label>
                            <input id="due_date" type="date" name="due_date" class="form-control">
                            <small class="text-muted">Deadline for remaining payment.</small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="notes" class="col-form-label font-weight-bold">Notes / Special Instructions</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Enter any specific details about this order..."></textarea>
                </div>

                <div class="form-group mb-0 text-right">
                    <button type="reset" class="btn btn-light rounded-pill px-4 mr-2 border">Reset Form</button>
                    <button class="btn btn-primary rounded-pill px-5 shadow-sm" type="submit">Create Order</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css">
<style>
    .form-control:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.1rem rgba(78, 115, 223, 0.25);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"></script>
<script>
    $(document).ready(function() {
        // Function to calculate totals
        function calculateTotals() {
            let grandTotal = 0;
            $('#items-body tr').each(function() {
                let qty = parseFloat($(this).find('.qty-input').val()) || 0;
                let price = parseFloat($(this).find('.price-input').val()) || 0;
                let subtotal = qty * price;
                $(this).find('.subtotal-input').val(subtotal.toFixed(2));
                grandTotal += subtotal;
            });
            $('#grand-total').text(grandTotal.toFixed(2));

            let paid = parseFloat($('#paid_amount').val()) || 0;
            let pending = grandTotal - paid;
            $('#pending_display').val(pending.toFixed(2));
        }

        // Recalculate on paid amount change
        $('#paid_amount').on('input', function() {
            calculateTotals();
        });

        // Add item
        $('#add-item').click(function() {
            let row = $('#items-body tr:first').clone();
            row.find('input').val('');
            row.find('.qty-input').val(1);
            row.find('.subtotal-input').val('0.00');
            
            // Re-initialize selectpicker for cloned row
            row.find('.bootstrap-select').replaceWith(function() { return $('select', this); });
            $('#items-body').append(row);
            $('.selectpicker').selectpicker('render');
        });

        // Remove item
        $(document).on('click', '.remove-item', function() {
            if ($('#items-body tr').length > 1) {
                $(this).closest('tr').remove();
                calculateTotals();
            }
        });

        // Price update on product select
        $(document).on('change', '.product-select', function() {
            let price = $(this).find(':selected').data('price') || 0;
            $(this).closest('tr').find('.price-input').val(price);
            calculateTotals();
        });

        // Recalculate on input
        $(document).on('input', '.qty-input, .price-input', function() {
            calculateTotals();
        });
    });
</script>
@endpush
