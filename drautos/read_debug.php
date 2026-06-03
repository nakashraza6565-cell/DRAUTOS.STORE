<?php
$json = file_get_contents('debug.json');
$data = json_decode($json, true);
if (is_array($data)) {
    // Print the first 10 lines
    foreach (array_slice($data, 0, 10) as $line) {
        echo $line;
    }
}
