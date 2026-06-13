<?php

// 1. Fix pos/index.blade.php
$pos_file = 'resources/views/backend/pos/index.blade.php';
$pos_content = file_get_contents($pos_file);
// It currently ends with:
// @endpush
// @endsection
// We want to remove the @endsection at the very end.
$pos_content = rtrim($pos_content);
if (substr($pos_content, -11) === '@endsection') {
    $pos_content = substr($pos_content, 0, -11);
    file_put_contents($pos_file, $pos_content);
    echo "Removed extra @endsection from pos/index.blade.php\n";
} else {
    echo "Could not find @endsection at the very end of pos/index.blade.php\n";
}

// 2. Fix cart_drawer.blade.php
$drawer_file = 'resources/views/backend/layouts/cart_drawer.blade.php';
$drawer_content = file_get_contents($drawer_file);

// Find the missing braces for save-customer-btn ajax
// The block ends with:
//             error: function(err) {
//                 Swal.fire('Error', 'Failed to add customer', 'error');
//             }
//     window.fetchLastPurchase = function(cartItem) {

$target = "error: function(err) {
                Swal.fire('Error', 'Failed to add customer', 'error');
            }";

$replacement = "error: function(err) {
                Swal.fire('Error', 'Failed to add customer', 'error');
            }
        });
    });";

if (strpos($drawer_content, $target) !== false && strpos($drawer_content, $replacement) === false) {
    $drawer_content = str_replace($target, $replacement, $drawer_content);
    file_put_contents($drawer_file, $drawer_content);
    echo "Added missing braces to cart_drawer.blade.php\n";
} else {
    echo "Target not found or already replaced in cart_drawer.blade.php\n";
}
