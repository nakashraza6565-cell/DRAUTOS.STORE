@extends('backend.layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary float-left">Incoming Goods Records</h6>
      <a href="{{route('inventory-incoming.create')}}" class="btn btn-primary btn-sm float-right"><i class="fas fa-plus"></i> New Entry</a>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        @if(count($incoming)>0)
        <table class="table table-bordered responsive-table-to-cards" id="data-table" width="100%" cellspacing="0">
          <thead class="bg-light">
            <tr>
              <th>Reference #</th>
              <th>Date</th>
              <th>Supplier</th>
              <th>Warehouse</th>
              <th>Items</th>
              <th>Total Cost</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($incoming as $entry)   
                <tr>
                    <td data-title="Reference #"><strong>{{$entry->reference_number}}</strong></td>
                    <td data-title="Date">{{$entry->received_date->format('d M Y')}}</td>
                    <td data-title="Supplier">{{$entry->supplier->name ?? 'N/A'}}</td>
                    <td data-title="Warehouse">{{$entry->warehouse->name ?? 'N/A'}}</td>
                    <td data-title="Items">
                        <span class="badge badge-pill badge-info px-2">{{$entry->items->count()}} items</span>
                        @php $pkgCount = $entry->items->whereNotNull('packaging_item_id')->count(); @endphp
                        @if($pkgCount > 0)
                            <div class="small text-info mt-1"><i class="fas fa-box"></i> {{$pkgCount}} packed</div>
                        @endif
                    </td>
                    <td data-title="Total Cost" class="font-weight-bold text-dark">PKR {{number_format($entry->totalCost, 2)}}</td>
                    <td data-title="Status">
                        @if($entry->status == 'pending')
                            <span class="badge badge-pill badge-warning px-3">Pending</span>
                        @elseif($entry->status == 'verified')
                            <span class="badge badge-pill badge-info px-3">Verified</span>
                        @else
                            <span class="badge badge-pill badge-success px-3">Completed</span>
                        @endif
                    </td>
                    <td data-title="Action">
                        <div class="d-flex" style="gap: 5px;">
                            <a href="{{route('inventory-incoming.show',$entry->id)}}" class="btn btn-info btn-sm rounded-circle" title="View" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-eye fa-sm"></i>
                            </a>
                            <a href="{{route('inventory-incoming.print-barcodes',$entry->id)}}" class="btn btn-secondary btn-sm rounded-circle" title="Print Barcodes" target="_blank" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-barcode fa-sm"></i>
                            </a>
                            <a href="{{route('inventory-incoming.thermal',$entry->id)}}" class="btn btn-warning btn-sm rounded-circle" title="Thermal Print" target="_blank" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-print fa-sm"></i>
                            </a>
                            @if($entry->status == 'pending')
                            <form method="POST" action="{{route('inventory-incoming.verify',$entry->id)}}" style="display:inline;">
                              @csrf
                              <button class="btn btn-primary btn-sm rounded-circle" title="Verify" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-check fa-sm"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>  
            @endforeach
          </tbody>
        </table>
        <div class="mt-3">
            {{ $incoming->links() }}
        </div>
        @else
          <h6 class="text-center">No Records Found!</h6>
        @endif
      </div>
    </div>
</div>
@endsection

@push('styles')
<link href="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endpush

@push('scripts')
<script src="{{asset('backend/vendor/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
<script>
$(document).ready(function() {
    $('#data-table').DataTable();
});
</script>
@endpush
