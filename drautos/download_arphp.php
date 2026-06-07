<?php
$url = 'https://raw.githubusercontent.com/KhaledAlshamaa/ar-php/master/src/Arabic.php';
$content = file_get_contents($url);
file_put_contents('app/Http/Arabic.php', $content);

$url2 = 'https://raw.githubusercontent.com/KhaledAlshamaa/ar-php/master/src/Arabic/Glyphs.php';
$content2 = file_get_contents($url2);
file_put_contents('app/Http/Glyphs.php', $content2);

echo "Downloaded Arabic.php and Glyphs.php\n";
