<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Product Analysis Report - {{ date('Y-m-d') }}</title>
    <style>
        @page { margin: 4mm; size: a5; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #4e73df;
            padding-bottom: 12px;
        }
        .header h1 {
            color: #4e73df;
            margin: 0;
            font-size: 22px;
        }
        .header p {
            margin: 4px 0;
            color: #666;
        }
        .product-info {
            background: #f8f9fc;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .product-info h2 {
            color: #4e73df;
            margin: 0 0 8px 0;
            font-size: 16px;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .stat-card {
            display: table-cell;
            width: 25%;
            padding: 12px;
            text-align: center;
            background: #f8f9fc;
            border: 1px solid #e3e6f0;
        }
        .stat-card h4 {
            margin: 0 0 4px 0;
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
        }
        .stat-card p {
            margin: 0;
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }
        .stat-card small {
            display: block;
            font-size: 8px;
            color: #888;
            margin-top: 2px;
        }
        .stat-primary { border-left: 4px solid #4e73df; }
        .stat-success { border-left: 4px solid #1cc88a; }
        .stat-info { border-left: 4px solid #36b9cc; }
        .stat-warning { border-left: 4px solid #f6c23e; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table th {
            background-color: #4e73df;
            color: white;
            padding: 8px 6px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
        }
        table td {
            padding: 6px;
            border-bottom: 1px solid #e3e6f0;
            font-size: 10px;
        }
        
        .type-sale { color: #d9534f; font-weight: bold; }
        .type-purchase { color: #5cb85c; font-weight: bold; }
        .type-return { color: #f0ad4e; font-weight: bold; }

        .row-sale { background-color: rgba(78, 115, 223, 0.02); }
        .row-purchase { background-color: rgba(28, 200, 138, 0.02); }
        .row-return { background-color: rgba(231, 74, 59, 0.02); }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #e3e6f0;
            padding-top: 8px;
        }
    </style>

@include('backend.layouts.watermark', ['type' => 'pdf'])
</head>
<body>
    <div class="header">
        <h1>📊 Product Advanced Analysis</h1>
        <p>Period: <strong>{{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</strong></p>
        <p>Generated on: <strong>{{ date('F d, Y h:i A') }}</strong></p>
    </div>

    @if($selectedProduct)
    <div class="product-info">
        <h2>{{ $selectedProduct->title }}</h2>
        <p><strong>SKU:</strong> {{ $selectedProduct->sku }} | <strong>Category:</strong> {{ $selectedProduct->cat_info->title ?? 'N/A' }} | <strong>Current Stock:</strong> {{ $selectedProduct->stock }} {{ $selectedProduct->unit }}</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card stat-primary">
            <h4>Net Sold Volume</h4>
            <p>{{ number_format($stats['net_sold']) }} {{ $selectedProduct->unit }}</p>
            <small>Return Rate: {{ number_format($stats['return_ratio'], 1) }}%</small>
        </div>
        <div class="stat-card stat-success">
            <h4>Net Revenue</h4>
            <p>Rs. {{ number_format($stats['net_revenue'], 2) }}</p>
            <small>Refunds: Rs. {{ number_format($stats['refunded_revenue'], 2) }}</small>
        </div>
        <div class="stat-card stat-info">
            <h4>Goods Received</h4>
            <p>{{ number_format($stats['purchased_qty']) }} {{ $selectedProduct->unit }}</p>
            <small>Total Cost: Rs. {{ number_format($stats['total_purchased_cost'], 2) }}</small>
        </div>
        <div class="stat-card stat-warning">
            <h4>Gross Profit</h4>
            <p>Rs. {{ number_format($stats['gross_profit'], 2) }}</p>
            @if($stats['net_revenue'] > 0)
                <small>Margin: {{ number_format(($stats['gross_profit'] / $stats['net_revenue']) * 100, 1) }}%</small>
            @endif
        </div>
    </div>

    <h3 style="color: #4e73df; border-bottom: 2px solid #4e73df; padding-bottom: 6px; margin-top: 20px; font-size: 13px;">Inventory Flow Ledger</h3>
    
    <table>
        <thead>
            <tr>
                <th style="width: 20%;">Date</th>
                <th style="width: 15%;">Event Type</th>
                <th style="width: 15%;">Reference #</th>
                <th style="width: 25%;">Party (Customer/Supplier)</th>
                <th style="width: 10%; text-align: center;">Qty</th>
                <th style="width: 15%; text-align: right;">Price/Cost</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salesHistory as $event)
            @php
                $rowClass = '';
                $typeLabel = '';
                $typeClass = '';
                $qtyPrefix = '';
                
                if ($event->type == 'sale') {
                    $rowClass = 'row-sale';
                    $typeLabel = 'Sale (Out)';
                    $typeClass = 'type-sale';
                    $qtyPrefix = '-';
                } elseif ($event->type == 'purchase') {
                    $rowClass = 'row-purchase';
                    $typeLabel = 'Incoming Goods';
                    $typeClass = 'type-purchase';
                    $qtyPrefix = '+';
                } elseif ($event->type == 'return') {
                    $rowClass = 'row-return';
                    $typeLabel = 'Sale Return';
                    $typeClass = 'type-return';
                    $qtyPrefix = '+';
                }
            @endphp
            <tr class="{{ $rowClass }}">
                <td>{{ \Carbon\Carbon::parse($event->date)->format('d M Y h:i A') }}</td>
                <td class="{{ $typeClass }}">{{ $typeLabel }}</td>
                <td>{{ $event->ref }}</td>
                <td>{{ $event->party_name }}</td>
                <td style="text-align: center; font-weight: bold;">{{ $qtyPrefix }}{{ abs($event->qty) }} {{ $selectedProduct->unit }}</td>
                <td style="text-align: right; font-weight: bold;">Rs. {{ number_format($event->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="text-align: center; padding: 50px; color: #666;">
        <p>No product selected for analysis.</p>
    </div>
    @endif

    <div class="footer">
        <p>© {{ date('Y') }} Danyal Autos Co. - All Rights Reserved</p>
    </div>
</body>
</html>
