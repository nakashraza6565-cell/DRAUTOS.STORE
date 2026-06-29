<?php
require __DIR__ . '/drautos/vendor/autoload.php';
$app = require_once __DIR__ . '/drautos/bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->handle(Illuminate\Http\Request::capture());

function prepForGd($text) {
    $text = str_replace('(Order Number)', '__ORDER_NUMBER__', $text);
    $shaped = Helper::reshapeUrdu($text);
    $words = explode(' ', $shaped);
    $reversedWords = [];
    foreach ($words as $word) {
        if (preg_match('/^[a-zA-Z0-9\(\)\%\.\-\:\,\/\_]+$/', $word)) {
            $reversedWords[] = $word;
        } else {
            preg_match_all('/./us', $word, $ar);
            $reversedWords[] = implode('', array_reverse($ar[0]));
        }
    }
    $reversedSentence = implode(' ', array_reverse($reversedWords));
    return str_replace('__ORDER_NUMBER__', '(Order Number)', $reversedSentence);
}

$width = 330;
$height = 290;
$im = imagecreatetruecolor($width, $height);
$white = imagecolorallocate($im, 255, 255, 255);
$dark = imagecolorallocate($im, 34, 41, 47);
$red = imagecolorallocate($im, 211, 47, 47);

imagefilledrectangle($im, 0, 0, $width, $height, $white);

$fontPath = __DIR__ . '/revue/tahoma.ttf';

if (!file_exists($fontPath)) {
    die("Font not found at $fontPath");
}

// Title
$title = prepForGd('شرائط و ضوابط');
$bbox = imagettfbbox(11, 0, $fontPath, $title);
$textWidth = $bbox[2] - $bbox[0];
$x = $width - $textWidth - 10;
imagettftext($im, 11, 0, $x, 25, $red, $fontPath, $title);

// 7 Points split manually for 330px width
$lines = [
    [prepForGd('خریدے گئے سامان کی واپسی یا تبدیلی 15 یوم کے اندر ممکن ہے۔') . ' .1'],
    [
        prepForGd('15 دن گزر جانے کے بعد، یا واپسی کی کوئی واضح وجہ') . ' .2',
        prepForGd('پیش نہ کرنے کی صورت میں، رقم سے 25 فیصد کٹوتی کی جائے گی۔') . '   '
    ],
    [
        prepForGd('گاہک کی خواہش کے مطابق رقم کی واپسی نقد (کیش)') . ' .3',
        prepForGd('یا سٹور کریڈٹ کی شکل میں کی جائے گی۔') . '   '
    ],
    [
        prepForGd('واپسی کے وقت اصل بل پیش کرنا ضروری ہے۔ بل نہ') . ' .4',
        prepForGd('ہونے کی صورت میں آرڈر نمبر (Order Number) فراہم کرنا لازمی ہے۔') . '   '
    ],
    [prepForGd('درآمد شدہ (امپورٹڈ) سامان اور ٹوٹ پھوٹ کا شکار اشیاء ہرگز واپس نہیں لی جائیں گی۔') . ' .5'],
    [
        prepForGd('اگر کوئی پراڈکٹ اپنے مقصد کے مطابق کام نہ کرے تو نقص کی') . ' .6',
        prepForGd('صورت میں اسے کسی بھی وقت واپس کیا جا سکتا ہے، بشرطیکہ') . '   ',
        prepForGd('سامان اپنی اصل پیکنگ میں ہو۔') . '   '
    ],
    [prepForGd('تمام پائپ وارنٹی کے حامل ہیں اور ان کا کلیم قابلِ قبول ہے۔') . ' .7']
];

$y = 52;
foreach ($lines as $group) {
    foreach ($group as $line) {
        $bbox = imagettfbbox(8, 0, $fontPath, $line);
        $textWidth = $bbox[2] - $bbox[0];
        $x = $width - $textWidth - 10;
        
        imagettftext($im, 8, 0, $x, $y, $dark, $fontPath, $line);
        $y += 18;
    }
    $y += 4; // Extra space between bullet points
}

if (!is_dir(__DIR__ . '/backend/img')) {
    mkdir(__DIR__ . '/backend/img', 0755, true);
}

imagepng($im, __DIR__ . '/backend/img/urdu_terms.png');
imagedestroy($im);
echo "Image generated successfully at " . __DIR__ . '/backend/img/urdu_terms.png';
