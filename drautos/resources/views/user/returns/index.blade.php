@extends('user.layouts.master')
@section('title','Returns & Claims || ' . (Settings::first()->title ?? 'Auto Store'))
@section('main-content')
<div class="container-fluid">
    @include('user.layouts.notification')
    
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Returns & Claims</h1>
        <div>
            <button class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#newReturnModal">
                <i class="fas fa-plus fa-sm text-white-50"></i> New Return / Claim
            </button>
        </div>
    </div>
    <p class="text-muted">You can request a return or claim for products from your previous orders.</p>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Your Requests</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="returns-dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>Order No.</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($returns as $return)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td><strong>{{$return->return_number}}</strong></td>
                                <td>{{$return->order->order_number}}</td>
                                <td>
                                    @if($return->type == 'claim')
                                        <span class="badge badge-warning">Claim</span>
                                    @else
                                        <span class="badge badge-info">Return</span>
                                    @endif
                                </td>
                                <td>{{$return->created_at->format('M d, Y')}}</td>
                                <td>Rs. {{number_format($return->total_return_amount, 2)}}</td>
                                <td>
                                    @if($return->status == 'pending')
                                        <span class="badge badge-secondary">Pending</span>
                                    @elseif($return->status == 'approved')
                                        <span class="badge badge-success">Approved</span>
                                    @elseif($return->status == 'rejected')
                                        <span class="badge badge-danger">Rejected</span>
                                    @else
                                        <span class="badge badge-primary">{{$return->status}}</span>
                                    @endif
                                </td>
                                <td>{{Str::limit($return->reason, 30)}}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <h5 class="text-muted">No return or claim requests found.</h5>
                                    <p>Go to "My Orders" and select an order to initiate a return or claim.</p>
                                    <a href="{{route('user.order.index')}}" class="btn btn-primary btn-sm">View My Orders</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-3">
                    {{$returns->links()}}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal to select order -->
<div class="modal fade" id="newReturnModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Select Order for Return/Claim</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info small">
                    <i class="fas fa-info-circle mr-1"></i> Only <strong>Delivered</strong> orders are eligible for returns or warranty claims.
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover" id="delivered-orders-table">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deliveredOrders as $order)
                                <tr>
                                    <td>{{$order->order_number}}</td>
                                    <td>{{$order->created_at->format('d M Y')}}</td>
                                    <td>Rs. {{number_format($order->total_amount, 2)}}</td>
                                    <td>
                                        <a href="{{route('user.returns.create', $order->id)}}" class="btn btn-primary btn-sm">
                                            Select
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No delivered orders found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
