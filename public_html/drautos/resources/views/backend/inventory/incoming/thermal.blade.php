<!DOCTYPE html>
<html>
<head>
    <title>Incoming Goods #{{ $inventoryIncoming->reference_number }}</title>
    <style>
        * { box-sizing: border-box; }
        @page { margin: 0; }
        @font-face {
            font-family: 'Revue';
            src: url("{{ str_replace('\\', '/', public_path('revue/reve.ttf')) }}") format("truetype");
            font-weight: normal;
            font-style: normal;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            width: 80mm;
            margin: 0 auto;
            padding: 40px;
            font-size: 13px;
            color: #000;
            line-height: 1.3;
            font-weight: 700;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: 900; }
        
        .header-container {
            position: relative;
            margin-bottom: 12px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            overflow: hidden;
        }
        .watermark-bg {
            position: absolute;
            top: 5px;
            left: 5px;
            font-family: 'Revue', sans-serif;
            font-size: 80px;
            color: #000;
            opacity: 0.12;
            z-index: -1;
            white-space: nowrap;
            pointer-events: none;
        }
        .merchant-name {
            font-size: 24px;
            font-weight: 900;
            text-transform: uppercase;
            margin-bottom: 2px;
            padding-top: 15px;
        }
        .merchant-address {
            font-size: 10px;
            text-transform: uppercase;
        }

        .info-grid {
            margin-bottom: 10px;
            font-size: 11px;
            text-transform: uppercase;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }

        .separator {
            border-top: 1px solid #000;
            margin: 5px 0;
        }

        .item-list {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
        }
        .item-list th {
            text-align: left;
            font-size: 10px;
            border-bottom: 1px solid #000;
            padding: 5px 0;
        }
        .item-list td {
            padding: 8px 0;
            vertical-align: top;
            border-bottom: 0.5px solid #eee;
        }
        .item-name {
            font-size: 13px;
            display: block;
            font-weight: 900;
        }
        .item-details {
            font-size: 10px;
            opacity: 0.9;
        }

        .totals-block {
            width: 100%;
            margin-top: 10px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
            font-size: 14px;
        }
        .grand-total-row {
            margin-top: 5px;
            padding: 10px 0;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            font-size: 20px;
            font-weight: 900;
        }

        .footer-note {
            margin-top: 15px;
            font-size: 14px;
            text-align: center;
            font-weight: 900;
            border-top: 2px solid #000;
            padding-top: 10px;
        }
        .social-info {
            font-size: 10px;
            text-align: center;
            margin-top: 5px;
            padding-bottom: 20px;
        }

        .ledger-box {
            border: 2px solid #000;
            padding: 8px;
            margin-top: 15px;
            background: #fff;
        }
        .ledger-title {
            text-align: center;
            font-size: 14px;
            font-weight: 900;
            border-bottom: 1px solid #000;
            margin-bottom: 5px;
            padding-bottom: 2px;
        }

        @media print {
            body { padding: 0; margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="watermark-bg">DR</div>
    <div class="header-container text-center">
        <div class="merchant-name">DANYAL AUTOS</div>
        <div class="merchant-address">
            INCOMING GOODS RECEIPT<br>
            {{ now()->format('d M Y, h:i A') }}
        </div>
    </div>

    <div class="info-grid">
        <div class="info-row">
            <span>Reference: <strong>{{ $inventoryIncoming->reference_number }}</strong></span>
            <span>Date: <strong>{{ $inventoryIncoming->received_date->format('d/m/y') }}</strong></span>
        </div>
        <div class="info-row">
            <span>Supplier: <strong>{{ strtoupper($inventoryIncoming->supplier->name ?? 'N/A') }}</strong></span>
        </div>
        @if($inventoryIncoming->invoice_number)
        <div class="info-row">
            <span>Invoice #: <strong>{{ $inventoryIncoming->invoice_number }}</strong></span>
        </div>
        @endif
        <div class="info-row">
            <span>Receiver: <strong>{{ strtoupper($inventoryIncoming->receiver->name ?? 'Admin') }}</strong></span>
            <span>Status: <strong>{{ strtoupper($inventoryIncoming->status) }}</strong></span>
        </div>
    </div>

    <table class="item-list">
        <thead>
            <tr>
                <th width="60%">ITEM DETAILS</th>
                <th width="15%" class="text-center">QTY</th>
                <th width="25%" class="text-right">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventoryIncoming->items as $item)
                <tr>
                    <td>
                        <span class="item-name">{{ strtoupper($item->product->title) }}</span>
                        <span class="item-details">
                            SKU: {{ $item->product->sku }}
                            @if($item->packaging_item_id)
                                | PKG: {{ $item->packagingItem->name }}
                            @endif
                        </span>
                        <span class="item-details"><br>UNIT COST: {{ number_format($item->unit_cost, 0) }}</span>
                    </td>
                    <td class="text-center bold" style="font-size: 16px;">{{ $item->quantity }}</td>
                    <td class="text-right bold">Rs.{{ number_format($item->total_cost, 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="separator"></div>
    <div class="totals-block">
        @if($inventoryIncoming->shipping_cost > 0)
        <div class="total-row">
            <span>Items Subtotal</span>
            <span>Rs.{{ number_format($inventoryIncoming->items->sum('total_cost'), 0) }}</span>
        </div>
        <div class="total-row">
            <span>Shipping Cost</span>
            <span>Rs.{{ number_format($inventoryIncoming->shipping_cost, 0) }}</span>
        </div>
        @endif
        <div class="grand-total-row">
            <span>GRAND TOTAL</span>
            <span>Rs.{{ number_format($inventoryIncoming->total_cost, 0) }}</span>
        </div>
    </div>

    @if($inventoryIncoming->notes)
    <div class="info-grid" style="margin-top: 10px;">
        <div class="bold small">NOTES:</div>
        <div style="font-style: italic;">{{ $inventoryIncoming->notes }}</div>
    </div>
    @endif

    @if(isset($ledger) && count($ledger) > 0)
        <div style="page-break-before: always; margin-top: 20px;"></div>
        <div class="ledger-box">
            <div class="ledger-title">SUPPLIER LEDGER SUMMARY</div>
            <div class="info-grid" style="margin-top: 5px;">
                @foreach($ledger as $l)
                <div class="info-row" style="border-bottom: 0.5px solid #eee; padding: 2px 0;">
                    <span style="font-size: 9px;">{{ $l->transaction_date->format('d/m') }} {{ substr($l->description, 0, 20) }}</span>
                    <span class="bold">Rs.{{ number_format($l->amount, 0) }} {{ strtoupper($l->type[0]) }}</span>
                </div>
                @endforeach
            </div>
            <div class="grand-total-row" style="font-size: 16px; padding: 5px 0; margin-top: 5px;">
                <span>PAYABLE BALANCE</span>
                <span>Rs.{{ number_format($inventoryIncoming->supplier->current_balance, 0) }}</span>
            </div>
        </div>
    @endif

    <div class="footer-note">
        INCOMING GOODS RECORD
    </div>
    <div class="social-info">
        DR AUTO PARTS | {{ now()->format('d/m/y H:i') }}
    </div>

    <div class="text-center no-print" style="margin-top: 10mm;">
        <button onclick="window.print()" style="padding: 10px 20px; font-weight: bold; background: #000; color: #fff; border: none; border-radius: 5px; cursor: pointer;">
            <i class="fas fa-print"></i> PRINT RECEIPT
        </button>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
