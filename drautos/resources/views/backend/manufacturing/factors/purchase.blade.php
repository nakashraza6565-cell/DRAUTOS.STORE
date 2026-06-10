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
            <table class="table table-bordered mobile-block-table" id="itemsTable">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th width="30%">Raw Material / Factor</th>
                            <th width="12%">Quantity</th>
                            <th width="15%">Per Unit Cost</th>
                            <th width="10%">Unit</th>
                            <th width="18%">Total Cost</th>
                            <th width="10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td data-label="Raw Material / Factor" class="mob-full">
                                <select name="items[0][factor_id]" class="form-control select2 factor-select" required>
                                    <option value="">-- Select Material --</option>
                                    @foreach($factors as $factor)
                                        <option value="{{$factor->id}}" data-unit="{{$factor->unit}}" data-cost="{{$factor->cost_price}}">{{$factor->name}} ({{$factor->unit}})</option>
                                    @endforeach
                                </select>
                            </td>
                            <td data-label="Quantity" class="mob-half">
                                <input type="number" step="0.01" name="items[0][quantity]" class="form-control form-control-sm quantity-input" placeholder="Qty" required>
                            </td>
                            <td data-label="Per Unit Cost" class="mob-hide">
                                <input type="number" step="0.01" name="items[0][per_unit_cost]" class="form-control form-control-sm per-unit-cost-input" placeholder="Cost/Unit" required>
                            </td>
                            <td class="align-middle text-center mob-hide" data-label="Unit">
                                <span class="unit-display font-weight-bold text-muted">-</span>
                            </td>
                            <td data-label="Total Cost" class="mob-half">
                                <input type="number" step="0.01" name="items[0][total_cost]" class="form-control form-control-sm cost-input" placeholder="Total Cost" required>
                            </td>
                            <td data-label="Action" class="mob-hide">
                                <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
                            </td>
                            <td class="mob-full p-0 border-0 text-center">
                                <button type="button" class="expand-row-btn d-md-none"><i class="fas fa-chevron-down"></i> Edit Details</button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6">
                                <button type="button" class="btn btn-success btn-sm" id="addRowBtn"><i class="fas fa-plus"></i> Add Another Item</button>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="4" class="text-right">Grand Total:</th>
                            <th colspan="2" id="grandTotalDisplay">Rs. 0.00</th>
                        </tr>
                    </tfoot>
                </table>

            <div class="form-group mb-3 text-right">
                <a href="{{route('manufacturing.production-factors.index')}}" class="btn btn-secondary">Cancel</a>
                <button class="btn btn-info" type="submit"><i class="fas fa-save"></i> Save & Receive Materials</button>
            </div>
        </form>
    </div>
</div>

