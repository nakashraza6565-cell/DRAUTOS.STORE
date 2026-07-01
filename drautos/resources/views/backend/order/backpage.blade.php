<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice Backpage | Danyal Autos</title>
    <style>
        @font-face {
            font-family: 'Revue';
            src: url("{{ str_replace('\\', '/', base_path('../revue/reve.ttf')) }}") format("truetype");
        }
        @font-face {
            font-family: 'TahomaUrdu';
            src: url("{{ str_replace('\\', '/', base_path('../revue/tahoma.ttf')) }}") format("truetype");
        }
        
        @page { 
            margin: 6mm; 
            size: a5; 
        }
        
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            margin: 0; padding: 0; 
            color: #111; 
            line-height: 1.3; 
            font-size: 10px; 
            background: #fff;
        }
        
        table { width: 100%; border-collapse: collapse; }
        td, th { vertical-align: top; }
        
        .header-table { 
            width: 100%; 
            border-bottom: 2px solid #062038; 
            padding-bottom: 6px; 
            margin-bottom: 12px; 
        }
        
        .logo-img {
            width: 50px;
            height: auto;
        }
        
        .company-name { 
            font-family: 'Revue', sans-serif; 
            font-size: 20px; 
            font-weight: bold; 
            color: #062038; 
            letter-spacing: 0.5px;
            line-height: 1;
        }
        
        .company-tagline { 
            font-size: 8.5px; 
            color: #a0b2c6; 
            font-weight: bold; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
            margin-top: 2px;
        }
        
        .header-details {
            font-size: 8.5px;
            color: #444;
            line-height: 1.3;
            text-align: right;
        }
        
        .section-title {
            font-size: 10px;
            font-weight: bold;
            color: #062038;
            border-bottom: 1.5px solid #a0b2c6;
            padding-bottom: 3px;
            margin-bottom: 6px;
            text-transform: uppercase;
        }
        
        .section-title-urdu {
            font-family: 'TahomaUrdu', sans-serif;
            font-size: 11px;
            font-weight: bold;
            color: #062038;
            border-bottom: 1.5px solid #a0b2c6;
            padding-bottom: 3px;
            margin-bottom: 6px;
            text-align: right;
        }
        
        .qr-box {
            background-color: #fcfcfc;
            border: 1px solid #eef2f5;
            border-radius: 6px;
            padding: 8px;
            text-align: center;
        }
        
        .qr-title {
            font-size: 9px;
            font-weight: bold;
            color: #062038;
            margin-bottom: 6px;
            text-transform: uppercase;
        }
        
        .qr-title-urdu {
            font-family: 'TahomaUrdu', sans-serif;
            font-size: 9.5px;
            color: #555;
            font-weight: normal;
        }
        
        .terms-list-english {
            margin: 0;
            padding-left: 12px;
            font-size: 8.5px;
            color: #222;
        }
        
        .terms-list-english li {
            margin-bottom: 4px;
        }
        
        .terms-list-urdu {
            font-family: 'TahomaUrdu', sans-serif;
            margin: 0;
            padding-right: 12px;
            font-size: 9px;
            color: #222;
            direction: rtl;
            text-align: right;
        }
        
        .terms-list-urdu li {
            margin-bottom: 4px;
        }
        
        .footer-bar {
            text-align: center;
            border-top: 1px dashed #ccc;
            padding-top: 6px;
            margin-top: 12px;
            font-size: 7.5px;
            color: #666;
        }
    </style>
