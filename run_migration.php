<?php
$output = shell_exec('cd drautos && php artisan migrate --force 2>&1');
echo "<pre>Migration output:\n$output</pre>";
