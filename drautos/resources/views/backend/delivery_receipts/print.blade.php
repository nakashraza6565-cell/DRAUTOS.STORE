<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bilty - {{$receipt->receipt_number}}</title>
    <style>
        @page { margin: 0; }
        body {
            margin: 0;
            padding: 10px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            color: #000;
            background: #fff;
            width: 80mm; /* standard thermal receipt width */
            margin: 0 auto;
        }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .mt-2 { margin-top: 10px; }
        .mb-2 { margin-bottom: 10px; }
        .divider {
            border-bottom: 1px dashed #000;
            margin: 10px 0;
        }
        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .row .label { font-weight: bold; width: 40%; }
        .row .value { width: 60%; text-align: right; word-wrap: break-word; }
        .header-title { font-size: 18px; font-weight: bold; margin-bottom: 5px; text-transform: uppercase; }
        @media print {
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print(); setTimeout(() => window.history.back(), 1000);">

    <div class="text-center">
        <div class="header-title">Danyal Autos</div>
        <div>(Lahore)</div>
        <div class="mt-2 text-bold">DELIVERY RECEIPT</div>
    </div>

    <div class="divider"></div>

    <div class="row">
        <div class="label">Receipt No:</div>
        <div class="value">{{$receipt->receipt_number}}</div>
    </div>
    <div class="row">
        <div class="label">Date:</div>
        <div class="value">{{$receipt->date}}</div>
    </div>
    <div class="row">
        <div class="label">Courier:</div>
        <div class="value">{{$receipt->courier_company ?? 'N/A'}}</div>
    </div>

    <div class="divider"></div>

    <div class="row">
        <div class="label">Sender:</div>
        <div class="value">{{$receipt->sender_name}}</div>
    </div>
    
    <div class="divider"></div>

    <div class="row">
        <div class="label">Receiver:</div>
        <div class="value text-bold" style="font-size:16px;">{{$receipt->receiver_name}}</div>
    </div>
    @if($receipt->address)
    <div class="row">
        <div class="label">Address:</div>
        <div class="value">{{$receipt->address}}</div>
    </div>
    @endif
    @if($receipt->city)
    <div class="row">
        <div class="label">City:</div>
        <div class="value text-bold" style="font-size:16px;">{{$receipt->city}}</div>
    </div>
    @endif

    <div class="divider"></div>

    <div class="row">
        <div class="label">Cartons:</div>
        <div class="value">{{$receipt->no_of_cartons}}</div>
    </div>
    <div class="row">
        <div class="label">Bags:</div>
        <div class="value">{{$receipt->no_of_bags}}</div>
    </div>
    <div class="divider"></div>
    <div class="row" style="font-size: 16px;">
        <div class="label">Total Parcels:</div>
        <div class="value text-bold">{{$receipt->total_parcels}}</div>
    </div>

    <div class="divider"></div>
    
    <div class="text-center mt-2" style="font-size: 12px;">
        *** Thank You ***<br>
        Powered by DRAUTOS
    </div>

</body>
</html>
