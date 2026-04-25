@extends('user.layouts.master')

@section('main-content')
<div class="container-fluid">
    @include('user.layouts.notification')
    
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Overview</h1>
        <form class="form-inline" method="GET">
            <div class="form-group mr-2">
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{request()->date_from}}" placeholder="From Date">
            </div>
            <div class="form-group mr-2">
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{request()->date_to}}" placeholder="To Date">
            </div>
            <div class="form-group mr-2">
                <select name="status" class="form-control form-control-sm">
                    <option value="">All Status</option>
                    <option value="new" {{request()->status == 'new' ? 'selected' : ''}}>New</option>
                    <option value="process" {{request()->status == 'process' ? 'selected' : ''}}>Process</option>
                    <option value="delivered" {{request()->status == 'delivered' ? 'selected' : ''}}>Delivered</option>
                    <option value="cancel" {{request()->status == 'cancel' ? 'selected' : ''}}>Cancelled</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-sm mr-1"><i class="fas fa-filter"></i> Filter</button>
            <a href="{{route('user')}}" class="btn btn-secondary btn-sm"><i class="fas fa-undo"></i> Reset</a>
        </form>
    </div>

    <!-- Content Row -->
    <div class="row">

        <!-- Total Orders Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{$stats['total_orders']}}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Orders Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{$stats['pending_orders']}}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Paid Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Paid</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{number_format($stats['total_paid'], 2)}}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Balance Pending Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Balance Pending</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{number_format($stats['total_pending'], 2)}}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Orders</h6>
                    <a href="{{route('user.order.index')}}" class="btn btn-primary btn-sm">View All Orders</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="order-dataTable" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Order No.</th>
                                    <th>Date</th>
                                    <th>Quantity</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)   
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td><strong>{{$order->order_number}}</strong></td>
                                        <td>{{$order->created_at->format('M d, Y')}}</td>
                                        <td>{{$order->quantity}}</td>
                                        <td>Rs. {{number_format($order->total_amount,2)}}</td>
                                        <td>
                                            @if($order->status=='new')
                                                <span class="badge badge-primary text-capitalize">{{$order->status}}</span>
                                            @elseif($order->status=='process')
                                                <span class="badge badge-warning text-capitalize">{{$order->status}}</span>
                                            @elseif($order->status=='delivered')
                                                <span class="badge badge-success text-capitalize">{{$order->status}}</span>
                                            @else
                                                <span class="badge badge-danger text-capitalize">{{$order->status}}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($order->payment_status == 'paid')
                                                <span class="badge badge-success">Paid</span>
                                            @else
                                                <span class="badge badge-secondary">Unpaid</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{route('user.order.show',$order->id)}}" class="btn btn-link btn-sm" title="View Details">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>  
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="fas fa-shopping-basket fa-3x text-gray-300 mb-3"></i>
                                                <h4 class="text-gray-500">You have no orders yet!</h4>
                                                <p>Start shopping to see your orders here.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
    .card { border-radius: 10px; border: none; }
    .table thead th { border-top: none; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.05rem; }
    .badge { padding: 0.5em 0.8em; border-radius: 5px; }
    .empty-state h4 { color: #858796; }
</style>
@endpush
