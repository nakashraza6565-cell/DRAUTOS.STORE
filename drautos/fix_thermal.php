<?php
$files = glob("resources/views/backend/**/*.blade.php");
$files = array_merge($files, glob("resources/views/backend/**/**/*.blade.php"));
$thermalFiles = [];

foreach ($files as $file) {
    if (strpos($file, 'thermal') !== false) {
        $thermalFiles[] = $file;
    }
}

foreach ($thermalFiles as $file) {
    $content = file_get_contents($file);
    
    // Fix CSS
    $oldCss = "html, body, div, span, table, th, td, p, a, strong, b, .item-name, .item-details { font-weight: 900 !important; color: #000 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; -webkit-text-stroke: 0.5px #000 !important; text-shadow: 0.5px 0.5px 0px #000 !important; font-family: {{ request('lang') === 'ur' ? \"'Noto Nastaliq Urdu', 'Arial Unicode MS'\" : \"'Arial', 'Helvetica', sans-serif\" }} !important; }";
    
    $newCss = "html, body, div, span, table, th, td, p, a, strong, b, .item-name, .item-details { color: #000 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; font-family: {{ request('lang') === 'ur' ? \"'Noto Nastaliq Urdu', 'Arial Unicode MS'\" : \"'Arial', 'Helvetica', sans-serif\" }} !important; }\n"
            . "        @if(request('lang') !== 'ur')\n"
            . "        html, body, div, span, table, th, td, p, a, strong, b, .item-name, .item-details { font-weight: 900 !important; -webkit-text-stroke: 0.5px #000 !important; text-shadow: 0.5px 0.5px 0px #000 !important; }\n"
            . "        @else\n"
            . "        html, body, div, span, table, th, td, p, a, strong, b, .item-name, .item-details { font-weight: 700 !important; }\n"
            . "        @endif";
            
    if (strpos($content, $oldCss) !== false) {
        $content = str_replace($oldCss, $newCss, $content);
    }
    
    // Change translatePartTitle(..., $isUrdu) to translatePartTitle(..., false)
    $content = str_replace('translatePartTitle($item->product->title ?? ($item->bundle->name ?? \'Item\'), $isUrdu)', 'translatePartTitle($item->product->title ?? ($item->bundle->name ?? \'Item\'), false)', $content);
    $content = str_replace('translatePartTitle($item->product->title ?? \'Item\', $isUrdu)', 'translatePartTitle($item->product->title ?? \'Item\', false)', $content);
    $content = str_replace('translatePartTitle($product->title ?? \'Item\', $isUrdu)', 'translatePartTitle($product->title ?? \'Item\', false)', $content);

    // Some places use it without variables, maybe?
    // Let's use a regex to be safe:
    $content = preg_replace('/translatePartTitle\((.+?),\s*\$isUrdu\)/', 'translatePartTitle($1, false)', $content);

    file_put_contents($file, $content);
}

echo "Done fixing " . count($thermalFiles) . " files.\n";
