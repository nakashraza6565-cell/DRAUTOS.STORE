<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order - {{ $purchaseOrder->po_number }}</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 80mm;
            margin: 0;
            padding: 5mm;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .header { margin-bottom: 5mm; }
        .divider { border-top: 1px dashed #000; margin: 3mm 0; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; border-bottom: 1px solid #000; padding: 1mm 0; }
        td { padding: 1mm 0; vertical-align: top; }
        .item-row td { padding-top: 2mm; }
        .footer { margin-top: 5mm; font-size: 10px; }
    </style>
</head>
<body onload="window.print();">
    <div class="text-center header">
        <h2 style="margin: 0; font-size: 18px;">DANYAL AUTOS</h2>
        <p style="margin: 2px 0;">PURCHASE ORDER (PERFORMA)</p>
        <p style="margin: 2px 0; font-size: 10px;">{{ now()->format('d M Y, h:i A') }}</p>
    </div>

    <div class="divider"></div>

    <div>
        <table style="font-size: 11px;">
            <tr>
                <td width="40%">PO #:</td>
                <td class="font-bold">{{ $purchaseOrder->po_number }}</td>
            </tr>
            <tr>
                <td>Date:</td>
                <td>{{ $purchaseOrder->order_date }}</td>
            </tr>
            <tr>
                <td>Supplier:</td>
                <td class="font-bold">{{ $purchaseOrder->supplier->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Company:</td>
                <td>{{ $purchaseOrder->supplier->company_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Phone:</td>
                <td>{{ $purchaseOrder->supplier->phone ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div class="divider"></div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-right">Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchaseOrder->items as $item)
            <tr class="item-row">
                <td colspan="2">
                    <div class="font-bold">{{ $item->product->title ?? 'N/A' }}</div>
                </td>
            </tr>
            <tr>
                <td style="font-size: 10px; color: #555;">{{ $item->product->sku ?? '' }}</td>
                <td class="text-right font-bold" style="font-size: 14px;">{{ $item->quantity }} {{ $item->product->unit ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    @if($purchaseOrder->notes)
    <div style="font-size: 10px; margin-bottom: 3mm;">
        <span class="font-bold">Notes:</span> {{ $purchaseOrder->notes }}
    </div>
    @endif

    <div class="text-center footer">
        <p>This is a computer generated Performa Invoice.</p>
        <p>Please supply above items at earliest.</p>
        <p>Software by Dr Auto Store</p>
    </div>

    <div class="text-center no-print" style="margin-top: 10mm;">
        <button onclick="window.print()" style="padding: 5mm 10mm;">Print Again</button>
    </div>
</body>
</html>
