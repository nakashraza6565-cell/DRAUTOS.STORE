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
                                <td class="font-weight-bold text-dark">
                                    <div class="d-flex align-items-center">
                                        <span class="mr-2">{{$bundle->name}}</span>
                                        <button type="button" class="btn btn-info btn-sm rounded-circle d-flex align-items-center justify-content-center shadow-sm" 
                                            style="width: 22px; height: 22px; padding: 0; font-size: 10px;" 
                                            onclick="showProductHistory({{$bundle->id}}, 'bundle')" title="Selling History">
                                            <i class="fas fa-info text-white"></i>
                                        </button>
                                    </div>
                                </td>
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
    function showProductHistory(pid, type) {
        Swal.fire({
            title: '<i class="fas fa-info-circle mr-2 text-info"></i> Selling History',
            html: '<div class="text-center py-3"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i></div>',
            showConfirmButton: false,
            showCloseButton: true,
            width: '450px',
            didOpen: () => {
                $.get("{{ route('admin.product-selling-history') }}", { product_id: pid, item_type: type }, function(res) {
                    if (res.success) {
                        let historyHtml = `
                            <div class="text-left px-1" style="font-family: 'Inter', sans-serif;">
                                <div class="alert alert-light border mb-3 p-2 d-flex justify-content-between align-items-center" style="border-radius: 10px; background: #f8fafc;">
                                    <div class="small font-weight-bold text-uppercase text-muted">Price Range</div>
                                    <div class="font-weight-bold text-primary">Rs. ${res.min_price.toLocaleString()} - Rs. ${res.max_price.toLocaleString()}</div>
                                </div>
                                
                                <label class="small font-weight-bold text-uppercase text-muted mb-2">Last 5 Sales</label>
                                <div class="table-responsive">
                                    <table class="table table-sm table-borderless mb-0" style="font-size: 12px;">
                                        <thead>
                                            <tr class="border-bottom">
                                                <th style="color: #64748b;">Customer</th>
                                                <th class="text-center" style="color: #64748b;">Qty</th>
                                                <th class="text-right" style="color: #64748b;">Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${res.history.length > 0 ? res.history.map(s => `
                                                <tr class="border-bottom">
                                                    <td>
                                                        <div class="font-weight-bold text-dark">${s.customer}</div>
                                                        <div class="text-muted" style="font-size: 10px;">${s.date}</div>
                                                    </td>
                                                    <td class="text-center align-middle">${s.qty}</td>
                                                    <td class="text-right align-middle font-weight-bold text-success">Rs. ${s.price.toLocaleString()}</td>
                                                </tr>
                                            `).join('') : '<tr><td colspan="3" class="text-center py-3 text-muted">No sales history found</td></tr>'}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        `;
                        Swal.update({
                            html: historyHtml
                        });
                    } else {
                        Swal.update({
                            html: '<div class="text-center py-3 text-danger">Failed to load history</div>'
                        });
                    }
                });
            }
        });
    }
  </script>
@endpush
