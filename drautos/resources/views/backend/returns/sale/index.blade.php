@extends('backend.layouts.master')

@section('main-content')
<style>
/* ---- Search row ---- */
.sr-search-row { display:flex; gap:8px; margin-bottom:16px; }
.sr-search-row input {
    flex-grow:1; padding:10px 12px; font-size:.95rem;
    border:1.5px solid #d1d5db; border-radius:8px; outline:none;
}
.sr-search-row input:focus { border-color:#2563eb; }
.sr-search-row button, .sr-search-row a {
    padding:10px 16px; border-radius:8px; font-size:.88rem;
    font-weight:600; white-space:nowrap; cursor:pointer; border:none;
    text-decoration:none; display:inline-flex; align-items:center;
}
.btn-search  { background:#2563eb; color:#fff; }
.btn-clear-s { background:#e5e7eb; color:#374151; }

/* ---- Return cards (mobile) ---- */
.return-card {
    border:1.5px solid #e5e7eb; border-radius:10px;
    padding:14px; margin-bottom:10px; background:#fff;
    box-shadow:0 1px 3px rgba(0,0,0,.05);
}
.return-card .rc-top {
    display:flex; justify-content:space-between; align-items:flex-start; gap:8px;
}
.return-card .rc-num { font-weight:800; font-size:.97rem; color:#1d4ed8; }
.return-card .rc-cust { font-size:.88rem; color:#374151; margin-top:2px; }
.return-card .rc-meta {
    display:flex; flex-wrap:wrap; gap:6px;
    margin-top:8px; font-size:.78rem; color:#6b7280;
}
.return-card .rc-meta span { background:#f3f4f6; border-radius:5px; padding:2px 7px; }
.return-card .rc-bottom {
    display:flex; justify-content:space-between; align-items:center;
    margin-top:10px; gap:8px;
}
.return-card .rc-amount { font-weight:800; font-size:1rem; color:#15803d; }
.return-card .rc-actions { display:flex; gap:6px; }
.btn-view {
    padding:8px 14px; background:#0ea5e9; color:#fff;
    border:none; border-radius:7px; font-size:.85rem;
    font-weight:600; cursor:pointer; text-decoration:none;
    display:inline-flex; align-items:center; gap:5px;
}
.btn-approve {
    padding:8px 14px; background:#16a34a; color:#fff;
    border:none; border-radius:7px; font-size:.85rem;
    font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:5px;
}

/* Status badges */
.badge-pending  { background:#fef3c7; color:#92400e; border-radius:5px; padding:3px 8px; font-size:.75rem; font-weight:700; }
.badge-approved { background:#dcfce7; color:#15803d; border-radius:5px; padding:3px 8px; font-size:.75rem; font-weight:700; }
.badge-rejected { background:#fee2e2; color:#b91c1c; border-radius:5px; padding:3px 8px; font-size:.75rem; font-weight:700; }
.badge-completed{ background:#e0f2fe; color:#0369a1; border-radius:5px; padding:3px 8px; font-size:.75rem; font-weight:700; }

/* Hide table on mobile, show cards */
@media (max-width: 767px) {
    .desktop-table { display:none !important; }
    .mobile-cards  { display:block !important; }
}
@media (min-width: 768px) {
    .desktop-table { display:block !important; }
    .mobile-cards  { display:none !important; }
}
</style>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary float-left">Sale Returns</h6>
        <a href="{{ route('returns.sale.create-smart') }}" class="btn btn-primary btn-sm float-right">
            <i class="fas fa-plus mr-1"></i> New Return
        </a>
    </div>

    <div class="card-body">
        {{-- Search --}}
        <form action="{{ route('returns.sale.index') }}" method="GET">
            <div class="sr-search-row">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search customer, order #, return #...">
                <button type="submit" class="btn-search"><i class="fas fa-search"></i></button>
                @if(request('search'))
                    <a href="{{ route('returns.sale.index') }}" class="btn-clear-s">Clear</a>
                @endif
            </div>
        </form>

        @if(count($returns ?? []) > 0)

        {{-- ===== DESKTOP TABLE ===== --}}
        <div class="desktop-table table-responsive">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Return #</th>
                        <th>Date</th>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($returns as $return)
                    <tr>
                        <td><strong>{{ $return->return_number }}</strong></td>
                        <td>{{ $return->return_date->format('d M Y') }}</td>
                        <td>{{ $return->order->order_number ?? '—' }}</td>
                        <td>{{ $return->customer->name ?? 'N/A' }}</td>
                        <td>PKR {{ number_format($return->total_return_amount, 2) }}</td>
                        <td>
                            @if($return->status == 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @elseif($return->status == 'approved')
                                <span class="badge badge-success">Approved</span>
                            @elseif($return->status == 'rejected')
                                <span class="badge badge-danger">Rejected</span>
                            @else
                                <span class="badge badge-info">Completed</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('returns.sale.show', $return->id) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                            @if($return->status == 'pending')
                            <form method="POST" action="{{ route('returns.sale.approve', $return->id) }}" style="display:inline;">
                                @csrf
                                <button class="btn btn-success btn-sm" title="Approve"><i class="fas fa-check"></i></button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ===== MOBILE CARDS ===== --}}
        <div class="mobile-cards" style="display:none;">
            @foreach($returns as $return)
            <div class="return-card">
                <div class="rc-top">
                    <div>
                        <div class="rc-num">{{ $return->return_number }}</div>
                        <div class="rc-cust">{{ $return->customer->name ?? 'N/A' }}</div>
                    </div>
                    @if($return->status == 'pending')
                        <span class="badge-pending">Pending</span>
                    @elseif($return->status == 'approved')
                        <span class="badge-approved">Approved</span>
                    @elseif($return->status == 'rejected')
                        <span class="badge-rejected">Rejected</span>
                    @else
                        <span class="badge-completed">Completed</span>
                    @endif
                </div>
                <div class="rc-meta">
                    <span>📅 {{ $return->return_date->format('d M Y') }}</span>
                    @if($return->order)
                        <span>🧾 {{ $return->order->order_number }}</span>
                    @else
                        <span>🧾 Multi-order</span>
                    @endif
                </div>
                <div class="rc-bottom">
                    <span class="rc-amount">PKR {{ number_format($return->total_return_amount, 2) }}</span>
                    <div class="rc-actions">
                        <a href="{{ route('returns.sale.show', $return->id) }}" class="btn-view">
                            <i class="fas fa-eye"></i> View
                        </a>
                        @if($return->status == 'pending')
                        <form method="POST" action="{{ route('returns.sale.approve', $return->id) }}">
                            @csrf
                            <button type="submit" class="btn-approve">
                                <i class="fas fa-check"></i> Approve
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @else
            <div class="text-center py-5 text-muted">
                <i class="fas fa-undo fa-2x mb-3 d-block" style="color:#d1d5db;"></i>
                No sale returns found.
                @if(request('search'))
                    <br><a href="{{ route('returns.sale.index') }}">Clear search</a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
