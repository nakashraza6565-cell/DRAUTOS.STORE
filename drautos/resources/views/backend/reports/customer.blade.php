@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid" id="customerReportPage">

    {{-- ════════════════════════════════════════════════════════ --}}
    {{-- PAGE HEADER                                              --}}
    {{-- ════════════════════════════════════════════════════════ --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Customer Report</h1>
            <p class="text-muted small mb-0">Full account statement, order & payment history</p>
        </div>
        @if($selectedCustomer)
        <div class="d-flex gap-2 mt-2 mt-sm-0">
            <button onclick="window.print()" class="btn btn-sm btn-outline-secondary shadow-sm">
                <i class="fas fa-print fa-sm mr-1"></i> Print
            </button>
            <a href="{{ route('reports.customer.pdf', array_merge(request()->all(), ['customer_id' => $selectedCustomer->id])) }}"
               class="btn btn-sm btn-success shadow-sm">
                <i class="fas fa-file-pdf fa-sm mr-1"></i> Download PDF
            </a>
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $selectedCustomer->phone) }}?text={{ urlencode('Hello '.$selectedCustomer->name.', please find your account statement from Danyal Autos.') }}"
               target="_blank" class="btn btn-sm btn-success shadow-sm" style="background:#25D366;border-color:#25D366;">
                <i class="fab fa-whatsapp fa-sm mr-1"></i> WhatsApp
            </a>
        </div>
        @endif
    </div>

    {{-- ════════════════════════════════════════════════════════ --}}
    {{-- CUSTOMER FILTER FORM                                     --}}
    {{-- ════════════════════════════════════════════════════════ --}}
    <div class="card shadow mb-4">
        <div class="card-body py-3">
            <form action="{{ route('reports.customer') }}" method="GET" id="customerFilterForm">
                <div class="form-row align-items-end">
                    <div class="col-md-5 mb-2">
                        <label class="small font-weight-bold text-gray-700" for="customer_id">Select Customer</label>
                        <select name="customer_id" id="customer_id" class="form-control form-control-sm select2-customer" required>
                            <option value="">-- Select Customer --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}"
                                    {{ $selectedCustomer && $selectedCustomer->id == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                    ({{ $customer->phone ?? $customer->email ?? 'N/A' }})
                                    @if($customer->current_balance > 0)
                                        | Due: Rs.{{ number_format($customer->current_balance,0) }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="small font-weight-bold text-gray-700" for="start_date">From</label>
                        <input type="date" name="start_date" id="start_date" class="form-control form-control-sm"
                               value="{{ $startDate->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="small font-weight-bold text-gray-700" for="end_date">To</label>
                        <input type="date" name="end_date" id="end_date" class="form-control form-control-sm"
                               value="{{ $endDate->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-3 mb-2 d-flex" style="gap:6px;">
                        <button type="submit" class="btn btn-primary btn-sm flex-fill">
                            <i class="fas fa-search mr-1"></i> Generate
                        </button>
                        <a href="{{ route('reports.customer') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($selectedCustomer)

    {{-- ════════════════════════════════════════════════════════ --}}
    {{-- CUSTOMER PROFILE CARD                                    --}}
    {{-- ════════════════════════════════════════════════════════ --}}
    <div class="card shadow mb-4 border-left-primary">
        <div class="card-body py-3">
            <div class="row align-items-center">
                {{-- Avatar / initials --}}
                <div class="col-auto">
                    <div class="customer-avatar">
                        {{ strtoupper(substr($selectedCustomer->name, 0, 1)) }}
                    </div>
                </div>
                {{-- Info --}}
                <div class="col">
                    <div class="h5 font-weight-bold mb-0 text-gray-800">
                        {{ $selectedCustomer->name }}
                        @if($selectedCustomer->business_name)
                            <small class="text-muted font-weight-normal"> — {{ $selectedCustomer->business_name }}</small>
                        @endif
                    </div>
                    <div class="text-xs text-muted mt-1">
                        @if($selectedCustomer->phone)
                            <span class="mr-3"><i class="fas fa-phone fa-xs mr-1"></i>{{ $selectedCustomer->phone }}</span>
                        @endif
                        @if($selectedCustomer->city)
                            <span class="mr-3"><i class="fas fa-map-marker-alt fa-xs mr-1"></i>{{ $selectedCustomer->city }}</span>
                        @endif
                        @if($selectedCustomer->customer_type)
                            <span class="badge badge-info" style="font-size:10px;text-transform:capitalize;">{{ $selectedCustomer->customer_type }}</span>
                        @endif
                        @if($lifetimeStats['customer_since'])
                            <span class="ml-2"><i class="fas fa-calendar-check fa-xs mr-1 text-success"></i>
                                Customer since {{ \Carbon\Carbon::parse($lifetimeStats['customer_since'])->format('M Y') }}
                            </span>
                        @endif
                    </div>
                </div>
                {{-- Balance badge --}}
                <div class="col-auto text-right">
                    <div class="small font-weight-bold text-muted text-uppercase mb-1">Current Balance Due</div>
                    <div class="h4 font-weight-extrabold mb-0 {{ $lifetimeStats['outstanding'] > 0 ? 'text-danger' : 'text-success' }}">
                        Rs. {{ number_format($lifetimeStats['outstanding'], 0) }}
                    </div>
                    <div class="text-xs text-muted">{{ $lifetimeStats['outstanding'] > 0 ? 'Outstanding' : 'Cleared' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════ --}}
    {{-- KPI ROW 1 — LIFETIME                                     --}}
    {{-- ════════════════════════════════════════════════════════ --}}
    <div class="row mb-1">
        <div class="col-12 mb-2">
            <span class="badge badge-dark px-3 py-1" style="font-size:11px;letter-spacing:1px;">
                <i class="fas fa-infinity mr-1"></i> LIFETIME (ALL TIME)
            </span>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body py-2 px-3">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Sales</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{ number_format($lifetimeStats['total_sales'], 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body py-2 px-3">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Paid</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{ number_format($lifetimeStats['total_paid'], 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body py-2 px-3">
                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Outstanding</div>
                    <div class="h5 mb-0 font-weight-bold {{ $lifetimeStats['outstanding'] > 0 ? 'text-danger' : 'text-success' }}">
                        Rs. {{ number_format(abs($lifetimeStats['outstanding']), 0) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body py-2 px-3">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Orders</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $lifetimeStats['orders_count'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body py-2 px-3">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Returns Value</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{ number_format($lifetimeStats['returns_total'], 0) }}</div>
                </div>
            </div>
        </div>
        @php
            $recoveryRate = $lifetimeStats['total_sales'] > 0
                ? round(($lifetimeStats['total_paid'] / $lifetimeStats['total_sales']) * 100, 1)
                : 0;
        @endphp
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body py-2 px-3">
                    <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Recovery Rate</div>
                    <div class="h5 mb-0 font-weight-bold {{ $recoveryRate >= 80 ? 'text-success' : ($recoveryRate >= 50 ? 'text-warning' : 'text-danger') }}">
                        {{ $recoveryRate }}%
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════ --}}
    {{-- KPI ROW 2 — PERIOD                                       --}}
    {{-- ════════════════════════════════════════════════════════ --}}
    <div class="row mb-1">
        <div class="col-12 mb-2">
            <span class="badge badge-primary px-3 py-1" style="font-size:11px;letter-spacing:1px;">
                <i class="fas fa-calendar-alt mr-1"></i>
                PERIOD: {{ $startDate->format('d M Y') }} — {{ $endDate->format('d M Y') }}
            </span>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 col-6 mb-3">
            <div class="card shadow h-100 py-2" style="border-left:4px solid #4e73df;">
                <div class="card-body py-2 px-3">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Sales (Period)</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{ number_format($periodStats['total_sales'], 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-6 mb-3">
            <div class="card shadow h-100 py-2" style="border-left:4px solid #1cc88a;">
                <div class="card-body py-2 px-3">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Paid (Period)</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{ number_format($periodStats['total_paid'], 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-6 mb-3">
            <div class="card shadow h-100 py-2" style="border-left:4px solid #36b9cc;">
                <div class="card-body py-2 px-3">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Orders (Period)</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $periodStats['orders_count'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-6 mb-3">
            <div class="card shadow h-100 py-2" style="border-left:4px solid #f6c23e;">
                <div class="card-body py-2 px-3">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Avg Order Value</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{ number_format($periodStats['avg_order'], 0) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════ --}}
    {{-- ORDER HISTORY TABLE (with expandable rows)               --}}
    {{-- ════════════════════════════════════════════════════════ --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center justify-content-between bg-white">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-shopping-bag mr-2"></i>Order History — {{ $startDate->format('d M Y') }} to {{ $endDate->format('d M Y') }}
            </h6>
            <span class="badge badge-primary">{{ $orders->count() }} orders</span>
        </div>
        <div class="card-body p-0">
            @if($orders->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0" id="orderHistoryTable">
                    <thead class="thead-light">
                        <tr>
                            <th style="width:30px;"></th>
                            <th>Date</th>
                            <th>Order #</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th class="text-right">Total</th>
                            <th class="text-right">Paid</th>
                            <th class="text-right">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        @php
                            $orderPaid  = $order->payment_status == 'paid' ? $order->total_amount : ($order->amount_paid ?? 0);
                            $orderDue   = $order->total_amount - $orderPaid;
                            $statusColors = [
                                'delivered'  => 'success',
                                'processing' => 'info',
                                'pending'    => 'warning',
                                'new'        => 'secondary',
                                'cancelled'  => 'danger',
                            ];
                            $sc = $statusColors[$order->status] ?? 'secondary';
                        @endphp
                        {{-- MAIN ROW --}}
                        <tr class="order-main-row {{ $order->status == 'cancelled' ? 'text-muted' : '' }}"
                            data-order-id="{{ $order->id }}"
                            style="cursor:pointer;">
                            <td class="text-center expand-toggle">
                                <i class="fas fa-chevron-right text-muted" style="font-size:10px;transition:transform .2s;"></i>
                            </td>
                            <td>{{ $order->created_at->format('d M Y') }}</td>
                            <td>
                                <span class="font-weight-bold">{{ $order->order_number }}</span>
                                @if($order->staff)
                                    <br><small class="text-muted">by {{ $order->staff->name }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $sc }}">{{ ucfirst($order->status) }}</span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $order->payment_status == 'paid' ? 'success' : 'danger' }}">
                                    {{ ucfirst($order->payment_status ?? 'unpaid') }}
                                </span>
                            </td>
                            <td class="text-right font-weight-bold">Rs. {{ number_format($order->total_amount, 0) }}</td>
                            <td class="text-right text-success">Rs. {{ number_format($orderPaid, 0) }}</td>
                            <td class="text-right {{ $orderDue > 0 ? 'text-danger font-weight-bold' : 'text-success' }}">
                                Rs. {{ number_format($orderDue, 0) }}
                            </td>
                        </tr>
                        {{-- EXPANDED ITEMS ROW --}}
                        <tr class="order-items-row d-none" id="items-{{ $order->id }}">
                            <td colspan="8" class="p-0">
                                <div class="bg-light px-4 py-3 border-top">
                                    @if($order->cart_info && $order->cart_info->count() > 0)
                                    <table class="table table-sm mb-0" style="font-size:12px;">
                                        <thead>
                                            <tr class="text-muted">
                                                <th>Product</th>
                                                <th class="text-center">Qty</th>
                                                <th class="text-right">Unit Price</th>
                                                <th class="text-right">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($order->cart_info as $item)
                                            <tr>
                                                <td>
                                                    @if($item->product)
                                                        {{ $item->product->title }}
                                                        @if($item->product->sku)
                                                            <span class="text-muted">({{ $item->product->sku }})</span>
                                                        @endif
                                                    @else
                                                        {{ $item->product_id ? 'Product #'.$item->product_id : 'Bundle/Other' }}
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $item->quantity }}
                                                    @if($item->product && $item->product->unit)
                                                        <small class="text-muted">{{ $item->product->unit }}</small>
                                                    @endif
                                                </td>
                                                <td class="text-right">Rs. {{ $item->quantity > 0 ? number_format($item->amount / $item->quantity, 0) : 0 }}</td>
                                                <td class="text-right font-weight-bold">Rs. {{ number_format($item->amount, 0) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="font-weight-bold">
                                                <td colspan="3" class="text-right">Order Total:</td>
                                                <td class="text-right">Rs. {{ number_format($order->total_amount, 0) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    @else
                                        <p class="text-muted small mb-0"><i class="fas fa-info-circle mr-1"></i>No item details available.</p>
                                    @endif
                                    @if($order->pending_items_note)
                                        <div class="alert alert-warning py-1 px-2 mt-2 mb-0 small">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>{{ $order->pending_items_note }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="thead-light">
                        <tr>
                            <th colspan="5" class="text-right">Period Total:</th>
                            <th class="text-right">Rs. {{ number_format($periodStats['total_sales'], 0) }}</th>
                            <th class="text-right text-success">Rs. {{ number_format($periodStats['total_paid'], 0) }}</th>
                            <th class="text-right text-danger">Rs. {{ number_format($periodStats['total_sales'] - $periodStats['total_paid'], 0) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @else
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                    No orders found in the selected date range.
                </div>
            @endif
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════ --}}
    {{-- LEDGER HISTORY (collapsible)                             --}}
    {{-- ════════════════════════════════════════════════════════ --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center justify-content-between bg-white"
             data-toggle="collapse" data-target="#ledgerSection" style="cursor:pointer;">
            <h6 class="m-0 font-weight-bold text-success">
                <i class="fas fa-book mr-2"></i>Payment / Ledger History (All Time)
            </h6>
            <div class="d-flex align-items-center">
                <span class="badge badge-success mr-2">{{ $ledger->count() }} entries</span>
                <i class="fas fa-chevron-down text-muted" style="font-size:12px;"></i>
            </div>
        </div>
        <div class="collapse" id="ledgerSection">
            <div class="card-body p-0">
                @if($ledger->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th class="text-right">Amount</th>
                                <th class="text-right">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ledger as $entry)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($entry->transaction_date)->format('d M Y') }}</td>
                                <td>
                                    <span class="badge badge-{{ $entry->type == 'credit' ? 'success' : 'danger' }}">
                                        {{ ucfirst($entry->type) }}
                                    </span>
                                </td>
                                <td><span class="text-capitalize text-muted small">{{ $entry->category }}</span></td>
                                <td class="small">{{ $entry->description }}</td>
                                <td class="text-right {{ $entry->type == 'credit' ? 'text-success' : 'text-danger' }} font-weight-bold">
                                    {{ $entry->type == 'credit' ? '-' : '+' }} Rs. {{ number_format($entry->amount, 0) }}
                                </td>
                                <td class="text-right font-weight-bold {{ $entry->balance > 0 ? 'text-danger' : 'text-success' }}">
                                    Rs. {{ number_format($entry->balance, 0) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                    <div class="text-center py-3 text-muted">
                        <i class="fas fa-inbox mr-1"></i> No ledger entries found.
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════ --}}
    {{-- RETURNS HISTORY (collapsible)                            --}}
    {{-- ════════════════════════════════════════════════════════ --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center justify-content-between bg-white"
             data-toggle="collapse" data-target="#returnsSection" style="cursor:pointer;">
            <h6 class="m-0 font-weight-bold text-warning">
                <i class="fas fa-undo-alt mr-2"></i>Returns History (All Time)
            </h6>
            <div class="d-flex align-items-center">
                <span class="badge badge-warning mr-2">{{ $returns->count() }} returns</span>
                <i class="fas fa-chevron-down text-muted" style="font-size:12px;"></i>
            </div>
        </div>
        <div class="collapse" id="returnsSection">
            <div class="card-body p-0">
                @if($returns->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Date</th>
                                <th>Return #</th>
                                <th>Items</th>
                                <th>Reason</th>
                                <th>Refund Method</th>
                                <th class="text-right">Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($returns as $ret)
                            <tr>
                                <td>{{ $ret->return_date->format('d M Y') }}</td>
                                <td class="font-weight-bold">{{ $ret->return_number }}</td>
                                <td>
                                    @foreach($ret->items as $ri)
                                        <div class="small">{{ $ri->product->title ?? 'Item #'.$ri->id }}
                                            <span class="text-muted">×{{ $ri->quantity }}</span>
                                        </div>
                                    @endforeach
                                </td>
                                <td class="small text-muted">{{ $ret->reason ?? '—' }}</td>
                                <td class="small">{{ $ret->refund_method ?? '—' }}</td>
                                <td class="text-right font-weight-bold text-warning">Rs. {{ number_format($ret->total_return_amount, 0) }}</td>
                                <td>
                                    <span class="badge badge-{{ $ret->status == 'completed' ? 'success' : 'warning' }}">
                                        {{ ucfirst($ret->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="thead-light">
                            <tr>
                                <th colspan="5" class="text-right">Total Returned:</th>
                                <th class="text-right text-warning">Rs. {{ number_format($lifetimeStats['returns_total'], 0) }}</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                    <div class="text-center py-3 text-muted">
                        <i class="fas fa-check-circle mr-1 text-success"></i> No returns on record for this customer.
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════ --}}
    {{-- TOP PRODUCTS (collapsible)                               --}}
    {{-- ════════════════════════════════════════════════════════ --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center justify-content-between bg-white"
             data-toggle="collapse" data-target="#topProductsSection" style="cursor:pointer;">
            <h6 class="m-0 font-weight-bold text-info">
                <i class="fas fa-star mr-2"></i>Top Products Purchased (All Time)
            </h6>
            <div class="d-flex align-items-center">
                <span class="badge badge-info mr-2">Top {{ $topProducts->count() }}</span>
                <i class="fas fa-chevron-down text-muted" style="font-size:12px;"></i>
            </div>
        </div>
        <div class="collapse" id="topProductsSection">
            <div class="card-body p-0">
                @if($topProducts->count() > 0)
                @php $maxVal = $topProducts->max('total_value'); @endphp
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th class="text-center">Orders</th>
                                <th class="text-center">Total Qty</th>
                                <th class="text-right">Total Value</th>
                                <th style="width:120px;">Share</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topProducts as $i => $tp)
                            @php $barPct = $maxVal > 0 ? round(($tp->total_value / $maxVal) * 100) : 0; @endphp
                            <tr>
                                <td class="font-weight-bold text-muted">{{ $i + 1 }}</td>
                                <td class="font-weight-bold">{{ $tp->title }}</td>
                                <td class="text-center">{{ $tp->times_ordered }}</td>
                                <td class="text-center">{{ number_format($tp->total_qty, 0) }}
                                    @if($tp->unit)<small class="text-muted">{{ $tp->unit }}</small>@endif
                                </td>
                                <td class="text-right font-weight-bold">Rs. {{ number_format($tp->total_value, 0) }}</td>
                                <td>
                                    <div class="progress" style="height:8px;">
                                        <div class="progress-bar bg-info" role="progressbar"
                                             style="width:{{ $barPct }}%"></div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                    <div class="text-center py-3 text-muted">
                        <i class="fas fa-inbox mr-1"></i> No product purchase data available.
                    </div>
                @endif
            </div>
        </div>
    </div>

    @else
    {{-- No customer selected yet --}}
    <div class="card shadow mb-4">
        <div class="card-body text-center py-5">
            <i class="fas fa-user-circle fa-4x text-gray-300 mb-3 d-block"></i>
            <h5 class="text-gray-500">Select a customer above to generate their full report.</h5>
            <p class="text-muted small">Shows lifetime & period stats, order history, payments, returns, and top products.</p>
        </div>
    </div>
    @endif

</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
<style>
    /* ── Customer Avatar ─────────────────── */
    .customer-avatar {
        width: 56px; height: 56px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4e73df, #36b9cc);
        color: #fff;
        font-size: 24px;
        font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 2px 8px rgba(78,115,223,.3);
    }
    /* ── Expand row ──────────────────────── */
    .order-main-row:hover { background: #f8f9fc !important; }
    .order-main-row.expanded .expand-toggle i { transform: rotate(90deg); }
    .order-items-row td { border-top: none !important; }
    /* ── Collapsible chevron rotate ──────── */
    [data-toggle="collapse"] .fa-chevron-down { transition: transform .2s; }
    [data-toggle="collapse"][aria-expanded="true"] .fa-chevron-down { transform: rotate(180deg); }
    /* ── Gap utility (BS4 doesn't have it) ── */
    .gap-2 { gap: .5rem; }
    /* ══════════════════════════════════════ */
    /* PRINT STYLES                           */
    /* ══════════════════════════════════════ */
    @media print {
        .no-print, .navbar-nav, .sticky-footer, form, .btn,
        #sidebarToggle, #sidebarToggleTop, .sidebar,
        .topbar, .d-sm-flex.align-items-center.justify-content-between .d-flex { display: none !important; }
        .content-wrapper, #content-wrapper { margin-left: 0 !important; }
        .card { box-shadow: none !important; border: 1px solid #dee2e6 !important; page-break-inside: avoid; }
        .collapse { display: block !important; }
        body { background: #fff !important; }
        .container-fluid { padding: 0 !important; }
        a[href]:after { content: '' !important; }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function () {
    // ── Select2 ─────────────────────────────────────────────────
    $('.select2-customer').select2({
        theme: 'bootstrap4',
        placeholder: 'Search customer by name or phone...',
        allowClear: true,
        width: '100%'
    });

    // ── Expandable order rows ────────────────────────────────────
    $(document).on('click', '.order-main-row', function () {
        var orderId  = $(this).data('order-id');
        var itemsRow = $('#items-' + orderId);
        var icon     = $(this).find('.expand-toggle i');

        if (itemsRow.hasClass('d-none')) {
            itemsRow.removeClass('d-none');
            $(this).addClass('expanded');
            icon.css('transform', 'rotate(90deg)');
        } else {
            itemsRow.addClass('d-none');
            $(this).removeClass('expanded');
            icon.css('transform', 'rotate(0deg)');
        }
    });

    // ── Collapsible section chevrons ─────────────────────────────
    $('#ledgerSection, #returnsSection, #topProductsSection').on('show.bs.collapse', function () {
        $(this).prev('.card-header').find('.fa-chevron-down').css('transform', 'rotate(180deg)');
    }).on('hide.bs.collapse', function () {
        $(this).prev('.card-header').find('.fa-chevron-down').css('transform', 'rotate(0deg)');
    });
});
</script>
@endpush
