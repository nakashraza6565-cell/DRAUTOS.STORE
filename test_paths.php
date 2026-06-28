<?php
require __DIR__ . '/drautos/vendor/autoload.php';
$app = require_once __DIR__ . '/drautos/bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->handle(Illuminate\Http\Request::capture());

$p1 = public_path('backend/img/urdu_terms.png');
$p2 = base_path('../backend/img/urdu_terms.png');
$p3 = __DIR__ . '/backend/img/urdu_terms.png';

echo "public_path file exists: " . (file_exists($p1) ? 'YES' : 'NO') . " ($p1)\n";
echo "base_path file exists: " . (file_exists($p2) ? 'YES' : 'NO') . " ($p2)\n";
echo "absolute dir file exists: " . (file_exists($p3) ? 'YES' : 'NO') . " ($p3)\n";

