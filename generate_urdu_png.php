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

$width = 250;
$height = 330;
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
$bbox = imagettfbbox(10, 0, $fontPath, $title);
$textWidth = $bbox[2] - $bbox[0];
$x = $width - $textWidth - 5;
imagettftext($im, 10, 0, $x, 22, $red, $fontPath, $title);

// 7 Points split manually for 250px width
$lines = [
    [
        prepForGd('خریدے گئے سامان کی واپسی یا تبدیلی') . ' .1',
        prepForGd('15 یوم کے اندر ممکن ہے۔') . '   '
    ],
    [
        prepForGd('15 دن گزر جانے کے بعد، یا واپسی') . ' .2',
        prepForGd('کی کوئی واضح وجہ پیش نہ کرنے کی') . '   ',
        prepForGd('صورت میں، رقم سے 25 فیصد') . '   ',
        prepForGd('کٹوتی کی جائے گی۔') . '   '
    ],
    [
        prepForGd('گاہک کی خواہش کے مطابق رقم کی') . ' .3',
        prepForGd('واپسی نقد (کیش) یا سٹور') . '   ',
        prepForGd('کریڈٹ کی شکل میں کی جائے گی۔') . '   '
    ],
    [
        prepForGd('واپسی کے وقت اصل بل پیش کرنا') . ' .4',
        prepForGd('ضروری ہے۔ بل نہ ہونے کی صورت') . '   ',
        prepForGd('میں آرڈر نمبر (Order Number)') . '   ',
        prepForGd('فراہم کرنا لازمی ہے۔') . '   '
    ],
    [
        prepForGd('درآمد شدہ (امپورٹڈ) سامان اور ٹوٹ') . ' .5',
        prepForGd('پھوٹ کا شکار اشیاء ہرگز واپس') . '   ',
        prepForGd('نہیں لی جائیں گی۔') . '   '
    ],
    [
        prepForGd('اگر کوئی پراڈکٹ اپنے مقصد کے') . ' .6',
        prepForGd('مطابق کام نہ کرے تو نقص کی') . '   ',
        prepForGd('صورت میں اسے کسی بھی وقت واپس') . '   ',
        prepForGd('کیا جا سکتا ہے، بشرطیکہ سامان') . '   ',
        prepForGd('اپنی اصل پیکنگ میں ہو۔') . '   '
    ],
    [
        prepForGd('تمام پائپ وارنٹی کے حامل ہیں') . ' .7',
        prepForGd('اور ان کا کلیم قابلِ قبول ہے۔') . '   '
    ]
];

$y = 44;
foreach ($lines as $group) {
    foreach ($group as $line) {
        $bbox = imagettfbbox(7.5, 0, $fontPath, $line);
        $textWidth = $bbox[2] - $bbox[0];
        $x = $width - $textWidth - 5;
        
        imagettftext($im, 7.5, 0, $x, $y, $dark, $fontPath, $line);
        $y += 13;
    }
    $y += 4; // Space between points
}

if (!is_dir(__DIR__ . '/backend/img')) {
    mkdir(__DIR__ . '/backend/img', 0755, true);
}

imagepng($im, __DIR__ . '/backend/img/urdu_terms.png');
imagedestroy($im);
echo "Image generated successfully at " . __DIR__ . '/backend/img/urdu_terms.png';
