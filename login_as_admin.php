<?php
require 'drautos/vendor/autoload.php';
$app = require_once 'drautos/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$user = \App\User::find(1);
\Auth::login($user);

header('Location: /admin/order/534/edit');
exit;
