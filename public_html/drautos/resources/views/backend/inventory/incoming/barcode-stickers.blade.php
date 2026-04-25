<!DOCTYPE html>
<html>
<head>
    <title>Barcode Stickers - {{$inventoryIncoming->reference_number}}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        @media print {
            @page {
                size: 80mm auto;
                margin: 0;
            }
            
            body {
                width: 80mm;
                margin: 0;
                padding: 0;
            }
            
            .no-print {
                display: none;
            }
        }
        
        body {
            font-family: 'Arial', sans-serif;
            width: 80mm;
            margin: 0 auto;
            background: #fff;
        }
        
        .sticker {
            width: 100%;
            padding: 8px;
            border-bottom: 1px dashed #ccc;
            page-break-inside: avoid;
        }
        
        .sticker-header {
            text-align: center;
            margin-bottom: 6px;
        }
        
        .product-name {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 4px;
            text-align: center;
            line-height: 1.2;
        }
        
        .sku {
            font-size: 10px;
            color: #666;
            text-align: center;
            margin-bottom: 8px;
        }
        
        .barcode-container {
            text-align: center;
            margin: 8px 0;
        }
        
        .barcode-container img {
            max-width: 100%;
            height: auto;
        }
        
        .barcode-number {
            font-size: 10px;
            text-align: center;
            letter-spacing: 2px;
            margin-top: 2px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-top: 6px;
            font-size: 11px;
        }
        
        .info-label {
            font-weight: bold;
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .print-button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">
        <i class="fas fa-print"></i> Print Stickers
    </button>
    
    @foreach($barcodes as $barcode)
    <div class="sticker">
        <div class="product-name">{{ Str::limit($barcode['product_name'], 40) }}</div>
        <div class="sku">SKU: {{ $barcode['sku'] ?? 'N/A' }}</div>
        
        <div class="barcode-container">
            <img src="{{ $barcode['barcode_image'] }}" alt="Barcode">
            <div class="barcode-number">{{ $barcode['barcode_code'] }}</div>
        </div>
        
        <div class="info-row">
            <div>
                <span class="info-label">Box Qty:</span> {{ $barcode['box_quantity'] ?? 'N/A' }}
            </div>
            <div>
                <span class="info-label">Price:</span> PKR {{ number_format($barcode['price'], 2) }}
            </div>
        </div>
    </div>
    @endforeach
    
    <script>
        // Auto print on load (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
