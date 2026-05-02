<!DOCTYPE html>
<html>
<head>
    <title>Sale Order #{{ $salesOrder->order_number }}</title>
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

        @media print {
            body { padding: 0; margin: 0; }
        }
    </style>
</head>
<body>
    @php
        $items = $salesOrder->items;
        // Only items with remaining quantity
        $pendingItems = $items->filter(fn($i) => ($i->quantity - $i->delivered_quantity) > 0);
        $chunks = $pendingItems->chunk(15);
        $totalChunks = $chunks->count();
        $totalPendingQty = $pendingItems->sum(fn($i) => $i->quantity - $i->delivered_quantity);
    @endphp

    @if($pendingItems->isEmpty())
        <div style="text-align:center; padding: 40px 0; font-size:16px; font-weight:bold;">
            ✅ ALL ITEMS FULFILLED<br>
            <small style="font-weight:normal; font-size:12px;">No pending items to print.</small>
        </div>
    @else

    @foreach($chunks as $pageIndex => $chunk)
        <div class="watermark-bg">SO</div>
        <div class="header-container text-center">
            <div class="merchant-name">DANYAL AUTOS</div>
            <div class="merchant-address">
                12-Butt Market, Badami Bagh, Lahore<br>
                TEL: 042-37727045 | MOB: 0304-2000274
            </div>
        </div>

        <div class="info-grid">
            <div class="info-row">
                <span>Sale Order: <strong>{{ $salesOrder->order_number }}</strong></span>
                <span>Date: <strong>{{ now()->format('d/m/y H:i') }}</strong></span>
            </div>
            <div class="info-row">
                <span>Staff: <strong>{{ strtoupper($salesOrder->staff->name ?? 'System') }}</strong></span>
                <span>Status: <strong>{{ strtoupper($salesOrder->status) }}</strong></span>
            </div>
            <div class="separator"></div>
            <div class="info-row">
                <span>Customer: <strong>{{ strtoupper($salesOrder->user->name ?? 'Guest') }}</strong></span>
            </div>
            @if($salesOrder->user && $salesOrder->user->phone)
            <div class="info-row">
                <span>Contact: <strong>{{ $salesOrder->user->phone }}</strong></span>
            </div>
            @endif
            <div class="separator"></div>
            <div style="text-align:center; font-size:11px; font-weight:900; letter-spacing:2px; padding: 3px 0; background:#000; color:#fff;">
                ⏳ PENDING ITEMS ONLY
            </div>
        </div>

        <table class="item-list">
            <thead>
                <tr>
                    <th width="75%">PRODUCT DETAILS</th>
                    <th width="25%" class="text-center">PENDING QTY</th>
                </tr>
            </thead>
            <tbody>
                @foreach($chunk as $item)
                @php $pendingQty = $item->quantity - $item->delivered_quantity; @endphp
                    <tr>
                        <td>
                            <span class="item-name">
                                {{ strtoupper($item->product->title ?? 'Item') }}
                                @if($item->product && $item->product->brand)
                                    ({{ strtoupper($item->product->brand->title) }})
                                @endif
                            </span>
                            <span class="item-details">
                                @if($item->product)
                                    @if($item->product->model) MODEL: {{ $item->product->model }} @endif
                                    @if($item->product->sku) | SKU: {{ $item->product->sku }} @endif
                                @endif
                            </span>
                            @if($item->delivered_quantity > 0)
                            <span class="item-details"><br>
                                Ordered: {{ $item->quantity }} | Delivered: {{ $item->delivered_quantity }}
                            </span>
                            @endif
                        </td>
                        <td class="text-center bold" style="font-size: 18px;">
                            {{ $pendingQty }}<span style="font-size: 10px; margin-left: 2px;">{{ strtoupper($item->product->unit ?? '') }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($pageIndex == $totalChunks - 1)
            <div class="separator"></div>
            <div style="display:flex; justify-content:space-between; font-size:13px; font-weight:900; padding: 8px 0;">
                <span>TOTAL PENDING ITEMS</span>
                <span>{{ $pendingItems->count() }} varieties</span>
            </div>

            @if($salesOrder->note)
            <div class="info-grid" style="margin-top: 8px;">
                <div class="bold">NOTE:</div>
                <div style="font-size:10px;">{{ $salesOrder->note }}</div>
            </div>
            @endif

            <div class="footer-note">PENDING DELIVERY SLIP</div>
            <div class="social-info">WhatsApp: 0304-2000274 | FB: /DanyalAutos</div>
        @else
            <div class="text-center bold" style="padding:10px; border:2px solid #000; margin:15px 0;">
                --- CONTINUED ON PAGE {{ $pageIndex + 2 }} ---
            </div>
        @endif
    @endforeach
    @endif

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
