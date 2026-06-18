<?php
header('Content-Type: text/plain; charset=utf-8');
require 'drautos/vendor/autoload.php';
$app = require_once 'drautos/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $admins = \App\User::where('role', 'admin')->get();
    foreach ($admins as $admin) {
        echo "ID: {$admin->id} | Name: {$admin->name} | Email: {$admin->email} | Role: {$admin->role}\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
