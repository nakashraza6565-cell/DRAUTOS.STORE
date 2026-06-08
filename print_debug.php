<?php
$data = json_decode(file_get_contents('debug.json'));
if (is_array($data)) {
    foreach (array_slice($data, -80) as $line) {
        echo $line;
    }
}
