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
        <table class="table table-bordered" id="data-table" width="100%" cellspacing="0">
          <thead>
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
                    <td><strong>{{$entry->reference_number}}</strong></td>
                    <td>{{$entry->received_date->format('d M Y')}}</td>
                    <td>{{$entry->supplier->name ?? 'N/A'}}</td>
                    <td>{{$entry->warehouse->name ?? 'N/A'}}</td>
                    <td>
                        {{$entry->items->count()}} items
                        @php $pkgCount = $entry->items->whereNotNull('packaging_item_id')->count(); @endphp
                        @if($pkgCount > 0)
                            <div class="small text-info"><i class="fas fa-box"></i> {{$pkgCount}} packed</div>
                        @endif
                    </td>
                    <td>PKR {{number_format($entry->totalCost, 2)}}</td>
                    <td>
                        @if($entry->status == 'pending')
                            <span class="badge badge-warning">Pending</span>
                        @elseif($entry->status == 'verified')
                            <span class="badge badge-info">Verified</span>
                        @else
                            <span class="badge badge-success">Completed</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{route('inventory-incoming.show',$entry->id)}}" class="btn btn-info btn-sm" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{route('inventory-incoming.print-barcodes',$entry->id)}}" class="btn btn-secondary btn-sm" title="Print Barcodes" target="_blank">
                            <i class="fas fa-barcode"></i>
                        </a>
                        <a href="{{route('admin.supplier-ledger.thermal',$entry->supplier_id)}}" class="btn btn-info btn-sm" title="Supplier Ledger" target="_blank">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </a>
                        <a href="{{route('inventory-incoming.thermal',$entry->id)}}" class="btn btn-warning btn-sm" title="Thermal Print" target="_blank">
                            <i class="fas fa-print"></i>
                        </a>
                        @if($entry->status == 'pending')
                        <a href="{{route('inventory-incoming.edit',$entry->id)}}" class="btn btn-primary btn-sm" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form method="POST" action="{{route('inventory-incoming.verify',$entry->id)}}" style="display:inline;">
                          @csrf
                          <button class="btn btn-primary btn-sm" title="Verify"><i class="fas fa-check"></i></button>
                        </form>
                        @endif
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
