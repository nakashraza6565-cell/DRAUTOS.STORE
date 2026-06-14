<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order #{{ $purchaseOrder->po_number }}</title>
    <style>
        * { box-sizing: border-box; }
        html, body, div, span, table, th, td, p, a, strong, b, .item-name, .item-details { color: #000 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; font-family: {{ request('lang') === 'ur' ? "'Noto Nastaliq Urdu', 'Arial Unicode MS'" : "'Arial', 'Helvetica', sans-serif" }} !important; }
        @if(request('lang') !== 'ur')
        html, body, div, span, table, th, td, p, a, strong, b, .item-name, .item-details { font-weight: 900 !important; -webkit-text-stroke: 0.5px #000 !important; text-shadow: 0.5px 0.5px 0px #000 !important; }
        @else
        html, body, div, span, table, th, td, p, a, strong, b, .item-name, .item-details { font-weight: 700 !important; }
        @endif

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
        .bold { font-weight: {{ request('lang') === 'ur' ? '700' : '900' }}; }
        
        .header-container {
            position: relative;
            margin-bottom: 12px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            overflow: hidden;
        }
        .merchant-name {
            font-size: 22px;
            font-weight: {{ request('lang') === 'ur' ? '700' : '900' }};
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
            font-weight: {{ request('lang') === 'ur' ? '700' : '900' }};
        }
        .item-details {
            font-size: 10px;
            opacity: 0.9;
        }

        .footer-note {
            margin-top: 15px;
            font-size: 13px;
            text-align: center;
            font-weight: {{ request('lang') === 'ur' ? '700' : '900' }};
            border-top: 2px solid #000;
            padding-top: 10px;
        }

        .page-break {
            margin-top: 40px;
            padding-top: 40px;
            border-top: 3px dashed #000;
            page-break-before: always;
        }

        .empty-box {
            height: 25px;
            border: 1px solid #000;
        }

        @media print {
            body { padding: 5px; margin: 0; }
            .no-print { display: none; }
        }
    </style>

@include('backend.layouts.watermark', ['type' => 'thermal'])
</head>
<body onload="(function(){ var m=/Android|iPhone|iPad/i.test(navigator.userAgent); if(m){ if(localStorage.getItem('drautos_bt_name')){ setTimeout(function(){window.drautosBTPrint();},1200); } } else { window.print(); } })();">
    <!-- RECEIPT 1: PURCHASE ORDER (PERFORMA) -->
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

    <div style="margin-top: 30px; display: flex; justify-content: flex-end;">
        <div style="text-align: center; width: 50%;">
            <div style="border-top: 2px solid #000; padding-top: 5px; font-weight: {{ request('lang') === 'ur' ? '700' : '900' }}; font-size: 12px; text-transform: uppercase;">
                SIGNATURE
            </div>
        </div>
    </div>

    <!-- PAGE BREAK / SEPARATOR FOR SECOND RECEIPT -->
    <div class="page-break"></div>

    <!-- RECEIPT 2: SUPPLIER RECORD -->
    <div class="header-container text-center">
        <div class="merchant-name">DANYAL AUTOS</div>
        <div class="merchant-address">
            12-Butt Market, Badami Bagh, Lahore<br>
            SUPPLIER RECORD RECEIPT
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
    </div>

    <table class="item-list">
        <thead>
            <tr>
                <th width="40%">ITEM NAME</th>
                <th width="20%" class="text-center">QTY</th>
                <th width="20%" class="text-center">PRICE</th>
                <th width="20%" class="text-center">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchaseOrder->items as $item)
                <tr>
                    <td style="border-bottom: 0.5px solid #000;">
                        <span class="item-name">{{ strtoupper($item->product->title ?? 'N/A') }}</span>
                    </td>
                    <td style="border-bottom: 0.5px solid #000;"><div class="empty-box"></div></td>
                    <td style="border-bottom: 0.5px solid #000;"><div class="empty-box"></div></td>
                    <td style="border-bottom: 0.5px solid #000;"><div class="empty-box"></div></td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td class="text-right bold" style="padding-top: 10px; font-size: 14px;">GRAND TOTAL:</td>
                <td colspan="3" style="padding-top: 10px;"><div class="empty-box" style="height: 30px; border: 2px solid #000;"></div></td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 30px; display: flex; justify-content: flex-end;">
        <div style="text-align: center; width: 50%;">
            <div style="border-top: 2px solid #000; padding-top: 5px; font-weight: {{ request('lang') === 'ur' ? '700' : '900' }}; font-size: 12px; text-transform: uppercase;">
                SIGNATURE
            </div>
        </div>
    </div>
    
    <div class="text-center no-print" style="margin-top: 10mm;">
        <button onclick="window.print()" style="padding: 5mm 10mm;">Print Again</button>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="/backend/js/bluetooth-print.js"></script>
    @include('backend.partials.bluetooth-print-btn')
</body>
</html>


