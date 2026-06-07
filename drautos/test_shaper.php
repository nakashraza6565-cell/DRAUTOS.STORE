<?php

class ArabicShaper {
    public static function shape($text) {
        $chars = [
            // Base => [Isolated, End, Middle, Beginning]
            'ا' => ["\u{0627}", "\u{FE8E}", "\u{FE8E}", "\u{0627}"],
            'آ' => ["\u{0622}", "\u{FE82}", "\u{FE82}", "\u{0622}"],
            'ب' => ["\u{0628}", "\u{FE90}", "\u{FE92}", "\u{FE91}"],
            'پ' => ["\u{067E}", "\u{FB57}", "\u{FB59}", "\u{FB58}"],
            'ت' => ["\u{062A}", "\u{FE96}", "\u{FE98}", "\u{FE97}"],
            'ٹ' => ["\u{0679}", "\u{FB67}", "\u{FB69}", "\u{FB68}"],
            'ث' => ["\u{062B}", "\u{FE9A}", "\u{FE9C}", "\u{FE9B}"],
            'ج' => ["\u{062C}", "\u{FE9E}", "\u{FEA0}", "\u{FE9F}"],
            'چ' => ["\u{0686}", "\u{FB7B}", "\u{FB7D}", "\u{FB7C}"],
            'ح' => ["\u{062D}", "\u{FEA2}", "\u{FEA4}", "\u{FEA3}"],
            'خ' => ["\u{062E}", "\u{FEA6}", "\u{FEA8}", "\u{FEA7}"],
            'د' => ["\u{062F}", "\u{FEAA}", "\u{FEAA}", "\u{062F}"],
            'ڈ' => ["\u{0688}", "\u{FB89}", "\u{FB89}", "\u{0688}"],
            'ذ' => ["\u{0630}", "\u{FEAC}", "\u{FEAC}", "\u{0630}"],
            'ر' => ["\u{0631}", "\u{FEAE}", "\u{FEAE}", "\u{0631}"],
            'ڑ' => ["\u{0691}", "\u{FB8D}", "\u{FB8D}", "\u{0691}"],
            'ز' => ["\u{0632}", "\u{FEB0}", "\u{FEB0}", "\u{0632}"],
            'ژ' => ["\u{0698}", "\u{FB8B}", "\u{FB8B}", "\u{0698}"],
            'س' => ["\u{0633}", "\u{FEB6}", "\u{FEB8}", "\u{FEB7}"],
            'ش' => ["\u{0634}", "\u{FEBA}", "\u{FEBC}", "\u{FEBB}"],
            'ص' => ["\u{0635}", "\u{FEBE}", "\u{FEC0}", "\u{FEBF}"],
            'ض' => ["\u{0636}", "\u{FEC2}", "\u{FEC4}", "\u{FEC3}"],
            'ط' => ["\u{0637}", "\u{FEC6}", "\u{FEC8}", "\u{FEC7}"],
            'ظ' => ["\u{0638}", "\u{FECA}", "\u{FECC}", "\u{FECB}"],
            'ع' => ["\u{0639}", "\u{FECE}", "\u{FED0}", "\u{FECF}"],
            'غ' => ["\u{063A}", "\u{FED2}", "\u{FED4}", "\u{FED3}"],
            'ف' => ["\u{0641}", "\u{FED6}", "\u{FED8}", "\u{FED7}"],
            'ق' => ["\u{0642}", "\u{FEDA}", "\u{FEDC}", "\u{FEDB}"],
            'ک' => ["\u{06A9}", "\u{FB8F}", "\u{FB91}", "\u{FB90}"],
            'گ' => ["\u{06AF}", "\u{FB93}", "\u{FB95}", "\u{FB94}"],
            'ل' => ["\u{0644}", "\u{FEE0}", "\u{FEE2}", "\u{FEE1}"],
            'م' => ["\u{0645}", "\u{FEE4}", "\u{FEE6}", "\u{FEE5}"],
            'ن' => ["\u{0646}", "\u{FEE8}", "\u{FEEA}", "\u{FEE9}"],
            'ں' => ["\u{06BA}", "\u{FB9F}", "\u{FB9F}", "\u{06BA}"],
            'و' => ["\u{0648}", "\u{FEEE}", "\u{FEEE}", "\u{0648}"],
            'ہ' => ["\u{06C1}", "\u{FBA7}", "\u{FBA9}", "\u{FBA8}"],
            'ھ' => ["\u{06BE}", "\u{FBAB}", "\u{FBAD}", "\u{FBAC}"],
            'ء' => ["\u{0621}", "\u{0621}", "\u{0621}", "\u{0621}"],
            'ی' => ["\u{06CC}", "\u{FBFE}", "\u{FC00}", "\u{FBFF}"],
            'ے' => ["\u{06D2}", "\u{FBAF}", "\u{FBAF}", "\u{06D2}"],
            'ئ' => ["\u{0626}", "\u{FE8A}", "\u{FE8C}", "\u{FE8B}"],
        ];

        // Characters that don't connect to the left
        $nonConnecting = ['ا', 'آ', 'د', 'ڈ', 'ذ', 'ر', 'ڑ', 'ز', 'ژ', 'و', 'ے', 'ء'];

        $len = mb_strlen($text, 'UTF-8');
        $output = '';
        
        for ($i = 0; $i < $len; $i++) {
            $char = mb_substr($text, $i, 1, 'UTF-8');
            
            if (!isset($chars[$char])) {
                $output .= $char;
                continue;
            }

            $prevChar = $i > 0 ? mb_substr($text, $i - 1, 1, 'UTF-8') : null;
            $nextChar = $i < $len - 1 ? mb_substr($text, $i + 1, 1, 'UTF-8') : null;

            $connectsRight = $prevChar && isset($chars[$prevChar]) && !in_array($prevChar, $nonConnecting);
            $connectsLeft = $nextChar && isset($chars[$nextChar]);

            // If current character is non-connecting to the left, it cannot connect left
            if (in_array($char, $nonConnecting)) {
                $connectsLeft = false;
            }

            if ($connectsRight && $connectsLeft) {
                $output .= $chars[$char][2]; // Middle
            } elseif ($connectsRight) {
                $output .= $chars[$char][1]; // End
            } elseif ($connectsLeft) {
                $output .= $chars[$char][3]; // Beginning
            } else {
                $output .= $chars[$char][0]; // Isolated
            }
        }
        
        // Reverse string for RTL in some buggy PDF renderers if necessary, but DomPDF usually needs RTL reversed string
        // Actually, DomPDF needs the string reversed if it doesn't do bidi!
        // Let's check if the current reshapeUrdu reversed it.
        // Current reshapeUrdu in Helpers didn't reverse it.
        
        return $output;
    }
}

echo ArabicShaper::shape('دانیال آٹوز');
