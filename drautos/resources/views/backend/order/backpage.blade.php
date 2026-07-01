<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice Backpage Template | Danyal Autos</title>
    <style>
        @page { margin: 4mm; size: a5; }
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            margin: 0; padding: 0; 
            color: #111; 
            line-height: 1.25; 
            font-size: 11px; 
            background: #fff;
        }
        table { width: 100%; border-collapse: collapse; }
        td, th { vertical-align: top; }
        @font-face {
            font-family: 'TahomaUrdu';
            src: url("{{ str_replace('\\', '/', public_path('revue/tahoma.ttf')) }}") format("truetype");
        }
    </style>
</head>
<body>
    @php
        $settings = \App\Models\Settings::first();
    @endphp

    <div id="backpage-wrapper" style="position: relative; background: #fff; padding: 5px; font-family: 'Helvetica', 'Arial', sans-serif;">
        <!-- Branding Header -->
        <table style="width: 100%; background: #ffffff; border-bottom: 2px solid #062038; padding: 8px 0px; margin-bottom: 12px; color: #333; border-collapse: collapse;">
            <tr>
                <td style="width: 55%; vertical-align: middle;">
                    <table style="border: none; border-collapse: collapse; width: 100%;">
                        <tr>
                            <td style="width: 55px; vertical-align: middle; border: none; padding: 0;">
                                <!-- DR Logo SVG -->
                                <svg width="50" height="42" viewBox="0 0 75 65" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M 22.74 58.73 L 13.17 58.73 L 13.17 25.29 L 24.94 25.29 L 24.94 48.61 C 28.98 48.47 32.29 46.74 34.87 43.44 C 37.46 40.14 38.75 36.49 38.75 32.5 C 38.75 29.89 38.25 26.83 C 36.27 25.66 35.19 24.87 34.02 24.47 C 33.25 24.21 32.51 24.06 31.79 24.03 C 31.08 24.03 30.5 23.97 30.06 23.97 L 10.97 23.92 L 13.72 13.8 L 32.81 13.8 C 39.52 13.83 44.19 15.65 46.83 19.24 C 49.47 22.84 50.79 26.91 50.79 31.45 C 50.79 39.92 47.99 46.59 42.38 51.45 C 36.77 56.3 30.22 58.73 22.74 58.73 " fill="#062038"/>
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
