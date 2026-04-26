<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Purchase Order {{ $purchaseOrder->po_number }} | Dr Auto Store</title>
    <style>
        @page { margin: 10mm; size: a4; }
        @font-face {
            font-family: 'Revue';
            src: url("{{ str_replace('\\', '/', public_path('revue/reve.ttf')) }}") format("truetype");
            font-weight: normal; font-style: normal;
        }
        body { font-family: 'DejaVu Sans', sans-serif; margin: 0; padding: 0; color: #2D3748; font-size: 11px; }
        .watermark { position: fixed; top: 30%; left: 50%; transform: translate(-50%, -50%); font-size: 400px; color: #4b312c; opacity: 0.12; z-index: -1000; font-family: 'Revue', sans-serif; text-transform: uppercase; }
        .header { padding-bottom: 20px; border-bottom: 2px solid #e2e8f0; margin-bottom: 20px; }
        .company-name { font-size: 24px; font-weight: bold; color: #4b312c; text-transform: uppercase; }
        .company-details { font-size: 10px; color: #718096; }
        .po-title { text-align: right; float: right; }
        .po-label { font-size: 32px; font-weight: 900; color: #e2e8f0; margin: 0; }
        .po-no { font-size: 14px; font-weight: bold; color: #4b312c; }
        .info-grid { width: 100%; margin-bottom: 30px; }
        .info-box { width: 48%; float: left; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #4b312c; color: white; padding: 10px; text-align: left; text-transform: uppercase; font-size: 10px; }
        td { padding: 10px; border-bottom: 1px solid #edf2f7; }
        .text-right { text-align: right; }
        .total-row { background: #f7fafc; font-weight: bold; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; color: #a0aec0; font-size: 9px; padding: 10px 0; border-top: 1px solid #edf2f7; }
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>
    <div class="watermark">DR</div>
    
    <div class="header clearfix">
        <div style="float: left;">
            <div class="company-name">Danyal Autos</div>
            <div class="company-details">
                12-BUTT MARKET BADAMI BAGH LAHORE<br>
                Zip Code: 54000 | +923042000274 | 04237727045
            </div>
        </div>
        <div class="po-title">
            <h1 class="po-label">PURCHASE ORDER</h1>
            <div class="po-no">#{{ $purchaseOrder->po_number }}</div>
            <div style="font-size: 10px; color: #718096; margin-top: 5px;">
                Date: {{ \Carbon\Carbon::parse($purchaseOrder->order_date)->format('d M Y') }}
            </div>
        </div>
    </div>

    <div class="info-grid clearfix">
        <div class="info-box">
            <div style="color: #a0aec0; text-transform: uppercase; font-size: 9px; font-weight: bold; margin-bottom: 5px;">Supplier Info</div>
            <div style="font-size: 12px; font-weight: bold; color: #2d3748;">{{ $purchaseOrder->supplier->name }}</div>
            <div style="color: #4a5568;">
                {{ $purchaseOrder->supplier->company_name }}<br>
                Phone: {{ $purchaseOrder->supplier->phone }}<br>
                Email: {{ $purchaseOrder->supplier->email ?? 'N/A' }}
            </div>
        </div>
        <div class="info-box" style="float: right; text-align: right;">
            <div style="color: #a0aec0; text-transform: uppercase; font-size: 9px; font-weight: bold; margin-bottom: 5px;">Delivery Details</div>
            <div style="color: #4a5568;">
                Expected: {{ $purchaseOrder->expected_delivery_date ? \Carbon\Carbon::parse($purchaseOrder->expected_delivery_date)->format('d M Y') : 'N/A' }}<br>
                Status: {{ strtoupper($purchaseOrder->status) }}
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="55%">Product Description</th>
                <th class="text-right" width="10%">Qty</th>
                <th class="text-right" width="15%">Unit Price</th>
                <th class="text-right" width="15%">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchaseOrder->items as $index => $item)
            <tr>
                <td>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                <td>
                    <div style="font-weight: bold;">{{ $item->product->title ?? 'N/A' }}</div>
                    <div style="font-size: 9px; color: #718096;">SKU: {{ $item->product->sku ?? 'N/A' }}</div>
                </td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right">{{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="text-right">Grand Total</td>
                <td class="text-right">Rs. {{ number_format($purchaseOrder->total_amount, 2) }}</td>
            </tr>
            <tr>
                <td colspan="4" class="text-right" style="color: #718096;">Amount Paid</td>
                <td class="text-right" style="color: #718096;">Rs. {{ number_format($purchaseOrder->paid_amount, 2) }}</td>
            </tr>
            <tr style="font-size: 12px; color: #4b312c;">
                <td colspan="4" class="text-right"><strong>Net Payable</strong></td>
                <td class="text-right"><strong>Rs. {{ number_format($purchaseOrder->total_amount - $purchaseOrder->paid_amount, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    @if($purchaseOrder->notes)
    <div style="margin-top: 20px; padding: 10px; background: #fffaf0; border-left: 4px solid #4b312c;">
        <strong style="font-size: 9px; text-transform: uppercase; color: #4b312c;">Notes:</strong><br>
        <div style="color: #4a5568; margin-top: 5px;">{{ $purchaseOrder->notes }}</div>
    </div>
    @endif

    <div class="footer">
        Danyal Autos | Computer Generated Purchase Order | &copy; {{ date('Y') }}
        <br> This software is designed and developed by (Expert Code Sol) Email: irfanwaince555@gmail.com
    </div>
</body>
</html>
