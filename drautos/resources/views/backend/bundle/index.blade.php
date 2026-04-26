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
            <h6 class="m-0 font-weight-bold text-primary">Product Bundles / Kitting</h6>
            <a href="{{route('bundles.create')}}" class="btn btn-primary btn-sm rounded-pill shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Create New Bundle
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="bundle-dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th>Bundle Name</th>
                            <th>SKU</th>
                            <th>Total Price</th>
                            <th>Items Count</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bundles as $bundle)
                            <tr>
                                <td class="font-weight-bold text-dark">{{$bundle->name}}</td>
                                <td><code>{{$bundle->sku}}</code></td>
                                <td class="font-weight-bold">Rs. {{number_format($bundle->price, 2)}}</td>
                                <td>{{$bundle->items_count}} Products</td>
                                <td>
                                    <span class="badge badge-{{$bundle->status=='active' ? 'success' : 'danger'}} p-2 px-3 rounded-pill text-uppercase" style="font-size: 0.65rem;">
                                        {{$bundle->status}}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{route('bundles.pdf',$bundle->id)}}" class="btn btn-info btn-sm rounded-circle mr-1" style="height:30px; width:30px" title="Print Packing List"><i class="fas fa-print text-white"></i></a>
                                    <a href="{{route('bundles.edit',$bundle->id)}}" class="btn btn-primary btn-sm rounded-circle mr-1" style="height:30px; width:30px" title="Edit"><i class="fas fa-edit text-white"></i></a>
                                    <form method="POST" action="{{route('bundles.destroy',[$bundle->id])}}" style="display:inline-block">
                                      @csrf 
                                      @method('delete')
                                          <button class="btn btn-danger btn-sm rounded-circle dltBtn" data-id={{$bundle->id}} style="height:30px; width:30px"  title="Delete"><i class="fas fa-trash-alt text-white"></i></button>
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
@endpush

@push('scripts')
  <script src="{{asset('backend/vendor/datatables/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
  <script>
      $('#bundle-dataTable').DataTable({
          "columnDefs":[
              {
                  "orderable":false,
                  "targets":[5]
              }
          ]
      });
  </script>
@endpush
