<?php

$file = 'resources/views/backend/layouts/cart_drawer.blade.php';
$content = file_get_contents($file);

// 1. Replace button HTML
$old_btn = '<button id="global-cart-btn" class="btn btn-warning shadow-lg animated-pulse" style="position: fixed; right: 20px; top: 50%; transform: translateY(-50%); z-index: 1030; border-radius: 50%; width: 60px; height: 60px; display: none; align-items: center; justify-content: center; font-size: 24px; border: 3px solid #fff; cursor: pointer; transition: all 0.3s ease;">
    <i class="fas fa-shopping-basket text-white"></i>
    <span class="badge badge-danger position-absolute" id="global-cart-badge" style="top: -5px; right: -5px; font-size: 13px; border: 2px solid #fff; border-radius: 50%;">0</span>
</button>';

$new_btn = '<button id="global-cart-btn" class="shadow-lg" style="position: fixed !important; top: 50% !important; right: 0 !important; left: auto !important; bottom: auto !important; transform: translateY(-50%) !important; width: 44px; height: 80px; border-radius: 12px 0 0 12px !important; background: #facc15 !important; color: #083259 !important; display: none; flex-direction: column; align-items: center; justify-content: center; z-index: 999999 !important; cursor: pointer; border: none; transition: all 0.3s ease;">
    <span class="badge badge-danger position-absolute shadow-sm" id="global-cart-badge" style="top: -6px; left: -6px; font-size: 11px; border: 2px solid #fff; border-radius: 50%; padding: 4px 6px;">0</span>
    <i class="fas fa-shopping-basket mb-1" style="font-size: 15px;"></i>
    <span style="writing-mode: vertical-rl; text-orientation: mixed; transform: rotate(180deg); font-size: 11px; font-weight: 800; letter-spacing: 1px;">CART</span>
</button>';

if (strpos($content, '<button id="global-cart-btn"') !== false) {
    // Regex replace to handle exact whitespace differences just in case
    $content = preg_replace('/<button id="global-cart-btn"[^>]*>[\s\S]*?<\/button>/', $new_btn, $content);
}

// 2. Remove Drag JS
$drag_js = '/\/\/\s*Drag functionality for global cart button[\s\S]*?let isDragging = false;[\s\S]*?isDragging = false;\s*\},\s*50\);\s*\}/';
$content = preg_replace($drag_js, '', $content);

// 3. Remove if(isDragging) return;
$content = str_replace('if(isDragging) return;', '', $content);

// 4. Remove #global-cart-btn:hover { transform: ... } CSS block entirely, or update it
$hover_css = '/#global-cart-btn:hover\s*\{\s*transform:\s*translateY\(-50%\)\s*scale\(1\.1\);\s*\}/';
// We can just remove it because scaling a fixed edge tab feels weird, or we can make it pop out leftwards.
// Let's just remove the scale effect
$content = preg_replace($hover_css, '', $content);

file_put_contents($file, $content);
echo "Button updated.\n";
