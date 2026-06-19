<?php
$path = 'c:\Users\T L S\proj\drautos.store\rendered_edit_page.html';
if (file_exists($path)) {
    $content = file_get_contents($path);
    $pos = strpos($content, 'let rawCart =');
    if ($pos !== false) {
        $end = strpos($content, ';', $pos);
        echo "Found rawCart:\n" . substr($content, $pos, $end - $pos + 1) . "\n";
    } else {
        echo "rawCart not found in HTML file.\n";
    }
} else {
    echo "rendered_edit_page.html not found.\n";
}
