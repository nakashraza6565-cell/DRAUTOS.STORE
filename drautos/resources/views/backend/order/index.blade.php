@extends('backend.layouts.master')

@section('main-content')
 <!-- DataTales Example -->
 <div class="card shadow mb-4">
     <div class="row">
         <div class="col-md-12">
            @include('backend.layouts.notification')
         </div>
     </div>
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary float-left">Order Lists</h6>
    </div>
    <div class="card-body">
      <!-- Filters -->
      <form action="{{route('order.index')}}" method="GET" class="mb-4">
          <div class="row align-items-end">
              <div class="col-md-3">
                  <label class="small font-weight-bold">Status</label>
                  <select name="status" class="form-control">
                      <option value="">-- All Status --</option>
                      <option value="new" {{request('status')=='new' ? 'selected' : ''}}>New</option>
                      <option value="process" {{request('status')=='process' ? 'selected' : ''}}>Process</option>
                      <option value="delivered" {{request('status')=='delivered' ? 'selected' : ''}}>Delivered</option>
                      <option value="cancel" {{request('status')=='cancel' ? 'selected' : ''}}>Cancel</option>
                  </select>
              </div>
              <div class="col-md-2">
                  <label class="small font-weight-bold">City</label>
                  <select name="city" class="form-control">
                      <option value="">-- All Cities --</option>
                      @foreach($cities as $city)
                          <option value="{{$city}}" {{request('city')==$city ? 'selected' : ''}}>{{$city}}</option>
                      @endforeach
                  </select>
              </div>
              <div class="col-md-2">
                  <label class="small font-weight-bold">Order Source</label>
                  <select name="type" class="form-control">
                      <option value="">-- All Sources --</option>
                      <option value="website" {{request('type')=='website' ? 'selected' : ''}}>Website Order</option>
                      <option value="local" {{request('type')=='local' ? 'selected' : ''}}>Local (POS)</option>
                  </select>
              </div>
              <div class="col-md-3">
                  <label class="small font-weight-bold">Account / Staff</label>
                  <select name="staff_id" class="form-control">
                      <option value="">-- All Accounts --</option>
                      @foreach($staffs as $staff)
                          <option value="{{$staff->id}}" {{request('staff_id')==$staff->id ? 'selected' : ''}}>{{$staff->name}}</option>
                      @endforeach
                  </select>
              </div>
              <div class="col-md-2">
                  <button type="submit" class="btn btn-primary btn-block">Filter</button>
              </div>
              <div class="col-md-2">
                  <a href="{{route('order.index')}}" class="btn btn-secondary btn-block">Reset</a>
              </div>
          </div>
      </form>
      <div class="table-responsive">
        @if(count($orders)>0)
        <table class="table table-bordered" id="order-dataTable" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>S.N.</th>
              <th>Customer Name</th>
              <th>Customer City</th>
              <th>Assigned Staff</th>
              <th>Action & Status</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th>S.N.</th>
              <th>Customer Name</th>
              <th>Customer City</th>
              <th>Assigned Staff</th>
              <th>Action & Status</th>
              </tr>
          </tfoot>
          <tbody>
            @foreach($orders as $order)  
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>
                        <div class="font-weight-bold">{{$order->first_name}} {{$order->last_name}}</div>
                        <div class="d-flex align-items-center">
                            <small class="text-muted mr-2">{{$order->order_number}}</small>
                            @if($order->sales_order_id)
                                <a href="{{route('sales-orders.show', $order->sales_order_id)}}" class="badge badge-primary" title="View parent Sale Order">
                                    <i class="fas fa-link mr-1"></i>SO:{{$order->sales_order->order_number ?? $order->sales_order_id}}
                                </a>
                            @endif
                        </div>
                        <small class="text-muted">{{$order->phone}}</small>
                    </td>
                    <td>
                        {{$order->user->city ?? (Str::contains($order->address1, 'POS') ? 'POS Counter' : $order->address1)}}
                    </td>
                    <td>
                        @if($order->staff)
                            <span class="badge badge-success">{{$order->staff->name}}</span>
                        @else
                            <span class="badge badge-secondary text-white">Unassigned</span>
                        @endif
                    </td>
                    <td>
                        <div class="mb-2">
                            <form action="{{route('order.update', $order->id)}}" method="POST" class="status-update-form">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="form-control form-control-sm font-weight-bold" onchange="this.form.submit()" style="
                                    background: {{ $order->status=='new' ? '#4e73df' : ($order->status=='process' ? '#f6c23e' : ($order->status=='delivered' ? '#1cc88a' : '#e74a3b')) }};
                                    color: #fff;
                                    border: none;
                                    height: 30px;
                                ">
                                    <option value="new" {{$order->status=='new' ? 'selected' : ''}}>NEW</option>
                                    <option value="process" {{$order->status=='process' ? 'selected' : ''}}>PROCESS</option>
                                    <option value="delivered" {{$order->status=='delivered' ? 'selected' : ''}}>DELIVERED</option>
                                    <option value="cancel" {{$order->status=='cancel' ? 'selected' : ''}}>CANCEL</option>
                                </select>
                            </form>
                        </div>
                        <div class="d-flex flex-wrap">
                            <form method="POST" action="{{route('order.toggle-pin', $order->id)}}" style="display:inline-block">
                                @csrf
                                <button type="submit" class="btn btn-{{$order->pinned ? 'info' : 'outline-secondary'}} btn-sm mr-1 mb-1" style="height:25px; width:25px;border-radius:50%; font-size: 10px;" data-toggle="tooltip" title="{{$order->pinned ? 'Unpin' : 'Pin'}}" data-placement="bottom">
                                    <i class="fas fa-thumbtack"></i>
                                </button>
                            </form>
                            <a href="{{route('order.show',$order->id)}}" class="btn btn-warning btn-sm mr-1 mb-1" style="height:25px; width:25px;border-radius:50%; font-size: 10px;" data-toggle="tooltip" title="view" data-placement="bottom"><i class="fas fa-eye"></i></a>
                            <a href="{{route('returns.sale.create',$order->id)}}" class="btn btn-dark btn-sm mr-1 mb-1" style="height:25px; width:25px;border-radius:50%; font-size: 10px;" data-toggle="tooltip" title="Refund" data-placement="bottom"><i class="fas fa-undo"></i></a>
                            @if($order->status == 'delivered')
                                <button class="btn btn-secondary btn-sm mr-1 mb-1" style="height:25px; width:25px;border-radius:50%;opacity:0.5;cursor:not-allowed; font-size: 10px;" disabled data-toggle="tooltip" title="Cannot edit delivered order" data-placement="bottom"><i class="fas fa-edit"></i></button>
                            @else
                                <a href="{{route('order.edit',$order->id)}}" class="btn btn-primary btn-sm mr-1 mb-1" style="height:25px; width:25px;border-radius:50%; font-size: 10px;" data-toggle="tooltip" title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                            @endif
                            <form method="POST" action="{{route('order.destroy',[$order->id])}}">
                              @csrf 
                              @method('delete')
                                  <button class="btn btn-danger btn-sm dltBtn" data-id={{$order->id}} style="height:25px; width:25px;border-radius:50%; font-size: 10px;" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>  
            @endforeach
          </tbody>
        </table>
        <span style="float:right">{{$orders->links()}}</span>
        @else
          <h6 class="text-center">No orders found!!! Please order some products</h6>
        @endif
      </div>
    </div>
    </div>
</div>

    </div>
</div>
@endsection

@push('styles')
  <link href="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
  <style>
      
  </style>
@endpush

@push('scripts')

  <!-- Page level plugins -->
  <script src="{{asset('backend/vendor/datatables/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

  <!-- Page level custom scripts -->
  <script src="{{asset('backend/js/demo/datatables-demo.js')}}"></script>
  <script>
      
    $('#order-dataTable').DataTable( {
            "order": [],
            "columnDefs":[
                {
                    "orderable":false,
                    "targets":[4]
                }
            ]
        } );

        // Sweet alert

        function deleteData(id){
            
        }
  </script>
  <script>
      $(document).ready(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
          $('.dltBtn').click(function(e){
            var form=$(this).closest('form');
              var dataID=$(this).data('id');
              // alert(dataID);
              e.preventDefault();
              swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover this data!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                       form.submit();
                    } else {
                        swal("Your data is safe!");
                    }
                });
          })
      })


  </script>
@endpush
