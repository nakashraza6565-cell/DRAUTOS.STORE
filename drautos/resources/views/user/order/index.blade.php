@extends('user.layouts.master')

@section('main-content')
<div class="container-fluid px-3 py-4">
    <!-- Header Section -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h3 class="h4 font-weight-bold text-gray-800 mb-0">My Orders</h3>
        <a href="{{route('user.online-order')}}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
            <i class="fas fa-plus mr-1"></i> New Order
        </a>
    </div>

    @include('user.layouts.notification')

    <!-- Tab Navigation -->
    <ul class="nav nav-pills mb-4 bg-white p-2 rounded-pill shadow-sm" id="orderTabs" role="tablist">
        <li class="nav-item flex-fill" role="presentation">
            <a class="nav-link active rounded-pill text-center font-weight-bold" id="booking-tab" data-toggle="pill" href="#booking" role="tab">
                <i class="fas fa-bookmark mr-1"></i> Booking
                <span class="badge badge-light ml-1">{{count($sales_orders) + count($booking_online)}}</span>
            </a>
        </li>
        <li class="nav-item flex-fill" role="presentation">
            <a class="nav-link rounded-pill text-center font-weight-bold" id="pending-tab" data-toggle="pill" href="#pending" role="tab">
                <i class="fas fa-clock mr-1"></i> Pending
                <span class="badge badge-light ml-1">{{count($pending_online)}}</span>
            </a>
        </li>
        <li class="nav-item flex-fill" role="presentation">
            <a class="nav-link rounded-pill text-center font-weight-bold" id="delivered-tab" data-toggle="pill" href="#delivered" role="tab">
                <i class="fas fa-check-circle mr-1"></i> Delivered
                <span class="badge badge-light ml-1">{{$delivered_orders->total()}}</span>
            </a>
        </li>
    </ul>

    <div class="tab-content" id="orderTabsContent">
        <!-- Booking Orders -->
        <div class="tab-pane fade show active" id="booking" role="tabpanel">
            @if(count($sales_orders) > 0 || count($booking_online) > 0)
                <div class="row">
                    <!-- Online Booking Orders -->
                    @foreach($booking_online as $order)
                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                        <div class="card border-left-primary shadow-sm h-100 py-2 ripple-card" onclick="window.location='{{route('user.order.show', $order->id)}}'">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            {{$order->order_number}} <span class="badge badge-primary-soft ml-1" style="background: #e0e7ff; color: #4338ca;">Online</span>
                                        </div>
                                        <div class="h6 mb-0 font-weight-bold text-gray-800">
                                            Rs. {{number_format($order->total_amount, 2)}}
                                        </div>
                                        <div class="text-xs text-muted mt-2">
                                            <i class="fas fa-calendar-alt mr-1"></i> {{$order->created_at->format('d M, Y')}}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="badge badge-info p-2 px-3 rounded-pill text-capitalize">
                                            {{$order->status}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <!-- Admin Sales Orders -->
                    @foreach($sales_orders as $so)
                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                        <div class="card border-left-warning shadow-sm h-100 py-2 ripple-card" onclick="window.location='{{route('user.sales-order.show', $so->id)}}'">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            {{$so->order_number}} <span class="badge badge-warning-soft ml-1" style="background: #fef3c7; color: #92400e;">Booking</span>
                                        </div>
                                        <div class="h6 mb-0 font-weight-bold text-gray-800">
                                            Rs. {{number_format($so->total_amount, 2)}}
                                        </div>
                                        <div class="text-xs text-muted mt-2">
                                            <i class="fas fa-calendar-alt mr-1"></i> {{$so->created_at->format('d M, Y')}}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="badge badge-warning p-2 px-3 rounded-pill text-capitalize">
                                            {{str_replace('_', ' ', $so->status)}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <img src="https://illustrations.popsy.co/amber/waiting-list.svg" style="max-width: 150px;" class="mb-4">
                    <h6 class="text-gray-500">No booking orders yet.</h6>
                </div>
            @endif
        </div>

        <!-- Pending Orders -->
        <div class="tab-pane fade" id="pending" role="tabpanel">
            @if(count($pending_online) > 0)
                <div class="row">
                    @foreach($pending_online as $order)
                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                        <div class="card border-left-info shadow-sm h-100 py-2 ripple-card" onclick="window.location='{{route('user.order.show', $order->id)}}'">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            {{$order->order_number}}
                                        </div>
                                        <div class="h6 mb-0 font-weight-bold text-gray-800">
                                            Rs. {{number_format($order->total_amount, 2)}}
                                        </div>
                                        <div class="text-xs text-muted mt-2">
                                            <i class="fas fa-calendar-alt mr-1"></i> {{$order->created_at->format('d M, Y')}}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="badge badge-info p-2 px-3 rounded-pill text-capitalize">
                                            {{$order->status}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <img src="https://illustrations.popsy.co/amber/waiting-list.svg" style="max-width: 150px;" class="mb-4">
                    <h6 class="text-gray-500">No pending orders yet.</h6>
                </div>
            @endif
        </div>

        <!-- Delivered Orders -->
        <div class="tab-pane fade" id="delivered" role="tabpanel">
            @if(count($delivered_orders) > 0)
                <div class="row">
                    @foreach($delivered_orders as $order)
                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                        <div class="card border-left-success shadow-sm h-100 py-2 ripple-card" onclick="window.location='{{route('user.order.show', $order->id)}}'">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            {{$order->order_number}}
                                        </div>
                                        <div class="h6 mb-0 font-weight-bold text-gray-800">
                                            Rs. {{number_format($order->total_amount, 2)}}
                                        </div>
                                        <div class="text-xs text-muted mt-2">
                                            <i class="fas fa-calendar-alt mr-1"></i> {{$order->created_at->format('d M, Y')}}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="badge badge-success p-2 px-3 rounded-pill text-capitalize">
                                            {{$order->status}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    {{$delivered_orders->links()}}
                </div>
            @else
                <div class="text-center py-5">
                    <img src="https://illustrations.popsy.co/amber/delivery-service.svg" style="max-width: 150px;" class="mb-4">
                    <h6 class="text-gray-500">No delivered orders found.</h6>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .nav-pills .nav-link {
        color: #4e73df;
        transition: all 0.3s ease;
    }
    .nav-pills .nav-link.active {
        background-color: #4e73df;
        color: #fff;
        box-shadow: 0 4px 12px rgba(78, 115, 223, 0.2);
    }
    .ripple-card {
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        border-radius: 12px;
    }
    .ripple-card:active {
        transform: scale(0.98);
    }
    .ripple-card:hover {
        box-shadow: 0 8px 20px rgba(0,0,0,0.1) !important;
    }
    .badge {
        font-size: 0.7rem;
        font-weight: 800;
    }
</style>
@endpush
