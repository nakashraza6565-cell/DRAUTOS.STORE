@extends('user.layouts.master')

@section('main-content')
<div class="container-fluid pb-5">
    @include('user.layouts.notification')
    
    <!-- Personalized Welcome Section -->
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h4 class="font-weight-800 mb-1" style="color:var(--primary);">Hello, {{Auth::user()->name}}!</h4>
            <p class="text-muted small mb-0">Welcome back to your dashboard.</p>
        </div>
        <div class="d-md-none">
             <a href="{{route('user.setting')}}" class="text-decoration-none">
                <img src="{{Auth::user()->photo ? Auth::user()->photo : asset('backend/img/avatar.png')}}" 
                     class="rounded-circle border shadow-sm" style="width:45px; height:45px; object-fit:cover;">
             </a>
        </div>
    </div>

    <!-- Quick Actions Section -->
    <div class="mb-4">
        <h6 class="font-weight-700 text-uppercase small text-muted mb-3">Quick Actions</h6>
        <div class="row no-gutters" style="gap:12px;">
            <div class="col">
                <a href="{{route('user.online-order')}}" class="card h-100 text-center text-decoration-none py-3 border-0 shadow-sm">
                    <i class="fas fa-plus-circle text-primary mb-2 fa-lg"></i>
                    <span class="small font-weight-700 text-primary">New Order</span>
                </a>
            </div>
            <div class="col">
                <a href="{{route('user.order.index')}}" class="card h-100 text-center text-decoration-none py-3 border-0 shadow-sm">
                    <i class="fas fa-list-ul text-warning mb-2 fa-lg"></i>
                    <span class="small font-weight-700 text-warning">History</span>
                </a>
            </div>
            <div class="col">
                <a href="{{route('user.setting')}}" class="card h-100 text-center text-decoration-none py-3 border-0 shadow-sm">
                    <i class="fas fa-cog text-info mb-2 fa-lg"></i>
                    <span class="small font-weight-700 text-info">Settings</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Highlight Cards Row -->
    <div class="row">
        <!-- Balance Pending Card -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 border-0" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius: 20px; overflow: hidden;">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center mb-4 cursor-pointer" onclick="window.location.href='{{ route('user.ledger') }}'">
                        <div class="stat-icon bg-white text-primary mr-3">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div>
                            <div class="stat-label text-white-50 uppercase small font-weight-700" style="letter-spacing: 0.5px;">Outstanding Balance</div>
                            <div class="stat-value text-white h3 font-weight-900 mb-0">Rs. {{number_format($stats['total_pending'], 2)}}</div>
                        </div>
                        <i class="fas fa-chevron-right ml-auto text-white-50 small"></i>
                    </div>

                    <!-- Mini Ledger Preview -->
                    <div class="ledger-preview mt-3 pt-3 border-top border-secondary">
                        <h6 class="extra-small text-white-50 font-weight-700 text-uppercase mb-2" style="letter-spacing: 1px;">Recent Transactions</h6>
                        <div class="transaction-list custom-scrollbar" style="max-height: 200px; overflow-y: auto;">
                            @forelse($recent_ledger as $item)
                                <div class="d-flex justify-content-between align-items-center mb-2 p-2 rounded" style="background: rgba(255,255,255,0.05);">
                                    <div style="flex: 1; overflow: hidden;">
                                        <div class="extra-small font-weight-600 text-white truncate-text">{{ $item->description }}</div>
                                        <div class="extra-small text-white-50">{{ date('d M', strtotime($item->transaction_date)) }}</div>
                                    </div>
                                    <div class="text-right ml-2">
                                        <div class="extra-small font-weight-800 {{ $item->type == 'debit' ? 'text-danger' : 'text-success' }}">
                                            {{ $item->type == 'debit' ? '-' : '+' }} {{ number_format($item->amount, 0) }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-2 text-white-50 extra-small">No recent activity</div>
                            @endforelse
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('user.ledger.pdf') }}" class="btn btn-primary btn-sm rounded-pill px-3 font-weight-700" style="font-size: 10px; background: #3b82f6; border: none; box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);">
                                <i class="fas fa-file-pdf mr-1"></i> DOWNLOAD PDF STATEMENT
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Status Card -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body stat-card">
                    <div class="stat-icon bg-light text-warning">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <div>
                        <div class="stat-label">In-Process Orders</div>
                        <div class="stat-value">{{$stats['pending_orders']}} <small class="font-weight-normal text-muted">/ {{$stats['total_orders']}} total</small></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders List -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="font-weight-700 text-uppercase small text-muted mb-0">Recent Orders</h6>
                <a href="{{route('user.order.index')}}" class="small font-weight-700 text-accent">View All</a>
            </div>

            @forelse($orders as $order)
                <div class="mobile-order-card shadow-sm border-0 mb-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div class="font-weight-700 text-primary mb-0">{{$order->order_number}}</div>
                            <div class="text-muted" style="font-size:0.75rem;">{{$order->created_at->format('d M, Y | h:i A')}}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-weight-800 text-primary">Rs. {{number_format($order->total_amount, 2)}}</div>
                            @if($order->status=='new')
                                <span class="badge badge-primary-soft text-capitalize px-2" style="font-size:0.6rem; background:#ebf5ff; color:#3b82f6;">{{$order->status}}</span>
                            @elseif($order->status=='process')
                                <span class="badge badge-warning-soft text-capitalize px-2" style="font-size:0.6rem; background:#fff7ed; color:#f59e0b;">{{$order->status}}</span>
                            @elseif($order->status=='delivered')
                                <span class="badge badge-success-soft text-capitalize px-2" style="font-size:0.6rem; background:#f0fdf4; color:#10b981;">{{$order->status}}</span>
                            @else
                                <span class="badge badge-danger-soft text-capitalize px-2" style="font-size:0.6rem; background:#fef2f2; color:#ef4444;">{{$order->status}}</span>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mt-3 pt-2 border-top border-light">
                        <span class="text-muted small"><i class="fas fa-boxes mr-1"></i> {{$order->quantity}} Items</span>
                        <a href="{{route('user.order.show', $order->id)}}" class="btn btn-sm btn-light font-weight-700 px-3 rounded-pill" style="font-size:0.75rem;">
                            View Details <i class="fas fa-chevron-right ml-1 small"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="card border-0 shadow-sm text-center py-5">
                    <div class="card-body">
                        <i class="fas fa-shopping-basket fa-3x text-light mb-3"></i>
                        <h6 class="text-muted">No orders found!</h6>
                        <a href="{{route('home')}}" class="btn btn-primary btn-sm rounded-pill px-4 mt-2">Start Shopping</a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
    .font-weight-800 { font-weight: 800; }
    .font-weight-700 { font-weight: 700; }
    .text-primary { color: var(--primary) !important; }
    .text-accent { color: var(--accent) !important; }
    
    /* Animations */
    .mobile-order-card {
        transition: transform 0.2s ease;
    }
    .mobile-order-card:active {
        transform: scale(0.98);
    }
    .truncate-text {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
    }
    .hover-lift { transition: transform 0.2s, box-shadow 0.2s; }
    .hover-lift:active { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.2) !important; }
    
    /* Scrollbar for mini list */
    .transaction-list::-webkit-scrollbar { width: 3px; }
    .transaction-list::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
</style>
@endpush

