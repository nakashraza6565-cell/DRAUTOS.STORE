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
                        Total ordered: {{$item->quantity}} {{$item->product->unit}}
                    </div>
                    <div class="text-xs font-weight-bold mt-1">
                        @if($item->delivered_quantity >= $item->quantity)
                            <span class="text-success"><i class="fas fa-check-circle mr-1"></i> Fully Fulfilled</span>
                        @elseif($item->delivered_quantity > 0)
                            <span class="text-info"><i class="fas fa-truck-loading mr-1"></i> Fulfilled: {{$item->delivered_quantity}} / {{$item->quantity}}</span>
                        @else
                            <span class="text-warning"><i class="fas fa-clock mr-1"></i> Pending Fulfillment</span>
                        @endif
                    </div>
                </div>
                <div class="text-right">
                    <div class="font-weight-bold text-gray-800 small">Rs. {{number_format($item->quantity * $item->price, 2)}}</div>
                    <div class="text-xs mt-1">
                        @if($item->delivered_quantity < $item->quantity)
                             <span class="badge badge-warning-soft" style="background: #fffbeb; color: #b45309;">Outstanding: {{$item->quantity - $item->delivered_quantity}}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    {{-- Photo Upload & Gallery Section --}}
    <div class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3 ml-1">
            <h6 class="font-weight-bold text-gray-800 mb-0"><i class="fas fa-camera mr-1"></i> Order Reference Photos</h6>
            <button class="btn btn-sm btn-outline-primary" onclick="document.getElementById('customer_photo_input').click()">
                <i class="fas fa-upload mr-1"></i> Upload Order/Item Photo
            </button>
            <form id="customer-photo-upload-form" action="{{ route('user.sales-orders.photos.upload', $salesOrder->id) }}" method="POST" enctype="multipart/form-data" style="display:none;">
                @csrf
                <input type="file" id="customer_photo_input" name="order_photos[]" multiple accept="image/*,.pdf" onchange="document.getElementById('customer-photo-upload-form').submit()">
            </form>
        </div>

        @if($salesOrder->photos->count() > 0)
        <div class="row px-1">
            @foreach($salesOrder->photos as $photo)
            <div class="col-6 col-md-4 px-2 mb-3">
                <a href="{{ route('user.sales-orders.photos.view', [$salesOrder->id, $photo->id]) }}" target="_blank" class="card shadow-sm border-0 rounded-lg overflow-hidden text-decoration-none">
                    <div style="height: 120px; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                        @if(str_contains($photo->mime_type ?? '', 'pdf'))
                            <i class="fas fa-file-pdf fa-3x text-danger"></i>
                        @else
                            <img src="{{ route('user.sales-orders.photos.view', [$salesOrder->id, $photo->id]) }}" style="width: 100%; height: 100%; object-fit: cover;" alt="Order Photo">
                        @endif
                    </div>
                    <div class="card-footer bg-white py-2 px-2 border-0 text-center">
                        <div class="text-xs text-truncate text-gray-800 font-weight-bold" title="{{$photo->original_name}}">{{$photo->original_name}}</div>
                        <div class="text-xs text-muted" style="font-size: 10px;">{{$photo->created_at->format('d M Y')}}</div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
        @else
        <div class="p-4 bg-light rounded-lg text-center">
            <p class="text-muted small mb-0">No photos attached. You can upload photos as evidence or reference for this order.</p>
        </div>
        @endif
    </div>

    @if($salesOrder->note)
    <div class="mt-4 p-3 bg-light rounded-lg">
        <div class="text-xs font-weight-bold text-uppercase text-muted mb-1">Note</div>
        <p class="small text-gray-800 mb-0">{{$salesOrder->note}}</p>
    </div>
    @endif
</div>
@endsection
