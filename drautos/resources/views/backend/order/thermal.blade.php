<!DOCTYPE html>
<html dir="{{ request('lang') === 'ur' ? 'rtl' : 'ltr' }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $order->order_number }}</title>
    @if(request('lang') === 'ur')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@400;700&display=swap" rel="stylesheet">
    @endif
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
        .text-right { text-align: {{ request('lang') === 'ur' ? 'left' : 'right' }}; }
        .bold { font-weight: {{ request('lang') === 'ur' ? '700' : '900' }}; }
        
        .header-container {
            position: relative;
            margin-bottom: 12px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            overflow: hidden;
        }
        
        .merchant-name {
            font-size: {{ request('lang') === 'ur' ? '22px' : '24px' }};
            font-weight: {{ request('lang') === 'ur' ? '700' : '900' }};
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
        .item-list th.col-right {
            text-align: {{ request('lang') === 'ur' ? 'left' : 'right' }};
        }
        .item-list td {
            padding: 8px 0;
            vertical-align: top;
            border-bottom: 0.5px solid #eee;
        }
        .item-name {
            font-size: {{ request('lang') === 'ur' ? '15px' : '13px' }};
            display: block;
            font-weight: {{ request('lang') === 'ur' ? '700' : '900' }};
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
            font-weight: {{ request('lang') === 'ur' ? '700' : '900' }};
        }

        .footer-note {
            margin-top: 15px;
            font-size: 14px;
            text-align: center;
            font-weight: {{ request('lang') === 'ur' ? '700' : '900' }};
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
        $items = $order->cart_info;
        $chunks = $items->chunk(15);
        $totalChunks = $chunks->count();
    @endphp

    @foreach($chunks as $pageIndex => $chunk)
        @include('backend.layouts.watermark', ['type' => 'thermal'])
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
                <span>{{ $isUrdu ? 'رسید:' : 'Receipt:' }} <strong>{{ $order->order_number }}</strong></span>
                <span>{{ $isUrdu ? 'تاریخ:' : 'Date:' }} <strong>{{ $order->created_at->format('d/m/y H:i') }}</strong></span>
            </div>
            <div class="info-row">
                <span>{{ $isUrdu ? 'کیشیئر:' : 'Cashier:' }} <strong>{{ strtoupper(Auth::user()->name ?? 'Admin') }}</strong></span>
                <span>{{ $isUrdu ? 'قسم:' : 'Type:' }} <strong>{{ $isUrdu ? 'پی او ایس سیل' : 'POS SALE' }}</strong></span>
            </div>
            <div class="separator"></div>
            <div class="info-row">
                <span>{{ $isUrdu ? 'گاہک:' : 'Customer:' }} <strong>{{ strtoupper($order->first_name) }} {{ strtoupper($order->last_name) }}</strong></span>
            </div>
            @if($order->phone)
            <div class="info-row">
                <span>{{ $isUrdu ? 'رابطہ:' : 'Contact:' }} <strong>{{ $order->phone }}</strong></span>
            </div>
            @endif
        </div>

        <table class="item-list">
            <thead>
                <tr>
                    <th width="50%">{{ $isUrdu ? 'پروڈکٹ کی تفصیل' : 'PRODUCT DETAILS' }}</th>
                    <th width="20%" class="text-center">{{ $isUrdu ? 'تعداد' : 'QTY' }}</th>
                    <th width="30%" class="col-right">{{ $isUrdu ? 'کل رقم' : 'TOTAL' }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($chunk as $item)
                    <tr>
                        <td>
                            <span class="item-name">
                                {{ Helper::translatePartTitle($item->product->title ?? ($item->bundle->name ?? 'Item'), false) }}
                                @if($item->product && $item->product->brand)
                                    ({{ $item->product->brand->title }})
                                @endif
                            </span>
                            <span class="item-details">
                                @if($item->product)
                                    @if($item->product->model) {{ $isUrdu ? 'ماڈل:' : 'MODEL:' }} {{ $item->product->model }} @endif
                                    @if($item->product->sku) | {{ $isUrdu ? 'ایس کے یو:' : 'SKU:' }} {{ $item->product->sku }} @endif
                                @elseif($item->bundle && $item->bundle->sku)
                                    {{ $isUrdu ? 'ایس کے یو:' : 'SKU:' }} {{ $item->bundle->sku }}
                                @endif
                            </span>
                            <span class="item-details"><br>{{ $isUrdu ? 'فی قیمت:' : 'UNIT PRICE:' }} {{ number_format($item->price, 0) }}</span>
                            @php
                                $returnedQty = 0;
                                $pendingReturnQty = 0;
                                if ($item->product_id) {
                                    $returnedQty = \App\Models\SaleReturnItem::where('order_id', $order->id)
                                        ->where('product_id', $item->product_id)
                                        ->whereHas('saleReturn', function ($q) {
                                            $q->where('status', 'approved');
                                        })
                                        ->sum('quantity');

                                    $pendingReturnQty = \App\Models\SaleReturnItem::where('order_id', $order->id)
                                        ->where('product_id', $item->product_id)
                                        ->whereHas('saleReturn', function ($q) {
                                            $q->where('status', 'pending');
                                        })
                                        ->sum('quantity');
                                }
                            @endphp
                            @if($returnedQty > 0)
                                <span class="item-details" style="display: block; margin-top: 2px;">
                                    <strong>* {{ Helper::translateLabel('Returned:', false) }} {{ $returnedQty }}</strong>
                                </span>
                            @endif
                            @if($pendingReturnQty > 0)
                                <span class="item-details" style="display: block; margin-top: 2px;">
                                    <strong>* {{ Helper::translateLabel('Pending Return:', false) }} {{ $pendingReturnQty }}</strong>
                                </span>
                            @endif
                        </td>
                        <td class="text-center bold" style="font-size: 16px;">
                            {{ $item->quantity }}<span style="font-size: 10px; margin-{{ $isUrdu ? 'right' : 'left' }}: 2px;">{{ strtoupper($item->product->unit ?? '') }}</span>
                        </td>
                        <td class="{{ $isUrdu ? '' : 'text-right' }} bold" style="{{ $isUrdu ? 'text-align:left;' : '' }}">Rs.{{ number_format($item->amount, 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($pageIndex == $totalChunks - 1)
            <div class="separator"></div>
            <div class="totals-block">
                <div class="total-row">
                    <span>{{ $isUrdu ? 'ذیلی کل:' : 'Subtotal' }}</span>
                    <span>Rs.{{ number_format($order->sub_total, 0) }}</span>
                </div>
                @if($order->coupon > 0)
                <div class="total-row">
                    <span>{{ $isUrdu ? 'رعایت:' : 'Discount Applied' }}</span>
                    <span>-Rs.{{ number_format($order->coupon, 0) }}</span>
                </div>
                @endif
                <div class="grand-total-row total-row">
                    <span>{{ $isUrdu ? 'کل خالص رقم:' : 'NET AMOUNT' }}</span>
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
                    <span>{{ $isUrdu ? 'ادائیگی کا طریقہ:' : 'Payment Method:' }}</span>
                    <span class="bold">{{ strtoupper($order->payment_method ?? 'CASH') }}</span>
                </div>
                <div class="info-row">
                    <span>{{ $isUrdu ? 'موصول رقم:' : 'Amount Received:' }}</span>
                    <span class="bold">Rs.{{ number_format($amount_paid, 0) }}</span>
                </div>
                <div class="info-row">
                    <span>{{ $isUrdu ? 'پچھلا بقایا:' : 'Previous Balance:' }}</span>
                    <span class="bold">Rs.{{ number_format($previous_balance, 0) }}</span>
                </div>
                <div class="info-row" style="font-size: 16px; border-top: 1px solid #000; padding-top: 5px; margin-top: 5px;">
                    <span>{{ $isUrdu ? 'واجب الادا بقایا:' : 'BALANCE DUE:' }}</span>
                    <span class="bold">Rs.{{ number_format($final_balance_due, 0) }}</span>
                </div>
                @if($order->due_date)
                <div class="info-row" style="color: #d00; margin-top: 5px;">
                    <span>{{ $isUrdu ? 'ادائیگی کی آخری تاریخ:' : 'PAYMENT DUE BY:' }}</span>
                    <span class="bold">{{ date('d/m/y', strtotime($order->due_date)) }}</span>
                </div>
                @endif
            </div>

            @php
                $secureToken = hash_hmac('sha256', $order->id . $order->order_number, config('app.key'));
                $invoiceUrl = route('order.pdf', ['id' => $order->id, 'token' => $secureToken]);

                // Load terms from database settings
                $settings = \App\Models\Settings::first();
                $termsUrdu    = $settings && $settings->terms_urdu    ? strip_tags($settings->terms_urdu)    : '';
                $termsEnglish = $settings && $settings->terms_english ? strip_tags($settings->terms_english) : '';

                // Fallback hardcoded terms if DB is empty
                $defaultTerms = [
                    ['ur' => 'خریدے گئے سامان کی واپسی بلاشرطی 15 دن کے اندر ممکن ہے۔',             'en' => 'Returns or exchanges accepted within 15 days.'],
                    ['ur' => '15 دن کے بعد واپسی پر 25% کٹوتی لاگو ہوگی۔',                         'en' => 'A 25% deduction applies to returns after 15 days.'],
                    ['ur' => 'ادائیگی نقد یا اسٹور کریڈٹ میں کی جائے گی۔',                          'en' => 'Payment issued in Cash or Store Credit.'],
                    ['ur' => 'واپسی کے لیے اصل بل پیش کرنا ضروری ہے۔',                              'en' => 'Original bill must be presented for returns.'],
                    ['ur' => 'درآمد شدہ اور خراب اشیاء واپس نہیں ہوں گی۔',                         'en' => 'Imported and damaged items are non-returnable.'],
                    ['ur' => 'خراب مصنوعات اصل پیکنگ میں واپس کی جا سکتی ہیں۔',                    'en' => 'Defective products can be returned in original packaging.'],
                    ['ur' => 'تمام پائپوں پر وارنٹی ہے اور دعوے قابل قبول ہیں۔',                   'en' => 'All pipes carry a warranty and claims are acceptable.'],
                ];
                $useDefault = empty($termsUrdu) && empty($termsEnglish);
            @endphp

            {{-- Terms & Conditions rendered as pure HTML text (always prints clearly) --}}
            <div style="border: 1px solid #000; border-radius: 4px; padding: 8px 10px; margin: 12px 0; background: #fff;">
                <div style="font-size: 11px; font-weight: 900; text-align: center; border-bottom: 1px solid #000; padding-bottom: 5px; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">
                    @if($isUrdu) شرائط و ضوابط @else Terms &amp; Conditions @endif
                </div>

                @if($useDefault)
                    {{-- Hardcoded default terms --}}
                    <table style="width: 100%; border-collapse: collapse;">
                        @foreach($defaultTerms as $i => $term)
                        <tr>
                            <td style="font-size: 9px; font-weight: 700; color: #000; vertical-align: top; padding: 2px 4px 2px 0; width: 14px;">{{ $i + 1 }}.</td>
                            <td style="font-size: 9px; font-weight: 700; color: #000; vertical-align: top; padding: 2px 0; line-height: 1.4; direction: {{ $isUrdu ? 'rtl' : 'ltr' }}; text-align: {{ $isUrdu ? 'right' : 'left' }};">
                                {{ $isUrdu ? $term['ur'] : $term['en'] }}
                            </td>
                        </tr>
                        @endforeach
                    </table>
                @else
                    {{-- Terms from database --}}
                    @if($isUrdu && $termsUrdu)
                        <div style="font-size: 9px; font-weight: 700; color: #000; direction: rtl; text-align: right; line-height: 1.6; white-space: pre-line;">{{ $termsUrdu }}</div>
                    @elseif($termsEnglish)
                        <div style="font-size: 9px; font-weight: 700; color: #000; direction: ltr; text-align: left; line-height: 1.6; white-space: pre-line;">{{ $termsEnglish }}</div>
                    @endif
                @endif
            </div>

            {{-- QR Code --}}
            <div style="text-align: center; margin: 8px 0; padding: 6px; border: 1px dashed #000;">
                <div style="font-size: 8px; font-weight: 900; margin-bottom: 4px; text-transform: uppercase;">{{ $isUrdu ? 'بل ڈاؤن لوڈ کرنے کے لیے اسکین کریں' : 'Scan to Download Bill' }}</div>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=60x60&data={{ urlencode($invoiceUrl) }}" alt="Invoice QR" style="width: 60px; height: 60px; display: block; margin: 0 auto;">
            </div>

            <div class="footer-note">
                {{ $isUrdu ? 'آپ کے کاروبار کا شکریہ!' : 'THANK YOU FOR YOUR BUSINESS!' }}
            </div>
            <div class="social-info">
                WhatsApp: 0304-2000274 | FB: /DanyalAutos
            </div>
        @else
            <div class="text-center bold" style="padding: 10px; border: 2px solid #000; margin: 15px 0;">
                --- {{ $isUrdu ? 'اگلا صفحہ' : 'CONTINUED ON PAGE' }} {{ $pageIndex + 2 }} ---
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


