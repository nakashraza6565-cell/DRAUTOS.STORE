<!DOCTYPE html>
<html lang="ur" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بلٹی - {{$receipt->receipt_number}}</title>
    <style>
        @page { margin: 0; }
        body {
            margin: 0;
            padding: 10px;
            font-family: 'Jameel Noori Nastaleeq', 'Noto Nastaliq Urdu', 'Nafees Regular', Arial, sans-serif;
            font-size: 20px;
            color: #000;
            background: #fff;
            width: 80mm;
            margin: 0 auto;
        }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .mt-2 { margin-top: 10px; }
        .mb-2 { margin-bottom: 10px; }
        .divider {
            border-bottom: 2px dashed #000;
            margin: 15px 0;
        }
        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            align-items: center;
        }
        .row .label { font-weight: bold; width: 35%; text-align: right; }
        .row .value { width: 65%; text-align: left; word-wrap: break-word; }
        
        /* Prominent Classes */
        .prominent-label { font-size: 24px; font-weight: 900; }
        .prominent-value { font-size: 26px; font-weight: 900; }
        
        .header-title { font-size: 32px; font-weight: 900; margin-bottom: 5px; }
        @media print {
            body { padding: 0; }
        }
    </style>
</head>
<body dir="rtl">

    <div class="text-center">
        <div class="header-title">دانیال آٹوز</div>
        <div style="font-size: 22px;">(لاہور)</div>
        <div class="mt-2 text-bold" style="font-size: 26px; border: 2px solid #000; padding: 5px; border-radius: 8px; display: inline-block;">بلٹی / رسید</div>
    </div>

    <div class="divider"></div>

    <div class="row">
        <div class="label">رسید نمبر:</div>
        <div class="value" style="font-family: sans-serif;">{{$receipt->receipt_number}}</div>
    </div>
    <div class="row">
        <div class="label">تاریخ:</div>
        <div class="value" style="font-family: sans-serif;">{{$receipt->date}}</div>
    </div>

    <div class="divider"></div>

    <!-- PROMINENT COURIER -->
    <div class="row text-center" style="display: block; margin-bottom: 15px;">
        <div class="prominent-label" style="text-align: center;">کوریئر / اڈا:</div>
        <div class="prominent-value" id="val-courier" style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 5px;">{{$receipt->courier_company ?? 'N/A'}}</div>
    </div>

    <div class="row">
        <div class="label">بھیجنے والا:</div>
        <div class="value text-bold">دانیال آٹوز (لاہور)</div>
    </div>
    
    <div class="divider"></div>

    <!-- PROMINENT RECEIVER AND CITY -->
    <div class="row text-center" style="display: block; margin-bottom: 10px;">
        <div class="prominent-label" style="text-align: center;">وصول کنندہ (نام):</div>
        <div class="prominent-value" id="val-receiver" style="text-align: center;">{{$receipt->receiver_name}}</div>
    </div>

    @if($receipt->address)
    <div class="row">
        <div class="label">پتہ:</div>
        <div class="value text-bold" id="val-address" style="font-size: 22px;">{{$receipt->address}}</div>
    </div>
    @endif
    
    @if($receipt->city)
    <div class="row text-center" style="display: block; margin-top: 15px; margin-bottom: 15px;">
        <div class="prominent-label" style="text-align: center;">شہر:</div>
        <div class="prominent-value" id="val-city" style="text-align: center; border: 2px solid #000; padding: 5px;">{{$receipt->city}}</div>
    </div>
    @endif

    <div class="divider"></div>

    <div class="row">
        <div class="label">کارٹن:</div>
        <div class="value text-bold" style="font-family: sans-serif; font-size: 24px;">{{$receipt->no_of_cartons}}</div>
    </div>
    <div class="row">
        <div class="label">بورے / تھیلے:</div>
        <div class="value text-bold" style="font-family: sans-serif; font-size: 24px;">{{$receipt->no_of_bags}}</div>
    </div>
    
    <!-- PROMINENT TOTAL PARCELS -->
    <div class="row" style="margin-top: 15px; border-top: 2px solid #000; border-bottom: 2px solid #000; padding: 10px 0;">
        <div class="prominent-label" style="width: 50%;">کل پارسل (نگ):</div>
        <div class="prominent-value" style="width: 50%; font-family: sans-serif; font-size: 34px;">{{$receipt->total_parcels}}</div>
    </div>
    
    <div class="text-center mt-2" style="font-size: 16px; margin-top: 20px;">
        *** شکریہ ***<br>
        <span style="font-family: sans-serif; font-size: 14px;">Powered by DRAUTOS</span>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="/backend/js/bluetooth-print.js"></script>
    <script>
        // Check if text contains mostly English characters
        function isEnglish(text) {
            return /[a-zA-Z]/.test(text);
        }

        async function translateToUrdu(text, elementId) {
            if (!text || text.trim() === '' || !isEnglish(text)) return;
            try {
                // Use a stable Chrome extension client ID which has higher rate limits
                const response = await fetch(`https://translate.googleapis.com/translate_a/single?client=dict-chrome-ex&sl=en&tl=ur&dt=t&q=${encodeURIComponent(text)}`);
                const data = await response.json();
                const translatedText = data[0].map(item => item[0]).join('');
                let el = document.getElementById(elementId);
                if (el) el.innerText = translatedText;
            } catch (e) {
                console.error('Google Translation error, trying fallback:', e);
                try {
                    // Fallback to MyMemory API if Google rate-limits us
                    const response2 = await fetch(`https://api.mymemory.translated.net/get?q=${encodeURIComponent(text)}&langpair=en|ur`);
                    const data2 = await response2.json();
                    if (data2 && data2.responseData && data2.responseData.translatedText) {
                        let el = document.getElementById(elementId);
                        if (el) el.innerText = data2.responseData.translatedText;
                    }
                } catch(err2) {
                    console.error('Fallback translation also failed.', err2);
                }
            }
        }

        const _biltyMobile = /Android|iPhone|iPad/i.test(navigator.userAgent);

        window.onload = async function() {
            // Auto translate fields if they are in English.
            // Use json_encode to safely encode quotes and newlines, preventing Javascript syntax errors.
            await Promise.all([
                translateToUrdu({!! json_encode($receipt->receiver_name) !!}, "val-receiver"),
                translateToUrdu({!! json_encode($receipt->city) !!}, "val-city"),
                translateToUrdu({!! json_encode($receipt->address) !!}, "val-address"),
                translateToUrdu({!! json_encode($receipt->courier_company) !!}, "val-courier")
            ]);

            if (_biltyMobile) {
                // On mobile: use Bluetooth printing after translations complete
                const savedPrinter = localStorage.getItem('drautos_bt_name');
                if (savedPrinter) {
                    setTimeout(function() { window.drautosBTPrint(); }, 500);
                }
                // If no printer paired, user taps the BT button manually
            } else {
                // Desktop: use system print dialog then go back
                window.print();
                setTimeout(() => window.history.back(), 1000);
            }
        };
    </script>
@include('backend.partials.bluetooth-print-btn')
</body>
</html>
