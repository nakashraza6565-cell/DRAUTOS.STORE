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
                    <div class="input-group">
                        <select name="supplier_id" id="supplier_select" class="form-control select2" required>
                            <option value="">-- Select Supplier --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{$supplier->id}}">{{$supplier->name}} (Balance: {{$supplier->current_balance}})</option>
                            @endforeach
                        </select>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#quickAddSupplierModal" title="Quick Add Supplier">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 form-group">
                    <label>Date <span class="text-danger">*</span></label>
                    <input type="date" name="date" class="form-control" value="{{date('Y-m-d')}}" required>
                </div>
            </div>

            <hr>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Items</h5>
                <button type="button" class="btn btn-sm btn-info shadow-sm" data-toggle="modal" data-target="#quickAddMaterialModal"><i class="fas fa-plus fa-sm text-white-50"></i> Add New Material</button>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered" id="itemsTable">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th width="40%">Raw Material / Factor</th>
                            <th width="15%">Quantity</th>
                            <th width="15%">Unit</th>
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
                                        <option value="{{$factor->id}}" data-unit="{{$factor->unit}}">{{$factor->name}} ({{$factor->unit}})</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" step="0.01" name="items[0][quantity]" class="form-control quantity-input" placeholder="Qty" required>
                            </td>
                            <td class="align-middle text-center">
                                <span class="unit-display font-weight-bold text-muted">-</span>
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
                            <td colspan="5">
                                <button type="button" class="btn btn-success btn-sm" id="addRowBtn"><i class="fas fa-plus"></i> Add Another Item</button>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="3" class="text-right">Grand Total:</th>
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
                    <option value="{{$factor->id}}" data-unit="{{$factor->unit}}">{{$factor->name}} ({{$factor->unit}})</option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" step="0.01" name="items[INDEX][quantity]" class="form-control quantity-input" placeholder="Qty" required>
        </td>
        <td class="align-middle text-center">
            <span class="unit-display font-weight-bold text-muted">-</span>
        </td>
        <td>
            <input type="number" step="0.01" name="items[INDEX][total_cost]" class="form-control cost-input" placeholder="Total Cost" required>
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
        </td>
    </tr>
</template>

<!-- Quick Add Supplier Modal -->
<div class="modal fade" id="quickAddSupplierModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Quick Add Supplier</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="quickAddSupplierForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Supplier Name *</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. John Doe">
                    </div>
                    <div class="form-group">
                        <label>Company Name</label>
                        <input type="text" name="company_name" class="form-control" placeholder="e.g. Steel Mill Ltd">
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" class="form-control" placeholder="e.g. 03001234567">
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" name="address" class="form-control" placeholder="e.g. Industrial Area Lahore">
                    </div>
                    <input type="hidden" name="status" value="active">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveSupplierBtn">Save Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Quick Add Material Modal -->
<div class="modal fade" id="quickAddMaterialModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Quick Add Raw Material</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="quickAddMaterialForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Material / Factor Name *</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Raw Steel">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Default Stock *</label>
                            <input type="number" name="stock" class="form-control" value="0" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Purchase / Cost Price</label>
                            <input type="number" step="0.01" name="cost_price" class="form-control" value="0">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label>Unit (e.g. kg, pcs, hr)</label>
                            <input type="text" name="unit" class="form-control" value="piece">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-info" id="saveMaterialBtn">Save Material</button>
                </div>
            </form>
        </div>
    </div>
</div>

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

        // Handle unit display update on material selection change
        $(document).on('change', '.factor-select', function() {
            let selectedOption = $(this).find(':selected');
            let unit = selectedOption.data('unit') || '-';
            $(this).closest('tr').find('.unit-display').text(unit);
        });

        // Trigger change initially to set correct unit display for initial row
        $('.factor-select').trigger('change');

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

        // Quick Add Supplier AJAX Submission
        $('#quickAddSupplierForm').submit(function(e) {
            e.preventDefault();
            let form = $(this);
            let btn = $('#saveSupplierBtn');
            
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
            
            $.ajax({
                url: "{{route('supplier.quick-store')}}",
                type: "POST",
                data: form.serialize(),
                success: function(response) {
                    btn.prop('disabled', false).text('Save Supplier');
                    if (response.status === 'success') {
                        let newOption = new Option(response.supplier.name + ' (Balance: 0.00)', response.supplier.id, true, true);
                        $('#supplier_select').append(newOption).trigger('change');
                        
                        form[0].reset();
                        $('#quickAddSupplierModal').modal('hide');
                        
                        alert('Supplier added successfully!');
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('Save Supplier');
                    let errors = xhr.responseJSON;
                    alert('Error: ' + (errors ? errors.message : 'Something went wrong'));
                }
            });
        });

        // Quick Add Material AJAX Submission
        $('#quickAddMaterialForm').submit(function(e) {
            e.preventDefault();
            let form = $(this);
            let btn = $('#saveMaterialBtn');
            
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
            
            $.ajax({
                url: "{{route('manufacturing.production-factors.quick-store')}}",
                type: "POST",
                data: form.serialize() + "&_token={{csrf_token()}}",
                success: function(response) {
                    btn.prop('disabled', false).text('Save Material');
                    if(response.status == 'success') {
                        // Create option elements for dropdowns
                        let optionText = response.factor.name + ' (' + response.factor.unit + ')';
                        let factorId = response.factor.id;
                        let factorUnit = response.factor.unit;
                        
                        // Form option
                        let newOption = $('<option>', {
                            value: factorId,
                            text: optionText
                        }).attr('data-unit', factorUnit);
                        
                        // Append to all current selects and select it in the active/focused select row if possible,
                        // or just append to all .factor-select dropdowns
                        $('.factor-select').append(newOption.clone()).trigger('change');
                        
                        // Also append to the rowTemplate for future added rows
                        let templateHtml = $('#rowTemplate').html();
                        let updatedTemplate = templateHtml.replace('</select>', '<option value="'+factorId+'" data-unit="'+factorUnit+'">'+optionText+'</option></select>');
                        $('#rowTemplate').html(updatedTemplate);
                        
                        // Reset and close modal
                        form[0].reset();
                        $('#quickAddMaterialModal').modal('hide');
                        
                        alert('Raw Material added successfully!');
                    } else {
                        alert(response.message || 'Error adding material');
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('Save Material');
                    alert('An error occurred. Check your input.');
                }
            });
        });

        $('#purchaseForm').submit(function() {
            $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        });
    });
</script>
@endpush
