@extends('backend.layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary float-left">Sale Returns</h6>
      <button class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#addSaleReturnModal"><i class="fas fa-plus"></i> Add Sale Return</button>
    </div>

    <!-- Add Sale Return Modal -->
    <div class="modal fade" id="addSaleReturnModal" tabindex="-1" role="dialog" aria-labelledby="addSaleReturnModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addSaleReturnModalLabel"><i class="fas fa-undo mr-2 text-primary"></i>Select Order for Sale Return</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body p-0">
            <!-- Search Bar -->
            <div class="p-3 border-bottom bg-light">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                </div>
                <input type="text" id="orderSearchInput" class="form-control border-left-0" placeholder="Search by order number, customer name or phone...">
                <div class="input-group-append">
                  <button class="btn btn-outline-secondary" type="button" onclick="clearOrderSearch()"><i class="fas fa-times"></i></button>
                </div>
              </div>
              <small class="text-muted mt-1 d-block">Showing orders with status: Process / Delivered</small>
            </div>
            <!-- Order List -->
            <div style="max-height: 380px; overflow-y: auto;" id="orderListContainer">
              <div class="list-group list-group-flush" id="orderList">
                @forelse($orders as $order)
                  <a href="javascript:void(0)"
                     class="list-group-item list-group-item-action order-list-item"
                     data-id="{{$order->id}}"
                     data-search="{{strtolower($order->order_number . ' ' . $order->first_name . ' ' . $order->last_name . ' ' . $order->phone)}}">
                    <div class="d-flex justify-content-between align-items-center">
                      <div>
                        <strong class="text-primary">{{$order->order_number}}</strong>
                        <span class="ml-2">{{$order->first_name}} {{$order->last_name}}</span>
                        @if($order->phone)
                          <small class="text-muted ml-2"><i class="fas fa-phone fa-xs"></i> {{$order->phone}}</small>
                        @endif
                      </div>
                      <span class="badge badge-{{$order->status == 'delivered' ? 'success' : 'warning'}}">{{ucfirst($order->status)}}</span>
                    </div>
                  </a>
                @empty
                  <div class="text-center py-4 text-muted" id="noOrdersMsg">No orders found.</div>
                @endforelse
              </div>
              <div class="text-center py-4 text-muted d-none" id="noSearchResults"><i class="fas fa-search-minus mr-2"></i>No orders match your search.</div>
            </div>
            <!-- Selected Order -->
            <div class="p-3 border-top bg-light d-none" id="selectedOrderInfo">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <small class="text-muted">Selected Order:</small>
                  <strong id="selectedOrderLabel" class="d-block text-primary"></strong>
                </div>
                <button class="btn btn-sm btn-outline-danger" onclick="clearSelectedOrder()"><i class="fas fa-times"></i> Clear</button>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="proceedBtn" onclick="proceedToReturn()" disabled>
              <i class="fas fa-arrow-right mr-1"></i> Proceed to Return Form
            </button>
          </div>
        </div>
      </div>
    </div>

    <script>
        var selectedOrderId = null;

        // Search filtering
        document.getElementById('orderSearchInput').addEventListener('input', function() {
            var query = this.value.toLowerCase().trim();
            var items = document.querySelectorAll('.order-list-item');
            var visibleCount = 0;
            items.forEach(function(item) {
                var matches = item.getAttribute('data-search').includes(query);
                item.style.display = matches ? '' : 'none';
                if (matches) visibleCount++;
            });
            document.getElementById('noSearchResults').classList.toggle('d-none', visibleCount > 0);
        });

        function clearOrderSearch() {
            document.getElementById('orderSearchInput').value = '';
            document.querySelectorAll('.order-list-item').forEach(function(item) {
                item.style.display = '';
            });
            document.getElementById('noSearchResults').classList.add('d-none');
        }

        // Selecting an order
        document.querySelectorAll('.order-list-item').forEach(function(item) {
            item.addEventListener('click', function() {
                // Deselect previous
                document.querySelectorAll('.order-list-item').forEach(function(i) {
                    i.classList.remove('active');
                });
                this.classList.add('active');
                selectedOrderId = this.getAttribute('data-id');
                var label = this.querySelector('strong').innerText + ' – ' + this.querySelector('span').innerText.trim();
                document.getElementById('selectedOrderLabel').innerText = label;
                document.getElementById('selectedOrderInfo').classList.remove('d-none');
                document.getElementById('proceedBtn').removeAttribute('disabled');
            });
        });

        function clearSelectedOrder() {
            selectedOrderId = null;
            document.querySelectorAll('.order-list-item').forEach(function(i) { i.classList.remove('active'); });
            document.getElementById('selectedOrderInfo').classList.add('d-none');
            document.getElementById('proceedBtn').setAttribute('disabled', true);
        }

        // Reset modal on close
        document.getElementById('addSaleReturnModal').addEventListener('hidden.bs.modal', function() {
            clearSelectedOrder();
            clearOrderSearch();
        });

        function proceedToReturn() {
            if(selectedOrderId) {
                window.location.href = "{{url('admin/returns/sale/create')}}/" + selectedOrderId;
            } else {
                alert('Please select an order first.');
            }
        }
    </script>
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
