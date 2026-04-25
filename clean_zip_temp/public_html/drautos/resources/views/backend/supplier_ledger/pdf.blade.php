<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Supplier Ledger | {{ $supplier->name }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #333; line-height: 1.5; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #2c3e50; padding-bottom: 10px; }
        .company-name { font-size: 24px; font-weight: bold; color: #2c3e50; text-transform: uppercase; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { width: 50%; vertical-align: top; }
        .ledger-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .ledger-table th { background-color: #2c3e50; color: white; padding: 8px; text-align: left; text-transform: uppercase; font-size: 10px; }
        .ledger-table td { padding: 8px; border-bottom: 1px solid #eee; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-weight-bold { font-weight: bold; }
        .footer { margin-top: 50px; text-align: center; font-size: 9px; color: #777; }
        .balance-card { background: #f9f9f9; padding: 15px; border-radius: 5px; margin-top: 10px; }
        @font-face {
            font-family: 'Revue';
            src: url("{{ str_replace('\\', '/', public_path('revue/reve.ttf')) }}") format("truetype");
            font-weight: normal;
            font-style: normal;
        }
        .watermark {
            position: fixed;
            top: 30%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 400px;
            color: #2c3e50;
            opacity: 0.15;
            z-index: -1000;
            font-family: 'Revue', sans-serif;
            text-transform: uppercase;
            pointer-events: none;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="watermark">DR</div>
    <div class="header">
        <div class="company-name">Danyal Autos</div>
        <div>12-BUTT MARKET BADAMI BAGH LAHORE</div>
        <div>Contact: +923042000274 | 04237727045</div>
        <h2 style="margin-top: 15px; color: #2c3e50;">SUPPLIER ACCOUNT STATEMENT</h2>
    </div>

    <table class="info-table">
        <tr>
            <td>
                <strong>SUPPLIER DETAILS:</strong><br>
                Name: {{ $supplier->name }}<br>
                Company: {{ $supplier->company_name }}<br>
                Phone: {{ $supplier->phone }}
            </td>
            <td class="text-right">
                <strong>STATEMENT SUMMARY:</strong><br>
                Date: {{ date('d M, Y') }}<br>
                Statement Period: Up to {{ date('d M, Y') }}<br>
                <div class="balance-card">
                    <span style="font-size: 14px;">Total Payable:</span><br>
                    <span style="font-size: 18px; font-weight: bold; color: #c0392b;">Rs. {{ number_format($supplier->current_balance, 2) }}</span>
                </div>
            </td>
        </tr>
    </table>

    <table class="ledger-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Category</th>
                <th>Description</th>
                <th class="text-right">Debit (Purchases)</th>
                <th class="text-right">Credit (Payments)</th>
                <th class="text-right">Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ledger as $item)
                <tr>
                    <td>{{ $item->transaction_date->format('d/m/Y') }}</td>
                    <td>{{ ucfirst($item->category) }}</td>
                    <td>{{ $item->description }}</td>
                    <td class="text-right">{{ $item->type == 'debit' ? number_format($item->amount, 2) : '-' }}</td>
                    <td class="text-right">{{ $item->type == 'credit' ? number_format($item->amount, 2) : '-' }}</td>
                    <td class="text-right font-weight-bold">Rs. {{ number_format($item->balance, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <strong>THANK YOU FOR YOUR PARTNERSHIP!</strong><br>
        This is a computer generated document. Danyal Autos &copy; {{ date('Y') }}
    </div>
</body>
</html>
