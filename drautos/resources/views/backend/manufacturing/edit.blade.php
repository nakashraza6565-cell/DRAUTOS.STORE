@extends('backend.layouts.master')

@section('main-content')
@include('backend.layouts.notification')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Edit BOM: {{$bom->bom_number}}</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="{{route('manufacturing.update', $bom->id)}}">
            @csrf
            @method('PUT')
            
            <div class="form-row">
                <div class="col-md-3 mb-3">
                    <label for="bom_number">BOM # <span class="text-danger">*</span></label>
                    <input type="text" name="bom_number" class="form-control" value="{{$bom->bom_number}}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="product_id">Finished Product <span class="text-danger">*</span></label>
                    <select name="product_id" id="product_id" class="form-control select2" required>
                        <option value="">-- Select Product --</option>
                        @foreach($products as $product)
                            <option value="{{$product->id}}" {{$bom->product_id == $product->id ? 'selected' : ''}}>{{$product->title}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="batch_quantity">Batch Quantity <span class="text-danger">*</span></label>
                    <input type="number" name="batch_quantity" id="batch_quantity" class="form-control" value="{{$bom->batch_quantity}}" min="1" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="status">Production Status <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-control select2" required>
                        <option value="wip" {{$bom->status == 'wip' ? 'selected' : ''}}>WIP (Work In Progress)</option>
                        <option value="completed" {{$bom->status == 'completed' ? 'selected' : ''}}>Completed (Deduct stock & add finished product)</option>
                        <option value="inactive" {{$bom->status == 'inactive' ? 'selected' : ''}}>Inactive</option>
                    </select>
                </div>
            </div>

            <hr>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Raw Materials / Components</h5>
                <button type="button" class="btn btn-sm btn-info shadow-sm" data-toggle="modal" data-target="#quickAddMaterialModal"><i class="fas fa-plus fa-sm text-white-50"></i> Add New Material</button>
            </div>
            
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th width="50%">Component (Raw Material)</th>
                        <th width="30%">Quantity Required</th>
                        <th width="10%">Action</th>
                    </tr>
                </thead>
                <tbody id="components_body">
                    @foreach($bom->components as $index => $component)
                    <tr>
                        <td>
                            <select name="components[{{$index}}][product_id]" class="form-control select2 component-select" required>
                                <option value="">Select Ingredient</option>
                                <optgroup label="Raw Materials & Labor" class="factors-group">
                                    @foreach($factors as $factor)
                                        <option value="factor_{{$factor->id}}" {{($component->ingredient_type === 'App\Models\ProductionFactor' && $component->component_product_id == $factor->id) ? 'selected' : ''}}>{{$factor->name}} (Stock: {{$factor->stock_quantity}} {{$factor->unit}})</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="Products (Intermediate/Finished)" class="products-group">
                                    @foreach($products as $product)
                                        <option value="product_{{$product->id}}" {{($component->ingredient_type === 'App\Models\Product' && $component->component_product_id == $product->id) ? 'selected' : ''}}>{{$product->title}} (Stock: {{$product->stock}})</option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </td>
                        <td>
                            <input type="number" step="0.01" name="components[{{$index}}][quantity]" class="form-control" value="{{$component->quantity_required}}" required>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3">
                            <button type="button" class="btn btn-success btn-sm" id="add_component"><i class="fas fa-plus"></i> Add Component</button>
                        </td>
                    </tr>
                </tfoot>
            </table>

            <hr>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Overhead Costs (Per Batch)</h5>
                <button type="button" class="btn btn-sm btn-info shadow-sm" id="add_custom_overhead_type_btn"><i class="fas fa-plus fa-sm text-white-50"></i> Add Custom Overhead Type</button>
            </div>
            
            <table class="table table-bordered" id="overheads_table">
                <thead>
                    <tr>
                        <th width="25%">Overhead Type</th>
                        <th width="25%">Subcontractor / Supplier</th>
                        <th width="20%">Per Piece Cost (Rs.)</th>
                        <th width="20%">Total Cost (Rs.)</th>
                        <th width="10%">Action</th>
                    </tr>
                </thead>
                <tbody id="overheads_body">
                    @php
                        $overhead_index = 0;
                        $overhead_details = $bom->overhead_details ?? [];
                        $has_overheads = false;
                        $q = max(1, $bom->batch_quantity);
                    @endphp

                    @if(count($overhead_details) > 0)
                        @php $has_overheads = true; @endphp
                        @foreach($overhead_details as $ov)
                            @php
                                $perPieceCostVal = $ov['per_piece_cost'] ?? ($ov['cost'] / $q);
                            @@endphp
                            <tr>
                                <td>
                                    <select name="overheads[{{$overhead_index}}][type]" class="form-control select2" required>
                                        <option value="machining" {{$ov['type'] == 'machining' ? 'selected' : ''}}>Machining Cost</option>
                                        <option value="labour" {{$ov['type'] == 'labour' ? 'selected' : ''}}>Labour Cost</option>
                                        <option value="packaging" {{$ov['type'] == 'packaging' ? 'selected' : ''}}>Packaging Cost</option>
                                        <option value="material" {{$ov['type'] == 'material' ? 'selected' : ''}}>Raw Material Cost</option>
                                        <option value="overhead" {{$ov['type'] == 'overhead' ? 'selected' : ''}}>Other Overheads</option>
                                        @if(!in_array($ov['type'], ['machining', 'labour', 'packaging', 'material', 'overhead']))
                                            <option value="{{$ov['type']}}" selected>{{$ov['name'] ?? ucfirst(str_replace('_', ' ', $ov['type']))}}</option>
                                        @endif
                                    </select>
                                </td>
                                <td>
                                    <select name="overheads[{{$overhead_index}}][subcontractor_id]" class="form-control select2">
                                        <option value="">-- No Subcontractor (In-house) --</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{$supplier->id}}" {{($ov['subcontractor_id'] ?? '') == $supplier->id ? 'selected' : ''}}>{{$supplier->name}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" step="0.0001" name="overheads[{{$overhead_index}}][per_piece_cost]" class="form-control per-piece-cost-input" value="{{$perPieceCostVal}}" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="overheads[{{$overhead_index}}][cost]" class="form-control total-cost-input" value="{{$ov['cost']}}" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-overhead-row"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            @php $overhead_index++; @endphp
                        @endforeach
                    @else
                        @if($bom->machining_cost > 0)
                            @php $has_overheads = true; @endphp
                            <tr>
                                <td>
                                    <select name="overheads[{{$overhead_index}}][type]" class="form-control select2" required>
                                        <option value="machining" selected>Machining Cost</option>
                                        <option value="labour">Labour Cost</option>
                                        <option value="packaging">Packaging Cost</option>
                                        <option value="material">Raw Material Cost</option>
                                        <option value="overhead">Other Overheads</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="overheads[{{$overhead_index}}][subcontractor_id]" class="form-control select2">
                                        <option value="">-- No Subcontractor (In-house) --</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{$supplier->id}}" {{$bom->subcontractor_id == $supplier->id ? 'selected' : ''}}>{{$supplier->name}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" step="0.0001" name="overheads[{{$overhead_index}}][per_piece_cost]" class="form-control per-piece-cost-input" value="{{$bom->machining_cost / $q}}" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="overheads[{{$overhead_index}}][cost]" class="form-control total-cost-input" value="{{$bom->machining_cost}}" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-overhead-row"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            @php $overhead_index++; @endphp
                        @endif

                        @if($bom->labour_cost > 0)
                            @php $has_overheads = true; @endphp
                            <tr>
                                <td>
                                    <select name="overheads[{{$overhead_index}}][type]" class="form-control select2" required>
                                        <option value="machining">Machining Cost</option>
                                        <option value="labour" selected>Labour Cost</option>
                                        <option value="packaging">Packaging Cost</option>
                                        <option value="material">Raw Material Cost</option>
                                        <option value="overhead">Other Overheads</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="overheads[{{$overhead_index}}][subcontractor_id]" class="form-control select2">
                                        <option value="">-- No Subcontractor (In-house) --</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{$supplier->id}}" {{$bom->subcontractor_id == $supplier->id ? 'selected' : ''}}>{{$supplier->name}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" step="0.0001" name="overheads[{{$overhead_index}}][per_piece_cost]" class="form-control per-piece-cost-input" value="{{$bom->labour_cost / $q}}" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="overheads[{{$overhead_index}}][cost]" class="form-control total-cost-input" value="{{$bom->labour_cost}}" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-overhead-row"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            @php $overhead_index++; @endphp
                        @endif

                        @if($bom->packaging_cost > 0)
                            @php $has_overheads = true; @endphp
                            <tr>
                                <td>
                                    <select name="overheads[{{$overhead_index}}][type]" class="form-control select2" required>
                                        <option value="machining">Machining Cost</option>
                                        <option value="labour">Labour Cost</option>
                                        <option value="packaging" selected>Packaging Cost</option>
                                        <option value="material">Raw Material Cost</option>
                                        <option value="overhead">Other Overheads</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="overheads[{{$overhead_index}}][subcontractor_id]" class="form-control select2">
                                        <option value="">-- No Subcontractor (In-house) --</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{$supplier->id}}" {{$bom->subcontractor_id == $supplier->id ? 'selected' : ''}}>{{$supplier->name}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" step="0.0001" name="overheads[{{$overhead_index}}][per_piece_cost]" class="form-control per-piece-cost-input" value="{{$bom->packaging_cost / $q}}" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="overheads[{{$overhead_index}}][cost]" class="form-control total-cost-input" value="{{$bom->packaging_cost}}" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-overhead-row"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            @php $overhead_index++; @endphp
                        @endif

                        @if($bom->overhead_cost > 0 || !$has_overheads)
                            <tr>
                                <td>
                                    <select name="overheads[{{$overhead_index}}][type]" class="form-control select2" required>
                                        <option value="machining">Machining Cost</option>
                                        <option value="labour">Labour Cost</option>
                                        <option value="packaging">Packaging Cost</option>
                                        <option value="material">Raw Material Cost</option>
                                        <option value="overhead" selected>Other Overheads</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="overheads[{{$overhead_index}}][subcontractor_id]" class="form-control select2">
                                        <option value="">-- No Subcontractor (In-house) --</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{$supplier->id}}" {{$bom->subcontractor_id == $supplier->id ? 'selected' : ''}}>{{$supplier->name}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" step="0.0001" name="overheads[{{$overhead_index}}][per_piece_cost]" class="form-control per-piece-cost-input" value="{{($bom->overhead_cost ?? 0) / $q}}" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="overheads[{{$overhead_index}}][cost]" class="form-control total-cost-input" value="{{$bom->overhead_cost ?? 0}}" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-overhead-row" {{!$has_overheads ? 'disabled' : ''}}><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            @php $overhead_index++; @endphp
                        @endif
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5">
                            <button type="button" class="btn btn-success btn-sm" id="add_overhead"><i class="fas fa-plus"></i> Add Overhead</button>
                        </td>
                    </tr>
                </tfoot>
            </table>

            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" class="form-control" rows="3">{{$bom->notes}}</textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-lg">Update BOM</button>
        </form>
    </div>
