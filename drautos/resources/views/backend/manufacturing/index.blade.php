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
            <table class="table table-bordered table-hover order-table-to-cards bom-table-to-cards" id="dataTable" width="100%" cellspacing="0">
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
                        <td data-title="BOM #">{{$bom->bom_number}}</td>
                        <td data-title="Product">{{$bom->product->title ?? 'N/A'}}</td>
                        <td data-title="Batch Qty">{{$bom->batch_quantity}}</td>
                        <td data-title="Total Cost/Unit">Rs. {{number_format($bom->total_cost_per_unit, 2)}}</td>
                        <td data-title="Status">
                            @if($bom->status == 'completed')
                                <span class="badge badge-success">Completed</span>
                            @elseif($bom->status == 'wip')
                                <span class="badge badge-warning">WIP (In Progress)</span>
                            @else
                                <span class="badge badge-secondary">{{ucfirst($bom->status)}}</span>
                            @endif
                        </td>
                        <td data-title="Actions">
                            <div class="d-flex flex-nowrap justify-content-end align-items-center" style="gap: 4px;">
                                <a href="{{route('manufacturing.show', $bom->id)}}" class="btn btn-info btn-sm btn-circle act-view" style="height:28px; width:28px; padding:0; display:flex; align-items:center; justify-content:center; font-size: 11px;" title="View"><i class="fas fa-eye"></i></a>
                                <a href="{{route('manufacturing.edit', $bom->id)}}" class="btn btn-primary btn-sm btn-circle act-edit" style="height:28px; width:28px; padding:0; display:flex; align-items:center; justify-content:center; font-size: 11px;" title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="{{route('manufacturing.clone', $bom->id)}}" class="btn btn-success btn-sm btn-circle act-clone" style="height:28px; width:28px; padding:0; display:flex; align-items:center; justify-content:center; font-size: 11px;" title="Clone Recipe for New Run"><i class="fas fa-copy"></i></a>
                                
                                <form method="POST" action="{{route('manufacturing.destroy', [$bom->id])}}" class="act-delete" style="display:inline-block; margin:0;">
                                    @csrf 
                                    @method('delete')
                                    <button class="btn btn-danger btn-sm btn-circle dltBtn" style="height:28px; width:28px; padding:0; display:flex; align-items:center; justify-content:center; font-size: 11px;" data-id="{{$bom->id}}" title="Delete"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
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
