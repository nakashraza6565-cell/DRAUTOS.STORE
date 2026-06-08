<?php
$dir = __DIR__ . '/drautos/resources/views/backend';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
foreach ($iterator as $file) {
    if ($file->isFile() && strpos($file->getFilename(), 'thermal') !== false && strpos($file->getFilename(), '.blade.php') !== false) {
        $content = file_get_contents($file->getPathname());
        
        // Remove the previous robust rule
        $content = preg_replace('/\* \{ box-sizing: border-box; \}\s*html, body, div, span, table, th, td, p, a, strong, b, \.item-name, \.item-details \{[^\}]+\}\s*/', '', $content);
        
        // Add new robust CSS rule WITH text-stroke and text-shadow to force thickness
        $newCss = "* { box-sizing: border-box; }\n        html, body, div, span, table, th, td, p, a, strong, b, .item-name, .item-details { font-weight: 900 !important; color: #000 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; -webkit-text-stroke: 0.5px #000 !important; text-shadow: 0px 0px 1px #000 !important; font-family: 'Arial', 'Helvetica', sans-serif !important; }\n";
        
        // Fix the urdu part: if Urdu is needed, we will do it differently. Wait, if I do font-family Arial here, Urdu breaks. 
        // Let's use a blade conditional inside the CSS string!
        $newCss = "* { box-sizing: border-box; }\n        html, body, div, span, table, th, td, p, a, strong, b, .item-name, .item-details { font-weight: 900 !important; color: #000 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; -webkit-text-stroke: 0.5px #000 !important; text-shadow: 0.5px 0.5px 0px #000 !important; font-family: {{ request('lang') === 'ur' ? \"'Noto Nastaliq Urdu', 'Arial Unicode MS'\" : \"'Arial', 'Helvetica', sans-serif\" }} !important; }\n";

        // Insert after <style>
        $content = preg_replace('/<style>/', "<style>\n        " . $newCss, $content);
        
        file_put_contents($file->getPathname(), $content);
        echo "Updated: " . $file->getPathname() . "\n";
    }
}
