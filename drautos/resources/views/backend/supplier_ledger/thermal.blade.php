<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Ledger - {{ $supplier->name }}</title>
    <style>
        * { box-sizing: border-box; }
        html, body, div, span, table, th, td, p, a, strong, b, .item-name, .item-details { color: #000 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; font-family: {{ request('lang') === 'ur' ? "'Noto Nastaliq Urdu', 'Arial Unicode MS'" : "'Arial', 'Helvetica', sans-serif" }} !important; }
        @if(request('lang') !== 'ur')
        html, body, div, span, table, th, td, p, a, strong, b, .item-name, .item-details { font-weight: 900 !important; -webkit-text-stroke: 0.5px #000 !important; text-shadow: 0.5px 0.5px 0px #000 !important; }
        @else
        html, body, div, span, table, th, td, p, a, strong, b, .item-name, .item-details { font-weight: 700 !important; }
        @endif

        @page { margin: 0; }
        @font-face {
            font-family: 'Revue';
            src: url('/revue/reve.ttf?v=1.1') format("truetype");
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            width: 80mm;
            margin: 0 auto;
            padding: 20px;
            font-size: 12px;
            color: #000;
            line-height: 1.2;
            font-weight: 700;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: {{ request('lang') === 'ur' ? '700' : '900' }}; }
        
        .header-container {
            position: relative;
            margin-bottom: 10px;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
        }
        .merchant-name {
            font-size: 20px;
            font-weight: {{ request('lang') === 'ur' ? '700' : '900' }};
            text-transform: uppercase;
        }
        .merchant-address {
            font-size: 9px;
            text-transform: uppercase;
        }

        .info-grid {
            margin-bottom: 8px;
            font-size: 10px;
            text-transform: uppercase;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1px;
        }

        .separator {
            border-top: 1px solid #000;
            margin: 5px 0;
        }

        .ledger-table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
            font-size: 10px;
        }
        .ledger-table th {
            text-align: left;
            border-bottom: 1px solid #000;
            padding: 3px 0;
        }
        .ledger-table td {
            padding: 5px 0;
            vertical-align: top;
            border-bottom: 0.5px solid #eee;
        }

        .balance-block {
            margin-top: 10px;
            padding: 8px;
            border: 2px solid #000;
            background: #f9f9f9;
        }
        .final-balance {
            font-size: 18px;
            font-weight: {{ request('lang') === 'ur' ? '700' : '900' }};
            text-align: center;
        }

        .footer-note {
            margin-top: 15px;
            font-size: 11px;
            text-align: center;
            font-weight: {{ request('lang') === 'ur' ? '700' : '900' }};
            border-top: 1px solid #000;
            padding-top: 5px;
        }

        @media print {
            body { padding: 0; margin: 0; }
        }
    </style>

@include('backend.layouts.watermark', ['type' => 'thermal'])
</head>
<body>
    <div class="header-container text-center">
        <div class="merchant-name">DANYAL AUTOS</div>
        <div class="merchant-address">
            12-Butt Market, Badami Bagh, Lahore<br>
            TEL: 042-37727045 | MOB: 0304-2000274
        </div>
    </div>

    <div class="info-grid text-center">
        <div class="bold" style="font-size: 14px; margin-bottom: 5px;">SUPPLIER STATEMENT</div>
        <div class="info-row">
            <span>Supplier: <strong>{{ strtoupper($supplier->name) }}</strong></span>
        </div>
        @if($supplier->company_name)
        <div class="info-row">
            <span>Company: <strong>{{ strtoupper($supplier->company_name) }}</strong></span>
        </div>
        @endif
        <div class="info-row">
            <span>Printed: <strong>{{ date('d/m/y H:i') }}</strong></span>
        </div>
    </div>

    <table class="ledger-table">
        <thead>
            <tr>
                <th width="25%">DATE</th>
                <th width="45%">DESC</th>
                <th width="30%" class="text-right">AMOUNT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ledger as $item)
                <tr>
                    <td>{{ date('d/m/y', strtotime($item->transaction_date)) }}</td>
                    <td>
                        {{ strtoupper($item->description) }}
                        <div style="font-size: 8px; color: #666;">
                            TYPE: {{ strtoupper($item->type) }}
                        </div>
                    </td>
                    <td class="text-right bold">
                        {{ $item->type == 'credit' ? '-' : '' }}{{ number_format($item->amount, 0) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="balance-block">
        <div class="text-center" style="font-size: 10px; text-transform: uppercase;">Current Balance Payable</div>
        <div class="final-balance">Rs. {{ number_format($supplier->current_balance, 0) }}</div>
    </div>

    <div class="footer-note">
        ACCOUNT STATEMENT SUMMARY
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="/backend/js/bluetooth-print.js"></script>
    <script>
        const _isMobile = /Android|iPhone|iPad/i.test(navigator.userAgent);
        window.onload = function() {
            if (_isMobile) {
                if (localStorage.getItem('drautos_bt_name')) {
                    setTimeout(function() { window.drautosBTPrint(); }, 1000);
                }
            } else {
                window.print();
            }
        }
    </script>
@include('backend.partials.bluetooth-print-btn')
</body>
</html>