</head>
<body>
    @php
        $settings = \App\Models\Settings::first();
        
        // Load official logo image locally
        $logoPath = base_path('../frontend/img/logo.png');
        $base64Logo = '';
        if (file_exists($logoPath)) {
            $base64Logo = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }
        
        // Load JazzCash QR locally
        $jazzcashPath = public_path('backend/img/jazzcash_qr.jpg');
        $base64Jazz = '';
        if (file_exists($jazzcashPath)) {
            $base64Jazz = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($jazzcashPath));
        }
        
        // Load WhatsApp QR locally
        $whatsappPath = public_path('backend/img/whatsapp_qr.png');
        $base64WA = '';
        if (file_exists($whatsappPath)) {
            $base64WA = 'data:image/png;base64,' . base64_encode(file_get_contents($whatsappPath));
        }
    @endphp

    <div id="backpage-wrapper">
        <!-- Branding Header -->
        <table class="header-table">
            <tr>
                <td style="width: 55%; vertical-align: middle;">
                    <table style="border: none; border-collapse: collapse; width: 100%;">
                        <tr>
                            @if($base64Logo)
                            <td style="width: 55px; vertical-align: middle; border: none; padding: 0;">
                                <img src="{{ $base64Logo }}" class="logo-img" alt="Branding Logo">
                            </td>
                            @endif
                            <td style="vertical-align: middle; border: none; padding-left: 8px;">
                                <div class="company-name">{{ $settings->title ?? 'Danyal Autos' }}</div>
                                <div class="company-tagline">PREMIUM TRUCK PARTS B2B</div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width: 45%; vertical-align: middle;">
                    <div class="header-details">
                        <strong>Phone:</strong> {{ $settings->phone ?? '+923042000274' }}<br>
                        <strong>Email:</strong> {{ $settings->email ?? 'drautostore@gmail.com' }}<br>
                        <strong>Address:</strong> {{ $settings->address ?? '12-BUTT MARKET BADAMI BAGH LAHORE' }}
                    </div>
                </td>
            </tr>
        </table>
    
        <!-- QR Codes Section (Two Columns) -->
        <table style="width: 100%; border: none; border-collapse: collapse; margin-bottom: 12px;">
            <tr>
                <!-- JazzCash QR Code -->
                <td style="width: 50%; border: none; padding-right: 6px;">
                    <div class="qr-box">
                        <div class="qr-title">
                            JazzCash / Raast Payment<br>
                            <span class="qr-title-urdu">جاز کیش / راست ادائیگی</span>
                        </div>
                        @if($base64Jazz)
                            <img src="{{ $base64Jazz }}" style="width: 110px; height: auto; border: 1px solid #eee; border-radius: 4px;" alt="JazzCash QR">
                        @else
                            <div style="font-size: 8px; color: red;">QR Image Missing</div>
                        @endif
                    </div>
                </td>
    
                <!-- WhatsApp Support QR Code -->
                <td style="width: 50%; border: none; padding-left: 6px;">
                    <div class="qr-box">
                        <div class="qr-title">
                            WhatsApp Support<br>
                            <span class="qr-title-urdu">واٹس ایپ رابطہ (+923042000274)</span>
                        </div>
                        @if($base64WA)
                            <img src="{{ $base64WA }}" style="width: 110px; height: auto; border: 1px solid #eee; border-radius: 4px;" alt="WhatsApp QR">
                        @else
                            <div style="font-size: 8px; color: red;">QR Image Missing</div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    
        <!-- Terms & Conditions Section (Two Columns) -->
        <table style="width: 100%; border: none; border-collapse: collapse; margin-bottom: 6px;">
            <tr>
                <!-- English Terms -->
                <td style="width: 50%; border: none; padding-right: 12px;">
                    <div class="section-title">Terms & Conditions</div>
                    @if(!empty($settings->terms_english))
                        <div style="font-size: 8.5px; color: #222; line-height: 1.3;">
                            {!! $settings->terms_english !!}
                        </div>
                    @else
                        <ol class="terms-list-english">
                            <li>Returns or exchanges of purchased items are accepted within 15 days.</li>
                            <li>A 25% deduction will apply to returns after 15 days.</li>
                            <li>In case of return, payment will be issued in Cash or Store Credit.</li>
                            <li>Original bill must be presented for returns. If the bill is lost, the Order Number is mandatory.</li>
                            <li>Imported items and damaged/broken items are strictly non-returnable.</li>
                            <li>Defective products can be returned at any time, provided they are in their original packaging.</li>
                            <li>All pipes carry a warranty and claims are acceptable.</li>
                        </ol>
                    @endif
                </td>
    
                <!-- Urdu Terms -->
                <td style="width: 50%; border: none; padding-left: 12px; vertical-align: top;">
                    <div class="section-title-urdu">شرائط و ضوابط</div>
                    @if(!empty($settings->terms_urdu))
                        <div class="terms-list-urdu" style="font-size: 9px; line-height: 1.35;">
                            {!! $settings->terms_urdu !!}
                        </div>
                    @else
                        <ol class="terms-list-urdu">
                            <li>خریدے گئے سامان کی واپسی یا تبدیلی 15 دن کے اندر ممکن ہے۔</li>
                            <li>15 دن گزر جانے کے بعد، رقم سے 25 فیصد کٹوتی کی جائے گی۔</li>
                            <li>واپسی کی صورت میں، رقم کی ادائیگی نقد یا سٹور کریڈٹ کی شکل میں کی جائے گی۔</li>
                            <li>واپسی کے وقت اصل بل پیش کرنا ضروری ہے۔ بل نہ ہونے کی صورت میں آرڈر نمبر (Order Number) فراہم کرنا لازمی ہے۔</li>
                            <li>امپورٹڈ سامان اور ٹوٹ پھوٹ کا شکار اشیاء ہرگز واپس نہیں لی جائیں گی۔</li>
                            <li>پروڈکٹ میں نقص ہونے کی صورت میں اسے کسی بھی وقت واپس کیا جا سکتا ہے، بشرطیکہ سامان اپنی اصل پیکنگ میں ہو۔</li>
                            <li>تمام پائپ وارنٹی کے حامل ہیں اور ان کا کلیم قابل قبول ہے۔</li>
                        </ol>
                    @endif
                </td>
            </tr>
        </table>
    
        <!-- Backpage Footer -->
        <div class="footer-bar">
            {{ $settings->title ?? 'Danyal Autos' }} &bull; {{ $settings->address ?? '12-BUTT MARKET BADAMI BAGH LAHORE' }} &bull; Phone: {{ $settings->phone ?? '+923042000274' }}
        </div>
    </div>
</body>
</html>
