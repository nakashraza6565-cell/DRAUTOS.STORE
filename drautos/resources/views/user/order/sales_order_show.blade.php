@extends('user.layouts.master')

@section('main-content')
<div class="container-fluid px-3 py-4">
    <div class="mb-4">
        <a href="{{route('user.order.index')}}" class="text-primary font-weight-bold mb-2 d-inline-block">
            <i class="fas fa-arrow-left mr-1"></i> Back to Orders
        </a>
        <h3 class="h4 font-weight-bold text-gray-800">Pending Order Details</h3>
    </div>

    <!-- SO Info Card -->
    <div class="card shadow-sm border-0 rounded-lg mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Order Number</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{$salesOrder->order_number}}</div>
                </div>
                <div class="col-auto">
                    <div class="badge badge-warning p-2 px-3 rounded-pill text-capitalize">
                        {{str_replace('_', ' ', $salesOrder->status)}}
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-6">
                    <div class="text-xs text-muted mb-1">Order Date</div>
                    <div class="small font-weight-bold">{{$salesOrder->created_at->format('d M, Y h:i A')}}</div>
                </div>
                <div class="col-6 text-right">
                    <div class="text-xs text-muted mb-1">Total Amount</div>
                    <div class="small font-weight-bold text-success">Rs. {{number_format($salesOrder->total_amount, 2)}}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Items List -->
    <h6 class="font-weight-bold text-gray-800 mb-3 ml-1">Ordered Items</h6>
    @foreach($salesOrder->items as $item)
    <div class="card shadow-sm border-0 rounded-lg mb-2 overflow-hidden">
        <div class="card-body p-3">
            <div class="d-flex align-items-center">
                @php
                    $photo = explode(',', $item->product->photo);
                @endphp
                <div class="mr-3">
                    <img src="{{$photo[0] ?? asset('backend/img/thumbnail-default.jpg')}}" 
                         class="rounded shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                </div>
                <div class="flex-grow-1">
                    <div class="font-weight-bold text-gray-800 small">{{$item->product->title}}</div>
                    <div class="text-xs text-muted">
                        {{$item->quantity}} {{$item->product->unit}} × Rs. {{number_format($item->price, 2)}}
                    </div>
                </div>
                <div class="text-right">
                    <div class="font-weight-bold text-gray-800 small">Rs. {{number_format($item->quantity * $item->price, 2)}}</div>
                    <div class="text-xs">
                        @if($item->status == 'pending')
                            <span class="text-warning">Pending</span>
                        @elseif($item->status == 'delivered')
                            <span class="text-success">Delivered</span>
                        @else
                            <span class="text-muted">{{ucfirst($item->status)}}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    @if($salesOrder->note)
    <div class="mt-4 p-3 bg-light rounded-lg">
        <div class="text-xs font-weight-bold text-uppercase text-muted mb-1">Note</div>
        <p class="small text-gray-800 mb-0">{{$salesOrder->note}}</p>
    </div>
    @endif
</div>
@endsection
