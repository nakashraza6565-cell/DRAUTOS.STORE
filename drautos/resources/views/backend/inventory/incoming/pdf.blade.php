<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Incoming Goods #{{ $inventoryIncoming->reference_number }} | Danyal Autos</title>
    <style>
        @page { margin: 10mm; size: a4; }
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            margin: 0; padding: 0; 
            color: #111; 
            line-height: 1.2; 
            font-size: 11px; 
        }
        table { width: 100%; border-collapse: collapse; }
        td, th { vertical-align: top; }
        
        .header-table { margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .company-name { font-size: 24px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .company-details { font-size: 10px; color: #555; margin-top: 4px; }
        
        .invoice-title { font-size: 28px; font-weight: bold; text-align: right; text-transform: uppercase; }
        .invoice-meta { text-align: right; font-size: 10px; color: #333; margin-top: 4px; }
        
        .info-table { margin-bottom: 15px; }
        .info-title { font-size: 10px; font-weight: bold; color: #777; text-transform: uppercase; border-bottom: 1px solid #ddd; padding-bottom: 2px; margin-bottom: 4px; }
        .info-content { font-size: 11px; line-height: 1.3; }
        
        .item-table { margin-bottom: 15px; }
        .item-table th { 
            background-color: #f4f4f4; 
            border-top: 1px solid #000; 
            border-bottom: 1px solid #000; 
            padding: 6px 4px; 
            font-size: 9px; 
            text-transform: uppercase; 
            text-align: left; 
        }
        .item-table td { padding: 5px 4px; border-bottom: 1px solid #eee; }
        .item-table th.text-right, .item-table td.text-right { text-align: right; }
        .item-table th.text-center, .item-table td.text-center { text-align: center; }
        
        .item-title { font-weight: bold; font-size: 11px; }
        .item-meta { font-size: 9px; color: #666; display: inline-block; margin-right: 6px; }
        
        .totals-table { width: 45%; float: right; margin-top: 10px; }
        .totals-table td { padding: 4px 0; border-bottom: 1px solid #f9f9f9; }
        .totals-table td.label { color: #555; font-size: 10px; text-transform: uppercase; }
        .totals-table td.value { text-align: right; font-weight: bold; }
        .grand-total { border-top: 2px solid #000; border-bottom: 2px solid #000; background: #f9f9f9; }
        .grand-total td { padding: 6px 0; font-size: 14px !important; }
        
        .footer { clear: both; margin-top: 30px; text-align: center; font-size: 9px; color: #888; border-top: 1px dashed #ddd; padding-top: 10px; }
        
        @font-face {
            font-family: 'Revue';
            src: url("{{ str_replace('\\', '/', public_path('revue/reve.ttf')) }}") format("truetype");
        }
        .watermark {
            position: fixed; top: 35%; left: 50%; transform: translate(-50%, -50%);
            font-size: 300px; color: #000; opacity: 0.04; z-index: -1000;
            font-family: 'Revue', sans-serif; pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="watermark">DR</div>
    
    <table class="header-table">
        <tr>
            <td width="50%">
                <div class="company-name">Danyal Autos</div>
                <div class="company-details">
                    12-BUTT MARKET BADAMI BAGH LAHORE<br>
                    Zip Code: 54000 | Contact: +923042000274, 04237727045
                </div>
            </td>
            <td width="50%" style="vertical-align: bottom;">
                <div class="invoice-title">INCOMING GOODS</div>
                <div class="invoice-meta">
                    <strong>Reference #:</strong> {{ $inventoryIncoming->reference_number }}<br>
                    <strong>Date Received:</strong> {{ $inventoryIncoming->received_date->format('M d, Y') }}<br>
                    <strong>Invoice #:</strong> {{ $inventoryIncoming->invoice_number ?: 'N/A' }}
                </div>
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td width="50%" style="padding-right: 15px;">
                <div class="info-title">Supplier Details</div>
                <div class="info-content">
                    <strong>{{ strtoupper($inventoryIncoming->supplier->name ?? 'N/A') }}</strong><br>
                    {{ $inventoryIncoming->supplier->company ?? '' }}<br>
                    Address: {{ $inventoryIncoming->supplier->address ?? 'N/A' }}<br>
                    Phone: {{ $inventoryIncoming->supplier->phone ?? 'N/A' }}
                </div>
            </td>
            <td width="50%">
                <div class="info-title">Warehouse & Receiver</div>
                <div class="info-content">
                    <strong>Warehouse:</strong> {{ $inventoryIncoming->warehouse->name ?? 'Default Location' }}<br>
                    <strong>Receiver:</strong> {{ $inventoryIncoming->receiver->name ?? 'System' }}<br>
                    <strong>Status:</strong> {{ strtoupper($inventoryIncoming->status) }}
                </div>
            </td>
        </tr>
    </table>

    <table class="item-table">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="45%">ITEM DESCRIPTION / SKU</th>
                <th width="15%" class="text-center">BATCH #</th>
                <th width="10%" class="text-center">QTY</th>
                <th width="10%" class="text-right">UNIT COST</th>
                <th width="15%" class="text-right">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventoryIncoming->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <div class="item-title">{{ $item->product->title ?? 'Product' }}</div>
                    <div class="item-meta">SKU: {{ $item->product->sku ?? 'N/A' }}</div>
                    @if($item->product->brand)
                        <span class="item-meta">| Brand: {{ $item->product->brand->title }}</span>
                    @endif
                </td>
                <td class="text-center">{{ $item->batch_number ?: '-' }}</td>
                <td class="text-center">{{ $item->quantity }} {{ $item->product->unit ?? '' }}</td>
                <td class="text-right">{{ number_format($item->unit_cost, 2) }}</td>
                <td class="text-right" style="font-weight:bold;">{{ number_format($item->total_cost, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="width: 100%; overflow: hidden;">
        @if($inventoryIncoming->notes)
        <div style="width: 50%; float: left;">
            <div class="info-title">Notes</div>
            <div class="info-content">{{ $inventoryIncoming->notes }}</div>
        </div>
        @endif
        
        <table class="totals-table">
            <tr>
                <td class="label">Items Total</td>
                <td class="value">PKR {{ number_format($inventoryIncoming->items->sum('total_cost'), 2) }}</td>
            </tr>
            @if($inventoryIncoming->shipping_cost > 0)
            <tr>
                <td class="label">Shipping Cost</td>
                <td class="value">PKR {{ number_format($inventoryIncoming->shipping_cost, 2) }}</td>
            </tr>
            @endif
            <tr class="grand-total">
                <td class="label">Grand Total</td>
                <td class="value">PKR {{ number_format($inventoryIncoming->total_cost, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <strong>INVENTORY RECEIPT | Danyal Autos</strong><br>
        This is a computer generated document. | Printed on {{ date('M d, Y H:i') }}
    </div>
</body>
</html>
