<?php
$json = file_get_contents('C:\Users\T L S\proj\drautos.store\debug.json');
if (substr($json, 0, 2) === "\xff\xfe") {
    $json = substr($json, 2);
    $json = str_replace("\x00", "", $json);
}

$data = json_decode($json, true);
if (is_array($data)) {
    foreach ($data as $line) {
        if (strpos($line, 'production.ERROR') !== false || strpos($line, 'Exception') !== false) {
            echo $line . "\n";
        }
    }
} else {
    echo "JSON decode error: " . json_last_error_msg();
}
