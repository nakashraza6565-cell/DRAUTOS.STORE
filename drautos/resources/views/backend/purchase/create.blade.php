@extends('backend.layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3" style="background: #f8fafc;">
        <h6 class="m-0 font-weight-bold text-primary">Create Purchase Order (Request)</h6>
    </div>
    <div class="card-body">
        @include('backend.layouts.notification')
        <form method="post" action="{{route('purchase-orders.store')}}">
            {{csrf_field()}}

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="supplier_id" class="col-form-label font-weight-bold">Supplier <span class="text-danger">*</span></label>
                        <select name="supplier_id" id="supplier_id" class="form-control select2" required>
                            <option value="">-- Select Supplier --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{$supplier->id}}">{{$supplier->name}} ({{$supplier->company_name}})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="order_date" class="col-form-label font-weight-bold">Order Date <span class="text-danger">*</span></label>
                        <input id="order_date" type="date" name="order_date" value="{{date('Y-m-d')}}" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="card border-0 bg-light mb-4 mt-3">
                <div class="card-body">
                    <h6 class="font-weight-bold mb-3">Add Products to Request</h6>
                    <div id="items-container">
                        <div class="item-row row mb-2 align-items-end">
                            <div class="col-md-7">
                                <label class="small font-weight-bold mb-1">Search Product</label>
                                <select class="form-control select2 product-select">
                                    <option value="">--Select Product--</option>
                                    @foreach($products as $product)
                                    <option value="{{$product->id}}" data-unit="{{$product->unit}}">
                                        {{$product->title}} 
                                        @if($product->brand) | {{$product->brand->title}} @endif
                                        | Rs. {{number_format($product->purchase_price, 0)}}
                                        @if($product->sku) ({{$product->sku}}) @endif
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small font-weight-bold mb-1">Quantity</label>
                                <div class="input-group">
                                    <input type="number" class="form-control qty-input" value="1" min="0.1" step="any">
                                    <div class="input-group-append">
                                        <span class="input-group-text unit-display" style="min-width: 60px;">Unit</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-primary btn-block add-item"><i class="fas fa-plus"></i> ADD</button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mt-3">
                        <table class="table table-sm bg-white rounded shadow-sm" id="added-items-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>Product / Description</th>
                                    <th width="150" class="text-right">Quantity</th>
                                    <th width="50"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Items populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="notes" class="col-form-label font-weight-bold">Notes / Special Instructions</label>
                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Enter any specific details about this order..."></textarea>
            </div>

            <div class="form-group mb-0 text-right">
                <button type="submit" class="btn btn-success px-5 shadow-sm font-weight-bold" id="submit-order" disabled>SAVE PURCHASE ORDER</button>
                <a href="{{route('purchase-orders.index')}}" class="btn btn-secondary px-4 ml-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single { height: 38px !important; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 38px !important; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px !important; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({ width: '100%' });

        let itemsCount = 0;

        $('.product-select').on('change', function() {
            let unit = $(this).find(':selected').data('unit') || 'Unit';
            $('.unit-display').text(unit);
        });

        $('.add-item').on('click', function() {
            let productSelect = $('.product-select');
            let productId = productSelect.val();
            let productName = productSelect.find(':selected').text();
            let unit = productSelect.find(':selected').data('unit') || '';
            let qty = parseFloat($('.qty-input').val());

            if (!productId || qty <= 0) {
                alert('Please select a product and valid quantity');
                return;
            }

            // Check if item already exists
            let existing = false;
            $('#added-items-table tbody tr').each(function() {
                if($(this).find('input[name*="product_id"]').val() == productId) {
                    existing = true;
                    return false;
                }
            });

            if(existing) {
                alert('Product already added to list');
                return;
            }

            addItemToTable(productId, productName, qty, unit);

            // Reset inputs
            productSelect.val('').trigger('change');
            $('.qty-input').val(1);
            $('.unit-display').text('Unit');
        });

        function addItemToTable(productId, productName, qty, unit) {
            let row = `
                <tr class="item-added-row">
                    <td class="align-middle">
                        <input type="hidden" name="product_id[]" value="${productId}">
                        <div class="font-weight-bold text-gray-900">${productName}</div>
                    </td>
                    <td class="align-middle" width="180">
                        <div class="input-group input-group-sm">
                            <input type="number" name="quantity[]" value="${qty}" class="form-control font-weight-bold text-right" min="0.1" step="any" required>
                            <div class="input-group-append">
                                <span class="input-group-text small">${unit}</span>
                            </div>
                        </div>
                    </td>
                    <td class="align-middle text-center">
                        <button type="button" class="btn btn-link text-danger p-0 remove-item"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;

            $('#added-items-table tbody').append(row);
            itemsCount++;
            $('#submit-order').prop('disabled', false);
        }

        $(document).on('click', '.remove-item', function() {
            $(this).closest('tr').remove();
            if ($('#added-items-table tbody tr').length === 0) {
                $('#submit-order').prop('disabled', true);
            }
        });
    });
</script>
@endpush
