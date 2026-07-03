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
            margin: 0; 
            size: a5 portrait; 
        }
        
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            margin: 0; padding: 0; 
            color: #111; 
            line-height: 1.3; 
            font-size: 8px; 
            background: #fff;
        }
        
        #backpage-wrapper {
            position: relative;
            box-sizing: border-box;
            background: #fff;
            height: 595px; /* DomPDF A5 height helper */
        }
        
        /* Top Header Bar */
        .top-bar {
            background-color: #1B2A4A;
            color: #fff;
            text-align: center;
            padding: 8px 10px;
            border-bottom: 2px solid #C9A84C;
        }
        
        .top-bar h1 {
            font-size: 12px;
            font-weight: bold;
            color: #C9A84C;
            margin: 0;
            padding: 0;
            text-transform: uppercase;
        }
        
        .top-bar p {
            font-size: 7.5px;
            color: #ffffff;
            margin: 1px 0 0 0;
            font-style: italic;
        }
        
        /* Main Content Container */
        .content-container {
            padding: 8px 12px;
        }
        
        /* Contact Section */
        .contact-table {
            width: 100%;
            margin-bottom: 4px;
            border-collapse: collapse;
        }
        
        .contact-title {
            font-size: 9px;
            font-weight: bold;
            color: #C9A84C;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        
        .contact-details {
            font-size: 7.5px;
            color: #333;
            line-height: 1.25;
        }
        
        /* Divider */
        .divider {
            border-top: 1px solid #C9A84C;
            margin: 4px 0;
        }
        
        /* Terms & Conditions Section */
        .section-header-tc {
            text-align: center;
            font-size: 9.5px;
            font-weight: bold;
            color: #C9A84C;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        
        .section-header-tc span {
            font-family: 'TahomaUrdu', sans-serif;
            font-size: 10px;
        }
        
        .terms-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }
        
        .terms-table td {
            padding: 2px 0;
            vertical-align: top;
        }
        
        .terms-num {
            width: 12px;
            font-weight: bold;
            color: #1B2A4A;
            font-size: 8px;
        }
        
        .terms-text-en {
            width: 48%;
            font-size: 7.5px;
            color: #222;
            padding-right: 6px;
        }
        
        .terms-text-ur {
            width: 48%;
            font-family: 'TahomaUrdu', sans-serif;
            font-size: 8px;
            color: #222;
            direction: rtl;
            text-align: right;
            line-height: 1.2;
        }
        
        /* How to Pay Section */
        .pay-header {
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            color: #C9A84C;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        
        .pay-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .pay-col-left {
            width: 48%;
            border-right: 1px solid #C9A84C;
            padding-right: 8px;
            vertical-align: top;
        }
        
        .pay-col-right {
            width: 48%;
            padding-left: 8px;
            vertical-align: top;
        }
        
        .pay-title {
            font-size: 8px;
            font-weight: bold;
            color: #1B2A4A;
            margin-bottom: 3px;
            text-transform: uppercase;
        }
        
        /* Till ID styling */
        .till-title {
            color: #d9534f;
            font-weight: bold;
            font-size: 7.5px;
            margin-top: 2px;
            margin-bottom: 1px;
        }
        
        .till-box-container {
            margin: 1px 0;
        }
        
        .till-box {
            display: inline-block;
            width: 11px;
            height: 11px;
            border: 1px solid #111;
            text-align: center;
            font-weight: bold;
            font-size: 8px;
            line-height: 10px;
            background: #fff;
            margin-right: 1px;
        }
        
        .pay-banner {
            background-color: #C9A84C;
            color: #1B2A4A;
            font-weight: bold;
            font-size: 7px;
            text-align: center;
            padding: 1px;
            margin-top: 3px;
            text-transform: uppercase;
        }
        
        /* Bank Details */
        .bank-details {
            font-size: 7.5px;
            color: #333;
            line-height: 1.3;
        }
        
        .bank-details strong {
            color: #1B2A4A;
        }
        
        /* Bottom Footer Bar */
        .footer-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #1B2A4A;
            color: #fff;
            padding: 4px 10px;
            height: 36px;
            box-sizing: border-box;
            border-top: 2px solid #C9A84C;
        }
        
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        /* DR Brand Logo inside Footer */
        .logo-box {
            background: #ffffff;
            padding: 1px 4px;
            border-radius: 2px;
            display: inline-block;
            vertical-align: middle;
        }
        
        .logo-text-d {
            font-family: 'Revue', sans-serif;
            font-size: 16px;
            font-weight: bold;
            color: #1B2A4A;
            display: inline-block;
        }
        
        .logo-text-r {
            font-family: 'Revue', sans-serif;
            font-size: 16px;
            font-weight: bold;
            color: #9aa8b6;
            display: inline-block;
            margin-left: -4px;
        }
        
        .footer-company-name {
            font-size: 11px;
            font-weight: bold;
            color: #C9A84C;
            line-height: 1;
        }
        
        .footer-location {
            font-size: 7px;
            color: #ffffff;
            margin-top: 1px;
        }
        
        .footer-tagline {
            font-size: 6px;
            color: #C9A84C;
            letter-spacing: 0.3px;
            margin-top: 1px;
            text-transform: uppercase;
        }
        
        /* QR Code inside Footer */
        .footer-qr-img {
            width: 26px;
            height: 26px;
            background: #ffffff;
            padding: 1px;
            border-radius: 2px;
            display: inline-block;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    @php
        try {
            $settings = \App\Models\Settings::first();
        } catch (\Exception $e) {
            $settings = null;
        }
        if (!$settings) {
            $settings = new \stdClass();
            $settings->title = 'Danyal Autos';
            $settings->phone = '+92 304 2000274';
            $settings->email = 'drautostore@gmail.com';
            $settings->address = '12-Butt Market, Badami Bagh, Lahore';
        }
        
        // Load JazzCash QR locally
        $jazzcashPath = public_path('backend/img/jazzcash_qr.jpg');
        $base64Jazz = '';
        if (file_exists($jazzcashPath)) {
            $base64Jazz = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($jazzcashPath));
        }
    @endphp

    <div id="backpage-wrapper">
        <!-- Top Header Bar -->
        <div class="top-bar">
            <h1>Thank You For Your Business!</h1>
            <p>Please retain this invoice for your records</p>
        </div>
        
        <div class="content-container">
            <!-- Contact Info -->
            <table class="contact-table">
                <tr>
                    <td style="width: 60%; vertical-align: top;">
                        <div class="contact-title">Contact Us</div>
                        <div class="contact-details">
                            📍 12-Butt Market, Badami Bagh, Lahore<br>
                            📞 +92 304 2000274 &bull; 042-37727045<br>
                            ✉ drautostore@gmail.com
                        </div>
                    </td>
                    <td style="width: 40%; vertical-align: top;">
                        <div class="contact-title">Visit Online</div>
                        <div class="contact-details">
                            💬 WhatsApp: +92 304 2000274
                        </div>
                    </td>
                </tr>
            </table>
            
            <div class="divider"></div>
            
            <!-- Terms & Conditions -->
            <div class="section-header-tc">
                Terms & Conditions / <span>شرائط و ضوابط</span>
            </div>
            
            <table class="terms-table">
                <tr>
                    <td class="terms-num">1.</td>
                    <td class="terms-text-en">Returns or exchanges are accepted within 15 days.</td>
                    <td class="terms-text-ur">واپسی یا تبادلہ خریداری کے 15 دن کے اندر قبول کیا جائے گا۔</td>
                </tr>
                <tr>
                    <td class="terms-num">2.</td>
                    <td class="terms-text-en">A 25% deduction will apply to returns after 15 days.</td>
                    <td class="terms-text-ur">15 دن کے بعد واپسی پر 25% کٹوتی لاگو ہوگی۔</td>
                </tr>
                <tr>
                    <td class="terms-num">3.</td>
                    <td class="terms-text-en">Payment will be issued in Cash or Store Credit.</td>
                    <td class="terms-text-ur">ادائیگی نقد یا اسٹور کریڈٹ میں کی جائے گی۔</td>
                </tr>
                <tr>
                    <td class="terms-num">4.</td>
                    <td class="terms-text-en">Original bill must be presented for returns.</td>
                    <td class="terms-text-ur">واپسی کے لیے اصل بل پیش کرنا ضروری ہے۔</td>
                </tr>
                <tr>
                    <td class="terms-num">5.</td>
                    <td class="terms-text-en">Imported and damaged items are non-returnable.</td>
                    <td class="terms-text-ur">درآمد شدہ اور خراب اشیاء واپس نہیں ہوں گی۔</td>
                </tr>
                <tr>
                    <td class="terms-num">6.</td>
                    <td class="terms-text-en">Defective products can be returned in original packaging.</td>
                    <td class="terms-text-ur">خراب مصنوعات اصل پیکنگ میں واپس کی جا سکتی ہیں۔</td>
                </tr>
                <tr>
                    <td class="terms-num">7.</td>
                    <td class="terms-text-en">All pipes carry a warranty and claims are acceptable.</td>
                    <td class="terms-text-ur">تمام پائپوں پر وارنٹی ہے اور دعوے قابل قبول ہیں۔</td>
                </tr>
            </table>
            
            <div class="divider"></div>
            
            <!-- How to Pay -->
            <div class="pay-header">How To Pay</div>
            
            <table class="pay-table">
                <tr>
                    <!-- JazzCash -->
                    <td class="pay-col-left">
                        <div class="pay-title">JazzCash / Raast</div>
                        <div class="till-title">TILL ID</div>
                        <div class="till-box-container">
                            <span class="till-box">9</span>
                            <span class="till-box">8</span>
                            <span class="till-box">3</span>
                            <span class="till-box">2</span>
                            <span class="till-box">6</span>
                            <span class="till-box">2</span>
                            <span class="till-box">7</span>
                            <span class="till-box">5</span>
                            <span class="till-box">1</span>
                        </div>
                        <div style="font-size: 7px; color: #555; margin-top: 2px;">
                            Dial *786*10# and enter TILL ID to pay via JazzCash.
                        </div>
                        <div class="pay-banner">QR Payments Accepted</div>
                    </td>
                    
                    <!-- Bank Transfer -->
                    <td class="pay-col-right">
                        <div class="pay-title">Bank Transfer</div>
                        <div class="bank-details">
                            <strong>Bank:</strong> Meezan Bank<br/>
                            <strong>Account Title:</strong> Shiekh Imtiaz Ali Tahir<br/>
                            <strong>Acc No:</strong> 02560103847320<br/>
                            <strong>Branch:</strong> Badami Bagh, Lahore<br/>
                            <strong>Payment accepted:</strong> Cash &bull; JazzCash &bull; Bank Transfer
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- Bottom Footer Bar -->
        <div class="footer-bar">
            <table class="footer-table">
                <tr>
                    <!-- Left side: DR Logo and Danyal Autos Text -->
                    <td style="width: 75%; vertical-align: middle; padding: 0;">
                        <table style="border: none; border-collapse: collapse; width: 100%;">
                            <tr>
                                <td style="width: 38px; vertical-align: middle; padding: 0;">
                                    <div class="logo-box">
                                        <span class="logo-text-d">D</span><span class="logo-text-r">R</span>
                                    </div>
                                </td>
                                <td style="vertical-align: middle; padding-left: 6px;">
                                    <div class="footer-company-name">DANYAL AUTOS</div>
                                    <div class="footer-location">Lahore, Pakistan</div>
                                    <div class="footer-tagline">AUTO PARTS &bull; ACCESSORIES &bull; WHOLESALE</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <!-- Right side: QR Code -->
                    <td style="width: 25%; text-align: right; vertical-align: middle; padding: 0;">
                        @if($base64Jazz)
                            <img src="{{ $base64Jazz }}" class="footer-qr-img" alt="Footer QR">
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
