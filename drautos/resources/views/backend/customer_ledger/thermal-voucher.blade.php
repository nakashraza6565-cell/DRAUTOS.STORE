<!DOCTYPE html>
<html>
<head>
    <title>Transaction Voucher #{{ $transaction->id }}</title>
    <style>
        * { box-sizing: border-box; }
        html, body, div, span, table, th, td, p, a, strong, b, .item-name, .item-details { font-weight: 900 !important; color: #000 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; -webkit-text-stroke: 0.5px #000 !important; text-shadow: 0.5px 0.5px 0px #000 !important; font-family: {{ request('lang') === 'ur' ? "'Noto Nastaliq Urdu', 'Arial Unicode MS'" : "'Arial', 'Helvetica', sans-serif" }} !important; }

        @page { margin: 0; }
        @font-face {
            font-family: 'Revue';
            src: url('/revue/reve.ttf?v=1.1') format("truetype");
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
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-family: 'Revue', sans-serif;
            font-size: 150px;
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
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body onload="window.print()">

    @php
        $settings = \App\Models\Settings::first();
    @endphp

<div id="receipt-content" style="background: #fff; padding-bottom: 10px; position: relative;">

    <div class="watermark-bg">DR</div>

    <div class="header-container text-center">
        <div class="merchant-name">{!! strip_tags(str_replace('&nbsp;', ' ', $settings->short_des ?? 'Danyal Autos')) !!}</div>
        <div class="merchant-address">{{ $settings->address ?? 'Liaquat Pur, RYK' }}</div>
        <div class="merchant-address">{{ $settings->phone }}</div>
    </div>

    <div class="text-center" style="font-size: 16px; margin-bottom: 15px; text-decoration: underline; text-transform: uppercase;">
        @if($transaction->category == 'payment' && $transaction->type == 'credit')
            PAYMENT RECEIPT
        @elseif($transaction->category == 'return' && $transaction->type == 'credit')
            GOODS RETURN VOUCHER
        @else
            TRANSACTION VOUCHER
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
            <span>Customer:</span>
            <span class="text-right">{{ $transaction->user->name }}<br>{{ $transaction->user->phone }}</span>
        </div>
    </div>

    <div class="amount-display">
        Rs. {{ number_format($transaction->amount, 2) }}
    </div>

    <table class="voucher-details">
        <tr>
            <td style="width: 40%; font-size: 12px; color: #555;">Received-in:</td>
            <td class="text-right text-uppercase">{{ $transaction->financialAccount ? $transaction->financialAccount->name : 'N/A' }}</td>
        </tr>
        <tr>
            <td style="width: 40%; font-size: 12px; color: #555;">Description:</td>
            <td class="text-right">{{ $transaction->description }}</td>
        </tr>
        <tr>
            <td style="width: 40%; font-size: 12px; color: #555; padding-top: 15px;">New Balance:</td>
            <td class="text-right" style="padding-top: 15px;">Rs. {{ number_format($transaction->balance, 2) }}</td>
        </tr>
    </table>

    <div class="signature-line">
        {{ auth()->check() ? auth()->user()->name : 'Admin' }}
    </div>

    <div class="footer">
        <div>Thank you for your business!</div>
    </div>
</div>

    <div class="no-print" style="text-align: center; margin-top: 30px; padding: 10px; padding-bottom: 30px;">
        <button onclick="shareReceipt()" style="background: #25D366; color: #fff; border: none; padding: 12px 24px; font-size: 16px; font-weight: bold; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 300px;">
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M13.5 1a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zM11 2.5a2.5 2.5 0 1 1 .603 1.628l-6.718 3.12a2.499 2.499 0 0 1 0 1.504l6.718 3.12a2.5 2.5 0 1 1-.488.876l-6.718-3.12a2.5 2.5 0 1 1 0-3.256l6.718-3.12A2.5 2.5 0 0 1 11 2.5zm-8.5 4a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm11 5.5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3z"/></svg>
            Share Receipt
        </button>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        async function shareReceipt() {
            const receiptElement = document.getElementById('receipt-content');
            
            try {
                const canvas = await html2canvas(receiptElement, { 
                    scale: 3, // High quality
                    useCORS: true,
                    backgroundColor: '#ffffff'
                });
                
                canvas.toBlob(async (blob) => {
                    const file = new File([blob], "receipt_01115.png", { type: "image/png" });
                    
                    if (navigator.share && navigator.canShare && navigator.canShare({ files: [file] })) {
                        await navigator.share({
                            files: [file],
                            title: 'Payment Receipt',
                            text: 'Payment Receipt from Danyal Autos'
                        });
                    } else {
                        // Fallback: Download the image to gallery/files
                        const link = document.createElement('a');
                        link.download = 'receipt_{{ $transaction->id }}.png';
                        link.href = canvas.toDataURL("image/png");
                        link.click();
                    }
                }, "image/png");
            } catch (err) {
                console.error("Error sharing receipt:", err);
                alert("Could not share receipt. Your browser might not support this feature.");
            }
        }
    </script>
</body>
</html>
