<?php
echo "Script hash check: " . md5_file(__FILE__) . "\n";
echo "Script modified: " . date('Y-m-d H:i:s', filemtime(__FILE__)) . "\n";
echo "First 200 chars:\n";
echo substr(file_get_contents(__FILE__), 0, 200) . "\n";
?>
