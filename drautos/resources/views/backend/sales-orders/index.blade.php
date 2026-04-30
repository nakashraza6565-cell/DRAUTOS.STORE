@extends('backend.layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="row">
        <div class="col-md-12">
            @include('backend.layouts.notification')
        </div>
    </div>
    <div class="card-header py-3 d-flex align-items-center justify-content-between flex-wrap" style="gap:10px;">
        <h6 class="m-0 font-weight-bold text-primary">Sale Orders List</h6>
        <div class="d-flex align-items-center flex-wrap" style="gap:8px;">

            {{-- Search Bar --}}
            <form method="GET" action="{{route('sales-orders.index')}}" class="d-flex align-items-center" style="gap:6px;">
                @if($city)<input type="hidden" name="city" value="{{$city}}">@endif
                <div class="input-group input-group-sm" style="width:240px;">
                    <input type="text" name="search" class="form-control border-primary"
                        placeholder="Order no, name, phone..." value="{{$search ?? ''}}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit" title="Search">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                @if($search)
                    <a href="{{route('sales-orders.index', $city ? ['city'=>$city] : [])}}" class="btn btn-sm btn-outline-secondary" title="Clear search">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>

            {{-- Status Filter --}}
            <form method="GET" action="{{route('sales-orders.index')}}" class="d-flex align-items-center" style="gap:6px;">
                @if($search)<input type="hidden" name="search" value="{{$search}}">@endif
                @if($city)<input type="hidden" name="city" value="{{$city}}">@endif
                @if($staffId)<input type="hidden" name="staff_id" value="{{$staffId}}">@endif
                <select name="status" class="form-control form-control-sm" onchange="this.form.submit()" style="min-width:130px;">
                    <option value="pending" {{$status == 'pending' ? 'selected' : ''}}>Pending</option>
                    <option value="partially_delivered" {{$status == 'partially_delivered' ? 'selected' : ''}}>Partial</option>
                    <option value="delivered" {{$status == 'delivered' ? 'selected' : ''}}>Fulfilled</option>
                    <option value="merged" {{$status == 'merged' ? 'selected' : ''}}>Merged/Consolidated</option>
                </select>
                @if($status)
                    <a href="{{route('sales-orders.index', array_filter(['search'=>$search,'city'=>$city,'staff_id'=>$staffId]))}}" class="btn btn-sm btn-outline-secondary" title="Clear status">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>

            {{-- Staff Filter --}}
            <form method="GET" action="{{route('sales-orders.index')}}" class="d-flex align-items-center" style="gap:6px;">
                @if($search)<input type="hidden" name="search" value="{{$search}}">@endif
                @if($city)<input type="hidden" name="city" value="{{$city}}">@endif
                <select name="staff_id" class="form-control form-control-sm" onchange="this.form.submit()" style="min-width:155px;">
                    <option value="">-- All Accounts --</option>
                    @foreach($allStaff as $stf)
                        <option value="{{$stf->id}}" {{$staffId == $stf->id ? 'selected' : ''}}>{{$stf->name}}</option>
                    @endforeach
                </select>
                @if($staffId)
                    <a href="{{route('sales-orders.index', array_filter(['search'=>$search,'city'=>$city]))}}" class="btn btn-sm btn-outline-secondary" title="Clear staff">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>

            {{-- City Filter --}}
            <form method="GET" action="{{route('sales-orders.index')}}" class="d-flex align-items-center" style="gap:6px;">
                @if($search)<input type="hidden" name="search" value="{{$search}}">@endif
                <select name="city" class="form-control form-control-sm" onchange="this.form.submit()" style="min-width:140px;">
                    <option value="">All Cities</option>
                    @foreach($cities as $c)
                        <option value="{{$c}}" {{$city == $c ? 'selected' : ''}}>{{$c}}</option>
                    @endforeach
                </select>
                @if($city)
                    <a href="{{route('sales-orders.index', $search ? ['search'=>$search] : [])}}" class="btn btn-sm btn-outline-secondary" title="Clear city">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>

            <a href="{{route('sales-orders.create')}}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Create Sale Order
            </a>
        </div>
    </div>

    {{-- Active filter summary --}}
    @if($search || $city)
    <div class="px-3 pt-2 pb-0">
        <div class="alert alert-info py-2 mb-2 d-flex align-items-center justify-content-between">
            <span class="small">
                <i class="fas fa-filter mr-1"></i>
                @if($search) Search: <strong>"{{$search}}"</strong> @endif
                @if($search && ($city || $staffId || $status)) &nbsp;+&nbsp; @endif
                @if($city) City: <strong>{{$city}}</strong> @endif
                @if($city && ($staffId || $status)) &nbsp;+&nbsp; @endif
                @if($staffId) Staff: <strong>{{$allStaff->find($staffId)->name ?? ''}}</strong> @endif
                @if($staffId && $status) &nbsp;+&nbsp; @endif
                @if($status) Status: <strong>{{ucfirst(str_replace('_',' ',$status))}}</strong> @endif
                &nbsp;— <strong>{{$salesOrders->total()}}</strong> result(s)
            </span>
            <a href="{{route('sales-orders.index')}}" class="btn btn-sm btn-outline-secondary py-0">Clear All</a>
        </div>
    </div>
    @endif

    <div class="card-body">
        <div class="table-responsive">
            @if(count($salesOrders) > 0)
            <table class="table table-bordered table-hover responsive-table-to-cards" id="order-dataTable" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th style="width:36px;"></th>
                        <th>Order No.</th>
                        <th>Customer</th>
                        <th>City</th>
                        <th>Assigned Staff</th>
                        <th class="text-center">Total Items</th>
                        <th class="text-center">Pending Varieties</th>
                        <th class="text-center">Status</th>
                        <th>Date</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salesOrders as $order)
                    @php
                        $pendingItems     = $order->items->filter(fn($item) => $item->quantity > $item->delivered_quantity);
                        $pendingVarieties = $pendingItems->count();
                        $totalItems       = $order->items->count();
                        $totalAmount      = $order->total_amount;
                        $pendingAmount    = $pendingItems->sum(fn($item) => ($item->quantity - $item->delivered_quantity) * $item->price);
                        $deliveredAmount  = $order->items->sum(fn($item) => $item->delivered_quantity * $item->price);
                        $deliveryPct      = $totalAmount > 0 ? min(100, round(($deliveredAmount / $totalAmount) * 100)) : 0;
                        $barColor         = $deliveryPct >= 75 ? '#1cc88a' : ($deliveryPct >= 40 ? '#f6c23e' : '#f97316');
                    @endphp
                    <tr style="{{ $order->is_priority ? 'border-left: 4px solid #f97316; background: #fffbf7;' : '' }}">
                        <td data-title="S.N.">{{$loop->iteration}}</td>
                        <td data-title="Priority" style="text-align:center; vertical-align:middle;">
                            <form method="POST" action="{{route('sales-orders.toggle-priority', $order->id)}}" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-link p-0 border-0" title="{{$order->is_priority ? 'Remove Priority' : 'Mark as Priority'}}" style="font-size:1.3rem; line-height:1; background:none;">
                                    @if($order->is_priority)
                                        <i class="fas fa-star" style="color:#f97316;"></i>
                                    @else
                                        <i class="far fa-star" style="color:#ccc;"></i>
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td data-title="Order #"><span class="font-weight-bold">{{$order->order_number}}</span></td>
                        <td data-title="Customer">
                            <div class="font-weight-bold">{{$order->user->name ?? 'Guest'}}</div>
                            <small class="text-muted">{{$order->user->phone ?? ''}}</small>
                        </td>
                        <td data-title="City">
                            @if($order->user && $order->user->city)
                                <span class="badge badge-light border">
                                    <i class="fas fa-map-marker-alt text-danger mr-1"></i>{{$order->user->city}}
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td data-title="Staff">
                            @if($order->staff)
                                <span class="badge badge-success">{{$order->staff->name}}</span>
                            @else
                                <span class="badge badge-secondary text-white">Unassigned</span>
                            @endif
                        </td>
                        <td data-title="Items" class="text-center">
                            <span class="ghost-ticker" data-tip="Total: Rs. {{ number_format($totalAmount, 0) }}">
                                <span class="badge badge-secondary px-3">{{$totalItems}}</span>
                            </span>
                        </td>
                        <td data-title="Pending" class="text-center">
                            @if($pendingVarieties > 0)
                                <span class="ghost-ticker" data-tip="Pending: Rs. {{ number_format($pendingAmount, 0) }}">
                                    <span class="badge badge-warning font-weight-bold px-3" style="font-size:0.85rem;">{{$pendingVarieties}}</span>
                                </span>
                            @else
                                <span class="badge badge-success px-3"><i class="fas fa-check"></i></span>
                            @endif
                        </td>
                        <td data-title="Status" class="text-center">
                            @if($order->status=='pending')
                                <span class="badge badge-warning">Pending</span>
                            @elseif($order->status=='partially_delivered')
                                <span class="badge badge-info d-block mb-1">Partial</span>
                                <div class="so-progress-track mx-auto mx-md-auto ml-auto" title="{{$deliveryPct}}% delivered (Rs. {{ number_format($deliveredAmount,0) }} of Rs. {{ number_format($totalAmount,0) }})">
                                    <div class="so-progress-bar" style="width:{{$deliveryPct}}%; background:{{$barColor}};" data-width="{{$deliveryPct}}"></div>
                                    <span class="so-progress-label">{{$deliveryPct}}%</span>
                                </div>
                            @elseif($order->status=='delivered')
                                <span class="badge badge-success">Delivered</span>
                            @elseif($order->status=='merged')
                                <span class="badge badge-dark"><i class="fas fa-layer-group mr-1"></i> Merged</span>
                            @else
                                <span class="badge badge-danger">{{$order->status}}</span>
                            @endif
                        </td>
                        <td data-title="Date">
                            <div>{{$order->created_at->format('d M Y')}}</div>
                            <small class="text-muted">{{$order->created_at->format('h:i A')}}</small>
                        </td>
                        <td data-title="Actions" class="text-center">
                            <div class="d-flex justify-content-end justify-content-md-center">
                                <a href="{{route('sales-orders.show', $order->id)}}" class="btn btn-warning btn-sm mr-1" style="height:32px;width:32px;border-radius:50%;padding:0;display:flex;align-items:center;justify-content:center;" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form method="POST" action="{{route('sales-orders.destroy', [$order->id])}}" class="d-inline">
                                    @csrf
                                    @method('delete')
                                    <button class="btn btn-danger btn-sm dltBtn" data-id="{{$order->id}}" style="height:32px;width:32px;border-radius:50%;padding:0;display:flex;align-items:center;justify-content:center;" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-2">{{$salesOrders->links()}}</div>
            @else
            <div class="text-center py-5 text-muted">
                <i class="fas fa-clipboard-list fa-3x mb-3 opacity-5"></i>
                <p class="font-weight-bold">No Sale Orders found!</p>
                @if($city)
                    <p class="small">No orders found for city "{{$city}}". <a href="{{route('sales-orders.index')}}">Clear filter</a></p>
                @else
                    <a href="{{route('sales-orders.create')}}" class="btn btn-primary btn-sm">Create First Sale Order</a>
                @endif
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
      div.dataTables_wrapper div.dataTables_paginate { display: none; }
      .badge { font-size: 0.78rem; }

      /* Delivery Progress Bar */
      .so-progress-track {
          position: relative;
          background: #e9ecef;
          border-radius: 20px;
          height: 14px;
          width: 80px;
          margin: 0 auto;
          overflow: hidden;
      }
      .so-progress-bar {
          height: 100%;
          border-radius: 20px;
          width: 0;
          transition: width 1.2s cubic-bezier(0.25, 1, 0.5, 1);
          position: relative;
          background-size: 20px 20px !important;
      }
      .so-progress-bar::after {
          content: '';
          position: absolute;
          top: 0; left: 0; right: 0; bottom: 0;
          background: repeating-linear-gradient(
              45deg,
              rgba(255,255,255,0.15) 0,
              rgba(255,255,255,0.15) 5px,
              transparent 5px,
              transparent 10px
          );
          background-size: 20px 20px;
          animation: so-stripe-move 0.7s linear infinite;
          border-radius: 20px;
      }
      @keyframes so-stripe-move {
          from { background-position: 0 0; }
          to   { background-position: 20px 0; }
      }
      .so-progress-label {
          position: absolute;
          top: 0; left: 0; right: 0; bottom: 0;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 0.62rem;
          font-weight: 900;
          color: #1e293b;
          letter-spacing: 0.5px;
          z-index: 2;
          pointer-events: none;
      }

      /* Ghost Ticker Tooltip */
      .ghost-ticker {
          position: relative;
          cursor: default;
          display: inline-block;
      }
      .ghost-ticker::after {
          content: attr(data-tip);
          position: absolute;
          bottom: calc(100% + 8px);
          left: 50%;
          transform: translateX(-50%) translateY(4px);
          background: rgba(15, 23, 42, 0.92);
          color: #fff;
          font-size: 0.72rem;
          font-weight: 700;
          letter-spacing: 0.5px;
          white-space: nowrap;
          padding: 4px 10px;
          border-radius: 6px;
          pointer-events: none;
          opacity: 0;
          transition: opacity 0.18s ease, transform 0.18s ease;
          z-index: 9999;
          box-shadow: 0 4px 12px rgba(0,0,0,0.25);
      }
      .ghost-ticker::before {
          content: '';
          position: absolute;
          bottom: calc(100% + 4px);
          left: 50%;
          transform: translateX(-50%);
          border: 5px solid transparent;
          border-top-color: rgba(15, 23, 42, 0.92);
          pointer-events: none;
          opacity: 0;
          transition: opacity 0.18s ease;
          z-index: 9999;
      }
      .ghost-ticker:hover::after,
      .ghost-ticker:hover::before {
          opacity: 1;
          transform: translateX(-50%) translateY(0);
      }
  </style>
@endpush

@push('scripts')
  <script src="{{asset('backend/vendor/datatables/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
  <script>
      $(document).ready(function(){
          // Animate progress bars on load
          setTimeout(function() {
              $('.so-progress-bar').each(function() {
                  $(this).css('width', $(this).data('width') + '%');
              });
          }, 200);

          $.ajaxSetup({
              headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
          });
          $('.dltBtn').click(function(e){
              var form = $(this).closest('form');
              e.preventDefault();
              swal({
                  title: "Are you sure?",
                  text: "Once deleted, you will not be able to recover this data!",
                  icon: "warning",
                  buttons: true,
                  dangerMode: true,
              }).then((willDelete) => {
                  if (willDelete) { form.submit(); }
                  else { swal("Your data is safe!"); }
              });
          });
      });
  </script>
@endpush
