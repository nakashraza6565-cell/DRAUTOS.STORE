<?php
echo "Fix script content check:\n";
$content = file_get_contents(__DIR__ . '/fix_return_to_credit.php');
echo "Length: " . strlen($content) . "\n";
echo "Contains u909342762: " . (strpos($content, 'u909342762') !== false ? 'YES' : 'NO') . "\n";
echo "Contains u704900370: " . (strpos($content, 'u704900370') !== false ? 'YES' : 'NO') . "\n";
echo "Contains VERSION 4: " . (strpos($content, 'VERSION 4') !== false ? 'YES' : 'NO') . "\n";
echo "First 300 chars:\n";
echo substr($content, 0, 300) . "\n";
?>
