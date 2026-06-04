<!DOCTYPE html>
<html dir="{{ request('lang') === 'ur' ? 'rtl' : 'ltr' }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Sale Order #{{ $salesOrder->order_number }}</title>
    @if(request('lang') === 'ur')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@400;700&display=swap" rel="stylesheet">
    @endif
    <style>
        * { box-sizing: border-box; }
        html, body, div, span, table, th, td, p, a, strong, b, .item-name, .item-details { font-weight: 900 !important; color: #000 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; -webkit-text-stroke: 0.5px #000 !important; text-shadow: 0.5px 0.5px 0px #000 !important; font-family: {{ request('lang') === 'ur' ? "'Noto Nastaliq Urdu', 'Arial Unicode MS'" : "'Arial', 'Helvetica', sans-serif" }} !important; }

        @page { margin: 0; }
        @font-face {
            font-family: 'Revue';
            src: url('/revue/reve.ttf?v=1.1') format("truetype");
        }
        body {
            font-family: {{ request('lang') === 'ur' ? "'Noto Nastaliq Urdu', 'Arial Unicode MS'" : "'Helvetica', 'Arial'" }}, sans-serif;
            direction: {{ request('lang') === 'ur' ? 'rtl' : 'ltr' }};
            text-align: {{ request('lang') === 'ur' ? 'right' : 'left' }};
            width: 80mm;
            margin: 0 auto;
            padding: 40px;
            font-size: {{ request('lang') === 'ur' ? '15px' : '13px' }};
            color: #000;
            line-height: {{ request('lang') === 'ur' ? '1.8' : '1.3' }};
            font-weight: 700;
            position: relative;
        }
        .text-center { text-align: center; }
        .text-right  { text-align: {{ request('lang') === 'ur' ? 'left' : 'right' }}; }
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
            top: 45%; left: 50%;
            transform: translate(-50%, -50%);
            font-family: 'Revue', sans-serif;
            font-size: 140px;
            color: #000;
            opacity: 0.22;
            z-index: -1;
            white-space: nowrap;
            pointer-events: none;
        }
        .merchant-name {
            font-size: {{ request('lang') === 'ur' ? '22px' : '24px' }};
            font-weight: 900;
            text-transform: {{ request('lang') === 'ur' ? 'none' : 'uppercase' }};
            margin-bottom: 2px;
            padding-top: 15px;
        }
        .merchant-address {
            font-size: {{ request('lang') === 'ur' ? '12px' : '10px' }};
            text-transform: {{ request('lang') === 'ur' ? 'none' : 'uppercase' }};
        }

        .info-grid {
            margin-bottom: 10px;
            font-size: {{ request('lang') === 'ur' ? '13px' : '11px' }};
            text-transform: {{ request('lang') === 'ur' ? 'none' : 'uppercase' }};
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
            text-align: {{ request('lang') === 'ur' ? 'right' : 'left' }};
            font-size: {{ request('lang') === 'ur' ? '12px' : '10px' }};
            border-bottom: 1px solid #000;
            padding: 5px 0;
        }
        .item-list th.col-center { text-align: center; }
        .item-list td {
            padding: 8px 0;
            vertical-align: top;
            border-bottom: 0.5px solid #eee;
        }
        .item-name {
            font-size: {{ request('lang') === 'ur' ? '15px' : '13px' }};
            display: block;
            font-weight: 900;
        }
        .item-details {
            font-size: {{ request('lang') === 'ur' ? '11px' : '10px' }};
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
        $isUrdu = request('lang') === 'ur';
        $items = $salesOrder->items;
        $pendingItems   = $items->filter(fn($i) => ($i->quantity - $i->delivered_quantity) > 0);
        $chunks         = $pendingItems->chunk(15);
        $totalChunks    = $chunks->count();
        $totalPendingQty = $pendingItems->sum(fn($i) => $i->quantity - $i->delivered_quantity);
    @endphp

    @if($pendingItems->isEmpty())
        <div style="text-align:center; padding: 40px 0; font-size:16px; font-weight:bold;">
            ✅ {{ $isUrdu ? 'تمام اشیاء مکمل' : 'ALL ITEMS FULFILLED' }}<br>
            <small style="font-weight:normal; font-size:12px;">{{ $isUrdu ? 'کوئی زیر التواء آئٹم نہیں۔' : 'No pending items to print.' }}</small>
        </div>
    @else

    @foreach($chunks as $pageIndex => $chunk)
        <div class="watermark-bg">SO</div>
        <div class="header-container text-center">
            <div class="merchant-name">{{ $isUrdu ? 'دنیال آٹوز' : 'DANYAL AUTOS' }}</div>
            <div class="merchant-address">
                @if($isUrdu)
                    ۱۲ بٹ مارکیٹ، بادامی باغ، لاہور<br>
                    ٹیل: 042-37727045 | موب: 0304-2000274
                @else
                    12-Butt Market, Badami Bagh, Lahore<br>
                    TEL: 042-37727045 | MOB: 0304-2000274
                @endif
            </div>
        </div>

        <div class="info-grid">
            <div class="info-row">
                <span>{{ $isUrdu ? 'سیل آرڈر:' : 'Sale Order:' }} <strong>{{ $salesOrder->order_number }}</strong></span>
                <span>{{ $isUrdu ? 'تاریخ:' : 'Date:' }} <strong>{{ now()->format('d/m/y H:i') }}</strong></span>
            </div>
            <div class="info-row">
                <span>{{ $isUrdu ? 'عملہ:' : 'Staff:' }} <strong>{{ strtoupper($salesOrder->staff->name ?? 'System') }}</strong></span>
                <span>{{ $isUrdu ? 'حالت:' : 'Status:' }} <strong>{{ strtoupper($salesOrder->status) }}</strong></span>
            </div>
            <div class="separator"></div>
            <div class="info-row">
                <span>{{ $isUrdu ? 'گاہک:' : 'Customer:' }} <strong>{{ strtoupper($salesOrder->user->name ?? 'Guest') }}</strong></span>
            </div>
            @if($salesOrder->user && $salesOrder->user->phone)
            <div class="info-row">
                <span>{{ $isUrdu ? 'رابطہ:' : 'Contact:' }} <strong>{{ $salesOrder->user->phone }}</strong></span>
            </div>
            @endif
            <div class="separator"></div>
            <div style="text-align:center; font-size:{{ $isUrdu ? '13px' : '11px' }}; font-weight:900; letter-spacing:{{ $isUrdu ? '0' : '2px' }}; padding: 3px 0; background:#000; color:#fff;">
                {{ $isUrdu ? '⏳ صرف زیر التواء اشیاء' : '⏳ PENDING ITEMS ONLY' }}
            </div>
        </div>

        <table class="item-list">
            <thead>
                <tr>
                    <th width="75%">{{ $isUrdu ? 'پروڈکٹ کی تفصیل' : 'PRODUCT DETAILS' }}</th>
                    <th width="25%" class="col-center">{{ $isUrdu ? 'باقی مقدار' : 'PENDING QTY' }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($chunk as $item)
                @php $pendingQty = $item->quantity - $item->delivered_quantity; @endphp
                    <tr>
                        <td>
                            <span class="item-name">
                                {{ Helper::translatePartTitle($item->product->title ?? 'Item', $isUrdu) }}
                                @if($item->product && $item->product->brand)
                                    ({{ $item->product->brand->title }})
                                @endif
                            </span>
                            <span class="item-details">
                                @if($item->product)
                                    @if($item->product->model) {{ $isUrdu ? 'ماڈل:' : 'MODEL:' }} {{ $item->product->model }} @endif
                                    @if($item->product->sku) | {{ $isUrdu ? 'ایس کے یو:' : 'SKU:' }} {{ $item->product->sku }} @endif
                                @endif
                            </span>
                            @if($item->delivered_quantity > 0)
                            <span class="item-details"><br>
                                {{ $isUrdu ? 'آرڈر:' : 'Ordered:' }} {{ $item->quantity }} | {{ $isUrdu ? 'ڈیلیور:' : 'Delivered:' }} {{ $item->delivered_quantity }}
                            </span>
                            @endif
                        </td>
                        <td class="text-center bold" style="font-size: 18px;">
                            {{ $pendingQty }}<span style="font-size: 10px; margin-{{ $isUrdu ? 'right' : 'left' }}: 2px;">{{ strtoupper($item->product->unit ?? '') }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($pageIndex == $totalChunks - 1)
            <div class="separator"></div>
            <div style="display:flex; justify-content:space-between; font-size:13px; font-weight:900; padding: 8px 0;">
                <span>{{ $isUrdu ? 'کل زیر التواء اشیاء:' : 'TOTAL PENDING ITEMS' }}</span>
                <span>{{ $pendingItems->count() }} {{ $isUrdu ? 'اقسام' : 'varieties' }}</span>
            </div>

            @if($salesOrder->note)
            <div class="info-grid" style="margin-top: 8px;">
                <div class="bold">{{ $isUrdu ? 'نوٹ:' : 'NOTE:' }}</div>
                <div style="font-size:10px;">{{ $salesOrder->note }}</div>
            </div>
            @endif

            <div class="footer-note">{{ $isUrdu ? 'ڈیلیوری سلپ' : 'PENDING DELIVERY SLIP' }}</div>
            <div class="social-info">WhatsApp: 0304-2000274 | FB: /DanyalAutos</div>
        @else
            <div class="text-center bold" style="padding:10px; border:2px solid #000; margin:15px 0;">
                --- {{ $isUrdu ? 'اگلا صفحہ' : 'CONTINUED ON PAGE' }} {{ $pageIndex + 2 }} ---
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

