<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = \App\User::where('role', 'admin')->get();
foreach($users as $user) {
    echo "User {$user->email} has roles: " . implode(', ', $user->getRoleNames()->toArray()) . "\n";
}
