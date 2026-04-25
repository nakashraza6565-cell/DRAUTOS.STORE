@extends('backend.layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary float-left">Purchase Returns</h6>
      <button class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#addPurchaseReturnModal"><i class="fas fa-plus"></i> New Purchase Return</button>
    </div>
 
    <!-- Select Purchase Order Modal -->
    <div class="modal fade" id="addPurchaseReturnModal" tabindex="-1" role="dialog" aria-labelledby="addPurchaseReturnModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addPurchaseReturnModalLabel"><i class="fas fa-truck-loading mr-2 text-primary"></i>Select Purchase Order for Return</h5>
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
                <input type="text" id="poSearchInput" class="form-control border-left-0" placeholder="Search by PO number or Supplier...">
                <div class="input-group-append">
                  <button class="btn btn-outline-secondary" type="button" onclick="clearPOSearch()"><i class="fas fa-times"></i></button>
                </div>
              </div>
            </div>
            <!-- PO List -->
            <div style="max-height: 380px; overflow-y: auto;" id="poListContainer">
              <div class="list-group list-group-flush" id="poList">
                @forelse($purchaseOrders as $po)
                  <a href="javascript:void(0)"
                     class="list-group-item list-group-item-action po-list-item"
                     data-id="{{$po->id}}"
                     data-search="{{strtolower($po->po_number . ' ' . ($po->supplier->name ?? ''))}}">
                    <div class="d-flex justify-content-between align-items-center">
                      <div>
                        <strong class="text-primary">{{$po->po_number}}</strong>
                        <span class="ml-2 text-dark font-weight-bold">{{$po->supplier->name ?? 'N/A'}}</span>
                        <small class="text-muted ml-2">{{$po->order_date}}</small>
                      </div>
                      <span class="badge badge-success text-uppercase">{{$po->status}}</span>
                    </div>
                  </a>
                @empty
                  <div class="text-center py-4 text-muted">No purchase orders found.</div>
                @endforelse
              </div>
              <div class="text-center py-4 text-muted d-none" id="noPOSearchResults"><i class="fas fa-search-minus mr-2"></i>No matching records.</div>
            </div>
            <!-- Selection Info -->
            <div class="p-3 border-top bg-light d-none" id="selectedPOInfo">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block text-uppercase font-weight-bold" style="font-size: 0.7rem;">Selected Record</small>
                        <h6 class="m-0 font-weight-bold text-dark" id="selectedPOLabel">PO-XXXX</h6>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm rounded-pill px-3" onclick="clearSelectedPO()">Change</button>
                </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="proceedPOBtn" disabled onclick="proceedToReturn()">Proceed to Return Form</button>
          </div>
        </div>
      </div>
    </div>
    <div class="card-body">
      <!-- Search Filter -->
      <form action="{{route('returns.purchase.index')}}" method="GET" class="mb-4">
          <div class="row align-items-end">
              <div class="col-md-6">
                  <label class="small font-weight-bold text-uppercase">Locate Purchase Return / Supplier</label>
                  <div class="input-group">
                      <div class="input-group-prepend">
                          <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                      </div>
                      <input type="text" name="search" class="form-control border-left-0" value="{{request('search')}}" placeholder="Search by Supplier Name or Return #...">
                  </div>
              </div>
              <div class="col-md-2">
                  <button type="submit" class="btn btn-primary btn-block">Search</button>
              </div>
              @if(request('search'))
              <div class="col-md-2">
                  <a href="{{route('returns.purchase.index')}}" class="btn btn-secondary btn-block">Clear</a>
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
              <th>Supplier</th>
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
                    <td>{{$return->supplier->name ?? 'N/A'}}</td>
                    <td>PKR {{number_format($return->total_return_amount, 2)}}</td>
                    <td>
                        @if($return->status == 'pending')
                            <span class="badge badge-warning">Pending</span>
                        @elseif($return->status == 'approved')
                            <span class="badge badge-success">Approved</span>
                        @else
                            <span class="badge badge-info">Completed</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{route('returns.purchase.show', $return->id)}}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                        @if($return->status == 'pending')
                        <form method="POST" action="{{route('returns.purchase.approve',$return->id)}}" style="display:inline;">
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
          <h6 class="text-center">No Purchase Returns Found!</h6>
        @endif
      </div>
    </div>
</div>
@endsection
 
@push('scripts')
<script>
    var selectedPOId = null;
 
    // Search filtering
    document.getElementById('poSearchInput').addEventListener('input', function() {
        var query = this.value.toLowerCase().trim();
        var items = document.querySelectorAll('.po-list-item');
        var visibleCount = 0;
        items.forEach(function(item) {
            var matches = item.getAttribute('data-search').includes(query);
            item.style.display = matches ? '' : 'none';
            if (matches) visibleCount++;
        });
        document.getElementById('noPOSearchResults').classList.toggle('d-none', visibleCount > 0);
    });
 
    function clearPOSearch() {
        document.getElementById('poSearchInput').value = '';
        document.querySelectorAll('.po-list-item').forEach(function(item) {
            item.style.display = '';
        });
        document.getElementById('noPOSearchResults').classList.add('d-none');
    }
 
    // Selecting a PO
    document.querySelectorAll('.po-list-item').forEach(function(item) {
        item.addEventListener('click', function() {
            // Deselect previous
            document.querySelectorAll('.po-list-item').forEach(function(i) {
                i.classList.remove('active');
            });
            this.classList.add('active');
            selectedPOId = this.getAttribute('data-id');
            var label = this.querySelector('strong').innerText + ' – ' + this.querySelector('span').innerText.trim();
            document.getElementById('selectedPOLabel').innerText = label;
            document.getElementById('selectedPOInfo').classList.remove('d-none');
            document.getElementById('proceedPOBtn').removeAttribute('disabled');
        });
    });
 
    function clearSelectedPO() {
        selectedPOId = null;
        document.querySelectorAll('.po-list-item').forEach(function(i) { i.classList.remove('active'); });
        document.getElementById('selectedPOInfo').classList.add('d-none');
        document.getElementById('proceedPOBtn').setAttribute('disabled', true);
    }
 
    function proceedToReturn() {
        if (selectedPOId) {
            window.location.href = "{{ url('admin/returns/purchase/create') }}/" + selectedPOId;
        }
    }
 
    // Reset modal on close
    document.getElementById('addPurchaseReturnModal').addEventListener('hidden.bs.modal', function() {
        clearSelectedPO();
        clearPOSearch();
    });
</script>
@endpush
