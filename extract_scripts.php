<?php
$html = file_get_contents('rendered_edit_page.html');
if ($html === false) {
    die("Failed to read rendered_edit_page.html");
}

$dom = new DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTML($html);
libxml_clear_errors();

$scripts = $dom->getElementsByTagName('script');
$output = "";
$count = 0;
foreach ($scripts as $script) {
    $src = $script->getAttribute('src');
    $content = $script->nodeValue;
    $count++;
    $output .= "=== Script #$count " . ($src ? "(src: $src)" : "(inline)") . " ===\n";
    if ($content) {
        $output .= trim($content) . "\n";
    } else {
        $output .= "[External script]\n";
    }
    $output .= "\n\n";
}

file_put_contents('extracted_scripts.txt', $output);
echo "Extracted $count scripts to extracted_scripts.txt\n";