</div>

<template id="component_row_template">
    <tr>
        <td>
            <select name="components[INDEX][product_id]" class="form-control select2-new component-select" required>
                <option value="">Select Ingredient</option>
                <optgroup label="Raw Materials & Labor" class="factors-group">
                    @foreach($factors as $factor)
                        <option value="factor_{{$factor->id}}">{{$factor->name}} (Stock: {{$factor->stock_quantity}} {{$factor->unit}})</option>
                    @endforeach
                </optgroup>
                <optgroup label="Products (Intermediate/Finished)" class="products-group">
                    @foreach($products as $product)
                        <option value="product_{{$product->id}}">{{$product->title}} (Stock: {{$product->stock}})</option>
                    @endforeach
                </optgroup>
            </select>
        </td>
        <td>
            <input type="number" step="0.01" name="components[INDEX][quantity]" class="form-control" placeholder="Qty" required>
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
        </td>
    </tr>
</template>

</template>

<template id="overhead_row_template">
    <tr>
        <td>
            <select name="overheads[INDEX][type]" class="form-control select2-new" required>
                <option value="">-- Select Overhead Type --</option>
                <option value="machining">Machining Cost</option>
                <option value="labour">Labour Cost</option>
                <option value="packaging">Packaging Cost</option>
                <option value="material">Raw Material Cost</option>
                <option value="overhead">Other Overheads</option>
            </select>
        </td>
        <td>
            <select name="overheads[INDEX][subcontractor_id]" class="form-control select2-new">
                <option value="">-- No Subcontractor (In-house) --</option>
                @foreach($suppliers as $supplier)
                    <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" step="0.0001" name="overheads[INDEX][per_piece_cost]" class="form-control per-piece-cost-input" placeholder="Per Pc Cost" value="0" required>
        </td>
        <td>
            <input type="number" step="0.01" name="overheads[INDEX][cost]" class="form-control total-cost-input" placeholder="Total Cost" value="0" required>
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm remove-overhead-row"><i class="fas fa-trash"></i></button>
        </td>
    </tr>
