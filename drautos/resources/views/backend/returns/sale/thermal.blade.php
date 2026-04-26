<!DOCTYPE html>
<html>
<head>
    <title>Return #{{ $return->return_number }}</title>
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
            padding: 15px;
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

        .return-title {
            background: #000;
            color: #fff;
            padding: 5px;
            margin: 10px 0;
            font-size: 16px;
            text-align: center;
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

        .grand-total-row {
            margin-top: 5px;
            padding: 10px 0;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            font-size: 18px;
            font-weight: 900;
            display: flex;
            justify-content: space-between;
        }

        .footer-note {
            margin-top: 15px;
            font-size: 14px;
            text-align: center;
            font-weight: 900;
            border-top: 2px solid #000;
            padding-top: 10px;
        }

        @media print {
            body { padding: 15px; margin: 0; }
        }
    </style>
</head>
<body>
    <div class="watermark-bg">DR</div>
    <div class="header-container text-center">
        <div class="merchant-name">DANYAL AUTOS</div>
        <div class="merchant-address">
            12-Butt Market, Badami Bagh, Lahore<br>
            TEL: 042-37727045 | MOB: 0304-2000274
        </div>
    </div>

    <div class="return-title">SALE RETURN RECEIPT</div>

    <div class="info-grid">
        <div class="info-row">
            <span>Return #: <strong>{{ $return->return_number }}</strong></span>
            <span>Date: <strong>{{ $return->return_date }}</strong></span>
        </div>
        <div class="info-row">
            <span>Original Order: <strong>{{ $return->order->order_number ?? 'N/A' }}</strong></span>
        </div>
        <div class="separator"></div>
        <div class="info-row">
            <span>Customer: <strong>{{ strtoupper($return->customer->name ?? 'Guest') }}</strong></span>
        </div>
        <div class="info-row">
            <span>Refund Method: <strong>{{ strtoupper(str_replace('_', ' ', $return->refund_method)) }}</strong></span>
        </div>
    </div>

    <table class="item-list">
        <thead>
            <tr>
                <th width="60%">RETURNED PRODUCT</th>
                <th width="15%" class="text-center">QTY</th>
                <th width="25%" class="text-right">REFUND</th>
            </tr>
        </thead>
        <tbody>
            @foreach($return->items as $item)
                <tr>
                    <td>
                        <span class="item-name">{{ strtoupper($item->product->title ?? 'Item') }}</span>
                        <small>Condition: {{ strtoupper($item->condition) }}</small>
                    </td>
                    <td class="text-center bold">{{ $item->quantity }}</td>
                    <td class="text-right bold">Rs.{{ number_format($item->total_price, 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="grand-total-row">
        <span>TOTAL REFUND</span>
        <span>Rs.{{ number_format($return->total_return_amount, 0) }}</span>
    </div>

    @if($return->reason)
    <div style="font-size: 10px; margin-top: 10px;">
        <strong>REASON:</strong> {{ $return->reason }}
    </div>
    @endif

    <div class="footer-note">
        RETURN PROCESSED SUCCESSFULLY
    </div>
    <div class="text-center" style="font-size: 10px; margin-top: 5px;">
        WhatsApp: 0304-2000274 | FB: /DanyalAutos
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
