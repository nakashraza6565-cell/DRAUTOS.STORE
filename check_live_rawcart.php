<?php
header('Content-Type: text/plain; charset=utf-8');
$html = file_get_contents('https://drautos.store/test-render-edit/1296?nocache=' . time());
if ($html === false) {
    echo "Failed to fetch from live server.\n";
    exit;
}

$pos = strpos($html, 'let rawCart =');
if ($pos !== false) {
    $end = strpos($html, ';', $pos);
    echo "Found rawCart on live server:\n" . substr($html, $pos, $end - $pos + 1) . "\n";
} else {
    echo "rawCart script block not found in the live response.\n";
}

$pos2 = strpos($html, '<title>');
if ($pos2 !== false) {
    $end2 = strpos($html, '</title>', $pos2);
    echo "Live page title: " . substr($html, $pos2, $end2 - $pos2 + 8) . "\n";
}
