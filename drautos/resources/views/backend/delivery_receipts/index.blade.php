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
      <h6 class="m-0 font-weight-bold text-primary float-left">Delivery Receipts (Bilties)</h6>
      <button data-toggle="modal" data-target="#quickBiltyModal" class="btn btn-primary btn-sm float-right"><i class="fas fa-plus"></i> Create Receipt</button>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        @if(count($receipts)>0)
        <table class="table table-bordered" id="receipt-dataTable" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>Receipt No.</th>
              <th>Date</th>
              <th>Receiver Name</th>
              <th>Courier</th>
              <th>City</th>
              <th>Parcels</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($receipts as $receipt)   
                <tr>
                    <td>{{$receipt->receipt_number}}</td>
                    <td>{{$receipt->date}}</td>
                    <td>{{$receipt->receiver_name}}</td>
                    <td>{{$receipt->courier_company}}</td>
                    <td>{{$receipt->city}}</td>
                    <td>{{$receipt->total_parcels}}</td>
                    <td>
                        <a href="{{route('delivery-receipts.print', $receipt->id)}}" class="btn btn-warning btn-sm float-left mr-1" style="height:30px; width:30px; border-radius:50%" data-toggle="tooltip" title="Print Bilty" data-placement="bottom"><i class="fas fa-print"></i></a>
                    </td>
                </tr>  
            @endforeach
          </tbody>
        </table>
        <div class="d-flex justify-content-center">
            {{ $receipts->links() }}
        </div>
        @else
          <h6 class="text-center">No delivery receipts found!!!</h6>
        @endif
      </div>
    </div>
</div>

<!-- Quick Bilty Modal is included from master layout or can be duplicated here, but since it is in dashboard, we should link back to dashboard or move modal to a partial. For now, button above works if we move modal to master. Wait, modal is in index.blade.php. Let's just make the button link to dashboard instead. -->
<script>
    document.querySelector('.btn-primary.btn-sm.float-right').addEventListener('click', function(e) {
        e.preventDefault();
        window.location.href = "{{route('admin')}}";
    });
</script>
@endsection
