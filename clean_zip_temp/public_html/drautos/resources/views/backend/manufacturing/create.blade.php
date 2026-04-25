@extends('backend.layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Create New Manufacturing Bill (BOM)</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="{{route('manufacturing.store')}}">
            @csrf
            
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <label for="bom_number">BOM # <span class="text-danger">*</span></label>
                    <input type="text" name="bom_number" class="form-control" value="BOM-{{strtoupper(uniqid())}}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="product_id">Finished Product <span class="text-danger">*</span></label>
                    <select name="product_id" id="product_id" class="form-control select2" required>
                        <option value="">-- Select Product --</option>
                        @foreach($products as $product)
                            <option value="{{$product->id}}">{{$product->title}} (Stock: {{$product->stock}})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="batch_quantity">Batch Quantity <span class="text-danger">*</span></label>
                    <input type="number" name="batch_quantity" class="form-control" value="1" min="1" required>
                    <small class="text-muted">How many units does this recipe produce?</small>
                </div>
            </div>

            <hr>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Raw Materials / Components</h5>
                <a href="{{route('product.create')}}" target="_blank" class="btn btn-sm btn-info shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Add New Material</a>
            </div>
            
            <table class="table table-bordered" id="components_table">
                <thead>
                    <tr>
                        <th width="50%">Component (Raw Material)</th>
                        <th width="30%">Quantity Required</th>
                        <th width="10%">Action</th>
                    </tr>
                </thead>
                <tbody id="components_body">
                    <tr>
                        <td>
                            <select name="components[0][product_id]" class="form-control select2 component-select" required>
                                <option value="">Select Material</option>
                                @foreach($products as $product)
                                    <option value="{{$product->id}}">{{$product->title}} (Stock: {{$product->stock}})</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" step="0.01" name="components[0][quantity]" class="form-control" placeholder="Qty" required>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-row" disabled><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
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
            <h5 class="mb-3">Overhead Costs (Per Batch)</h5>
            <div class="form-row">
                <div class="col-md-3 mb-3">
                    <label>Machining Cost</label>
                    <input type="number" step="0.01" name="machining_cost" class="form-control" value="0">
                </div>
                <div class="col-md-3 mb-3">
                    <label>Labour Cost</label>
                    <input type="number" step="0.01" name="labour_cost" class="form-control" value="0">
                </div>
                <div class="col-md-3 mb-3">
                    <label>Packaging Cost</label>
                    <input type="number" step="0.01" name="packaging_cost" class="form-control" value="0">
                </div>
                <div class="col-md-3 mb-3">
                    <label>Other Overheads</label>
                    <input type="number" step="0.01" name="overhead_cost" class="form-control" value="0">
                </div>
            </div>

            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" class="form-control" rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-lg">Create BOM</button>
        </form>
    </div>
</div>

{{-- Template Row for JS --}}
<template id="component_row_template">
    <tr>
        <td>
            <select name="components[INDEX][product_id]" class="form-control select2-new component-select" required>
                <option value="">Select Material</option>
                @foreach($products as $product)
                    <option value="{{$product->id}}">{{$product->title}} (Stock: {{$product->stock}})</option>
                @endforeach
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

        let rowIndex = 1;

        $('#add_component').click(function() {
            let template = $('#component_row_template').html();
            let newRow = template.replace(/INDEX/g, rowIndex++);
            $('#components_body').append(newRow);
            
            // Re-initialize select2 for new row
            $('.select2-new').select2({
                theme: 'bootstrap4',
                width: '100%'
            }).removeClass('select2-new').addClass('select2');
        });

        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
        });
    });
</script>
@endpush
