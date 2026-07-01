
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice {{ $order->order_number ?? '' }} | Danyal Autos</title>
    <style>
        /* International Minimalist Invoice Design */
        @page { margin: 4mm; size: a5; }
        body { 
            font-family: {{ request('lang') === 'ur' ? "'DejaVu Sans'" : "'Helvetica', 'Arial'" }}, sans-serif; 
            direction: {{ request('lang') === 'ur' ? 'rtl' : 'ltr' }};
            text-align: {{ request('lang') === 'ur' ? 'right' : 'left' }};
            margin: 0; padding: 0; 
            color: #111; 
            line-height: 1.2; 
            font-size: 11px; 
        }
        table { width: 100%; border-collapse: collapse; }
        td, th { vertical-align: top; }
        
        .header-table { margin-bottom: 8px; border-bottom: 2px solid #000; padding-bottom: 4px; }
        .company-name { font-size: 18px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .company-details { font-size: 9px; color: #555; margin-top: 2px; }
        
        .invoice-title { 
            font-size: 20px; 
            font-weight: bold; 
            text-align: {{ request('lang') === 'ur' ? 'left' : 'right' }}; 
            text-transform: uppercase; 
        }
        .invoice-meta { 
            text-align: {{ request('lang') === 'ur' ? 'left' : 'right' }}; 
            font-size: 9px; 
            color: #333; 
            margin-top: 2px; 
        }
        
        .info-table { margin-bottom: 8px; }
        .info-title { font-size: 9px; font-weight: bold; color: #777; text-transform: uppercase; border-bottom: 1px solid #ddd; padding-bottom: 1px; margin-bottom: 2px; }
        .info-content { font-size: 10px; line-height: 1.2; }
        
        .item-table { margin-bottom: 8px; }
        .item-table th { 
            background-color: #f4f4f4; 
            border-top: 1px solid #000; 
            border-bottom: 1px solid #000; 
            padding: 4px 4px; 
            font-size: 9px; 
            text-transform: uppercase; 
            text-align: {{ request('lang') === 'ur' ? 'right' : 'left' }}; 
        }
        .item-table td { padding: 3px 4px; border-bottom: 1px solid #eee; text-align: {{ request('lang') === 'ur' ? 'right' : 'left' }}; }
        .item-table th.text-right, .item-table td.text-right { text-align: {{ request('lang') === 'ur' ? 'left' : 'right' }}; }
        .item-table th.text-center, .item-table td.text-center { text-align: center; }
        
        .item-title { font-weight: bold; font-size: 11px; }
        .item-meta { font-size: 9px; color: #666; display: inline-block; margin-right: 6px; }
        
        .totals-wrapper { width: 100%; }
        .payment-info { 
            width: 50%; 
            float: {{ request('lang') === 'ur' ? 'right' : 'left' }}; 
            font-size: 10px; 
            color: #555; 
            padding-{{ request('lang') === 'ur' ? 'left' : 'right' }}: 20px; 
        }
        .totals-table { 
            width: 45%; 
            float: {{ request('lang') === 'ur' ? 'left' : 'right' }}; 
        }
        .totals-table td { padding: 4px 0; border-bottom: 1px solid #f9f9f9; }
        .totals-table td.label { 
            color: #555; 
            font-size: 10px; 
            text-transform: uppercase; 
            text-align: {{ request('lang') === 'ur' ? 'right' : 'left' }};
        }
        .totals-table td.value { 
            text-align: {{ request('lang') === 'ur' ? 'left' : 'right' }}; 
            font-weight: bold; 
        }
        .grand-total { border-top: 2px solid #000; border-bottom: 2px solid #000; background: #f9f9f9; }
        .grand-total td { padding: 6px 0; font-size: 14px !important; }
        
        .footer { clear: both; margin-top: 30px; text-align: center; font-size: 9px; color: #888; border-top: 1px dashed #ddd; padding-top: 10px; }
        
        .clearfix::after { content: ""; clear: both; display: table; }
 
        @font-face {
            font-family: 'Revue';
            src: url("{{ str_replace('\\', '/', public_path('revue/reve.ttf')) }}") format("truetype");
        }
        @font-face {
            font-family: 'TahomaUrdu';
            src: url("{{ str_replace('\\', '/', public_path('revue/tahoma.ttf')) }}") format("truetype");
        }
        
    </style>
</head>
<body>
    <div id="invoice-wrapper" style="position: relative; background: #fff; padding: 5px;">
        @include('backend.layouts.watermark', ['type' => 'pdf'])
    
    <table class="header-table">
        <tr>
            <td width="50%">
                <div class="company-name">{{ Helper::translateLabel('Danyal Autos') }}</div>
                <div class="company-details">
                    {{ Helper::translateLabel('12-BUTT MARKET BADAMI BAGH LAHORE') }}<br>
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
            <td width="35%" style="padding-{{ request('lang') === 'ur' ? 'left' : 'right' }}: 15px;">
                <div class="info-title">{{ Helper::translateLabel('Billed To') }}</div>
                <div class="info-content">
                    <strong>{{ $order->first_name . ' ' . $order->last_name }}</strong><br>
                    @if($order->address1 && strtolower(trim($order->address1)) !== 'pos counter')
                        {{ $order->address1 }}<br>
                    @endif
                    {{ Helper::translateLabel('Phone:') }} {{ $order->phone }}
                    @if($order->email && strpos($order->email, '@local.com') === false)
                        <br>{{ Helper::translateLabel('Email:') }} {{ $order->email }}
                    @endif
                </div>
            </td>
            <td width="35%" style="padding-{{ request('lang') === 'ur' ? 'left' : 'right' }}: 15px;">
                <div class="info-title">{{ Helper::translateLabel('Shipping Information') }}</div>
                <div class="info-content">
                    {{ Helper::translateLabel('Type:') }} {{ Helper::translateLabel(strtoupper($order->order_type ?? 'courier')) }}<br>
                    @if($order->courier_company)
                        <strong>{{ Helper::translateLabel('Courier:') }}</strong> {{ strtoupper($order->courier_company) }}<br>
                        <strong>{{ Helper::translateLabel('Tracking:') }}</strong> {{ $order->courier_number }}
                    @else
                        {{ Helper::translateLabel('Ship To:') }} {{ $order->country ?? 'Pakistan' }}
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
                <th width="4%">#</th>
                <th width="31%">{{ Helper::translateLabel('DESCRIPTION') }}</th>
                <th width="8%" class="text-center">{{ Helper::translateLabel('QTY') }}</th>
                <th width="12%" class="text-right">{{ Helper::translateLabel('PRICE') }}</th>
                <th width="14%" class="text-right">{{ Helper::translateLabel('DISCOUNT') }}</th>
                <th width="16%" class="text-right" style="white-space: nowrap;">{{ Helper::translateLabel('DISC. PRICE') }}</th>
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
                        <span class="item-meta">{{ Helper::translateLabel('Brand:') }} {{ $cart->product->brand->title }}</span>
                    @endif
 
                    @if($cart->product && $cart->product->model)
                        <span class="item-meta">{{ Helper::translateLabel('Model:') }} {{ $cart->product->model }}</span>
                    @endif
                    </div>

                    @php
                        $returnedQty = 0;
                        $pendingReturnQty = 0;
                        if ($cart->product_id) {
                            $returnedQty = \App\Models\SaleReturnItem::where('order_id', $order->id)
                                ->where('product_id', $cart->product_id)
                                ->whereHas('saleReturn', function ($q) {
                                    $q->where('status', 'approved');
                                })
                                ->sum('quantity');

                            $pendingReturnQty = \App\Models\SaleReturnItem::where('order_id', $order->id)
                                ->where('product_id', $cart->product_id)
                                ->whereHas('saleReturn', function ($q) {
                                    $q->where('status', 'pending');
                                })
                                ->sum('quantity');
                        }
                    @endphp
                    @if($returnedQty > 0)
                        <div style="margin-top: 4px;">
                            <span style="color: #d9534f; font-weight: bold; font-size: 9px; background-color: #fcebeb; padding: 1px 4px; border: 1px solid #d9534f; border-radius: 2px; display: inline-block;">
                                {{ Helper::translateLabel('Returned:') }} {{ $returnedQty }}
                            </span>
                        </div>
                    @endif
                    @if($pendingReturnQty > 0)
                        <div style="margin-top: 4px;">
                            <span style="color: #f0ad4e; font-weight: bold; font-size: 9px; background-color: #fcf8e3; padding: 1px 4px; border: 1px solid #f0ad4e; border-radius: 2px; display: inline-block;">
                                {{ Helper::translateLabel('Pending Return:') }} {{ $pendingReturnQty }}
                            </span>
                        </div>
                    @endif
                </td>
                <td class="text-center">{{ $cart->quantity }} <span style="font-size:8px;">{{ optional($cart->product)->unit ?? '' }}</span></td>
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
 
    <table style="width: 100%; border: none; border-collapse: collapse; margin-top: 10px; direction: {{ request('lang') === 'ur' ? 'rtl' : 'ltr' }};">
        <tr>
            <!-- Left/Right depending on direction: Empty space to push totals to the right -->
            <td style="width: 55%; border: none;"></td>
            <!-- Left/Right depending on direction: Totals Table -->
            <td style="width: 45%; vertical-align: top; border: none; padding: 0;">
                <table class="totals-table" style="width: 100%; float: none; margin: 0;">
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
                            $current_user_balance = $order->user->current_balance ?? 0;
                            $is_in_ledger = \App\Models\CustomerLedger::where('reference_id', $order->id)->where('category', 'order')->exists();
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
            </td>
        </tr>
    </table>
    <div class="footer">
        <strong>{{ Helper::translateLabel('THANK YOU FOR YOUR BUSINESS!') }}</strong><br>
        {{ Helper::translateLabel('This is a computer generated document. | Danyal Autos') }} &copy; {{ date('Y') }}
    </div>
    </div>

    @php
        $settings = \App\Models\Settings::first();
    @endphp

    <!-- Page Break for Backpage -->
    <div style="page-break-before: always;"></div>
    
    <div id="backpage-wrapper" style="position: relative; background: #fff; padding: 10px; font-family: 'Helvetica', 'Arial', sans-serif;">
        <!-- Branding Header -->
        <table style="width: 100%; background: #ffffff; border-bottom: 2px solid #062038; padding: 8px 0px; margin-bottom: 12px; color: #333; border-collapse: collapse;">
            <tr>
                <td style="width: 55%; vertical-align: middle;">
                    <table style="border: none; border-collapse: collapse; width: 100%;">
                        <tr>
                            <td style="width: 55px; vertical-align: middle; border: none; padding: 0;">
                                <!-- DR Logo SVG -->
                                <svg width="50" height="42" viewBox="0 0 75 65" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M 22.74 58.73 L 13.17 58.73 L 13.17 25.29 L 24.94 25.29 L 24.94 48.61 C 28.98 48.47 32.29 46.74 34.87 43.44 C 37.46 40.14 38.75 36.49 38.75 32.5 C 38.75 29.89 38.25 26.83 C 36.27 25.66 35.19 24.87 34.02 24.47 C 33.25 24.21 32.51 24.06 31.79 24.03 C 31.08 23.99 30.5 23.97 30.06 23.97 L 10.97 23.92 L 13.72 13.8 L 32.81 13.8 C 39.52 13.83 44.19 15.65 46.83 19.24 C 49.47 22.84 50.79 26.91 50.79 31.45 C 50.79 39.92 47.99 46.59 42.38 51.45 C 36.77 56.3 30.22 58.73 22.74 58.73 " fill="#062038"/>
                                    <path d="M 36.48 20.54 L 56.18 20.54 C 60.18 20.58 63.08 21.43 64.9 23.11 C 66.72 24.78 67.86 26.64 68.33 28.68 C 68.39 29.08 68.45 29.5 68.5 29.94 Q 68.55 30.37 68.58 31.19 C 68.58 34.16 67.71 36.67 65.98 38.73 C 64.24 40.79 62.19 42.57 59.83 44.07 C 61.39 47.2 63.18 50.21 65.2 53.11 C 67.22 56 69.38 58.76 71.68 61.39 L 62.73 67.59 C 59.43 63.86 56.49 59.84 53.93 55.53 C 51.36 51.22 49.06 46.79 47.03 42.23 C 47.53 41.83 48.14 41.38 48.88 40.88 C 49.61 40.38 50.36 39.85 51.13 39.28 C 52.46 38.28 53.68 37.19 54.8 36.01 C 55.92 34.83 56.48 33.62 56.48 32.39 C 56.48 31.43 56.13 30.78 55.45 30.44 C 54.77 30.11 54.03 29.93 53.23 29.89 C 52.96 29.86 52.7 29.84 52.45 29.84 C 52.2 29.84 51.96 29.84 51.73 29.84 C 51.66 29.84 51.59 29.84 51.53 29.84 C 51.46 29.84 51.39 29.84 51.33 29.84 L 33.98 29.79 L 36.48 20.54 Z M 46.68 31.04 L 46.68 61.39 L 35.98 61.39 L 35.98 31.04 L 46.68 31.04 Z" fill="#a0b2c6" stroke="#062038" stroke-width="1.5" stroke-linejoin="round"/>
                                </svg>
                            </td>
                            <td style="vertical-align: middle; border: none; padding-left: 8px; color: #062038;">
                                <div style="font-size: 15px; font-weight: bold; letter-spacing: 0.5px;">{{ strtoupper($settings->title ?? 'Danyal Autos') }}</div>
                                <div style="font-size: 8px; color: #a0b2c6; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-top: 1px;">PREMIUM TRUCK PARTS B2B</div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width: 45%; text-align: right; font-size: 8.5px; vertical-align: middle; line-height: 1.3; color: #333;">
                    Phone: {{ $settings->phone ?? '+923042000274' }}<br>
                    Email: {{ $settings->email ?? 'info@drautos.store' }}<br>
                    Address: {{ $settings->address ?? 'Badami Bagh, Lahore' }}
                </td>
            </tr>
        </table>
    
        <!-- QR Codes Section (Two Columns) -->
        <table style="width: 100%; border: none; border-collapse: collapse; margin-bottom: 12px;">
            <tr>
                <!-- JazzCash QR Code -->
                <td style="width: 50%; text-align: center; vertical-align: top; border: none; padding: 4px;">
                    <div style="font-size: 9.5px; font-weight: bold; color: #062038; margin-bottom: 4px; text-transform: uppercase;">
                        JazzCash / Raast Payment<br>
                        <span style="font-size: 8px; color: #555; font-weight: normal; font-family: 'TahomaUrdu', sans-serif;">جاز کیش / راست ادائیگی</span>
                    </div>
                    @php
                        $jazzcashPath = public_path('backend/img/jazzcash_qr.jpg');
                        $base64Jazz = '';
                        if (file_exists($jazzcashPath)) {
                            $base64Jazz = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($jazzcashPath));
                        }
                    @endphp
                    @if($base64Jazz)
                        <img src="{{ $base64Jazz }}" style="width: 110px; height: auto; border: 1px solid #ddd; padding: 2px; border-radius: 4px;" alt="JazzCash QR">
                    @else
                        <div style="font-size: 8px; color: red;">QR Image Missing</div>
                    @endif
                </td>
    
                <!-- WhatsApp Contact QR Code -->
                <td style="width: 50%; text-align: center; vertical-align: top; border: none; padding: 4px;">
                    <div style="font-size: 9.5px; font-weight: bold; color: #062038; margin-bottom: 4px; text-transform: uppercase;">
                        WhatsApp Support<br>
                        <span style="font-size: 8px; color: #555; font-weight: normal; font-family: 'TahomaUrdu', sans-serif;">واٹس ایپ رابطہ (+923042000274)</span>
                    </div>
                    @php
                        $whatsappPath = public_path('backend/img/whatsapp_qr.png');
                        $base64WA = '';
                        if (file_exists($whatsappPath)) {
                            $base64WA = 'data:image/png;base64,' . base64_encode(file_get_contents($whatsappPath));
                        }
                    @endphp
                    @if($base64WA)
                        <img src="{{ $base64WA }}" style="width: 110px; height: auto; border: 1px solid #ddd; padding: 2px; border-radius: 4px;" alt="WhatsApp QR">
                    @else
                        <div style="font-size: 8px; color: red;">QR Image Missing</div>
                    @endif
                </td>
            </tr>
        </table>
    
        <!-- Terms & Conditions Section (Two Columns) -->
        <table style="width: 100%; border: none; border-collapse: collapse; margin-bottom: 8px;">
            <tr>
                <!-- English Terms -->
                <td style="width: 50%; vertical-align: top; border: none; padding-right: 10px; text-align: left; font-size: 8px; line-height: 1.25; color: #222;">
                    <div style="font-size: 9px; font-weight: bold; border-bottom: 1px solid #062038; padding-bottom: 1px; margin-bottom: 4px; text-transform: uppercase; color: #062038;">
                        Terms & Conditions
                    </div>
                    @if(!empty($settings->terms_english))
                        {!! $settings->terms_english !!}
                    @else
                        <ol style="margin: 0; padding-left: 10px;">
                            <li>Electrical parts, sensors, or coils are non-returnable once opened or installed.</li>
                            <li>All returns/exchanges must be made within 7 days in original, undamaged packaging.</li>
                            <li>Parts must be installed by a qualified mechanic. We are not responsible for labor costs.</li>
                            <li>Check your package immediately. Report transit damage or shortages within 24 hours.</li>
                        </ol>
                    @endif
                </td>
    
                <!-- Urdu Terms -->
                <td style="width: 50%; vertical-align: top; border: none; padding-left: 10px; text-align: right; direction: rtl; font-size: 8.5px; line-height: 1.35; color: #222; font-family: 'TahomaUrdu', sans-serif;">
                    <div style="font-size: 9.5px; font-weight: bold; border-bottom: 1px solid #062038; padding-bottom: 1px; margin-bottom: 4px; color: #062038; text-align: right;">
                        شرائط و ضوابط
                    </div>
                    @if(!empty($settings->terms_urdu))
                        {!! $settings->terms_urdu !!}
                    @else
                        <ol style="margin: 0; padding-right: 10px; text-align: right;">
                            <li>کھولے گئے یا نصب شدہ الیکٹریکل پارٹس، سینسرز اور کوائلز واپس یا تبدیل نہیں ہوں گے۔</li>
                            <li>تمام واپسی یا تبدیلی 7 دن کے اندر اصل اور غیر نقصان دہ پیکنگ میں ہونی چاہیے۔</li>
                            <li>پارٹس کا مستند مکینک سے نصب ہونا ضروری ہے۔ ہم لیبر کے اخراجات کے ذمہ دار نہیں ہیں۔</li>
                            <li>وصولی پر سامان فوری چیک کریں۔ کسی بھی کمی یا نقصان کی اطلاع 24 گھنٹے کے اندر دیں۔</li>
                        </ol>
                    @endif
                </td>
            </tr>
        </table>
    
        <!-- Backpage Footer -->
        <div style="text-align: center; border-top: 1px dashed #ccc; padding-top: 6px; margin-top: 6px; font-size: 7.5px; color: #555;">
            {{ Helper::translateLabel('Danyal Autos') }} &bull; {{ Helper::translateLabel('12-Butt Market, Badami Bagh, Lahore') }} &bull; Phone: {{ $settings->phone ?? '+923042000274' }}
        </div>
    </div>
</body>
</html>


