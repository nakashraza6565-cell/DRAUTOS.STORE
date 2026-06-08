<?php
$dir = __DIR__ . '/drautos/resources/views/backend';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
foreach ($iterator as $file) {
    if ($file->isFile() && strpos($file->getFilename(), 'thermal') !== false && strpos($file->getFilename(), '.blade.php') !== false) {
        $content = file_get_contents($file->getPathname());
        $newContent = str_replace(
            '* { box-sizing: border-box; }',
            '* { box-sizing: border-box; font-weight: 900 !important; color: #000 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }',
            $content
        );
        file_put_contents($file->getPathname(), $newContent);
        echo "Updated: " . $file->getPathname() . "\n";
    }
}
