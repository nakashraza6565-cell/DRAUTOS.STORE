@extends('backend.layouts.master')

@section('main-content')
<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
      <h6 class="m-0 font-weight-bold text-primary">Factors of Production (Raw Materials & Labor)</h6>
      <div>
          <a href="{{route('manufacturing.production-factors.purchase.create')}}" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="bottom" title="Log Multiple Materials"><i class="fas fa-shopping-cart"></i> Receive Materials</a>
          <a href="{{route('manufacturing.production-factors.create')}}" class="btn btn-primary btn-sm ml-2" data-toggle="tooltip" data-placement="bottom" title="Add New"><i class="fas fa-plus"></i> Add New Factor</a>
      </div>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        @if(count($factors)>0)
        <table class="table table-bordered table-hover" id="factor-dataTable" width="100%" cellspacing="0">
          <thead class="bg-primary text-white">
            <tr>
              <th>S.N.</th>
              <th>Name</th>
              <th>Type</th>
              <th>Unit</th>
              <th>Default Cost</th>
              <th>Stock (Materials)</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($factors as $factor)
              <tr>
                  <td>{{$loop->iteration}}</td>
                  <td class="font-weight-bold">{{$factor->name}}</td>
                  <td>
                      @if($factor->type == 'material')
                        <span class="badge badge-info">Raw Material</span>
                      @elseif($factor->type == 'labor')
                        <span class="badge badge-warning">Labor / Subcontractor</span>
                      @elseif($factor->type == 'overhead')
                        <span class="badge badge-secondary">Overhead</span>
                      @else
                        <span class="badge badge-primary">{{ucfirst($factor->type)}}</span>
                      @endif
                  </td>
                  <td>{{$factor->unit ?? '-'}}</td>
                  <td>Rs. {{number_format($factor->cost_price, 2)}}</td>
                  <td>
                      @if($factor->type == 'material')
                        <span class="badge badge-{{$factor->stock_quantity > 0 ? 'success' : 'danger'}}">{{$factor->stock_quantity}} {{$factor->unit}}</span>
                      @else
                        -
                      @endif
                  </td>
                  <td>
                      @if($factor->status=='active')
                          <span class="badge badge-success">Active</span>
                      @else
                          <span class="badge badge-danger">Inactive</span>
                      @endif
                  </td>
                  <td>
                      <a href="{{route('manufacturing.production-factors.edit',$factor->id)}}" class="btn btn-primary btn-sm float-left mr-1" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                      <form method="POST" action="{{route('manufacturing.production-factors.destroy',[$factor->id])}}">
                        @csrf
                        @method('delete')
                            <button class="btn btn-danger btn-sm dltBtn" data-id={{$factor->id}} style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fas fa-trash-alt"></i></button>
                      </form>
                  </td>
              </tr>
            @endforeach
          </tbody>
        </table>
        <span style="float:right">{{$factors->links()}}</span>
        @else
          <h6 class="text-center">No factors found! Please add raw materials or labor types to start.</h6>
        @endif
      </div>
    </div>
</div>
@endsection

@push('styles')
  <link href="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
  <style>
      div.dataTables_wrapper div.dataTables_paginate{
          display: none;
      }
  </style>
@endpush

@push('scripts')
  <!-- Page level plugins -->
  <script src="{{asset('backend/vendor/datatables/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

  <!-- Page level custom scripts -->
  <script>
      $('#factor-dataTable').DataTable({
            "columnDefs":[
                {
                    "orderable":false,
                    "targets":[7]
                }
            ]
        });

        $('.dltBtn').click(function(e){
          var form=$(this).closest('form');
          var dataID=$(this).data('id');
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
                }
            });
        });
  </script>
@endpush
