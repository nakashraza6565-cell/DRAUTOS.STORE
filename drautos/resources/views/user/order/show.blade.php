@extends('user.layouts.master')

@section('main-content')
<div class="container-fluid px-3 py-4">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <a href="{{route('user.order.index')}}" class="text-primary font-weight-bold">
            <i class="fas fa-arrow-left mr-1"></i> Back
        </a>
        <a href="{{route('order.pdf',$order->id)}}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
            <i class="fas fa-file-pdf mr-1"></i> PDF Invoice
        </a>
    </div>

    <!-- Status Banner -->
    <div class="card shadow-sm border-0 rounded-lg mb-4 overflow-hidden">
        @php
            $bgClass = 'bg-primary';
            $icon = 'fa-shopping-bag';
            if($order->status == 'delivered') { $bgClass = 'bg-success'; $icon = 'fa-check-circle'; }
            if($order->status == 'process') { $bgClass = 'bg-warning'; $icon = 'fa-sync-alt'; }
            if($order->status == 'cancel') { $bgClass = 'bg-danger'; $icon = 'fa-times-circle'; }
        @endphp
        <div class="card-body {{$bgClass}} text-white py-4">
            <div class="d-flex align-items-center">
                <i class="fas {{$icon}} fa-3x opacity-50 mr-4"></i>
                <div>
                    <h5 class="font-weight-bold mb-1 text-uppercase letter-spacing-1">Order {{$order->status}}</h5>
                    <p class="small mb-0 opacity-80">Order #{{$order->order_number}}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tracking Section (Only if available) -->
    @if($order->courier_number || $order->courier_company)
    <div class="card shadow-sm border-0 rounded-lg mb-4">
        <div class="card-body">
            <h6 class="font-weight-bold text-gray-800 mb-3"><i class="fas fa-truck mr-2 text-primary"></i> Shipment Tracking</h6>
            <div class="row">
                @if($order->courier_company)
                <div class="col-6 border-right">
                    <div class="text-xs text-muted text-uppercase mb-1">Courier</div>
                    <div class="small font-weight-bold">{{$order->courier_company}}</div>
                </div>
                @endif
                @if($order->courier_number)
                <div class="col-6 pl-4">
                    <div class="text-xs text-muted text-uppercase mb-1">Tracking ID</div>
                    <div class="small font-weight-bold">{{$order->courier_number}}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Product List -->
    <h6 class="font-weight-bold text-gray-800 mb-3 ml-1">Order Items ({{$order->cart_info->count()}})</h6>
    @foreach($order->cart_info as $item)
    <div class="card shadow-sm border-0 rounded-lg mb-2">
        <div class="card-body p-3">
            <div class="d-flex align-items-center">
                @if($item->product)
                    @php $photo = explode(',', $item->product->photo); @endphp
                    <div class="mr-3">
                        <img src="{{$photo[0] ?? asset('backend/img/thumbnail-default.jpg')}}" 
                             class="rounded shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                    </div>
                    <div class="flex-grow-1">
                        <div class="font-weight-bold text-gray-800 small">{{$item->product->title}}</div>
                        <div class="text-xs text-muted">
                            {{$item->quantity}} × Rs. {{number_format($item->price, 2)}}
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-weight-bold text-gray-800 small">Rs. {{number_format($item->amount, 2)}}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endforeach

    <!-- Bill Summary -->
    <div class="card shadow-sm border-0 rounded-lg mt-4">
        <div class="card-body">
            <h6 class="font-weight-bold text-gray-800 mb-3">Order Summary</h6>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted small">Subtotal</span>
                <span class="font-weight-bold small">Rs. {{number_format($order->sub_total, 2)}}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted small">Shipping</span>
                <span class="font-weight-bold small">Rs. {{number_format($order->delivery_charge, 2)}}</span>
            </div>
            @if($order->coupon)
            <div class="d-flex justify-content-between mb-2">
                <span class="text-success small font-weight-bold">Coupon</span>
                <span class="text-success font-weight-bold small">-Rs. {{number_format($order->coupon, 2)}}</span>
            </div>
            @endif
            <hr class="my-2">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-gray-800 font-weight-bold">Total Bill</span>
                <span class="h5 mb-0 font-weight-bold text-primary">Rs. {{number_format($order->total_amount, 2)}}</span>
            </div>
        </div>
    </div>

    <!-- Shipping & Payment Info -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-lg mb-3">
                <div class="card-body">
                    <h6 class="font-weight-bold text-gray-800 mb-3">Delivery Address</h6>
                    <p class="small text-muted mb-0">
                        {{$order->first_name}} {{$order->last_name}}<br>
                        {{$order->address1}} {{$order->address2}}<br>
                        {{$order->country}}, {{$order->post_code}}<br>
                        <i class="fas fa-phone-alt mr-1 mt-2"></i> {{$order->phone}}
                    </p>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-body">
                    <h6 class="font-weight-bold text-gray-800 mb-3">Payment Info</h6>
                    <div class="d-flex align-items-center">
                        <div class="badge badge-light p-2 mr-2">
                            <i class="fas fa-money-check-alt text-primary"></i>
                        </div>
                        <div class="small">
                            <div class="font-weight-bold text-uppercase">{{str_replace('cod', 'Cash on Delivery', $order->payment_method)}}</div>
                            <div class="text-muted text-capitalize">{{$order->payment_status}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Footer (Cancel only if new) -->
    @if($order->status == 'new' || $order->status == 'process')
    <div class="mt-5 text-center">
        <a href="{{route('user.online-order.edit', $order->id)}}" class="btn btn-warning rounded-pill px-4 shadow-sm mb-3">
            <i class="fas fa-edit mr-1"></i> Edit Order Items
        </a>
        <br>
        <form method="POST" action="{{route('user.order.delete',[$order->id])}}">
            @csrf
            @method('delete')
            <button type="submit" class="btn btn-link text-danger text-decoration-none small dltBtn">
                <i class="fas fa-trash-alt mr-1"></i> Cancel Order
            </button>
        </form>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .letter-spacing-1 { letter-spacing: 1px; }
    .opacity-50 { opacity: 0.5; }
    .opacity-80 { opacity: 0.8; }
    .card { border-radius: 15px; }
</style>
@endpush
