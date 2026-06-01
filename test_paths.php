<?php
require __DIR__ . '/drautos/vendor/autoload.php';
$app = require_once __DIR__ . '/drautos/bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->handle(Illuminate\Http\Request::capture());
echo "public_path: " . public_path() . "\n";
echo "base_path: " . base_path() . "\n";
echo "storage_path: " . storage_path() . "\n";
