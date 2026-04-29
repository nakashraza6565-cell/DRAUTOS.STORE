<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice {{ $order->order_number ?? '' }} | Danyal Autos</title>
    <style>
        /* International Minimalist Invoice Design */
        @page { margin: 10mm; size: a4; }
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            margin: 0; padding: 0; 
            color: #111; 
            line-height: 1.2; 
            font-size: 11px; 
        }
        table { width: 100%; border-collapse: collapse; }
        td, th { vertical-align: top; }
        
        .header-table { margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .company-name { font-size: 24px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .company-details { font-size: 10px; color: #555; margin-top: 4px; }
        
        .invoice-title { font-size: 28px; font-weight: bold; text-align: right; text-transform: uppercase; }
        .invoice-meta { text-align: right; font-size: 10px; color: #333; margin-top: 4px; }
        
        .info-table { margin-bottom: 15px; }
        .info-title { font-size: 10px; font-weight: bold; color: #777; text-transform: uppercase; border-bottom: 1px solid #ddd; padding-bottom: 2px; margin-bottom: 4px; }
        .info-content { font-size: 11px; line-height: 1.3; }
        
        .item-table { margin-bottom: 15px; }
        .item-table th { 
            background-color: #f4f4f4; 
            border-top: 1px solid #000; 
            border-bottom: 1px solid #000; 
            padding: 6px 4px; 
            font-size: 9px; 
            text-transform: uppercase; 
            text-align: left; 
        }
        .item-table td { padding: 5px 4px; border-bottom: 1px solid #eee; }
        .item-table th.text-right, .item-table td.text-right { text-align: right; }
        .item-table th.text-center, .item-table td.text-center { text-align: center; }
        
        .item-title { font-weight: bold; font-size: 11px; }
        .item-meta { font-size: 9px; color: #666; display: inline-block; margin-right: 6px; }
        
        .totals-wrapper { width: 100%; }
        .payment-info { width: 50%; float: left; font-size: 10px; color: #555; padding-right: 20px; }
        .totals-table { width: 45%; float: right; }
        .totals-table td { padding: 4px 0; border-bottom: 1px solid #f9f9f9; }
        .totals-table td.label { color: #555; font-size: 10px; text-transform: uppercase; }
        .totals-table td.value { text-align: right; font-weight: bold; }
        .grand-total { border-top: 2px solid #000; border-bottom: 2px solid #000; background: #f9f9f9; }
        .grand-total td { padding: 6px 0; font-size: 14px !important; }
        
        .footer { clear: both; margin-top: 30px; text-align: center; font-size: 9px; color: #888; border-top: 1px dashed #ddd; padding-top: 10px; }
        
        .clearfix::after { content: ""; clear: both; display: table; }

        @font-face {
            font-family: 'Revue';
            src: url("{{ str_replace('\\', '/', public_path('revue/reve.ttf')) }}") format("truetype");
        }
        .watermark {
            position: fixed; top: 35%; left: 50%; transform: translate(-50%, -50%);
            font-size: 300px; color: #000; opacity: 0.04; z-index: -1000;
            font-family: 'Revue', sans-serif; pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="watermark">DR</div>
    
    <table class="header-table">
        <tr>
            <td width="50%">
                <div class="company-name">Danyal Autos</div>
                <div class="company-details">
                    12-BUTT MARKET BADAMI BAGH LAHORE<br>
                    Zip Code: 54000 | Contact: +923042000274, 04237727045
                </div>
            </td>
            <td width="50%" style="vertical-align: bottom;">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-meta">
                    <strong>Order #:</strong> {{ $order->order_number }}<br>
                    <strong>Date:</strong> {{ $order->created_at->format('M d, Y') }}<br>
                    <strong>Due Date:</strong> {{ now()->addDays(7)->format('M d, Y') }}
                </div>
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td width="35%" style="padding-right: 15px;">
                <div class="info-title">Billed To</div>
                <div class="info-content">
                    <strong>{{ strtoupper($order->first_name) }} {{ strtoupper($order->last_name) }}</strong><br>
                    {{ $order->address1 }}<br>
                    Phone: {{ $order->phone }}<br>
                    Email: {{ $order->email }}
                </div>
            </td>
            <td width="35%" style="padding-right: 15px;">
                <div class="info-title">Shipping Information</div>
                <div class="info-content">
                    Type: {{ strtoupper($order->order_type ?? 'courier') }}<br>
                    @if($order->courier_company)
                        <strong>Courier:</strong> {{ strtoupper($order->courier_company) }}<br>
                        <strong>Tracking:</strong> {{ $order->courier_number }}
                    @else
                        Ship To: {{ $order->country ?? 'Pakistan' }}
                    @endif
                </div>
            </td>
            <td width="30%">
                <div class="info-title">Account Status</div>
                <div class="info-content">
                    Current Balance: <strong style="font-size:12px;">Rs. {{ number_format($order->user->current_balance ?? $order->total_amount, 2) }}</strong><br>
                    Payment Status: {{ strtoupper($order->payment_status) }}
                </div>
            </td>
        </tr>
    </table>

    <table class="item-table">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="45%">DESCRIPTION</th>
                <th width="8%" class="text-center">QTY</th>
                <th width="14%" class="text-right">PRICE</th>
                <th width="14%" class="text-right">DISCOUNT</th>
                <th width="14%" class="text-right">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->cart_info as $index => $cart)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <div class="item-title">{{ $cart->product->title ?? ($cart->bundle->name ?? 'Item') }}</div>
                    <div style="margin-top:2px;">
                    @if($cart->product && $cart->product->sku)
                        <span class="item-meta">SKU: {{ $cart->product->sku }}</span>
                    @endif
                    @if($cart->product && $cart->product->brand)
                        <span class="item-meta">Brand: {{ $cart->product->brand->title }}</span>
                    @endif
                    @if($cart->product && $cart->product->model)
                        <span class="item-meta">Model: {{ $cart->product->model }}</span>
                    @endif
                    </div>
                </td>
                <td class="text-center">{{ $cart->quantity }} <span style="font-size:8px;">{{ $cart->product->unit ?? '' }}</span></td>
                <td class="text-right">
                    @php
                        $dbBasePrice = $cart->product->price ?? $cart->price;
                        $soldPrice = $cart->price;
                        $discount = 0;
                        
                        if ($soldPrice >= $dbBasePrice) {
                            $displayBasePrice = $soldPrice;
                        } else {
                            $displayBasePrice = $dbBasePrice;
                            $discount = ($displayBasePrice - $soldPrice) * $cart->quantity;
                        }
                    @endphp
                    {{ number_format($displayBasePrice, 2) }}
                </td>
                <td class="text-right">
                    {{ $discount > 0 ? number_format($discount, 2) : '-' }}
                </td>
                <td class="text-right" style="font-weight:bold;">{{ number_format($cart->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="clearfix">
        <div class="payment-info">
            <div class="info-title">Payment Instructions</div>
            Bank Name: Meezan Bank<br>
            Beneficiary: Sheikh Imtiaz ali tahir<br>
            Account No: 0256 0103847320<br>
            <br>
            <em>Please include the Order # with your payment.</em>
        </div>
        
            @php
                $gross_subtotal = 0;
                $item_discounts = 0;
                foreach($order->cart_info as $ci) {
                    $actual_price = $ci->product->price ?? $ci->price;
                    if($actual_price > $ci->price) {
                        $gross_subtotal += ($actual_price * $ci->quantity);
                        $item_discounts += ($actual_price - $ci->price) * $ci->quantity;
                    } else {
                        $gross_subtotal += ($ci->price * $ci->quantity);
                    }
                }
            @endphp
            <tr>
                <td class="label">Sub Total</td>
                <td class="value">Rs. {{ number_format($gross_subtotal, 2) }}</td>
            </tr>
            @if($item_discounts > 0)
            <tr>
                <td class="label">Item Discounts</td>
                <td class="value">- Rs. {{ number_format($item_discounts, 2) }}</td>
            </tr>
            @endif
            @if($order->coupon > 0)
            <tr>
                <td class="label">Coupon Discount</td>
                <td class="value">- Rs. {{ number_format($order->coupon, 2) }}</td>
            </tr>
            @endif
            @if($order->shipping && $order->shipping->price > 0)
            <tr>
                <td class="label">Shipping</td>
                <td class="value">Rs. {{ number_format($order->shipping->price, 2) }}</td>
            </tr>
            @endif
            <tr class="grand-total">
                <td class="label" style="font-weight:bold;">Grand Total</td>
                <td class="value">Rs. {{ number_format($order->total_amount, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Amount Paid</td>
                @php
                    $requested_pending = 0;
                    $reminder = \App\Models\PaymentReminder::where('reference_number', $order->order_number)->first();
                    if ($reminder) {
                        $requested_pending = $reminder->amount - $reminder->paid_amount;
                    }
                @endphp
                <td class="value">Rs. {{ number_format($order->total_amount - $requested_pending, 2) }}</td>
            </tr>
            <tr>
                <td class="label" style="color:#d32f2f;">Balance Due</td>
                <td class="value" style="color:#d32f2f;">Rs. {{ number_format($requested_pending, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <strong>THANK YOU FOR YOUR BUSINESS!</strong><br>
        This is a computer generated document. | Danyal Autos &copy; {{ date('Y') }}
    </div>
</body>
</html>
