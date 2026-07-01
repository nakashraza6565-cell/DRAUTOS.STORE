<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Report — {{ $selectedCustomer->name }}</title>
    <style>
        @page { margin: 10mm; size: a5; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; background: #fff; }
        .header { background: linear-gradient(135deg, #4e73df, #224abe); color: #fff; padding: 18px 24px; }
        .header h1 { font-size: 20px; font-weight: 700; margin-bottom: 2px; }
        .header .sub { font-size: 11px; opacity: .8; }
        .header .right { float: right; text-align: right; }
        .header .right .company { font-size: 14px; font-weight: 700; }
        .header .right .date { font-size: 10px; opacity: .8; margin-top: 3px; }
        .clearfix::after { content: ''; display: table; clear: both; }

        /* ── Profile ── */
        .profile-box { background: #f8f9fc; border: 1px solid #e3e6f0; border-radius: 6px; padding: 14px 18px; margin: 14px 0; }
        .profile-name { font-size: 15px; font-weight: 700; color: #2c3e50; }
        .profile-meta { font-size: 10px; color: #888; margin-top: 4px; }
        .profile-meta span { margin-right: 14px; }
        .balance-badge { float: right; text-align: right; }
        .balance-badge .label { font-size: 9px; text-transform: uppercase; color: #888; }
        .balance-badge .value { font-size: 18px; font-weight: 700; color: #e74c3c; }

        /* ── KPI Grid ── */
        .kpi-grid { display: table; width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .kpi-cell { display: table-cell; width: 16.66%; padding: 8px 10px; border: 1px solid #e3e6f0; background: #fff; text-align: center; vertical-align: middle; }
        .kpi-cell .kpi-label { font-size: 8px; text-transform: uppercase; color: #888; font-weight: 700; margin-bottom: 3px; }
        .kpi-cell .kpi-value { font-size: 13px; font-weight: 700; color: #2c3e50; }
        .kpi-cell.accent-blue  { border-top: 3px solid #4e73df; }
        .kpi-cell.accent-green { border-top: 3px solid #1cc88a; }
        .kpi-cell.accent-red   { border-top: 3px solid #e74a3b; }
        .kpi-cell.accent-teal  { border-top: 3px solid #36b9cc; }
        .kpi-cell.accent-gold  { border-top: 3px solid #f6c23e; }
        .kpi-cell.accent-gray  { border-top: 3px solid #858796; }

        /* ── Section header ── */
        .section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
                         color: #4e73df; border-bottom: 2px solid #4e73df; padding-bottom: 4px; margin: 14px 0 8px; }

        /* ── Tables ── */
        table { width: 100%; border-collapse: collapse; font-size: 10px; }
        table th { background: #f0f2f8; color: #555; font-weight: 700; padding: 6px 8px; text-align: left; border: 1px solid #e3e6f0; }
        table td { padding: 5px 8px; border: 1px solid #e3e6f0; vertical-align: top; }
        table tr:nth-child(even) { background: #fafbff; }
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .text-danger { color: #e74a3b; }
        .text-success { color: #1cc88a; }
        .text-muted { color: #888; }
        .font-bold { font-weight: 700; }
        tfoot td, tfoot th { background: #f0f2f8; font-weight: 700; }

        /* ── Badges ── */
        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 9px; font-weight: 700; }
        .badge-success  { background: #d4edda; color: #155724; }
        .badge-danger   { background: #f8d7da; color: #721c24; }
        .badge-warning  { background: #fff3cd; color: #856404; }
        .badge-info     { background: #cff4fc; color: #055160; }
        .badge-secondary{ background: #e2e3e5; color: #41464b; }

        /* ── Footer ── */
        .pdf-footer { margin-top: 20px; border-top: 1px solid #e3e6f0; padding-top: 8px;
                      font-size: 9px; color: #aaa; text-align: center; }

        /* Sub-items table */
        .items-table { margin: 0; font-size: 9.5px; }
        .items-table th { background: #e8ecf4; font-size: 9px; }
    </style>
</head>
<body>

    {{-- ── HEADER ──────────────────────────────────── --}}
    <div class="header clearfix">
        <div class="right">
            <div class="company">Danyal Autos</div>
            <div class="date">Generated: {{ now()->format('d M Y, h:i A') }}</div>
        </div>
        <h1>Customer Report</h1>
        <div class="sub">Period: {{ $startDate->format('d M Y') }} — {{ $endDate->format('d M Y') }}</div>
    </div>

    {{-- ── CUSTOMER PROFILE ─────────────────────────── --}}
    <div class="profile-box clearfix">
        <div class="balance-badge">
            <div class="label">Current Balance Due</div>
            <div class="value">Rs. {{ number_format(abs($lifetimeStats['outstanding']), 0) }}</div>
            <div style="font-size:9px;color:#888;">{{ $lifetimeStats['outstanding'] > 0 ? 'Outstanding' : 'Cleared' }}</div>
        </div>
        <div class="profile-name">{{ $selectedCustomer->name }}</div>
        @if($selectedCustomer->business_name)
            <div style="font-size:11px;color:#555;">{{ $selectedCustomer->business_name }}</div>
        @endif
        <div class="profile-meta" style="margin-top:6px;">
            @if($selectedCustomer->phone)
                <span>📞 {{ $selectedCustomer->phone }}</span>
            @endif
            @if($selectedCustomer->city)
                <span>📍 {{ $selectedCustomer->city }}</span>
            @endif
            @if($selectedCustomer->customer_type)
                <span>Type: {{ ucfirst($selectedCustomer->customer_type) }}</span>
            @endif
            @if($lifetimeStats['customer_since'])
                <span>Customer since: {{ \Carbon\Carbon::parse($lifetimeStats['customer_since'])->format('M Y') }}</span>
            @endif
        </div>
    </div>

    {{-- ── LIFETIME KPIs ────────────────────────────── --}}
    <div class="section-title">Lifetime Summary (All Time)</div>
    <div class="kpi-grid">
        <div class="kpi-cell accent-blue">
            <div class="kpi-label">Total Sales</div>
            <div class="kpi-value">Rs. {{ number_format($lifetimeStats['total_sales'], 0) }}</div>
        </div>
        <div class="kpi-cell accent-green">
            <div class="kpi-label">Total Paid</div>
            <div class="kpi-value">Rs. {{ number_format($lifetimeStats['total_paid'], 0) }}</div>
        </div>
        <div class="kpi-cell accent-red">
            <div class="kpi-label">Outstanding</div>
            <div class="kpi-value" style="color:#e74a3b;">Rs. {{ number_format(abs($lifetimeStats['outstanding']), 0) }}</div>
        </div>
        <div class="kpi-cell accent-teal">
            <div class="kpi-label">Total Orders</div>
            <div class="kpi-value">{{ $lifetimeStats['orders_count'] }}</div>
        </div>
        <div class="kpi-cell accent-gold">
            <div class="kpi-label">Returns</div>
            <div class="kpi-value">Rs. {{ number_format($lifetimeStats['returns_total'], 0) }}</div>
        </div>
        <div class="kpi-cell accent-gray">
            <div class="kpi-label">Recovery Rate</div>
            @php $rr = $lifetimeStats['total_sales'] > 0 ? round(($lifetimeStats['total_paid'] / $lifetimeStats['total_sales']) * 100, 1) : 0; @endphp
            <div class="kpi-value">{{ $rr }}%</div>
        </div>
    </div>

    {{-- ── ORDER HISTORY ────────────────────────────── --}}
    <div class="section-title">Order History — {{ $startDate->format('d M Y') }} to {{ $endDate->format('d M Y') }}</div>
    @if($orders->count() > 0)
    <table>
        <thead>
            <tr>
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
                $op = $order->payment_status == 'paid' ? $order->total_amount : ($order->amount_paid ?? 0);
                $od = $order->total_amount - $op;
                $sc = ['delivered'=>'success','processing'=>'info','pending'=>'warning','new'=>'secondary','cancelled'=>'danger'][$order->status] ?? 'secondary';
            @endphp
            <tr>
                <td>{{ $order->created_at->format('d M Y') }}</td>
                <td class="font-bold">{{ $order->order_number }}</td>
                <td><span class="badge badge-{{ $sc }}">{{ ucfirst($order->status) }}</span></td>
                <td><span class="badge badge-{{ $order->payment_status == 'paid' ? 'success' : 'danger' }}">{{ ucfirst($order->payment_status ?? 'unpaid') }}</span></td>
                <td class="text-right font-bold">Rs. {{ number_format($order->total_amount, 0) }}</td>
                <td class="text-right text-success">Rs. {{ number_format($op, 0) }}</td>
                <td class="text-right {{ $od > 0 ? 'text-danger font-bold' : 'text-success' }}">Rs. {{ number_format($od, 0) }}</td>
            </tr>
            @if($order->cart_info && $order->cart_info->count() > 0)
            <tr>
                <td colspan="7" style="padding:0 0 6px 24px;background:#fafbff;">
                    <table class="items-table" style="margin:4px 0;width:96%;">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-center">Qty</th>
                                <th class="text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->cart_info as $item)
                            <tr>
                                <td>{{ $item->product->title ?? 'Item #'.$item->id }}</td>
                                <td class="text-center">{{ $item->quantity }}{{ $item->product->unit ? ' '.$item->product->unit : '' }}</td>
                                <td class="text-right">Rs. {{ number_format($item->amount, 0) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
            @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right font-bold">Period Total:</td>
                <td class="text-right font-bold">Rs. {{ number_format($orders->sum('total_amount'), 0) }}</td>
                <td class="text-right text-success font-bold">
                    Rs. {{ number_format($orders->sum(function($o){ return $o->payment_status=='paid' ? $o->total_amount : ($o->amount_paid ?? 0); }), 0) }}
                </td>
                <td class="text-right text-danger font-bold">
                    Rs. {{ number_format($orders->sum(function($o){ $p = $o->payment_status=='paid' ? $o->total_amount : ($o->amount_paid ?? 0); return $o->total_amount - $p; }), 0) }}
                </td>
            </tr>
        </tfoot>
    </table>
    @else
        <p class="text-muted" style="padding:10px 0;">No orders found in this date range.</p>
    @endif

    {{-- ── LEDGER HISTORY ───────────────────────────── --}}
    @if($ledger->count() > 0)
    <div class="section-title" style="margin-top:18px;">Payment / Ledger History (All Time)</div>
    <table>
        <thead>
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
                <td><span class="badge badge-{{ $entry->type == 'credit' ? 'success' : 'danger' }}">{{ ucfirst($entry->type) }}</span></td>
                <td class="text-muted">{{ ucfirst($entry->category) }}</td>
                <td>{{ $entry->description }}</td>
                <td class="text-right {{ $entry->type == 'credit' ? 'text-success' : 'text-danger' }} font-bold">
                    {{ $entry->type == 'credit' ? '-' : '+' }} Rs. {{ number_format($entry->amount, 0) }}
                </td>
                <td class="text-right {{ $entry->balance > 0 ? 'text-danger' : 'text-success' }} font-bold">
                    Rs. {{ number_format($entry->balance, 0) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- ── FOOTER ───────────────────────────────────── --}}
    <div class="pdf-footer">
        Danyal Autos — Customer Report for {{ $selectedCustomer->name }} — Generated {{ now()->format('d M Y h:i A') }} —
        Current Balance: Rs. {{ number_format(abs($lifetimeStats['outstanding']), 0) }}
    </div>

</body>
</html>
