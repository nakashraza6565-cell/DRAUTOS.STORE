<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = \App\User::where('role', 'admin')->get();
foreach($users as $user) {
    if(!$user->hasRole('admin')) {
        $user->assignRole('admin');
        echo "Assigned admin role to {$user->email}\n";
    }
}
echo "Roles assignment complete.\n";
