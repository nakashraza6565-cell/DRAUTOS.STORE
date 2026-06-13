<?php

$pos_file = 'resources/views/backend/pos/index.blade.php';
$pos_content = file_get_contents($pos_file);

$start_idx = strpos($pos_content, "$('#complete-order').on('click'");
if ($start_idx !== false) {
    $brace_count = 0;
    $end_idx = -1;
    for ($i = $start_idx; $i < strlen($pos_content); $i++) {
        if ($pos_content[$i] === '{') {
            $brace_count++;
        } elseif ($pos_content[$i] === '}') {
            $brace_count--;
            if ($brace_count === 0) {
                $end_idx = $i + 1;
                while ($end_idx < strlen($pos_content) && in_array($pos_content[$end_idx], [')', ';', "\n", "\r", ' '])) {
                    $end_idx++;
                }
                break;
            }
        }
    }
    
    if ($end_idx !== -1) {
        $extracted_block = substr($pos_content, $start_idx, $end_idx - $start_idx);
        $pos_content = substr($pos_content, 0, $start_idx) . substr($pos_content, $end_idx);
        
        $extracted_block = str_replace("$('#complete-order').on('click', function() {", "$(document).on('click', '#complete-order', function() {", $extracted_block);
        $extracted_block = str_replace("cart: cart", "cart: window.posCart", $extracted_block);
        
        $drawer_file = 'resources/views/backend/layouts/cart_drawer.blade.php';
        $drawer_content = file_get_contents($drawer_file);
        
        $drawer_start = strpos($drawer_content, "$(document).on('click', '#complete-order'");
        if ($drawer_start !== false) {
            $drawer_brace_count = 0;
            $drawer_end = -1;
            for ($i = $drawer_start; $i < strlen($drawer_content); $i++) {
                if ($drawer_content[$i] === '{') {
                    $drawer_brace_count++;
                } elseif ($drawer_content[$i] === '}') {
                    $drawer_brace_count--;
                    if ($drawer_brace_count === 0) {
                        $drawer_end = $i + 1;
                        while ($drawer_end < strlen($drawer_content) && in_array($drawer_content[$drawer_end], [')', ';', "\n", "\r", ' '])) {
                            $drawer_end++;
                        }
                        break;
                    }
                }
            }
            
            if ($drawer_end !== -1) {
                $drawer_content = substr($drawer_content, 0, $drawer_start) . $extracted_block . substr($drawer_content, $drawer_end);
                file_put_contents($drawer_file, $drawer_content);
                echo "Updated cart_drawer.blade.php\n";
            }
        }
    }
}

if (strpos(substr($pos_content, -50), '@endsection') === false) {
    $pos_content = rtrim($pos_content) . "\n@endsection\n";
}

file_put_contents($pos_file, $pos_content);
echo "Updated pos/index.blade.php\n";
