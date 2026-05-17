@extends('backend.layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Receive Incoming Raw Materials</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="{{route('manufacturing.production-factors.purchase.store')}}" id="purchaseForm">
            @csrf
            
            <div class="row">
                <div class="col-md-6 form-group">
                    <label>Supplier <span class="text-danger">*</span></label>
                    <select name="supplier_id" class="form-control select2" required>
                        <option value="">-- Select Supplier --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{$supplier->id}}">{{$supplier->name}} (Balance: {{$supplier->current_balance}})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label>Date <span class="text-danger">*</span></label>
                    <input type="date" name="date" class="form-control" value="{{date('Y-m-d')}}" required>
                </div>
            </div>

            <hr>
            <h5 class="mb-3">Items</h5>
            <div class="table-responsive">
                <table class="table table-bordered" id="itemsTable">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th width="40%">Raw Material / Factor</th>
                            <th width="20%">Quantity</th>
                            <th width="20%">Total Cost</th>
                            <th width="10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select name="items[0][factor_id]" class="form-control select2 factor-select" required>
                                    <option value="">-- Select Material --</option>
                                    @foreach($factors as $factor)
                                        <option value="{{$factor->id}}">{{$factor->name}} ({{$factor->unit}})</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" step="0.01" name="items[0][quantity]" class="form-control quantity-input" placeholder="Qty" required>
                            </td>
                            <td>
                                <input type="number" step="0.01" name="items[0][total_cost]" class="form-control cost-input" placeholder="Total Cost" required>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4">
                                <button type="button" class="btn btn-success btn-sm" id="addRowBtn"><i class="fas fa-plus"></i> Add Another Item</button>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="2" class="text-right">Grand Total:</th>
                            <th colspan="2" id="grandTotalDisplay">Rs. 0.00</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="form-group mb-3 text-right">
                <a href="{{route('manufacturing.production-factors.index')}}" class="btn btn-secondary">Cancel</a>
                <button class="btn btn-info" type="submit"><i class="fas fa-save"></i> Save & Receive Materials</button>
            </div>
        </form>
    </div>
</div>

<template id="rowTemplate">
    <tr>
        <td>
            <select name="items[INDEX][factor_id]" class="form-control factor-select" required>
                <option value="">-- Select Material --</option>
                @foreach($factors as $factor)
                    <option value="{{$factor->id}}">{{$factor->name}} ({{$factor->unit}})</option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" step="0.01" name="items[INDEX][quantity]" class="form-control quantity-input" placeholder="Qty" required>
        </td>
        <td>
            <input type="number" step="0.01" name="items[INDEX][total_cost]" class="form-control cost-input" placeholder="Total Cost" required>
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
        </td>
    </tr>
</template>

@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single {
        height: 38px !important;
        border: 1px solid #d1d3e2 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px !important;
        color: #6e707e !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2();
        
        let rowIdx = 1;

        function updateGrandTotal() {
            let total = 0;
            $('.cost-input').each(function() {
                let val = parseFloat($(this).val());
                if (!isNaN(val)) {
                    total += val;
                }
            });
            $('#grandTotalDisplay').text('Rs. ' + total.toFixed(2));
        }

        $('#addRowBtn').click(function() {
            let html = $('#rowTemplate').html().replace(/INDEX/g, rowIdx);
            $('#itemsTable tbody').append(html);
            $('#itemsTable tbody tr:last .factor-select').select2();
            rowIdx++;
        });

        $(document).on('click', '.remove-row', function() {
            if ($('#itemsTable tbody tr').length > 1) {
                $(this).closest('tr').remove();
                updateGrandTotal();
            } else {
                alert('You must have at least one item.');
            }
        });

        $(document).on('input', '.cost-input', function() {
            updateGrandTotal();
        });

        $('#purchaseForm').submit(function() {
            $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        });
    });
</script>
@endpush
