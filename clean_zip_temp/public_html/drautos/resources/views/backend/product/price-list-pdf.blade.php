<!DOCTYPE html>
<html>
<head>
    <title>Price List - {{ date('d M, Y') }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #4e73df;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #4e73df;
            font-size: 24px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0;
            color: #777;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #4e73df;
            color: white;
            text-align: left;
            padding: 8px;
            text-transform: uppercase;
            font-size: 10px;
        }
        td {
            border-bottom: 1px solid #eee;
            padding: 8px;
            vertical-align: top;
        }
        tr:nth-child(even) {
            background-color: #f8f9fc;
        }
        .product-title {
            font-weight: bold;
            color: #2e59d9;
            font-size: 12px;
        }
        .sku {
            color: #858796;
            font-size: 9px;
            margin-top: 2px;
        }
        .price {
            font-weight: bold;
            color: #333;
            text-align: right;
            white-space: nowrap;
        }
        .stock {
            text-align: center;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }
        .category-label {
            font-size: 9px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Product Price List</h1>
        <p>Generated on: {{ date('d M, Y h:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="45%">Product Detail</th>
                <th width="15%">SKU</th>
                <th width="20%" style="text-align: right;">Selling Price</th>
                <th width="15%" style="text-align: center;">Stock</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    <div class="product-title">{{ $product->title }}</div>
                    <div class="category-label">{{ $product->cat_info->title ?? 'Uncategorized' }}</div>
                </td>
                <td>
                    <div class="sku">{{ $product->sku ?? '—' }}</div>
                </td>
                <td class="price">
                    PKR {{ number_format($product->price, 0) }}
                </td>
                <td class="stock">
                    {{ $product->stock }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        &copy; {{ date('Y') }} {{ config('app.name') }} | Page <script type="text/php">if (isset($pdf)) { $pdf->page_script('echo $PAGE_NUM." of ".$PAGE_COUNT;'); }</script>
    </div>
</body>
</html>
