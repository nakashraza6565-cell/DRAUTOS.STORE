@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid p-0">
    <form method="POST" action="{{route('packaging.purchases.store')}}">
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

        {{-- STICKY TOOLBAR --}}
        <div class="sticky-top bg-white border-bottom shadow-sm mb-4" style="z-index: 1020; top: 0;">
            <div class="container-fluid py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-box-open mr-2"></i> Record Packaging Purchase
                        </h5>
                        <div class="small text-muted mt-1">Consolidate multiple materials into a single purchase invoice.</div>
                    </div>
                    <div class="d-flex align-items-center" style="gap: 20px;">
                        <div class="text-right">
                            <div class="small text-muted font-weight-bold text-uppercase">Grand Total</div>
                            <div id="grand_total_display" class="h4 mb-0 font-weight-bold text-primary">Rs. 0.00</div>
                        </div>
                        <div class="border-left pl-3 ml-3">
                            <button type="submit" class="btn btn-success px-4 py-2 font-weight-bold shadow-sm">
                                <i class="fas fa-save mr-1"></i> Complete Purchase
                            </button>
                            <a href="{{route('packaging.purchases.index')}}" class="btn btn-light border ml-2">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-4">
            <div class="row">
                {{-- INVOICE META & SUPPLIER INFO --}}
                <div class="col-12">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-file-invoice mr-2 text-primary"></i> Invoice Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="supplier_id" class="font-weight-bold small text-uppercase">Supplier</label>
                                        <select name="supplier_id" class="form-control select2">
                                            <option value="">-- No Supplier (Walk-in / Cash) --</option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{$supplier->id}}">{{$supplier->name}} ({{$supplier->phone ?: 'No Phone'}})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="purchase_date" class="font-weight-bold small text-uppercase">Purchase Date <span class="text-danger">*</span></label>
                                        <input type="date" name="purchase_date" class="form-control" value="{{date('Y-m-d')}}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="invoice_no" class="font-weight-bold small text-uppercase">Invoice # (Optional)</label>
                                        <input type="text" name="invoice_no" class="form-control" placeholder="Leave blank for auto-generate">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ITEMS GRID --}}
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-list mr-2 text-primary"></i> Multiple Materials Purchase Grid</h6>
                            <button type="button" class="btn btn-outline-primary btn-sm font-weight-bold" onclick="addItemRow()">
                                <i class="fas fa-plus mr-1"></i> Add Row
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="items_table">
                                    <thead class="bg-light small font-weight-bold text-uppercase">
                                        <tr>
                                            <th style="width: 40%;">Material Description</th>
                                            <th style="width: 20%;" class="text-right">Quantity</th>
                                            <th style="width: 20%;" class="text-right">Unit Price (Rs.)</th>
                                            <th style="width: 15%;" class="text-right">Line Total</th>
                                            <th style="width: 5%;" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="items_body">
                                        <!-- Rows added dynamically via JS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top-0 py-3">
                            <button type="button" class="btn btn-primary font-weight-bold" onclick="addItemRow()">
                                <i class="fas fa-plus-circle mr-1"></i> ADD ANOTHER ITEM
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">
<style>
    .select2-container--bootstrap4 .select2-selection--single { height: 38px !important; }
    .sticky-top {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let itemIndex = 0;

    $(document).ready(function() {
        $('.select2').select2({ theme: 'bootstrap4', width: '100%' });

        // Add first row on load
        addItemRow();

        // Bind input calculations
        $(document).on('input change', '.qty-input, .price-input', function() {
            calculateRowTotal($(this).closest('tr'));
            calculateGrandTotal();
        });
    });

    function addItemRow() {
        let rowHtml = `
            <tr class="item-row">
                <td class="align-middle">
                    <select name="items[${itemIndex}][packaging_item_id]" class="form-control select2-dynamic material-select" required>
                        <option value="">-- Select Material --</option>
                        @foreach($items as $item)
                            <option value="{{$item->id}}" data-cost="{{$item->cost}}">
                                {{strtoupper($item->type)}}: {{$item->name}} ({{$item->size ?? 'N/A'}})
                            </option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" step="0.01" name="items[${itemIndex}][quantity]" class="form-control qty-input text-right font-weight-bold" placeholder="0.00" min="0.01" required>
                </td>
                <td>
                    <input type="number" step="0.01" name="items[${itemIndex}][price]" class="form-control price-input text-right" placeholder="0.00" min="0" required>
                </td>
                <td class="text-right align-middle">
                    <span class="line-total-display font-weight-bold text-dark">Rs. 0.00</span>
                </td>
                <td class="text-center align-middle">
                    <button type="button" class="btn btn-link text-danger remove-row p-0"><i class="fas fa-trash-alt fa-lg"></i></button>
                </td>
            </tr>
        `;

        let $row = $(rowHtml);
        $('#items_body').append($row);

        // Initialize dynamic select2
        $row.find('.select2-dynamic').select2({ theme: 'bootstrap4', width: '100%' });

        // Auto pre-fill cost when material is changed
        $row.find('.material-select').on('change', function() {
            let cost = $(this).find(':selected').data('cost');
            let $rowEl = $(this).closest('tr');
            if (cost !== undefined && cost !== '') {
                $rowEl.find('.price-input').val(cost);
            } else {
                $rowEl.find('.price-input').val('');
            }
            calculateRowTotal($rowEl);
            calculateGrandTotal();
        });

        // Bind delete action
        $row.find('.remove-row').on('click', function() {
            if ($('.item-row').length > 1) {
                $(this).closest('tr').remove();
                calculateGrandTotal();
            } else {
                Swal.fire('Info', 'At least one item is required in the purchase grid.', 'info');
            }
        });

        itemIndex++;
    }

    function calculateRowTotal($row) {
        let qty = parseFloat($row.find('.qty-input').val()) || 0;
        let price = parseFloat($row.find('.price-input').val()) || 0;
        let total = qty * price;
        $row.find('.line-total-display').text('Rs. ' + total.toLocaleString(undefined, {minimumFractionDigits: 2}));
    }

    function calculateGrandTotal() {
        let grandTotal = 0;
        $('.item-row').each(function() {
            let qty = parseFloat($(this).find('.qty-input').val()) || 0;
            let price = parseFloat($(this).find('.price-input').val()) || 0;
            grandTotal += qty * price;
        });
        $('#grand_total_display').text('Rs. ' + grandTotal.toLocaleString(undefined, {minimumFractionDigits: 2}));
    }
</script>
@endpush
