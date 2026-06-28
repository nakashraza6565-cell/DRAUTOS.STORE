<?php
// Bootstrap Laravel
define('LARAVEL_START', microtime(true));
require __DIR__.'/drautos/vendor/autoload.php';
$app = require_once __DIR__.'/drautos/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$admin = \App\User::where('role', 'admin')->first();
if ($admin) {
    auth()->login($admin);
}
header('Location: /admin');
