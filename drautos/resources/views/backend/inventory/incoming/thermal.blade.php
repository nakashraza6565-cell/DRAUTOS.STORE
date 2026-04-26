<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incoming Goods - {{ $inventoryIncoming->reference_number }}</title>
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
        .qr-code { margin: 5mm 0; }
    </style>
</head>
<body onload="window.print();">
    <div class="text-center header">
        <h2 style="margin: 0; font-size: 18px;">DANYAL AUTOS</h2>
        <p style="margin: 2px 0;">INCOMING GOODS RECEIPT</p>
        <p style="margin: 2px 0; font-size: 10px;">{{ now()->format('d M Y, h:i A') }}</p>
    </div>

    <div class="divider"></div>

    <div>
        <table style="font-size: 11px;">
            <tr>
                <td width="40%">Ref #:</td>
                <td class="font-bold">{{ $inventoryIncoming->reference_number }}</td>
            </tr>
            <tr>
                <td>Date:</td>
                <td>{{ $inventoryIncoming->received_date->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td>Supplier:</td>
                <td class="font-bold">{{ $inventoryIncoming->supplier->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Invoice:</td>
                <td>{{ $inventoryIncoming->invoice_number ?: 'N/A' }}</td>
            </tr>
            <tr>
                <td>Receiver:</td>
                <td>{{ $inventoryIncoming->receiver->name ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div class="divider"></div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventoryIncoming->items as $item)
            <tr class="item-row">
                <td colspan="3">
                    <div class="font-bold">{{ $item->product->title }}</div>
                </td>
            </tr>
            <tr>
                <td style="font-size: 10px; color: #555;">{{ $item->product->sku }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->total_cost, 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <table>
        <tr>
            <td class="font-bold" style="font-size: 14px;">GRAND TOTAL</td>
            <td class="text-right font-bold" style="font-size: 14px;">Rs. {{ number_format($inventoryIncoming->totalCost, 0) }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    @if($inventoryIncoming->notes)
    <div style="font-size: 10px; margin-bottom: 3mm;">
        <span class="font-bold">Notes:</span> {{ $inventoryIncoming->notes }}
    </div>
    @endif

    <div class="text-center footer">
        <p>This is a computer generated receipt.</p>
        <p>Software by Dr Auto Store</p>
    </div>

    {{-- Page Break for Ledger --}}
    <div style="page-break-after: always; margin-bottom: 20mm;"></div>
    <div class="divider" style="border-top: 3px double #000;"></div>

    @if(count($ledger) > 0)
    <div class="text-center header">
        <h2 style="margin: 0; font-size: 16px;">SUPPLIER LEDGER</h2>
        <p style="margin: 2px 0;">{{ $inventoryIncoming->supplier->name }}</p>
    </div>

    <div class="divider"></div>

    <table style="font-size: 10px;">
        <thead>
            <tr>
                <th width="20%">Date</th>
                <th width="50%">Description</th>
                <th width="30%" class="text-right">Amt</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ledger as $item)
            <tr>
                <td>{{ $item->transaction_date->format('d/m') }}</td>
                <td>
                    <div class="font-bold">{{ substr($item->description, 0, 25) }}</div>
                    <small>{{ strtoupper($item->type) }}</small>
                </td>
                <td class="text-right font-bold">
                    {{ $item->type == 'credit' ? '-' : '' }}{{ number_format($item->amount, 0) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <div style="border: 2px solid #000; padding: 5px; text-align: center; background: #f9f9f9;">
        <div style="font-size: 10px; text-transform: uppercase;">Current Balance Payable</div>
        <div style="font-size: 20px; font-weight: bold;">Rs. {{ number_format($inventoryIncoming->supplier->current_balance, 0) }}</div>
    </div>

    <div class="text-center footer" style="margin-top: 5px;">
        <p>ACCOUNT SUMMARY REPORT</p>
    </div>
    @endif

    <div class="text-center no-print" style="margin-top: 10mm;">
        <button onclick="window.print()" style="padding: 5mm 10mm;">Print Again</button>
    </div>
</body>
</html>
