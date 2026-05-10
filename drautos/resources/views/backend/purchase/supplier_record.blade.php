<!DOCTYPE html>
<html>
<head>
    <title>Supplier Record - {{ $purchaseOrder->po_number }}</title>
    <style>
        * { box-sizing: border-box; }
        @page { margin: 0; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            width: 80mm;
            margin: 0 auto;
            padding: 20px;
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
        .merchant-name {
            font-size: 22px;
            font-weight: 900;
            text-transform: uppercase;
            margin-bottom: 2px;
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

        .item-list {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
        }
        .item-list th {
            text-align: left;
            font-size: 10px;
            border-bottom: 1px solid #000;
            padding: 5px 0;
        }
        .item-list td {
            padding: 10px 0;
            vertical-align: top;
            border-bottom: 0.5px solid #000;
        }
        .item-name {
            font-size: 12px;
            display: block;
            font-weight: 900;
        }
        .empty-box {
            height: 25px;
            border: 1px solid #000;
        }

        .footer-note {
            margin-top: 15px;
            font-size: 13px;
            text-align: center;
            font-weight: 900;
            border-top: 2px solid #000;
            padding-top: 10px;
        }

        @media print {
            body { padding: 5px; margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print();">
    <div class="header-container text-center">
        <div class="merchant-name">DANYAL AUTOS</div>
        <div class="merchant-address">
            12-Butt Market, Badami Bagh, Lahore<br>
            SUPPLIER RECORD RECEIPT
        </div>
    </div>

    <div class="info-grid">
        <div class="info-row">
            <span>PO #: <strong>{{ $purchaseOrder->po_number }}</strong></span>
            <span>Date: <strong>{{ date('d/m/y', strtotime($purchaseOrder->order_date)) }}</strong></span>
        </div>
        <div class="separator"></div>
        <div class="info-row">
            <span>Supplier: <strong>{{ strtoupper($purchaseOrder->supplier->name ?? 'N/A') }}</strong></span>
        </div>
    </div>

    <table class="item-list">
        <thead>
            <tr>
                <th width="40%">ITEM NAME</th>
                <th width="20%" class="text-center">QTY</th>
                <th width="20%" class="text-center">PRICE</th>
                <th width="20%" class="text-center">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchaseOrder->items as $item)
                <tr>
                    <td>
                        <span class="item-name">{{ strtoupper($item->product->title ?? 'N/A') }}</span>
                    </td>
                    <td><div class="empty-box"></div></td>
                    <td><div class="empty-box"></div></td>
                    <td><div class="empty-box"></div></td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td class="text-right bold" style="padding-top: 10px; font-size: 14px;">GRAND TOTAL:</td>
                <td colspan="3" style="padding-top: 10px;"><div class="empty-box" style="height: 30px; border: 2px solid #000;"></div></td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 30px; display: flex; justify-content: flex-end;">
        <div style="text-align: center; width: 50%;">
            <div style="border-top: 2px solid #000; padding-top: 5px; font-weight: 900; font-size: 12px; text-transform: uppercase;">
                SUPPLIER SIGNATURE
            </div>
        </div>
    </div>
    
    <div class="text-center no-print" style="margin-top: 10mm;">
        <button onclick="window.print()" style="padding: 5mm 10mm;">Print Again</button>
    </div>
</body>
</html>
