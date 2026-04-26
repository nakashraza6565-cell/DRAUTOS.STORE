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
      <h6 class="m-0 font-weight-bold text-primary float-left">Die List</h6>
      <a href="{{route('die-management.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="bottom" title="Add User"><i class="fas fa-plus"></i> Add Die</a>
    </div>
     <div class="card-body">
       <div class="row">
         @if(count($dies)>0)
           @foreach($dies as $die)
             <div class="col-xl-4 col-md-6 mb-4">
               <div class="card shadow h-100 border-bottom-primary die-profile-card">
                 <div class="card-body">
                   <div class="row no-gutters align-items-center">
                     <div class="col-auto mr-3">
                       @if($die->photo)
                         <img src="{{$die->photo}}" class="img-fluid rounded" style="width: 100px; height: 100px; object-fit: cover; border: 2px solid #eaecf4;" alt="{{$die->name}}">
                       @else
                         <div class="bg-gray-200 rounded d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; border: 2px solid #eaecf4;">
                           <i class="fas fa-tools fa-2x text-gray-400"></i>
                         </div>
                       @endif
                     </div>
                     <div class="col">
                       <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Rack: {{$die->rack_number ?? 'N/A'}}</div>
                       <div class="h5 mb-0 font-weight-bold text-gray-800">{{$die->name}}</div>
                       <div class="mt-2">
                         @php
                            $qualityClass = 'badge-secondary';
                            if($die->quality_status == 'good') $qualityClass = 'badge-success';
                            if($die->quality_status == 'maintenance_required') $qualityClass = 'badge-warning';
                            if($die->quality_status == 'damaged') $qualityClass = 'badge-danger';
                         @endphp
                         <span class="badge {{$qualityClass}}">{{str_replace('_', ' ', strtoupper($die->quality_status))}}</span>
                         @if($die->status=='active')
                           <span class="badge badge-pill badge-primary">ACTIVE</span>
                         @else
                           <span class="badge badge-pill badge-light border">INACTIVE</span>
                         @endif
                       </div>
                     </div>
                   </div>
                   <hr class="my-3">
                   <div class="row small">
                     <div class="col-6">
                       <p class="mb-1"><strong>Maker:</strong> {{$die->maker ?? 'N/A'}}</p>
                       <p class="mb-0"><strong>Type:</strong> {{$die->die_type ?? 'N/A'}}</p>
                     </div>
                     <div class="col-6">
                       <p class="mb-1"><strong>Custody:</strong> {{$die->custody_of ?? 'N/A'}}</p>
                       <p class="mb-0"><strong>Contact:</strong> {{$die->custody_phone ?? 'N/A'}}</p>
                     </div>
                   </div>
                   <div class="mt-3 text-right">
                     <a href="{{route('die-management.edit',$die->id)}}" class="btn btn-primary btn-sm rounded-circle shadow-sm" style="width: 35px; height: 35px;" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></a>
                     <form method="POST" action="{{route('die-management.destroy',[$die->id])}}" class="d-inline">
                       @csrf 
                       @method('delete')
                       <button class="btn btn-danger btn-sm rounded-circle shadow-sm dltBtn" data-id={{$die->id}} style="width: 35px; height: 35px;" data-toggle="tooltip" title="Delete"><i class="fas fa-trash-alt"></i></button>
                     </form>
                   </div>
                 </div>
               </div>
             </div>
           @endforeach
         @else
           <div class="col-12">
             <h6 class="text-center py-5">No Dies found!!! Please add some.</h6>
           </div>
         @endif
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
      
      $('#banner-dataTable').DataTable( {
            "columnDefs":[
                {
                    "orderable":false,
                    "targets":[6]
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
