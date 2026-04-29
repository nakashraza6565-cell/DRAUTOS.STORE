@extends('user.layouts.master')
@section('title','Returns & Claims || ' . (Settings::first()->title ?? 'Auto Store'))
@section('main-content')
<div class="container-fluid px-2 py-3">
    @include('user.layouts.notification')
    
    <!-- Modern Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 px-1">
        <div>
            <h5 class="font-weight-bold text-gray-800 mb-0">Returns & Claims</h5>
            <p class="text-muted small mb-0">Track your return requests</p>
        </div>
        <button class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-toggle="modal" data-target="#newReturnModal">
            <i class="fas fa-plus mr-1"></i> New
        </button>
    </div>

    <!-- Request Cards -->
    <div class="returns-list">
        @forelse($returns as $return)
            <div class="card border-0 shadow-sm rounded-20 mb-3 ripple-card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="text-xs font-weight-bold text-uppercase text-muted">ID: {{$return->return_number}}</span>
                            <div class="font-weight-bold text-gray-800">Order #{{$return->order->order_number ?? 'N/A'}}</div>
                        </div>
                        @if($return->type == 'claim')
                            <span class="badge badge-warning-soft px-3 py-1 rounded-pill" style="background: #fef3c7; color: #92400e;">Warranty Claim</span>
                        @else
                            <span class="badge badge-info-soft px-3 py-1 rounded-pill" style="background: #e0f2fe; color: #0369a1;">Product Return</span>
                        @endif
                    </div>

                    <div class="row no-gutters align-items-center mb-3">
                        <div class="col">
                            <div class="text-xs text-muted mb-1"><i class="fas fa-calendar-alt mr-1"></i> {{$return->created_at->format('M d, Y')}}</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">Rs. {{number_format($return->total_return_amount, 0)}}</div>
                        </div>
                        <div class="col-auto text-right">
                            @if($return->status == 'pending')
                                <div class="badge badge-secondary px-3 py-1 rounded-pill text-capitalize">Pending</div>
                            @elseif($return->status == 'approved')
                                <div class="badge badge-success px-3 py-1 rounded-pill text-capitalize">Approved</div>
                            @elseif($return->status == 'rejected')
                                <div class="badge badge-danger px-3 py-1 rounded-pill text-capitalize">Rejected</div>
                            @else
                                <div class="badge badge-primary px-3 py-1 rounded-pill text-capitalize">{{$return->status}}</div>
                            @endif
                        </div>
                    </div>

                    <div class="bg-light p-2 rounded-12 small text-muted">
                        <i class="fas fa-comment-dots mr-1"></i> {{Str::limit($return->reason, 60)}}
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <img src="https://illustrations.popsy.co/amber/waiting-list.svg" style="max-width: 150px;" class="mb-4">
                <h5 class="text-muted">No requests found.</h5>
                <p class="small text-muted mb-4 px-4">You can initiate a return or claim for products from your delivered orders.</p>
                <button class="btn btn-primary rounded-pill px-4" data-toggle="modal" data-target="#newReturnModal">
                    Start a Return
                </button>
            </div>
        @endforelse
    </div>

    <div class="mt-4 px-1">
        {{$returns->links()}}
    </div>
</div>

<!-- Modal to select order -->
<div class="modal fade" id="newReturnModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title font-weight-bold">Select Order</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <p class="text-muted small mb-4">Choose a delivered order to start a return or claim.</p>
                
                <div class="order-selection-list">
                    @forelse($deliveredOrders as $order)
                        <div class="card border bg-white mb-2 rounded-16 hover-shadow" style="cursor: pointer;" 
                             onclick="window.location='{{route('user.returns.create', $order->id)}}'">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="font-weight-bold text-gray-800">#{{$order->order_number}}</div>
                                        <div class="text-xs text-muted">{{$order->created_at->format('d M Y')}} • Rs. {{number_format($order->total_amount, 0)}}</div>
                                    </div>
                                    <i class="fas fa-chevron-right text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-info-circle text-gray-300 fa-3x mb-3"></i>
                            <p class="text-muted small">No delivered orders available for return.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .rounded-20 { border-radius: 20px !important; }
    .rounded-16 { border-radius: 16px !important; }
    .rounded-12 { border-radius: 12px !important; }
    .text-xs { font-size: 0.7rem; }
    .ripple-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .ripple-card:active { transform: scale(0.98); }
    .hover-shadow:hover { border-color: var(--primary) !important; background: #f8fbff !important; }
    .modal-dialog-centered { margin: 1rem; }
</style>
@endpush
