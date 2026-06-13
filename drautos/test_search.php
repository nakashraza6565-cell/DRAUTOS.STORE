<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/pos/search-products', 'GET', ['customer_id' => 2]);
$response = $kernel->handle($request);
echo "STATUS: " . $response->getStatusCode() . "\n";
echo "CONTENT: " . substr($response->getContent(), 0, 500) . "\n";
if ($response->exception) {
    echo "EXCEPTION: " . $response->exception->getMessage() . "\n";
}
