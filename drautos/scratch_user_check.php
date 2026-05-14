<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\User;
$user1 = User::find(1);
if ($user1) {
    echo "User ID 1 Name: " . $user1->name . "\n";
} else {
    echo "User ID 1 not found.\n";
}
