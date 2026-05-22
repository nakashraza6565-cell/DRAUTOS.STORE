@extends('backend.layouts.master')
 
@section('title','Create Purchase Return')
 
@section('main-content')
<div class="card shadow-sm">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-truck-loading mr-2"></i>Create Purchase Return
            </h5>
            <small class="text-muted">PO: <strong>{{$purchaseOrder->po_number}}</strong> &mdash; Supplier: <strong>{{$purchaseOrder->supplier->name ?? 'N/A'}}</strong></small>
        </div>
        <a href="{{route('returns.purchase.index')}}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Back</a>
    </div>
    <div class="card-body">
        @include('backend.layouts.notification')
 
        <form action="{{route('returns.purchase.store')}}" method="POST" id="returnForm">
            @csrf
            <input type="hidden" name="purchase_order_id" value="{{$purchaseOrder->id}}">
            <input type="hidden" name="supplier_id" value="{{$purchaseOrder->supplier_id}}">
 
            {{-- Return Details --}}
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold">Return Date <span class="text-danger">*</span></label>
                        <input type="date" name="return_date" class="form-control" value="{{date('Y-m-d')}}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold">Refund Method <span class="text-danger">*</span></label>
                        <select name="refund_method" class="form-control" required>
                            <option value="cash">Cash</option>
                            <option value="credit_note">Credit Applied to Balance</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold">Reference <small class="text-muted">(Optional)</small></label>
                        <input type="text" name="refund_reference" class="form-control" placeholder="e.g. Transaction ID">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="font-weight-bold">Reason for Return <small class="text-muted">(Optional)</small></label>
                <textarea name="reason" class="form-control" rows="2" placeholder="Describe reason for returning items to supplier..."></textarea>
            </div>
 
            <hr>
 
            {{-- Product Selection --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="font-weight-bold m-0"><i class="fas fa-boxes mr-2 text-primary"></i>Select Items to Return</h6>
                <div>
                    <button type="button" class="btn btn-outline-primary btn-sm mr-1" id="selectAllBtn">
                        <i class="fas fa-check-double mr-1"></i>Select All
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="clearAllBtn">
                        <i class="fas fa-times mr-1"></i>Clear All
                    </button>
                </div>
            </div>

            {{-- Search Bar --}}
            <div class="mb-3">
                <div class="input-group shadow-sm">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-right-0">
                            <i class="fas fa-search text-primary"></i>
                        </span>
                    </div>
                    <input type="text" id="productSearch" class="form-control border-left-0" placeholder="Search by product name or SKU...">
                    <div class="input-group-append" id="clearSearchBtnContainer" style="display: none;">
                        <button class="btn btn-outline-secondary border-left-0 text-muted" type="button" id="clearSearchBtn" style="border-color: #ced4da;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
 
            <div class="table-responsive">
                <table class="table table-bordered mb-0" id="itemsTable">
                    <thead class="thead-dark">
                        <tr>
                            <th width="40" class="text-center">
                                <input type="checkbox" id="masterCheck" title="Select/Deselect all">
                            </th>
                            <th>Product</th>
                            <th class="text-center">Purchase Cost</th>
                            <th class="text-center">Qty Purchased</th>
                            <th class="text-center" width="130">Return Qty</th>
                            <th class="text-center" width="170">Condition</th>
                            <th class="text-center">Notes</th>
                            <th class="text-right" width="130">Row Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchaseOrder->items as $item)
                        @if($item->product)
                        <tr class="item-row" data-price="{{$item->unit_cost}}" data-max="{{$item->quantity}}">
                            <td class="text-center align-middle">
                                <input type="checkbox" class="item-check">
                            </td>
                            <td class="align-middle">
                                <div class="font-weight-bold">{{$item->product->title}}</div>
                                @if($item->product->sku)
                                    <small class="text-muted">SKU: {{$item->product->sku}}</small>
                                @endif
                                <input type="hidden" class="field-product-id" value="{{$item->product_id}}" disabled>
                                <input type="hidden" class="field-unit-cost" value="{{$item->unit_cost}}" disabled>
                            </td>
                            <td class="text-center align-middle">
                                <span class="badge badge-light border">PKR {{number_format($item->unit_cost, 2)}}</span>
                            </td>
                            <td class="text-center align-middle font-weight-bold">
                                {{$item->quantity}}
                            </td>
                            <td class="text-center align-middle">
                                <input type="number" class="form-control form-control-sm return-qty text-center"
                                       min="1" max="{{$item->quantity}}"
                                       value="{{$item->quantity}}" disabled
                                       style="width:80px; margin: 0 auto;">
                            </td>
                            <td class="align-middle">
                                <select class="form-control form-control-sm field-condition" disabled>
                                    <option value="expired">Expired</option>
                                    <option value="damaged">Damaged</option>
                                    <option value="wrong_item">Wrong Item</option>
                                    <option value="other">Other</option>
                                </select>
                            </td>
                            <td class="align-middle">
                                <input type="text" class="form-control form-control-sm field-notes" placeholder="Optional..." disabled>
                            </td>
                            <td class="text-right align-middle">
                                <span class="row-total font-weight-bold text-danger">PKR 0.00</span>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-dark">
                            <td colspan="7" class="text-right font-weight-bold">
                                <span id="selectedCount" class="badge badge-light mr-2">0 items selected</span>
                                Total Return Amount:
                            </td>
                            <td class="text-right">
                                <span id="grandTotal" class="font-weight-bold text-warning" style="font-size:1.2em;">PKR 0.00</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
 
            <div id="noItemsWarning" class="alert alert-warning mt-3 d-none">
                <i class="fas fa-exclamation-triangle mr-2"></i>Please select at least one item to return.
            </div>
 
            <div class="text-right mt-4">
                <a href="{{route('returns.purchase.index')}}" class="btn btn-secondary mr-2">Cancel</a>
                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                    <i class="fas fa-check mr-2"></i>Process Purchase Return
                </button>
            </div>
        </form>
    </div>
</div>
 
<style>
    .item-row.dimmed td { opacity: 0.4; }
    .item-row.dimmed td:first-child { opacity: 1; }
    .item-row.selected { background-color: #fffaf0; }
    .item-row.selected td { opacity: 1; }
</style>
@endsection
 
@push('scripts')
<script>
    $(document).ready(function() {
        $('.item-row').addClass('dimmed');
 
        // Product Search logic
        $('#productSearch').on('input keyup', function() {
            let query = $(this).val().toLowerCase().trim();
            let visibleCount = 0;

            // Remove existing no results row if any
            $('#noResultsRow').remove();

            if (query === '') {
                $('.item-row').show();
                $('#clearSearchBtnContainer').hide();
            } else {
                $('#clearSearchBtnContainer').show();

                $('.item-row').each(function() {
                    let title = $(this).find('.font-weight-bold').text().toLowerCase();
                    let sku = $(this).find('.text-muted').text().toLowerCase();

                    if (title.indexOf(query) !== -1 || sku.indexOf(query) !== -1) {
                        $(this).show();
                        visibleCount++;
                    } else {
                        $(this).hide();
                    }
                });

                if (visibleCount === 0) {
                    $('#itemsTable tbody').append(
                        '<tr id="noResultsRow">' +
                        '  <td colspan="8" class="text-center text-muted py-4">' +
                        '    <i class="fas fa-search fa-2x mb-2 text-gray-400"></i>' +
                        '    <p class="mb-0 font-weight-bold">No products match your search</p>' +
                        '    <small>Try checking the spelling or use a different keyword</small>' +
                        '  </td>' +
                        '</tr>'
                    );
                }
            }
        });

        // Clear Search Button
        $('#clearSearchBtn').on('click', function() {
            $('#productSearch').val('').trigger('input');
        });
 
        $('.item-check').on('change', function() {
            let row = $(this).closest('.item-row');
            let isChecked = $(this).is(':checked');
            let rowIndex = row.index();
 
            if (isChecked) {
                row.removeClass('dimmed').addClass('selected');
                row.find('input, select').not('.item-check').prop('disabled', false);
                
                row.find('.field-product-id').attr('name', 'items[' + rowIndex + '][product_id]');
                row.find('.field-unit-cost').attr('name', 'items[' + rowIndex + '][unit_cost]');
                row.find('.return-qty').attr('name', 'items[' + rowIndex + '][quantity]');
                row.find('.field-condition').attr('name', 'items[' + rowIndex + '][condition]');
                row.find('.field-notes').attr('name', 'items[' + rowIndex + '][notes]');
            } else {
                row.removeClass('selected').addClass('dimmed');
                row.find('input, select').not('.item-check').prop('disabled', true);
                row.find('input, select').not('.item-check').removeAttr('name');
            }
            
            updateRowTotal(row);
            updateSummary();
        });
 
        $('.return-qty').on('input', function() {
            let row = $(this).closest('.item-row');
            let max = parseInt(row.data('max'));
            let qty = parseInt($(this).val()) || 0;
            if (qty > max) $(this).val(max);
            if (qty < 1) $(this).val(1);
            updateRowTotal(row);
            updateSummary();
        });
 
        function updateRowTotal(row) {
            let qty = parseInt(row.find('.return-qty').val()) || 0;
            let price = parseFloat(row.data('price'));
            let total = qty * price;
            row.find('.row-total').text('PKR ' + total.toLocaleString(undefined, {minimumFractionDigits: 2}));
        }
 
        function updateSummary() {
            let selectedCount = $('.item-check:checked').length;
            let grandTotal = 0;
            $('.item-row.selected').each(function() {
                let qty = parseInt($(this).find('.return-qty').val()) || 0;
                let price = parseFloat($(this).data('price'));
                grandTotal += qty * price;
            });
            $('#selectedCount').text(selectedCount + ' items selected');
            $('#grandTotal').text('PKR ' + grandTotal.toLocaleString(undefined, {minimumFractionDigits: 2}));
            if (selectedCount > 0) $('#noItemsWarning').addClass('d-none');
        }
 
        $('#masterCheck').on('change', function() {
            $('.item-check').prop('checked', $(this).is(':checked')).trigger('change');
        });
 
        $('#selectAllBtn').on('click', function() {
            $('.item-check').prop('checked', true).trigger('change');
            $('#masterCheck').prop('checked', true);
        });
 
        $('#clearAllBtn').on('click', function() {
            $('.item-check').prop('checked', false).trigger('change');
            $('#masterCheck').prop('checked', false);
        });
 
        $('#returnForm').on('submit', function(e) {
            if ($('.item-check:checked').length === 0) {
                e.preventDefault();
                $('#noItemsWarning').removeClass('d-none');
                return false;
            }
            $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Processing...');
        });
    });
</script>
@endpush
