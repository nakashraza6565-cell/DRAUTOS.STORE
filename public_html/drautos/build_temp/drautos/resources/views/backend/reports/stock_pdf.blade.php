<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stock Report - {{ date('Y-m-d') }}</title>
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
        .summary {
            margin-bottom: 25px;
            background: #f8f9fc;
            padding: 15px;
            border-radius: 5px;
        }
        .summary h3 {
            color: #4e73df;
            margin-top: 0;
        }
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
        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #1cc88a;
            color: white;
        }
        .badge-warning {
            background-color: #f6c23e;
            color: #333;
        }
        .badge-danger {
            background-color: #e74a3b;
            color: white;
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
        <h1>📦 Inventory Status Report</h1>
        <p>Generated on: <strong>{{ date('F d, Y h:i A') }}</strong></p>
    </div>

    <div class="summary">
        <h3>Summary</h3>
        <p><strong>Total Stock Value (Purchase Cost):</strong> Rs. {{ number_format($totalStockValue, 2) }}</p>
        <p><strong>Total Products:</strong> {{ $products->count() }}</p>
        <p><strong>Low Stock Items:</strong> {{ $products->where('stock', '<', 5)->count() }}</p>
        <p><strong>Out of Stock Items:</strong> {{ $products->where('stock', '<=', 0)->count() }}</p>
    </div>

    <h3 style="color: #4e73df; border-bottom: 2px solid #4e73df; padding-bottom: 10px;">Detailed Stock Inventory</h3>
    
    <table>
        <thead>
            <tr>
                <th style="width: 30%;">Product</th>
                <th style="width: 12%;">SKU</th>
                <th style="width: 20%;">Category</th>
                <th style="width: 10%; text-align: center;">Quantity</th>
                <th style="width: 18%; text-align: right;">Value</th>
                <th style="width: 10%; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{ $product->title }}</td>
                <td>{{ $product->sku }}</td>
                <td>{{ $product->cat_info->title ?? 'N/A' }}</td>
                <td style="text-align: center; {{ $product->stock < 5 ? 'color: #e74a3b; font-weight: bold;' : '' }}">
                    {{ $product->stock }}
                </td>
                <td style="text-align: right;">Rs. {{ number_format($product->stock * ($product->purchase_price ?? 0), 2) }}</td>
                <td style="text-align: center;">
                    @if($product->stock <= 0)
                        <span class="badge badge-danger">Out</span>
                    @elseif($product->stock < 5)
                        <span class="badge badge-warning">Low</span>
                    @else
                        <span class="badge badge-success">OK</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>© {{ date('Y') }} DrAutosStore - All Rights Reserved</p>
    </div>
</body>
</html>
