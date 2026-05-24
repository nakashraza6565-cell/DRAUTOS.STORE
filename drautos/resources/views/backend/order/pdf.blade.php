<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice {{ $order->order_number ?? '' }} | Danyal Autos</title>
    <style>
        /* International Minimalist Invoice Design */
        @page { margin: 10mm; size: a4; }
        body { 
            font-family: {{ request('lang') === 'ur' ? "'Tahoma'" : "'Helvetica', 'Arial'" }}, sans-serif; 
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
        @font-face {
            font-family: 'Tahoma';
            src: url("{{ str_replace('\\', '/', public_path('revue/tahoma.ttf')) }}") format("truetype");
        }
        .watermark {
            position: fixed; top: 35%; left: 50%; transform: translate(-50%, -50%);
            font-size: 300px; color: #000; opacity: 0.04; z-index: -1000;
            font-family: 'Revue', sans-serif; pointer-events: none;
        }
    </style>
</head>
<body>
    <div id="invoice-wrapper" style="position: relative; background: #fff; padding: 20px;">
        <div class="watermark">DR</div>
    
    <table class="header-table">
        <tr>
            <td width="50%">
                <div class="company-name">{{ Helper::translatePartTitle('Danyal Autos', true) }}</div>
                <div class="company-details">
                    {{ Helper::translatePartTitle('12-BUTT MARKET BADAMI BAGH LAHORE', true) }}<br>
                    {{ Helper::translateLabel('SKU:') }} 54000 | {{ Helper::translateLabel('Phone:') }} +923042000274, 04237727045
                </div>
            </td>
            <td width="50%" style="vertical-align: bottom;">
                <div class="invoice-title">{{ Helper::translateLabel('INVOICE') }}</div>
                <div class="invoice-meta">
                    <strong>{{ Helper::translateLabel('Order #:') }}</strong> {{ $order->order_number }}<br>
                    <strong>{{ Helper::translateLabel('Date:') }}</strong> {{ $order->created_at->format('M d, Y') }}<br>
                    <strong>{{ Helper::translateLabel('Due Date:') }}</strong> {{ now()->addDays(7)->format('M d, Y') }}
                </div>
            </td>
        </tr>
    </table>
 
    <table class="info-table">
        <tr>
            <td width="35%" style="padding-right: 15px;">
                <div class="info-title">{{ Helper::translateLabel('Billed To') }}</div>
                <div class="info-content">
                    <strong>{{ Helper::translatePartTitle($order->first_name . ' ' . $order->last_name, true) }}</strong><br>
                    {{ Helper::translatePartTitle($order->address1, true) }}<br>
                    {{ Helper::translateLabel('Phone:') }} {{ $order->phone }}<br>
                    {{ Helper::translateLabel('Email:') }} {{ $order->email }}
                </div>
            </td>
            <td width="35%" style="padding-right: 15px;">
                <div class="info-title">{{ Helper::translateLabel('Shipping Information') }}</div>
                <div class="info-content">
                    {{ Helper::translateLabel('Type:') }} {{ Helper::translateLabel(strtoupper($order->order_type ?? 'courier')) }}<br>
                    @if($order->courier_company)
                        <strong>{{ Helper::translateLabel('Courier:') }}</strong> {{ Helper::translatePartTitle(strtoupper($order->courier_company), true) }}<br>
                        <strong>{{ Helper::translateLabel('Tracking:') }}</strong> {{ $order->courier_number }}
                    @else
                        {{ Helper::translateLabel('Ship To:') }} {{ Helper::translatePartTitle($order->country ?? 'Pakistan', true) }}
                    @endif
                </div>
            </td>
            <td width="30%">
                <div class="info-title">{{ Helper::translateLabel('Account Status') }}</div>
                <div class="info-content">
                    @if($order->user_id != 1)
                        {{ Helper::translateLabel('Current Balance:') }} <strong style="font-size:12px;">Rs. {{ number_format($order->user->current_balance ?? $order->total_amount, 2) }}</strong><br>
                    @endif
                    {{ Helper::translateLabel('Payment Status:') }} {{ Helper::translateLabel(strtoupper($order->payment_status)) }}
                </div>
            </td>
        </tr>
    </table>
 
    <table class="item-table">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="33%">{{ Helper::translateLabel('DESCRIPTION') }}</th>
                <th width="8%" class="text-center">{{ Helper::translateLabel('QTY') }}</th>
                <th width="13%" class="text-right">{{ Helper::translateLabel('PRICE') }}</th>
                <th width="13%" class="text-right">{{ Helper::translateLabel('DISCOUNT') }}</th>
                <th width="13%" class="text-right">{{ Helper::translateLabel('DISC. PRICE') }}</th>
                <th width="15%" class="text-right">{{ Helper::translateLabel('TOTAL') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->cart_info as $index => $cart)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <div class="item-title">{{ Helper::translatePartTitle($cart->product->title ?? ($cart->bundle->name ?? 'Item'), true) }}</div>
                    <div style="margin-top:2px;">
                    @if($cart->product && $cart->product->sku)
                        <span class="item-meta">{{ Helper::translateLabel('SKU:') }} {{ $cart->product->sku }}</span>
                    @elseif($cart->bundle && $cart->bundle->sku)
                        <span class="item-meta">{{ Helper::translateLabel('SKU:') }} {{ $cart->bundle->sku }}</span>
                    @endif
 
                    @if($cart->product && $cart->product->brand)
                        <span class="item-meta">{{ Helper::translateLabel('Brand:') }} {{ Helper::translatePartTitle($cart->product->brand->title, true) }}</span>
                    @endif
 
                    @if($cart->product && $cart->product->model)
                        <span class="item-meta">{{ Helper::translateLabel('Model:') }} {{ Helper::translatePartTitle($cart->product->model, true) }}</span>
                    @endif
                    </div>
                </td>
                <td class="text-center">{{ $cart->quantity }} <span style="font-size:8px;">{{ Helper::translateLabel(optional($cart->product)->unit ?? '') }}</span></td>
                <td class="text-right">
                    @php
                        $dbBasePrice = ($cart->product->price ?? ($cart->bundle->price ?? $cart->price));
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
                <td class="text-right">
                    {{ number_format($soldPrice, 2) }}
                </td>
                <td class="text-right" style="font-weight:bold;">{{ number_format($cart->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
 
    <div class="clearfix">
        <div class="payment-info">
            <div class="info-title">{{ Helper::translateLabel('Payment Instructions') }}</div>
            {{ Helper::translateLabel('Bank Name:') }} {{ Helper::translatePartTitle('Meezan Bank', true) }}<br>
            {{ Helper::translateLabel('Beneficiary:') }} {{ Helper::translatePartTitle('Sheikh Imtiaz ali tahir', true) }}<br>
            {{ Helper::translateLabel('Account No:') }} 0256 0103847320<br>
            <br>
            <em>{{ Helper::translateLabel('Please include the Order # with your payment.') }}</em>
        </div>
        
        <table class="totals-table">
            @php
                $gross_subtotal = 0;
                $item_discounts = 0;
                foreach($order->cart_info as $ci) {
                    $actual_price = $ci->product->price ?? ($ci->bundle->price ?? $ci->price);
                    if($actual_price > $ci->price) {
                        $gross_subtotal += ($actual_price * $ci->quantity);
                        $item_discounts += ($actual_price - $ci->price) * $ci->quantity;
                    } else {
                        $gross_subtotal += ($ci->price * $ci->quantity);
                    }
                }
            @endphp
            <tr>
                <td class="label">{{ Helper::translateLabel('Sub Total') }}</td>
                <td class="value">Rs. {{ number_format($gross_subtotal, 2) }}</td>
            </tr>
            @if($item_discounts > 0)
            <tr>
                <td class="label">{{ Helper::translateLabel('Item Discounts') }}</td>
                <td class="value">- Rs. {{ number_format($item_discounts, 2) }}</td>
            </tr>
            @endif
            @if($order->coupon > 0)
            <tr>
                <td class="label">{{ Helper::translateLabel('Coupon Discount') }}</td>
                <td class="value">- Rs. {{ number_format($order->coupon, 2) }}</td>
            </tr>
            @endif
            @if($order->shipping && $order->shipping->price > 0)
            <tr>
                <td class="label">{{ Helper::translateLabel('Shipping') }}</td>
                <td class="value">Rs. {{ number_format($order->shipping->price, 2) }}</td>
            </tr>
            @endif
            <tr class="grand-total">
                <td class="label" style="font-weight:bold;">{{ Helper::translateLabel('Grand Total') }}</td>
                <td class="value">Rs. {{ number_format($order->total_amount, 2) }}</td>
            </tr>
            @php
                $amount_paid = $order->amount_paid ?? 0;
                $current_bill_unpaid = $order->total_amount - $amount_paid;
                
                if($order->user_id == 1) {
                    $previous_balance = 0;
                    $final_balance_due = $current_bill_unpaid;
                } else {
                    // Get Current Ledger Balance
                    $current_user_balance = $order->user->current_balance ?? 0;
                    
                    // Check if this order is already recorded in the ledger
                    $is_in_ledger = \App\Models\CustomerLedger::where('reference_id', $order->id)->where('category', 'order')->exists();
                    
                    // If it's in the ledger, the current_balance already includes this bill.
                    // We subtract the unpaid portion to find what the balance was BEFORE this bill.
                    if($is_in_ledger) {
                        $previous_balance = $current_user_balance - $current_bill_unpaid;
                    } else {
                        $previous_balance = $current_user_balance;
                    }
                    
                    $final_balance_due = $previous_balance + $current_bill_unpaid;
                }
            @endphp
            <tr>
                <td class="label">{{ Helper::translateLabel('Current Bill Total') }}</td>
                <td class="value">Rs. {{ number_format($order->total_amount, 2) }}</td>
            </tr>
            <tr>
                <td class="label">{{ Helper::translateLabel('Amount Paid') }}</td>
                <td class="value">Rs. {{ number_format($amount_paid, 2) }}</td>
            </tr>
            <tr>
                <td class="label">{{ Helper::translateLabel('Previous Balance') }}</td>
                <td class="value">Rs. {{ number_format($previous_balance, 2) }}</td>
            </tr>
            <tr class="grand-total">
                <td class="label" style="font-weight:bold; color:#d32f2f;">{{ Helper::translateLabel('Balance Due') }}</td>
                <td class="value" style="color:#d32f2f;">Rs. {{ number_format($final_balance_due, 2) }}</td>
            </tr>
        </table>
    </div>
 
    <div class="footer">
        <strong>{{ Helper::translateLabel('THANK YOU FOR YOUR BUSINESS!') }}</strong><br>
        {{ Helper::translateLabel('This is a computer generated document. | Danyal Autos') }} &copy; {{ date('Y') }}
    </div>
    </div>
</body>
</html>
