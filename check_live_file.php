<?php
$content = file_get_contents('live_test.html');
if (strpos($content, 'renderOrderEditCart') !== false) {
    echo "SUCCESS: Found renderOrderEditCart in live rendered HTML!\n";
} else {
    echo "FAILURE: renderOrderEditCart not found in live rendered HTML.\n";
}
