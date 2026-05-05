<?php
$file = __DIR__ . '/drautos/resources/views/backend/index.blade.php';
echo "<h1>Deployment Debugger</h1>";
if (file_exists($file)) {
    echo "<p>File: <code>$file</code></p>";
    echo "<p>Last Modified: " . date("Y-m-d H:i:s", filemtime($file)) . "</p>";
    echo "<p>Content Preview (Ticker Color): <pre>";
    $content = file_get_contents($file);
    if (strpos($content, '#fbbf24') !== false) {
        echo "Found Yellow (#fbbf24) - FILE IS UPDATED";
    } else {
        echo "Found Old Version - FILE IS NOT UPDATED";
    }
    echo "</pre></p>";
} else {
    echo "<p style='color:red;'>ERROR: File not found at $file</p>";
}
?>
