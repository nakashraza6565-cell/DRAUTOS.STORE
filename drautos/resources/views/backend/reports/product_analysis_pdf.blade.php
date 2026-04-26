<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Product Analysis Report - {{ date('Y-m-d') }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4e73df;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #4e73df;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .product-info {
            background: #f8f9fc;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .product-info h2 {
            color: #4e73df;
            margin: 0 0 10px 0;
            font-size: 18px;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }
        .stat-card {
            display: table-cell;
            width: 25%;
            padding: 15px;
            text-align: center;
            background: #f8f9fc;
            border: 1px solid #e3e6f0;
        }
        .stat-card h4 {
            margin: 0 0 5px 0;
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
        }
        .stat-card p {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }
        .stat-primary { border-left: 4px solid #4e73df; }
        .stat-success { border-left: 4px solid #1cc88a; }
        .stat-info { border-left: 4px solid #36b9cc; }
        .stat-warning { border-left: 4px solid #f6c23e; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th {
            background-color: #4e73df;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
        }
        table td {
            padding: 8px;
            border-bottom: 1px solid #e3e6f0;
        }
        table tr:nth-child(even) {
            background-color: #f8f9fc;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #e3e6f0;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 Product Sales Analysis</h1>
        <p>Period: <strong>{{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</strong></p>
        <p>Generated on: <strong>{{ date('F d, Y h:i A') }}</strong></p>
    </div>

    @if($selectedProduct)
    <div class="product-info">
        <h2>{{ $selectedProduct->title }}</h2>
        <p><strong>SKU:</strong> {{ $selectedProduct->sku }}</p>
        <p><strong>Category:</strong> {{ $selectedProduct->cat_info->title ?? 'N/A' }}</p>
        <p><strong>Current Stock:</strong> {{ $selectedProduct->stock }} units</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card stat-primary">
            <h4>Total Sold</h4>
            <p>{{ $stats['quantity_sold'] }} Units</p>
        </div>
        <div class="stat-card stat-success">
            <h4>Total Revenue</h4>
            <p>Rs. {{ number_format($stats['total_revenue'], 2) }}</p>
        </div>
        <div class="stat-card stat-info">
            <h4>Total Cost</h4>
            <p>Rs. {{ number_format($stats['total_cost'], 2) }}</p>
        </div>
        <div class="stat-card stat-warning">
            <h4>Gross Profit</h4>
            <p>Rs. {{ number_format($stats['gross_profit'], 2) }}</p>
        </div>
    </div>

    <h3 style="color: #4e73df; border-bottom: 2px solid #4e73df; padding-bottom: 10px; margin-top: 30px;">Sales Transaction History</h3>
    
    <table>
        <thead>
            <tr>
                <th style="width: 25%;">Date</th>
                <th style="width: 20%;">Order #</th>
                <th style="width: 15%; text-align: center;">Quantity</th>
                <th style="width: 20%; text-align: right;">Unit Price</th>
                <th style="width: 20%; text-align: right;">Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salesHistory as $sale)
            <tr>
                <td>{{ \Carbon\Carbon::parse($sale->created_at)->format('d M Y h:i A') }}</td>
                <td>{{ $sale->order_number }}</td>
                <td style="text-align: center;">{{ $sale->quantity }}</td>
                <td style="text-align: right;">Rs. {{ number_format($sale->unit_price, 2) }}</td>
                <td style="text-align: right; font-weight: bold;">Rs. {{ number_format($sale->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #e3e6f0; font-weight: bold;">
                <td colspan="2" style="text-align: right; padding: 10px;">TOTALS:</td>
                <td style="text-align: center; padding: 10px;">{{ $stats['quantity_sold'] }}</td>
                <td style="text-align: right; padding: 10px;">-</td>
                <td style="text-align: right; padding: 10px;">Rs. {{ number_format($stats['total_revenue'], 2) }}</td>
            </tr>
        </tfoot>
    </table>
    @else
    <div style="text-align: center; padding: 50px; color: #666;">
        <p>No product selected for analysis.</p>
    </div>
    @endif

    <div class="footer">
        <p>© {{ date('Y') }} DrAutosStore - All Rights Reserved</p>
    </div>
</body>
</html>
