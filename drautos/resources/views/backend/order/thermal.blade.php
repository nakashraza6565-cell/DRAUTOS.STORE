<!DOCTYPE html>
<html>
<head>
    <title>Receipt #{{ $order->order_number }}</title>
    <style>
        * { box-sizing: border-box; }
        @page { margin: 0; }
        @font-face {
            font-family: 'Revue';
            src: url('/revue/reve.ttf?v=1.1') format("truetype");
            font-weight: normal;
            font-style: normal;
        }
        body {
            font-family: 'Revue', 'Helvetica', 'Arial', sans-serif;
            width: 80mm;
            margin: 0 auto;
            padding: 40px;
            font-size: 13px;
            color: #000;
            line-height: 1.3;
            font-weight: 700;
            position: relative;
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
            top: 45%;
            left: 50%;
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
        $items = $order->cart_info;
        $chunks = $items->chunk(15);
        $totalChunks = $chunks->count();
    @endphp

    @foreach($chunks as $pageIndex => $chunk)
        <div class="watermark-bg">DR</div>
        <div class="header-container text-center">
            <div class="merchant-name">DANYAL AUTOS</div>
            <div class="merchant-address">
                12-Butt Market, Badami Bagh, Lahore<br>
                TEL: 042-37727045 | MOB: 0304-2000274
            </div>
        </div>

        <div class="info-grid">
            <div class="info-row">
                <span>Receipt: <strong>{{ $order->order_number }}</strong></span>
                <span>Date: <strong>{{ $order->created_at->format('d/m/y H:i') }}</strong></span>
            </div>
            <div class="info-row">
                <span>Cashier: <strong>{{ strtoupper(Auth::user()->name ?? 'Admin') }}</strong></span>
                <span>Type: <strong>POS SALE</strong></span>
            </div>
            <div class="separator"></div>
            <div class="info-row">
                <span>Customer: <strong>{{ strtoupper($order->first_name) }} {{ strtoupper($order->last_name) }}</strong></span>
            </div>
            @if($order->phone)
            <div class="info-row">
                <span>Contact: <strong>{{ $order->phone }}</strong></span>
            </div>
            @endif
        </div>

        <table class="item-list">
            <thead>
                <tr>
                    <th width="50%">PRODUCT DETAILS</th>
                    <th width="20%" class="text-center">QTY</th>
                    <th width="30%" class="text-right">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($chunk as $item)
                    <tr>
                        <td>
                            <span class="item-name">
                                {{ strtoupper($item->product->title ?? ($item->bundle->name ?? 'Item')) }}
                                @if($item->product && $item->product->brand)
                                    ({{ strtoupper($item->product->brand->title) }})
                                @endif
                            </span>
                            <span class="item-details">
                                @if($item->product)
                                    @if($item->product->model) MODEL: {{ $item->product->model }} @endif
                                    @if($item->product->sku) | SKU: {{ $item->product->sku }} @endif
                                @elseif($item->bundle && $item->bundle->sku)
                                    SKU: {{ $item->bundle->sku }}
                                @endif
                            </span>
                            <span class="item-details"><br>UNIT PRICE: {{ number_format($item->price, 0) }}</span>
                        </td>
                        <td class="text-center bold" style="font-size: 16px;">
                            {{ $item->quantity }}<span style="font-size: 10px; margin-left: 2px;">{{ strtoupper($item->product->unit ?? '') }}</span>
                        </td>
                        <td class="text-right bold">Rs.{{ number_format($item->amount, 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($pageIndex == $totalChunks - 1)
            <div class="separator"></div>
            <div class="totals-block">
                <div class="total-row">
                    <span>Subtotal</span>
                    <span>Rs.{{ number_format($order->sub_total, 0) }}</span>
                </div>
                @if($order->coupon > 0)
                <div class="total-row">
                    <span>Discount Applied</span>
                    <span>-Rs.{{ number_format($order->coupon, 0) }}</span>
                </div>
                @endif
                <div class="grand-total-row">
                    <span>NET AMOUNT</span>
                    <span>Rs.{{ number_format($order->total_amount, 0) }}</span>
                </div>
            </div>

            @php
                $amount_paid = $order->amount_paid ?? 0;
                $current_bill_unpaid = $order->total_amount - $amount_paid;
                
                if($order->user_id == 1) {
                    $previous_balance = 0;
                    $final_balance_due = $current_bill_unpaid;
                } else {
                    $current_user_balance = $order->user->current_balance ?? 0;
                    
                    // Check if this order is already recorded in the ledger
                    $is_in_ledger = \App\Models\CustomerLedger::where('reference_id', $order->id)->where('category', 'order')->exists();

                    if($is_in_ledger) {
                        $previous_balance = $current_user_balance - $current_bill_unpaid;
                    } else {
                        $previous_balance = $current_user_balance;
                    }
                    $final_balance_due = $previous_balance + $current_bill_unpaid;
                }
            @endphp

            <div class="info-grid" style="margin-top: 10px;">
                <div class="info-row">
                    <span>Payment Method:</span>
                    <span class="bold">{{ strtoupper($order->payment_method ?? 'CASH') }}</span>
                </div>
                <div class="info-row">
                    <span>Amount Received:</span>
                    <span class="bold">Rs.{{ number_format($amount_paid, 0) }}</span>
                </div>
                <div class="info-row">
                    <span>Previous Balance:</span>
                    <span class="bold">Rs.{{ number_format($previous_balance, 0) }}</span>
                </div>
                <div class="info-row" style="font-size: 16px; border-top: 1px solid #000; padding-top: 5px; margin-top: 5px;">
                    <span>BALANCE DUE:</span>
                    <span class="bold">Rs.{{ number_format($final_balance_due, 0) }}</span>
                </div>
                @if($order->due_date)
                <div class="info-row" style="color: #d00; margin-top: 5px;">
                    <span>PAYMENT DUE BY:</span>
                    <span class="bold">{{ date('d/m/y', strtotime($order->due_date)) }}</span>
                </div>
                @endif
            </div>

            @php
                $secureToken = hash_hmac('sha256', $order->id . $order->order_number, config('app.key'));
                $invoiceUrl = route('order.pdf', ['id' => $order->id, 'token' => $secureToken]);
            @endphp
            <div class="text-center" style="margin: 15px 0; padding: 10px; background: #fff; display: flex; flex-direction: column; align-items: center; justify-content: center; border: 1px dashed #000;">
                <div style="font-size: 10px; font-weight: bold; margin-bottom: 5px; text-transform: uppercase;">Scan to View PDF Invoice</div>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($invoiceUrl) }}" alt="Invoice QR" style="width: 120px; height: 120px; display: block; margin: 0 auto;">
            </div>

            <div class="footer-note">
                THANK YOU FOR YOUR BUSINESS!
            </div>
            <div class="social-info">
                WhatsApp: 0304-2000274 | FB: /DanyalAutos
            </div>
        @else
            <div class="text-center bold" style="padding: 10px; border: 2px solid #000; margin: 15px 0;">
                --- CONTINUED ON PAGE {{ $pageIndex + 2 }} ---
            </div>
        @endif
    @endforeach

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
