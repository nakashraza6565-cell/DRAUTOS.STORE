@extends('backend.layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Manufacturing Bills (BOM)</h6>
        <div>
            <a href="{{route('manufacturing.create')}}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New BOM</a>
            <a href="{{route('manufacturing.production.index')}}" class="btn btn-success btn-sm"><i class="fas fa-industry"></i> Production Log</a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>BOM #</th>
                        <th>Product</th>
                        <th>Batch Qty</th>
                        <th>Total Cost/Unit</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($boms as $bom)
                    <tr>
                        <td>{{$bom->bom_number}}</td>
                        <td>{{$bom->product->title ?? 'N/A'}}</td>
                        <td>{{$bom->batch_quantity}}</td>
                        <td>Rs. {{number_format($bom->total_cost_per_unit, 2)}}</td>
                        <td>
                            <span class="badge badge-{{$bom->status=='active'?'success':'warning'}}">{{ucfirst($bom->status)}}</span>
                        </td>
                        <td>
                            <a href="{{route('manufacturing.show', $bom->id)}}" class="btn btn-info btn-sm btn-circle" title="View"><i class="fas fa-eye"></i></a>
                            <a href="{{route('manufacturing.edit', $bom->id)}}" class="btn btn-primary btn-sm btn-circle" title="Edit"><i class="fas fa-edit"></i></a>
                            
                            <form method="POST" action="{{route('manufacturing.destroy', [$bom->id])}}" class="d-inline">
                                @csrf 
                                @method('delete')
                                <button class="btn btn-danger btn-sm btn-circle dltBtn" data-id="{{$bom->id}}" title="Delete"><i class="fas fa-trash"></i></button>
                            </form>
                            
                            <a href="{{route('manufacturing.production.create', ['bom_id' => $bom->id])}}" class="btn btn-secondary btn-sm" title="Produce This"><i class="fas fa-cogs"></i> Produce</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <span style="float:right">{{$boms->links()}}</span>
        </div>
    </div>
</div>
@endsection
