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
    
    // Replace font-weight: 900; with font-weight: {{ request('lang') === 'ur' ? '700' : '900' }};
    $content = str_replace('font-weight: 900;', "font-weight: {{ request('lang') === 'ur' ? '700' : '900' }};", $content);
    
    // Just in case it was font-weight:900;
    $content = str_replace('font-weight:900;', "font-weight: {{ request('lang') === 'ur' ? '700' : '900' }};", $content);

    // Some places might have inline styles like style="font-weight: 900;"
    // We handle it too if any.

    file_put_contents($file, $content);
}

echo "Replaced font-weight: 900 in " . count($thermalFiles) . " thermal files.\n";
