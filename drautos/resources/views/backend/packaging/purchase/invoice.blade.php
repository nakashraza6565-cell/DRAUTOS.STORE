<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Purchase Invoice {{ $purchase->invoice_no }} | Danyal Autos</title>
    <style>
        @page { margin: 10mm; size: a4; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            color: #2D3748;
            line-height: 1.4;
            background: #fff;
            font-size: 11px;
        }
        
        .header {
            padding-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 20px;
            position: relative;
        }
        
        .company-info {
            float: left;
            width: 50%;
        }
        
        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #4b312c;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        
        .company-details {
            font-size: 10px;
            color: #718096;
        }
        
        .invoice-title-wrapper {
            float: right;
            width: 45%;
            text-align: right;
        }
        
        .invoice-label {
            font-size: 32px;
            font-weight: bold;
            color: #4b312c;
            margin: 0;
            line-height: 1;
        }
        
        .invoice-no-main {
            font-size: 12px;
            color: #718096;
            margin-top: 5px;
            font-weight: bold;
        }
        
        .info-grid {
            margin-bottom: 30px;
            margin-top: 20px;
        }
        
        .info-box {
            float: left;
            width: 55%;
        }
        
        .supplier-box {
            float: right;
            width: 40%;
            background: #fdfaf9;
            border: 1px solid #f2e9e7;
            padding: 12px;
            border-radius: 6px;
        }
        
        .box-title {
            font-size: 12px;
            font-weight: bold;
            color: #4b312c;
            margin-bottom: 8px;
            text-transform: uppercase;
            border-bottom: 1px solid #f2e9e7;
            padding-bottom: 3px;
        }
        
        .info-text {
            font-size: 11px;
            line-height: 1.5;
        }
        
        .item-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .item-table th {
            background-color: #4b312c;
            color: white;
            padding: 10px 8px;
            font-size: 10px;
            text-transform: uppercase;
            font-weight: bold;
            text-align: left;
        }
        
        .item-table td {
            padding: 10px 8px;
            font-size: 11px;
            border-bottom: 1px solid #edf2f7;
        }
        
        .totals-section {
            margin-top: 20px;
        }
        
        .totals-table-wrapper {
            float: right;
            width: 40%;
        }
        
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .totals-table td {
            padding: 8px 10px;
            font-size: 14px;
            font-weight: bold;
            background-color: #4b312c;
            color: white;
        }
        
        .final-footer {
            margin-top: 50px;
            text-align: center;
            font-size: 9px;
            color: #a0aec0;
            border-top: 1px solid #e2e8f0;
            padding-top: 20px;
        }
        
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    <div class="header clearfix">
        <div class="company-info">
            <div class="company-name">Danyal Autos</div>
            <div class="company-details">
                Industrial Zone, Gujranwala<br>
                Phone: +92 312 0000000<br>
                Date: {{ date('d M, Y', strtotime($purchase->purchase_date)) }}
            </div>
        </div>
        <div class="invoice-title-wrapper">
            <h1 class="invoice-label">PURCHASE INVOICE</h1>
            <div class="invoice-no-main">#{{ $purchase->invoice_no }}</div>
        </div>
    </div>

    <div class="info-grid clearfix">
        <div class="info-box">
            <div class="box-title">Purchased For:</div>
            <div class="info-text">
                <strong>Danyal Autos HQ</strong><br>
                Packaging & Stock Handling Department<br>
                Status: Completed
            </div>
        </div>
        <div class="supplier-box">
            <div class="box-title">Supplier Details:</div>
            <div class="info-text">
                <strong>{{ strtoupper($purchase->supplier->name ?? 'N/A') }}</strong><br>
                Phone: {{ $purchase->supplier->phone ?? 'N/A' }}<br>
                Email: {{ $purchase->supplier->email ?? 'N/A' }}
            </div>
        </div>
    </div>

    <table class="item-table">
        <thead>
            <tr>
                <th width="10%">TYPE</th>
                <th width="40%">MATERIAL DESCRIPTION</th>
                <th width="15%">SIZE</th>
                <th width="15%">QUANTITY</th>
                <th width="20%" style="text-align: right;">UNIT PRICE</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ strtoupper($purchase->packagingItem->type ?? 'N/A') }}</td>
                <td><strong>{{ $purchase->packagingItem->name ?? 'N/A' }}</strong></td>
                <td>{{ $purchase->packagingItem->size ?? 'N/A' }}</td>
                <td>{{ number_format($purchase->quantity, 2) }}</td>
                <td style="text-align: right;">Rs. {{ number_format($purchase->price, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="totals-section clearfix">
        <div class="totals-table-wrapper">
            <table class="totals-table">
                <tr>
                    <td style="text-align: left;">GRAND TOTAL</td>
                    <td style="text-align: right;">Rs. {{ number_format($purchase->total_price, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="final-footer">
        <strong>THANK YOU FOR YOUR SERVICE!</strong><br>
        This is a computer generated purchase record. | Danyal Autos &copy; {{ date('Y') }}
    </div>
</body>
</html>
