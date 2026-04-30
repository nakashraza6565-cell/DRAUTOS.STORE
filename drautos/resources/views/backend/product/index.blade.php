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
      <h6 class="m-0 font-weight-bold text-primary float-left">Product Lists</h6>
      <a href="{{route('product.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="bottom" title="Add User"><i class="fas fa-plus"></i> Add Product</a>
    </div>
    <div class="card-body">
      <!-- Simple Sleek Search -->
      <form action="{{route('product.index')}}" method="GET" class="mb-4">
          <div class="search-wrapper-sleek d-flex align-items-center px-3 shadow-sm border" style="background: #fff; border-radius: 100px; height: 50px;">
              <i class="fas fa-search text-muted mr-3"></i>
              <input type="text" name="title" class="form-control border-0 shadow-none p-0 bg-transparent" placeholder="Search products by title or SKU..." value="{{request('title')}}" style="font-weight: 500;">
              @if(request('title'))
                <a href="{{route('product.index')}}" class="text-danger ml-3" title="Clear Search"><i class="fas fa-times-circle"></i></a>
              @endif
              <button type="submit" class="btn btn-primary ml-3 px-4 d-none d-md-block" style="border-radius: 100px;">Search</button>
          </div>
      </form>
      <div class="table-responsive">
        @if(count($products)>0)
        <table class="table table-bordered responsive-table-to-cards" id="product-dataTable" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>S.N.</th>
              <th>Title</th>
              <th>Category</th>
              <th>Price</th>
              <th>Color/Brand</th>
              <th>Stock</th>
              <th>Photo</th>
              <th>Action & Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach($products as $product)
                <tr>
                    <td data-title="S.N.">{{$loop->iteration}}</td>
                    <td data-title="Product">
                        <div class="font-weight-bold editable-title" data-id="{{$product->id}}" style="cursor: pointer;" title="Click to edit title">{{$product->title}}</div>
                        <small class="text-muted d-block">SKU: {{$product->sku}}</small>
                        @if($product->is_featured) <span class="badge badge-warning" style="font-size: 10px;">Featured</span> @endif
                    </td>
                    <td data-title="Category">
                      {{$product->cat_info->title ?? 'N/A'}}
                      <div class="small text-muted">{{$product->sub_cat_info->title ?? ''}}</div>
                    </td>
                    <td data-title="Price" class="font-weight-bold">PKR {{number_format($product->price ?? 0, 0)}}</td>
                    <td data-title="Brand">
                        <div class="d-flex align-items-center">
                            @if($product->color)
                                <span class="mr-2" style="width: 14px; height: 14px; border-radius: 50%; background-color: {{$product->color}}; border: 1px solid #ddd;"></span>
                            @endif
                            <span>{{ucfirst($product->brand->title ?? 'N/A')}}</span>
                        </div>
                    </td>
                    <td data-title="Stock">
                      @if($product->stock>0)
                      <span class="badge badge-primary px-3">{{$product->stock}}</span>
                      @else
                      <span class="badge badge-danger px-3">{{$product->stock}}</span>
                      @endif
                    </td>
                    <td data-title="Photo" class="text-center">
                        <div class="position-relative d-inline-block">
                            @if($product->photo)
                                @php
                                  $photo=explode(',',$product->photo);
                                  $src = $photo[0];
                                  if(!Str::startsWith($src, ['http', '/'])) $src = '/' . $src;
                                @endphp
                                <img src="{{$src}}" class="img-fluid zoom rounded" style="max-width:60px" alt="Product Photo">
                            @else
                                <img src="{{asset('backend/img/thumbnail-default.jpg')}}" class="img-fluid rounded" style="max-width:60px" alt="default">
                            @endif
                            <button type="button" class="btn btn-light btn-sm rounded-circle position-absolute photo-upload-btn" data-id="{{$product->id}}" style="bottom: -5px; right: -5px; width: 20px; height: 20px; padding: 0; line-height: 20px; font-size: 10px; border: 1px solid #ccc;" title="Change Photo">
                                <i class="fas fa-camera"></i>
                            </button>
                        </div>
                    </td>
                    <td data-title="Actions">
                        <div class="mb-2">
                            @if($product->status=='active')
                                <span class="badge badge-success">{{$product->status}}</span>
                            @else
                                <span class="badge badge-warning">{{$product->status}}</span>
                            @endif
                        </div>
                        <div class="d-flex border-top pt-2 mt-1 justify-content-end">
                            <a href="{{route('product.edit',$product->id)}}" class="btn btn-primary btn-sm mr-1" style="height:32px; width:32px;border-radius:50%; display: flex; align-items: center; justify-content: center;" data-toggle="tooltip" title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                            <form method="POST" action="{{route('product.destroy',[$product->id])}}">
                                @csrf
                                @method('delete')
                                <button class="btn btn-danger btn-sm dltBtn" data-id={{$product->id}} style="height:32px; width:32px;border-radius:50%; display: flex; align-items: center; justify-content: center;" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
          </tbody>
        </table>
        <span style="float:right">{{$products->links()}}</span>
        @else
          <h6 class="text-center">No Products found!!! Please create Product</h6>
        @endif
      </div>
    </div>
