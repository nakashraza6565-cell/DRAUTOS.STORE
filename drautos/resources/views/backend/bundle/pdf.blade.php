<!DOCTYPE html>
<html>
<head>
    <title>{{$bundle->name}} - Packing List</title>
    <style>
        body { font-family: sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .bundle-info { margin-bottom: 20px; border: 1px solid #ddd; padding: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Dr Auto Store</h2>
        <h3>Bundle Packing List</h3>
    </div>

    <div class="bundle-info">
        <h3>{{$bundle->name}}</h3>
        <p><strong>SKU:</strong> {{$bundle->sku}}</p>
        <p><strong>Description:</strong> {{$bundle->description ?? 'N/A'}}</p>
        <p><strong>Price:</strong> Rs. {{number_format($bundle->price, 2)}}</p>
    </div>

    <h4>Components / Items:</h4>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product Name</th>
                <th>SKU</th>
                <th class="text-center">Usage Qty</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bundle->items as $index => $item)
            <tr>
                <td>{{$index + 1}}</td>
                <td>{{$item->product->title ?? 'N/A'}}</td>
                <td>{{$item->product->sku ?? 'N/A'}}</td>
                <td class="text-center">{{$item->quantity}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generated on {{date('Y-m-d H:i')}}</p>
    </div>
</body>
</html>
