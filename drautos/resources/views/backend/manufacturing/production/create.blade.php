@extends('backend.layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Record Production Run</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="{{route('manufacturing.production.store')}}">
            @csrf
            
            <div class="form-group">
                <label for="manufacturing_bill_id">Select BOM (Recipe) <span class="text-danger">*</span></label>
                <select name="manufacturing_bill_id" id="manufacturing_bill_id" class="form-control select2" required>
                    <option value="">-- Select BOM --</option>
                    @foreach($boms as $bom)
                        <option value="{{$bom->id}}" data-product-id="{{$bom->product_id}}" {{(isset($selectedBom) && $selectedBom->id == $bom->id) ? 'selected' : ''}}>
                            {{$bom->bom_number}} - {{$bom->product->title}} (Batch: {{$bom->batch_quantity}} units)
                        </option>
                    @endforeach
                </select>
                @if(isset($selectedBom))
                    <small class="text-info">
                        Selected BOM produces <strong>{{$selectedBom->batch_quantity}}</strong> units of <strong>{{$selectedBom->product->title}}</strong>.
                        Requires:
                        @foreach($selectedBom->components as $comp)
                            {{$comp->componentProduct->title}} ({{$comp->quantity_required}}), 
                        @endforeach
                    </small>
                @endif
            </div>

            <div class="form-group" id="die_select_group">
                <label for="die_id">Select Die (Mould) <span class="text-muted">(Optional)</span></label>
                <select name="die_id" id="die_id" class="form-control select2">
                    <option value="">-- Select Die --</option>
                    @foreach($dies as $die)
                        <option value="{{$die->id}}" data-product-id="{{$die->product_id}}">
                            {{$die->name}} (Rack: {{$die->rack_number ?? 'N/A'}}, Status: {{ucfirst(str_replace('_', ' ', $die->quality_status ?? 'Good'))}})
                        </option>
                    @endforeach
                </select>
                <small class="text-muted" id="die_helper_text">Only active dies mapped to this BOM's product will be listed.</small>
            </div>

            <div class="form-group">
                <label for="quantity_produced">Quantity to Produce <span class="text-danger">*</span></label>
                <input type="number" name="quantity_produced" class="form-control" value="{{isset($selectedBom) ? $selectedBom->batch_quantity : 1}}" min="1" required>
                <small class="text-muted">Enter the total number of finished units produced. Raw materials will be deducted proportionally.</small>
            </div>

            <div class="form-group">
                <label for="production_date">Production Date <span class="text-danger">*</span></label>
                <input type="date" name="production_date" class="form-control" value="{{date('Y-m-d')}}" required>
            </div>

            <div class="form-group">
                <label for="subcontractor_id">Subcontractor / Supplier (Optional)</label>
                <select name="subcontractor_id" id="subcontractor_id" class="form-control select2">
                    <option value="">-- Select Subcontractor to Attribute Labor Costs --</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{$supplier->id}}">{{$supplier->name}} (Balance: {{$supplier->current_balance}})</option>
                    @endforeach
                </select>
                <small class="text-muted">If this BOM recipe uses labor/subcontract services, selecting a subcontractor here will automatically log the total labor cost as a debit to their ledger.</small>
            </div>

            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea name="notes" class="form-control" rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-check"></i> Execute Production</button>
            <a href="{{route('manufacturing.index')}}" class="btn btn-secondary btn-lg">Cancel</a>
        </form>
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

        // Cache all die options
        const originalDieOptions = $('#die_id option').clone();

        function filterDies() {
            const selectedBomOption = $('#manufacturing_bill_id option:selected');
            const productId = selectedBomOption.attr('data-product-id');
            
            // Clear current options
            $('#die_id').empty();
            
            // Re-add default option
            $('#die_id').append('<option value="">-- Select Die --</option>');

            if (!productId) {
                // If no BOM selected, disable
                $('#die_id').prop('disabled', true);
                $('#die_helper_text').text('Please select a BOM recipe first.');
            } else {
                // Filter matching dies
                const matchingDies = originalDieOptions.filter(function() {
                    const dieProdId = $(this).attr('data-product-id');
                    return dieProdId === productId || $(this).val() === '';
                });

                if (matchingDies.length > 1) {
                    $('#die_id').append(matchingDies.not('[value=""]'));
                    $('#die_id').prop('disabled', false);
                    $('#die_helper_text').text('Select the specific die/mould used for this production run.');
                } else {
                    $('#die_id').prop('disabled', true);
                    $('#die_helper_text').text('No active dies found for the selected BOM product.');
                }
            }
            
            // Trigger select2 update
            $('#die_id').trigger('change.select2');
        }

        // Run filter on load (if BOM preselected)
        filterDies();

        // Run filter on change
        $('#manufacturing_bill_id').change(function(){
            filterDies();
        });
    });
</script>
@endpush