</div>
@endsection

@push('styles')
  <link href="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
  <style>
      .zoom { transition: transform .2s; }
      .zoom:hover { transform: scale(5); }
      .editable-title:hover { color: #4e73df; text-decoration: underline; }
      .title-input { padding: 2px 5px; border: 1px solid #4e73df; border-radius: 4px; width: 100%; }
  </style>
@endpush

@push('scripts')

  <!-- Page level plugins -->
  <script src="{{asset('backend/vendor/datatables/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

  <!-- Page level custom scripts -->
  <script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
  <script src="{{asset('backend/js/demo/datatables-demo.js')}}"></script>
  <script>
      // Global variable to keep track of product being updated
      var currentProductId = null;

      function initDirectPhotoUpload() {
          $(document).on('click', '.photo-upload-btn', function() {
              currentProductId = $(this).data('id');
              // Trigger LFM
              var route_prefix = "/laravel-filemanager";
              window.open(route_prefix + '?type=image', 'FileManager', 'width=900,height=600');
              
              // Define one-time callback
              window.SetUrl = function (items) {
                  var file_path = items.map(function (item) {
                      // Robust regex to strip protocol and domain, leaving the absolute path (e.g., /storage/...)
                      return item.url.replace(/^https?:\/\/[^\/]+/, '');
                  }).join(',');

                  if(currentProductId && file_path) {
                      updateProductPhoto(currentProductId, file_path);
                  }
              };
          });
      }

      function updateProductPhoto(id, photoPath) {
          $.ajax({
              url: "/admin/product/" + id + "/update-photo",
              type: "POST",
              data: {
                  _token: "{{csrf_token()}}",
                  photo: photoPath
              },
              success: function(res) {
                  if(res.status == 'success') {
                      // Reload page to show new photo or update DOM
                      location.reload();
                  }
              },
              error: function(err) {
                  alert('Failed to update photo');
              }
          });
      }

      $(document).ready(function() {
          initDirectPhotoUpload();

          // Inline Title Edit logic
          $(document).on('click', '.editable-title', function() {
              var $this = $(this);
              var currentTitle = $this.text().trim();
              var id = $this.data('id');
              
              var $input = $('<input type="text" class="title-input">').val(currentTitle);
              $this.hide().after($input);
              $input.focus();

              $input.on('blur keyup', function(e) {
                  if (e.type === 'keyup' && e.keyCode !== 13 && e.keyCode !== 27) return;

                  if (e.keyCode === 27) { // Cancel
                      $input.remove();
                      $this.show();
                      return;
                  }

                  var newTitle = $input.val().trim();
                  if (newTitle !== "" && newTitle !== currentTitle) {
                      $.ajax({
                          url: "/admin/product/" + id + "/update-title",
                          type: "POST",
                          data: {
                              _token: "{{csrf_token()}}",
                              title: newTitle
                          },
                          success: function(res) {
                              if (res.status === 'success') {
                                  $this.text(res.title);
                                  $input.remove();
                                  $this.show();
                                  // Optional: toast success
                              }
                          },
                          error: function(err) {
                              alert('Failed to update title');
                              $input.remove();
                              $this.show();
                          }
                      });
                  } else {
                      $input.remove();
                      $this.show();
                  }
              });
          });
      });

      var dataTable = $('#product-dataTable').DataTable({
        "scrollX": false,
        "columnDefs":[
            { "orderable": false, "targets": [7, 8] }
        ]
      });

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
