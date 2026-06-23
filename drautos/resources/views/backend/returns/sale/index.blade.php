@extends('backend.layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary float-left">Sale Returns</h6>
      <a href="{{ route('returns.sale.create-smart') }}" class="btn btn-primary btn-sm float-right">
        <i class="fas fa-plus mr-1"></i> New Return
      </a>
    </div>

    <div class="card-body">
      <!-- Search Filter -->
      <form action="{{route('returns.sale.index')}}" method="GET" class="mb-4">
          <div class="row align-items-end">
              <div class="col-md-6">
                  <label class="small font-weight-bold text-uppercase">Locate Return / Customer</label>
                  <div class="input-group">
                      <div class="input-group-prepend">
                          <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                      </div>
                      <input type="text" name="search" class="form-control border-left-0" value="{{request('search')}}" placeholder="Search by Customer Name, Order #, or Return #...">
                  </div>
              </div>
              <div class="col-md-2">
                  <button type="submit" class="btn btn-primary btn-block">Search</button>
              </div>
              @if(request('search'))
              <div class="col-md-2">
                  <a href="{{route('returns.sale.index')}}" class="btn btn-secondary btn-block">Clear</a>
              </div>
              @endif
          </div>
      </form>

      <div class="table-responsive">
        @if(count($returns ?? [])>0)
        <table class="table table-bordered" id="data-table">
          <thead>
            <tr>
               <th>Return #</th>
               <th>Date</th>
               <th>Order #</th>
               <th>Customer</th>
               <th>Amount</th>
               <th>Status</th>
               <th>Action</th>
             </tr>
           </thead>
           <tbody>
             @foreach($returns as $return)   
                 <tr>
                     <td><strong>{{$return->return_number}}</strong></td>
                     <td>{{$return->return_date->format('d M Y')}}</td>
                     <td>{{$return->order->order_number ?? 'N/A'}}</td>
                     <td>{{$return->customer->name ?? 'N/A'}}</td>
                     <td>PKR {{number_format($return->total_return_amount, 2)}}</td>
                    <td>
                        @if($return->status == 'pending')
                            <span class="badge badge-warning">Pending</span>
                        @elseif($return->status == 'approved')
                            <span class="badge badge-success">Approved</span>
                        @elseif($return->status == 'rejected')
                            <span class="badge badge-danger">Rejected</span>
                        @else
                            <span class="badge badge-info">Completed</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{route('returns.sale.show', $return->id)}}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                        @if($return->status == 'pending')
                        <form method="POST" action="{{route('returns.sale.approve',$return->id)}}" style="display:inline;">
                          @csrf
                          <button class="btn btn-success btn-sm" title="Approve"><i class="fas fa-check"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>  
            @endforeach
          </tbody>
        </table>
        @else
          <h6 class="text-center">No Sale Returns Found!</h6>
        @endif
      </div>
    </div>
</div>
@endsection
