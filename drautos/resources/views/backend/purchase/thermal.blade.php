<!DOCTYPE html>
<html>
<head>
    <title>Purchase Order #{{ $purchaseOrder->po_number }}</title>
    <style>
        * { box-sizing: border-box; }
        @page { margin: 0; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            width: 80mm;
            margin: 0 auto;
            padding: 20px;
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
        .merchant-name {
            font-size: 22px;
            font-weight: 900;
            text-transform: uppercase;
            margin-bottom: 2px;
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

        .footer-note {
            margin-top: 15px;
            font-size: 13px;
            text-align: center;
            font-weight: 900;
            border-top: 2px solid #000;
            padding-top: 10px;
        }

        @media print {
            body { padding: 5px; margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print();">
    <div class="header-container text-center">
        <div class="merchant-name">DANYAL AUTOS</div>
        <div class="merchant-address">
            12-Butt Market, Badami Bagh, Lahore<br>
            PURCHASE ORDER (PERFORMA)
        </div>
    </div>

    <div class="info-grid">
        <div class="info-row">
            <span>PO #: <strong>{{ $purchaseOrder->po_number }}</strong></span>
            <span>Date: <strong>{{ date('d/m/y', strtotime($purchaseOrder->order_date)) }}</strong></span>
        </div>
        <div class="separator"></div>
        <div class="info-row">
            <span>Supplier: <strong>{{ strtoupper($purchaseOrder->supplier->name ?? 'N/A') }}</strong></span>
        </div>
        <div class="info-row">
            <span>Company: <strong>{{ strtoupper($purchaseOrder->supplier->company_name ?? 'N/A') }}</strong></span>
        </div>
        @if($purchaseOrder->supplier && $purchaseOrder->supplier->phone)
        <div class="info-row">
            <span>Contact: <strong>{{ $purchaseOrder->supplier->phone }}</strong></span>
        </div>
        @endif
    </div>

    <table class="item-list">
        <thead>
            <tr>
                <th width="70%">PRODUCT DETAILS</th>
                <th width="30%" class="text-right">QUANTITY</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchaseOrder->items as $item)
                <tr>
                    <td>
                        <span class="item-name">{{ strtoupper($item->product->title ?? 'N/A') }}</span>
                        <span class="item-details">
                            @if($item->product)
                                @if($item->product->brand) BRAND: {{ strtoupper($item->product->brand->title) }} @endif
                                @if($item->product->sku) | SKU: {{ $item->product->sku }} @endif
                            @endif
                        </span>
                    </td>
                    <td class="text-right bold" style="font-size: 16px;">
                        {{ $item->quantity }}<span style="font-size: 10px; margin-left: 2px;">{{ strtoupper($item->product->unit ?? '') }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($purchaseOrder->notes)
    <div class="separator"></div>
    <div style="font-size: 11px; text-transform: uppercase;">
        <strong>Notes:</strong> {{ $purchaseOrder->notes }}
    </div>
    @endif

    <div class="footer-note">
        PLEASE SUPPLY ITEMS AT EARLIEST
    </div>
    
    <div class="text-center no-print" style="margin-top: 10mm;">
        <button onclick="window.print()" style="padding: 5mm 10mm;">Print Again</button>
    </div>
</body>
</html>
