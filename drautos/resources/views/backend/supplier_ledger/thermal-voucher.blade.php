<!DOCTYPE html>
<html>
<head>
    <title>Supplier Voucher #{{ $transaction->id }}</title>
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

        .voucher-details {
            width: 100%;
            margin: 15px 0;
            font-size: 14px;
        }
        .voucher-details td {
            padding: 5px 0;
        }

        .amount-display {
            font-size: 24px;
            font-weight: 900;
            text-align: center;
            margin: 20px 0;
            border: 2px solid #000;
            padding: 10px;
            border-radius: 8px;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 11px;
            text-transform: uppercase;
            border-top: 1px dashed #000;
            padding-top: 10px;
        }
        .signature-line {
            margin-top: 40px;
            border-top: 1px solid #000;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
            padding-top: 5px;
            text-align: center;
            font-size: 10px;
        }
    </style>
</head>
<body onload="window.print()">

    @php
        $settings = \App\Models\Settings::first();
    @endphp

    <div class="header-container text-center">
        <div class="watermark-bg">DA</div>
        <div class="merchant-name">{!! strip_tags(str_replace('&nbsp;', ' ', $settings->short_des ?? 'Danyal Autos')) !!}</div>
        <div class="merchant-address">{{ $settings->address ?? 'Liaquat Pur, RYK' }}</div>
        <div class="merchant-address">{{ $settings->phone }}</div>
    </div>

    <div class="text-center" style="font-size: 16px; margin-bottom: 15px; text-decoration: underline; text-transform: uppercase;">
        @if($transaction->category == 'payment' && $transaction->type == 'debit')
            PAYMENT ISSUED
        @elseif($transaction->category == 'return' && $transaction->type == 'credit')
            PURCHASE RETURN
        @else
            SUPPLIER VOUCHER
        @endif
    </div>

    <div class="info-grid">
        <div class="info-row">
            <span>Voucher No:</span>
            <span>#{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}</span>
        </div>
        <div class="info-row">
            <span>Date:</span>
            <span>{{ $transaction->transaction_date->format('d M Y') }}</span>
        </div>
        <div class="info-row">
            <span>Time:</span>
            <span>{{ $transaction->created_at->format('h:i A') }}</span>
        </div>
        <div class="info-row" style="margin-top: 5px;">
            <span>Supplier:</span>
            <span class="text-right">{{ $transaction->supplier->name }}<br>{{ $transaction->supplier->company_name ? $transaction->supplier->company_name . '<br>' : '' }}{{ $transaction->supplier->phone }}</span>
        </div>
    </div>

    <div class="amount-display">
        Rs. {{ number_format($transaction->amount, 2) }}
    </div>

    <table class="voucher-details">
        <tr>
            <td style="width: 40%; font-size: 12px; color: #555;">Type:</td>
            <td class="text-right text-uppercase">{{ $transaction->category }} ({{ $transaction->type }})</td>
        </tr>
        <tr>
            <td style="width: 40%; font-size: 12px; color: #555;">Description:</td>
            <td class="text-right">{{ $transaction->description }}</td>
        </tr>
    </table>

    @if($incoming && count($incoming->items) > 0)
        <div class="separator"></div>
        <div style="font-size: 11px; margin-bottom: 5px; text-transform: uppercase; text-decoration: underline;">Items Detail:</div>
        <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
            <thead>
                <tr style="border-bottom: 1px solid #000;">
                    <th class="text-left" style="padding: 2px 0;">Item</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($incoming->items as $item)
                    <tr style="border-bottom: 1px dashed #eee;">
                        <td style="padding: 4px 0;">
                            <div>{{ $item->product->title }}</div>
                            <small style="color: #666;">{{ $item->product->sku }}</small>
                        </td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">{{ number_format($item->total_cost, 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="separator"></div>
    @endif

    <table class="voucher-details" style="margin-top: 5px;">
        <tr>
            <td style="width: 40%; font-size: 12px; color: #555;">New Payable:</td>
            <td class="text-right" style="padding-top: 15px;">Rs. {{ number_format($transaction->balance, 2) }}</td>
        </tr>
    </table>

    <div class="signature-line">
        SIGNATURE
    </div>

    <div class="footer">
        <div>Thank you for your business!</div>
        <div style="font-size: 9px; margin-top: 5px; opacity: 0.8;">Software by Cellcity</div>
    </div>

</body>
</html>
