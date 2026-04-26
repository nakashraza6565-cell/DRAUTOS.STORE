@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            @include('backend.layouts.notification')
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: #f8fafc;">
            <h6 class="m-0 font-weight-bold text-primary">Purchase Orders List</h6>
            <a href="{{route('purchase-orders.create')}}" class="btn btn-primary btn-sm rounded-pill shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> New Purchase Order
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="purchase-order-dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>PO Number</th>
                            <th>Supplier</th>
                            <th>Order Date</th>
                            <th>Status</th>
                            <th>Total Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchase_orders as $po)
                            <tr>
                                <td class="font-weight-bold text-dark">{{$po->po_number}}</td>
                                <td>{{$po->supplier->name ?? 'N/A'}}</td>
                                <td>{{$po->order_date}}</td>
                                <td>
                                    @if($po->status=='pending')
                                        <span class="badge badge-warning p-2 px-3 rounded-pill text-uppercase" style="font-size: 0.7rem;">Pending</span>
                                    @elseif($po->status=='ordered')
                                        <span class="badge badge-info p-2 px-3 rounded-pill text-uppercase" style="font-size: 0.7rem;">Ordered</span>
                                    @elseif($po->status=='received')
                                        <span class="badge badge-success p-2 px-3 rounded-pill text-uppercase" style="font-size: 0.7rem;">Received</span>
                                    @else
                                        <span class="badge badge-danger p-2 px-3 rounded-pill text-uppercase" style="font-size: 0.7rem;">Cancelled</span>
                                    @endif
                                </td>
                                <td class="text-right font-weight-bold">Rs. {{number_format($po->total_amount,2)}}</td>
                                <td class="text-center">
                                    <a href="{{route('purchase-orders.show',$po->id)}}" class="btn btn-info btn-sm rounded-circle"><i class="fas fa-eye text-white"></i></a>
                                    <a href="{{route('purchase-orders.edit',$po->id)}}" class="btn btn-primary btn-sm rounded-circle"><i class="fas fa-edit text-white"></i></a>
                                    <form method="POST" action="{{route('purchase-orders.destroy',[$po->id])}}" style="display:inline-block">
                                      @csrf 
                                      @method('delete')
                                          <button class="btn btn-danger btn-sm rounded-circle dltBtn" data-id={{$po->id}} style="height:30px; width:30px"  title="Delete"><i class="fas fa-trash-alt text-white"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
  <link href="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
  <style>
      .table thead th {
          border-top: none;
          text-transform: uppercase;
          font-size: 0.8rem;
          letter-spacing: 0.5px;
          color: #64748b;
      }
      .table td {
          vertical-align: middle;
      }
  </style>
@endpush

@push('scripts')
  <script src="{{asset('backend/vendor/datatables/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
  <script>
      $('#purchase-order-dataTable').DataTable({
          "columnDefs":[
              {
                  "orderable":false,
                  "targets":[5]
              }
          ]
      });
  </script>
@endpush