<template id="rowTemplate">
    <tr>
        <td data-label="Raw Material / Factor" class="mob-full">
            <select name="items[INDEX][factor_id]" class="form-control factor-select" required>
                <option value="">-- Select Material --</option>
                @foreach($factors as $factor)
                    <option value="{{$factor->id}}" data-unit="{{$factor->unit}}" data-cost="{{$factor->cost_price}}">{{$factor->name}} ({{$factor->unit}})</option>
                @endforeach
            </select>
        </td>
        <td data-label="Quantity" class="mob-half">
            <input type="number" step="0.01" name="items[INDEX][quantity]" class="form-control form-control-sm quantity-input" placeholder="Qty" required>
        </td>
        <td data-label="Per Unit Cost" class="mob-hide">
            <input type="number" step="0.01" name="items[INDEX][per_unit_cost]" class="form-control form-control-sm per-unit-cost-input" placeholder="Cost/Unit" required>
        </td>
        <td class="align-middle text-center mob-hide" data-label="Unit">
            <span class="unit-display font-weight-bold text-muted">-</span>
        </td>
        <td data-label="Total Cost" class="mob-half">
            <input type="number" step="0.01" name="items[INDEX][total_cost]" class="form-control form-control-sm cost-input" placeholder="Total Cost" required>
        </td>
        <td data-label="Action" class="mob-hide">
            <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
        </td>
        <td class="mob-full p-0 border-0 text-center">
            <button type="button" class="expand-row-btn d-md-none"><i class="fas fa-chevron-down"></i> Edit Details</button>
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

    /* Clean inputs */
    .mobile-block-table input.form-control {
        border: 1px solid #d1d3e2;
        border-radius: 4px;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);
    }
    .mobile-block-table input.form-control:focus {
        border-color: #bac8f3;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }

    /* Mobile responsive tables - Accordion Style */
    @media (max-width: 768px) {
        .mobile-block-table thead { display: none; }
        .mobile-block-table tbody tr { 
            display: flex;
            flex-wrap: wrap;
            border: 1px solid #e3e6f0; 
            border-radius: 8px; 
            margin-bottom: 15px; 
            padding: 10px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.05); 
            position: relative;
        }
        .mobile-block-table tbody td { 
            display: block; 
            border: none !important; 
            padding: 4px 2px !important; 
        }
        .mobile-block-table tbody td::before { 
            content: attr(data-label); 
            font-weight: 700; 
            display: block; 
            margin-bottom: 5px; 
            color: #4e73df; 
            font-size: 0.75rem; 
        }
        .mobile-block-table tfoot td { 
            display: block; 
            width: 100%; 
            border: none; 
        }
        
        /* Accordion Column Logic */
        .mob-full { width: 100% !important; }
        .mob-half { width: 50% !important; }
        
        /* Hide secondary columns by default */
        .mob-hide { 
            display: none !important; 
            width: 100% !important; 
        }
        /* Show when expanded */
        tr.expanded .mob-hide { 
            display: block !important; 
            animation: fadeIn 0.3s ease;
        }
        
        .expand-row-btn {
            width: 100%;
            background: #f8f9fc;
            border: 1px solid #e3e6f0;
            color: #4e73df;
            border-radius: 4px;
            padding: 6px;
            margin-top: 8px;
            font-size: 0.8rem;
            font-weight: bold;
            transition: all 0.2s;
        }
        .expand-row-btn:active { background: #e3e6f0; }

        /* Ultra-compact single line when collapsed */
        .mobile-block-table tbody tr:not(.expanded) {
            cursor: pointer;
            background: #fdfdfd;
            flex-wrap: nowrap !important;
            padding: 10px 12px !important;
            align-items: center;
        }
        .mobile-block-table tbody tr:not(.expanded):hover {
            background: #f1f5f9;
        }
        .mobile-block-table tbody tr:not(.expanded) td {
            padding: 0 4px !important;
            width: auto !important;
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        /* Flex ratios for the single line */
        .mobile-block-table tbody tr:not(.expanded) td:nth-child(1) { flex: 2.5; }
        .mobile-block-table tbody tr:not(.expanded) td:nth-child(2) { flex: 1; text-align: center; }
        .mobile-block-table tbody tr:not(.expanded) td:nth-child(5) { flex: 1.5; text-align: right; } /* Total Cost */
        
        /* Hide Data Labels completely */
        .mobile-block-table tbody tr:not(.expanded) td::before {
            display: none !important;
        }

        /* Smaller text & disable inputs visually */
        .mobile-block-table tbody tr:not(.expanded) input.form-control,
        .mobile-block-table tbody tr:not(.expanded) .select2-selection__rendered {
            border: none !important;
            background: transparent !important;
            box-shadow: none !important;
            pointer-events: none !important;
            color: #475569 !important;
            padding: 0 !important;
            height: auto !important;
            font-size: 0.85rem !important;
            font-weight: 700 !important;
            text-align: inherit;
        }
        .mobile-block-table tbody tr:not(.expanded) .select2-container .select2-selection--single {
            border: none !important;
            background: transparent !important;
        }
        .mobile-block-table tbody tr:not(.expanded) .select2-selection__arrow {
            display: none !important;
        }

        /* Hide the Expand button completely in collapsed view, since the whole row is clickable */
        .mobile-block-table tbody tr:not(.expanded) td:last-child {
            display: none !important;
        }
    }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
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

        // Handle unit display update and default cost preloading on material selection change
        $(document).on('change', '.factor-select', function() {
            let selectedOption = $(this).find(':selected');
            let unit = selectedOption.data('unit') || '-';
            let cost = parseFloat(selectedOption.data('cost')) || 0;
            
            let row = $(this).closest('tr');
            row.find('.unit-display').text(unit);
            
            // Preload default cost if not already filled or if just changed
            if (cost > 0) {
                row.find('.per-unit-cost-input').val(cost);
                
                // If quantity exists, calculate total
                let qty = parseFloat(row.find('.quantity-input').val()) || 0;
                if (qty > 0) {
                    row.find('.cost-input').val((qty * cost).toFixed(2));
                    updateGrandTotal();
                }
            }
        });

        // Trigger change initially to set correct unit display for initial row
        $('.factor-select').trigger('change');

        // Bidirectional Calculations
        $(document).on('input', '.quantity-input, .per-unit-cost-input', function() {
            let row = $(this).closest('tr');
            let qty = parseFloat(row.find('.quantity-input').val()) || 0;
            let rate = parseFloat(row.find('.per-unit-cost-input').val()) || 0;
            
            if (qty >= 0 && rate >= 0) {
                row.find('.cost-input').val((qty * rate).toFixed(2));
            }
            updateGrandTotal();
        });

        $(document).on('input', '.cost-input', function() {
            let row = $(this).closest('tr');
            let qty = parseFloat(row.find('.quantity-input').val()) || 0;
            let total = parseFloat(row.find('.cost-input').val()) || 0;
            
            if (qty > 0 && total >= 0) {
                row.find('.per-unit-cost-input').val((total / qty).toFixed(2));
            }
            updateGrandTotal();
        });

        $('#addRowBtn').click(function() {
            // Auto-collapse existing rows
            $('#itemsTable tbody tr').removeClass('expanded');
            $('#itemsTable tbody tr .expand-row-btn').html('<i class="fas fa-chevron-down"></i> Edit Details');

            let html = $('#rowTemplate').html().replace(/INDEX/g, rowIdx);
            $('#itemsTable tbody').append(html);
            $('#itemsTable tbody tr:last .factor-select').select2();
            rowIdx++;

            // Auto-expand the newly added row
            $('#itemsTable tbody tr:last').addClass('expanded');
            $('#itemsTable tbody tr:last .expand-row-btn').html('<i class="fas fa-chevron-up"></i> Hide Details');
        });

        $(document).on('click', '.remove-row', function() {
            if ($('#itemsTable tbody tr').length > 1) {
                $(this).closest('tr').remove();
                updateGrandTotal();
            } else {
                alert('You must have at least one item.');
            }
        });

        // Expand first row on load for UX
        $('#itemsTable tbody tr:first').addClass('expanded').find('.expand-row-btn').html('<i class="fas fa-chevron-up"></i> Hide Details');

        // Click anywhere on a collapsed row to expand it
        $(document).on('click', '.mobile-block-table tbody tr:not(.expanded)', function(e) {
            if(!$(e.target).closest('.expand-row-btn').length && !$(e.target).closest('button').length) {
                $(this).find('.expand-row-btn').click();
            }
        });

        // Expand/Collapse Mobile Accordion Row
        $(document).on('click', '.expand-row-btn', function() {
            let tr = $(this).closest('tr');
            tr.toggleClass('expanded');
            if(tr.hasClass('expanded')) {
                $(this).html('<i class="fas fa-chevron-up"></i> Hide Details');
            } else {
                $(this).html('<i class="fas fa-chevron-down"></i> Edit Details');
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
                        let optionText = response.factor.name + ' (' + response.factor.unit + ')';
                        let factorId = response.factor.id;
                        let factorUnit = response.factor.unit;
                        let factorCost = response.factor.cost_price ?? 0;
                        
                        // Append option to all existing dropdowns safely
                        $('.factor-select').each(function() {
                            let opt = $('<option></option>').val(factorId).text(optionText)
                                        .attr('data-unit', factorUnit).attr('data-cost', factorCost);
                            $(this).append(opt);
                        });
                        
                        // Select it in the very last row automatically for great UX
                        let lastSelect = $('#itemsTable tbody tr:last .factor-select');
                        lastSelect.val(factorId).trigger('change');
                        
                        // Also append to the hidden rowTemplate for future added rows
                        let templateHtml = $('#rowTemplate').html();
                        let updatedTemplate = templateHtml.replace('</select>', '<option value="'+factorId+'" data-unit="'+factorUnit+'" data-cost="'+factorCost+'">'+optionText+'</option></select>');
                        $('#rowTemplate').html(updatedTemplate);
                        
                        // Reset and close modal
                        form[0].reset();
                        $('#quickAddMaterialModal').modal('hide');
                        
                        // Toast instead of alert for better UX
                        alert('Raw Material added and selected successfully!');
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
