@extends('backend.layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Production Log (Executed)</h6>
        <a href="{{route('manufacturing.production.create')}}" class="btn btn-primary btn-sm"><i class="fas fa-hammer"></i> Record Production</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Prod #</th>
                        <th>Finished Product</th>
                        <th>BOM Used</th>
                        <th>Qty Produced</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productions as $production)
                    <tr>
                        <td>{{$production->production_date ? \Carbon\Carbon::parse($production->production_date)->format('Y-m-d') : 'N/A'}}</td>
                        <td>{{$production->production_number}}</td>
                        <td>{{$production->manufacturingBill->product->title ?? 'N/A'}}</td>
                        <td>{{$production->manufacturingBill->bom_number ?? 'N/A'}}</td>
                        <td>{{$production->quantity_produced}}</td>
                        <td>
                            <a href="{{route('manufacturing.show', $production->manufacturing_bill_id)}}" class="btn btn-info btn-sm btn-circle" title="View BOM"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <span style="float:right">{{$productions->links()}}</span>
        </div>
    </div>
</div>
@endsection
