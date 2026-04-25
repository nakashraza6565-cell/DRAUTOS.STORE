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
                        <option value="{{$bom->id}}" {{(isset($selectedBom) && $selectedBom->id == $bom->id) ? 'selected' : ''}}>
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

        // Optional: reload page on BOM change to show ingredients hint (handled by server logic if I implemented it, but for now just basic select)
        // If I want dynamic hints without reload, I need an API endpoint or store data in data attributes.
        // For MVP, if they change selection, they rely on the dropdown text.
        // If they arrived via 'Produce This' button, the info is shown.
        $('#manufacturing_bill_id').change(function(){
             // Ideally fetch info via AJAX or redirect to self with ?bom_id=VAL
             // simplified:
             // window.location.href = "{{route('manufacturing.production.create')}}?bom_id=" + $(this).val();
             // Uncommenting the above line would make it interactive but might lose other inputs.
             // Let's stick to simple form submission.
        });
    });
</script>
@endpush
