<?php
$file = __DIR__ . '/drautos/routes/web.php';
$content = file_get_contents($file);

$route = <<<'EOD'
Route::get('/debug-logs', function() {
    $logFile = storage_path('logs/laravel.log');
    if (file_exists($logFile)) {
        $lines = file($logFile);
        $last_lines = array_slice($lines, -250);
        return response()->json($last_lines);
    }
    return 'Log file not found.';
});
EOD;

$content = str_replace("Route::get('/fix-db', function () {", $route . "\nRoute::get('/fix-db', function () {", $content);
file_put_contents($file, $content);
echo "Added debug-logs route.\n";
