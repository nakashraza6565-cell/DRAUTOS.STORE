@extends('backend.layouts.master')
@section('title','Dr Auto Parts || All Notifications')
@section('main-content')
<div class="card">
    <div class="row">
        <div class="col-md-12">
           @include('backend.layouts.notification')
        </div>
    </div>
  <h5 class="card-header">Notifications</h5>
  <div class="card-body">
    <div class="mb-4">
      <form action="{{route('all.notification')}}" method="GET" class="form-inline">
        <label class="mr-2">From:</label>
        <input type="date" name="date_from" value="{{request('date_from')}}" class="form-control form-control-sm mr-3">
        
        <label class="mr-2">To:</label>
        <input type="date" name="date_to" value="{{request('date_to')}}" class="form-control form-control-sm mr-3">
        
        <label class="mr-2">Status:</label>
        <select name="status" class="form-control form-control-sm mr-3">
          <option value="">-- All --</option>
          <option value="unread" {{request('status')=='unread' ? 'selected' : ''}}>Unread</option>
          <option value="read" {{request('status')=='read' ? 'selected' : ''}}>Read</option>
        </select>
        
        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        <a href="{{route('all.notification')}}" class="btn btn-secondary btn-sm ml-2">Reset</a>
      </form>
    </div>

    @if(count($notifications)>0)
    <table class="table  table-hover admin-table" id="notification-dataTable">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">Time</th>
          <th scope="col">Title</th>
          <th scope="col">Status</th>
          <th scope="col">Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach ( $notifications as $notification)

        <tr class="@if($notification->unread()) bg-light border-left-light @else border-left-success @endif">
          <td scope="row">{{$loop->index +1}}</td>
          <td>{{$notification->created_at->format('F d, Y h:i A')}}</td>
          <td>{{$notification->data['title']}}</td>
          <td>
            @if($notification->read_at)
              <span class="badge badge-success">Read</span>
            @else
              <span class="badge badge-danger">Unread</span>
            @endif
          </td>
          <td>
            <a href="{{route('admin.notification', $notification->id) }}" class="btn btn-primary btn-sm float-left mr-1" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="view" data-placement="bottom"><i class="fas fa-eye"></i></a>
            <form method="POST" action="{{ route('notification.delete', $notification->id) }}">
              @csrf
              @method('delete')
                  <button class="btn btn-danger btn-sm dltBtn" data-id={{$notification->id}} style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fas fa-trash-alt"></i></button>
            </form>
          </td>
        </tr>

        @endforeach
      </tbody>
    </table>
    <div class="mt-4">
      {{$notifications->appends(request()->input())->links()}}
    </div>
    @else
      <h2>Notifications Empty!</h2>
    @endif
  </div>
</div>
@endsection
@push('styles')
  <link href="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />

@endpush
@push('scripts')
  <script src="{{asset('backend/vendor/datatables/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

  <!-- Page level custom scripts -->
  <script src="{{asset('backend/js/demo/datatables-demo.js')}}"></script>
  <script>

      $('#notification-dataTable').DataTable( {
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