</template>

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
                    <button type="submit" class="btn btn-info">Save Material</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });

        let rowIndex = {{$bom->components->count() + 1}};

        $('#add_component').click(function() {
            let template = $('#component_row_template').html();
            let newRow = template.replace(/INDEX/g, rowIndex++);
            $('#components_body').append(newRow);
            
            $('.select2-new').select2({
                theme: 'bootstrap4',
                width: '100%'
            }).removeClass('select2-new').addClass('select2');
        });

        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
        });

        // Overhead costs dynamic rows
        let overheadIndex = {{$overhead_index + 1}};
        $('#add_overhead').click(function() {
            let template = $('#overhead_row_template').html();
            let newRow = template.replace(/INDEX/g, overheadIndex++);
            $('#overheads_body').append(newRow);
            
            $('.select2-new').select2({
                theme: 'bootstrap4',
                width: '100%'
            }).removeClass('select2-new').addClass('select2');
        });

        $(document).on('click', '.remove-overhead-row', function() {
            $(this).closest('tr').remove();
        });

        // Add custom overhead type dynamically
        $('#add_custom_overhead_type_btn').click(function() {
            let typeName = prompt('Enter Custom Overhead Name (e.g. Electricity, Rent, Tooling):');
            if (typeName && typeName.trim() !== '') {
                typeName = typeName.trim();
                let typeVal = typeName.toLowerCase().replace(/[^a-z0-9]/g, '_');
                
                // Check if already exists
                let exists = false;
                $('select[name="overheads[0][type]"] option').each(function() {
                    if ($(this).val() === typeVal) {
                        exists = true;
                    }
                });
                
                if (exists) {
                    alert('This overhead type already exists.');
                    return;
                }
                
                let optionHtml = '<option value="' + typeVal + '">' + typeName + '</option>';
                
                // Add option to all existing overhead selects
                $('select[name^="overheads"]').append(optionHtml).trigger('change');
                
                // Also update the hidden overhead template select box
                let templateHtml = $('#overhead_row_template').html();
                let updatedTemplate = templateHtml.replace('</select>', optionHtml + '</select>');
                $('#overhead_row_template').html(updatedTemplate);
                
                alert('Overhead type "' + typeName + '" added successfully! You can now select it in the dropdown.');
            }
        });

        // Auto-recalculate per piece cost vs total cost using batch quantity
        function getBatchQty() {
            let qty = parseFloat($('#batch_quantity').val());
            return isNaN(qty) || qty <= 0 ? 1 : qty;
        }

        // On Per Piece Cost change
        $(document).on('input change', '.per-piece-cost-input', function() {
            let row = $(this).closest('tr');
            let perPieceVal = parseFloat($(this).val()) || 0;
            let qty = getBatchQty();
            let totalVal = perPieceVal * qty;
            row.find('.total-cost-input').val(totalVal.toFixed(2));
        });

        // On Total Cost change
        $(document).on('input change', '.total-cost-input', function() {
            let row = $(this).closest('tr');
            let totalVal = parseFloat($(this).val()) || 0;
            let qty = getBatchQty();
            let perPieceVal = totalVal / qty;
            row.find('.per-piece-cost-input').val(perPieceVal.toFixed(4));
        });

        // On Batch Quantity change, recalculate all total costs keeping per-piece cost constant
        $('#batch_quantity').on('input change', function() {
            let qty = getBatchQty();
            $('#overheads_body tr').each(function() {
                let perPieceVal = parseFloat($(this).find('.per-piece-cost-input').val()) || 0;
                let totalVal = perPieceVal * qty;
                $(this).find('.total-cost-input').val(totalVal.toFixed(2));
            });
        });

        // Quick Add Material Form Submission
        $('#quickAddMaterialForm').submit(function(e) {
            e.preventDefault();
            let form = $(this);
            let btn = form.find('button[type="submit"]');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
            
            $.ajax({
                url: "{{route('manufacturing.production-factors.quick-store')}}",
                type: "POST",
                data: form.serialize() + "&_token={{csrf_token()}}",
                success: function(response) {
                    if(response.status == 'success') {
                        let newOption = new Option(response.factor.name + ' (Stock: ' + response.factor.stock_quantity + ' ' + response.factor.unit + ')', 'factor_' + response.factor.id, true, true);
                        
                        // Append to factors-group in all component selects
                        $('.component-select .factors-group').append(newOption).trigger('change');
                        
                        // Also append to the hidden template so future rows get it
                        let templateHtml = $('#component_row_template').html();
                        let updatedTemplate = templateHtml.replace('</optgroup>', '<option value="factor_'+response.factor.id+'">'+response.factor.name+' (Stock: '+response.factor.stock_quantity+' ' + response.factor.unit + ')</option></optgroup>');
                        $('#component_row_template').html(updatedTemplate);
                        
                        $('#quickAddMaterialModal').modal('hide');
                        form[0].reset();
                        
                        // Optional: Show simple alert or toast
                        alert('Material added successfully!');
                    } else {
                        alert(response.message || 'Error adding material');
                    }
                },
                error: function(xhr) {
                    alert('An error occurred. Check your input.');
                },
                complete: function() {
                    btn.prop('disabled', false).text('Save Material');
                }
            });
        });
    });
</script>
@endpush
