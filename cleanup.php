<?php

$file = 'drautos/resources/views/backend/pos/index.blade.php';
$content = file_get_contents($file);

// Replace let cart = [];
$content = str_replace('let cart = [];', '', $content);

// Replace cart.push, cart.forEach etc.
$content = preg_replace('/\bcart\./', 'window.posCart.', $content);
$content = preg_replace('/\bcart\[/', 'window.posCart[', $content);

function remove_function($func_name, $code) {
    $pattern = '/function\s+' . $func_name . '\s*\([^)]*\)\s*\{/';
    if (preg_match($pattern, $code, $matches, PREG_OFFSET_CAPTURE)) {
        $start_idx = $matches[0][1];
        $brace_count = 0;
        $in_string = false;
        $string_char = '';
        $i = $start_idx;
        while ($i < strlen($code)) {
            $char = $code[$i];
            if (in_array($char, ['"', "'", '`'])) {
                if (!$in_string) {
                    $in_string = true;
                    $string_char = $char;
                } elseif ($string_char === $char && $code[$i-1] !== '\\') {
                    $in_string = false;
                }
            }
            if (!$in_string) {
                if ($char === '{') {
                    $brace_count++;
                } elseif ($char === '}') {
                    $brace_count--;
                    if ($brace_count === 0) {
                        $end_idx = $i + 1;
                        return substr($code, 0, $start_idx) . substr($code, $end_idx);
                    }
                }
            }
            $i++;
        }
    }
    return $code;
}

function remove_jq_on($selector, $code) {
    $pattern = "/\$\(document\)\.on\([^,]+,\s*['\"]" . $selector . "['\"]\s*,\s*function\s*\([^)]*\)\s*\{/";
    if (preg_match($pattern, $code, $matches, PREG_OFFSET_CAPTURE)) {
        $start_idx = $matches[0][1];
        $brace_count = 0;
        $in_string = false;
        $string_char = '';
        $i = $start_idx;
        while ($i < strlen($code)) {
            $char = $code[$i];
            if (in_array($char, ['"', "'", '`'])) {
                if (!$in_string) {
                    $in_string = true;
                    $string_char = $char;
                } elseif ($string_char === $char && $code[$i-1] !== '\\') {
                    $in_string = false;
                }
            }
            if (!$in_string) {
                if ($char === '{') {
                    $brace_count++;
                } elseif ($char === '}') {
                    $brace_count--;
                    if ($brace_count === 0) {
                        $end_idx = $i + 1;
                        while ($end_idx < strlen($code) && in_array($code[$end_idx], [' ', "\n", "\r", "\t", ')', ';'])) {
                            $end_idx++;
                        }
                        return substr($code, 0, $start_idx) . substr($code, $end_idx);
                    }
                }
            }
            $i++;
        }
    }
    return $code;
}

$content = remove_function('renderCart', $content);
$content = remove_function('updateSummary', $content);
$content = remove_function('updatePrice', $content);
$content = remove_function('updateQty', $content);
$content = remove_function('removeFromCart', $content);
$content = remove_jq_on('#complete-order', $content);
$content = remove_jq_on('#clear-cart', $content);

// Change renderCart(); to window.saveCart();
$content = str_replace('renderCart();', 'window.saveCart();', $content);

file_put_contents($file, $content);
echo "Cleanup complete.\n";

