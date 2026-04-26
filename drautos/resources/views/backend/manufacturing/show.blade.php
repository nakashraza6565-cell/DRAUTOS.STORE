@extends('backend.layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">BOM Details: {{$bom->bom_number}}</h6>
        <a href="{{route('manufacturing.index')}}" class="btn btn-secondary btn-sm">Back to List</a>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h5>Finished Product</h5>
                <table class="table table-striped">
                    <tr>
                        <th>Product Name</th>
                        <td>{{$bom->product->title ?? 'N/A'}}</td>
                    </tr>
                    <tr>
                        <th>Batch Quantity</th>
                        <td>{{$bom->batch_quantity}} Units</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td><span class="badge badge-{{$bom->status=='active'?'success':'warning'}}">{{ucfirst($bom->status)}}</span></td>
                    </tr>
                    <tr>
                        <th>Notes</th>
                        <td>{{$bom->notes ?? 'N/A'}}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h5>Cost Analysis (Per Unit)</h5>
                <table class="table table-bordered">
                    <tr>
                        <th>Material Cost</th>
                        <td>Rs. {{number_format($bom->material_cost / $bom->batch_quantity, 2)}}</td>
                    </tr>
                    <tr>
                        <th>Overhead Costs</th>
                        <td>Rs. {{number_format(($bom->machining_cost + $bom->labour_cost + $bom->packaging_cost + $bom->overhead_cost) / $bom->batch_quantity, 2)}}</td>
                    </tr>
                    <tr class="table-primary">
                        <th>Total Cost Per Unit</th>
                        <th>Rs. {{number_format($bom->total_cost_per_unit, 2)}}</th>
                    </tr>
                </table>
            </div>
        </div>

        <hr>
        <h5>Components (Raw Materials)</h5>
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Component Product</th>
                        <th>Qty Required (Batch)</th>
                        <th>Unit Cost</th>
                        <th>Total Cost (Batch)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bom->components as $component)
                    <tr>
                        <td>{{$component->componentProduct->title ?? 'Deleted Product'}}</td>
                        <td>{{$component->quantity_required}}</td>
                        <td>Rs. {{number_format($component->cost_per_unit, 2)}}</td>
                        <td>Rs. {{number_format($component->total_cost, 2)}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <hr>
        <h5>Overhead Breakdown (Batch)</h5>
        <div class="row">
            <div class="col-md-3"><strong>Machining:</strong> Rs. {{number_format($bom->machining_cost, 2)}}</div>
            <div class="col-md-3"><strong>Labour:</strong> Rs. {{number_format($bom->labour_cost, 2)}}</div>
            <div class="col-md-3"><strong>Packaging:</strong> Rs. {{number_format($bom->packaging_cost, 2)}}</div>
            <div class="col-md-3"><strong>Other:</strong> Rs. {{number_format($bom->overhead_cost, 2)}}</div>
        </div>

        @if($bom->productions->count() > 0)
        <hr>
        <h5>Production History</h5>
        <ul>
            @foreach($bom->productions as $prod)
                <li>
                    {{$prod->production_date}}: Produced {{$prod->quantity_produced}} units (Batch #{{$prod->production_number}})
                </li>
            @endforeach
        </ul>
        @endif
    </div>
</div>
@endsection
