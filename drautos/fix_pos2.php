<?php

// 1. Fix cart_drawer.blade.php
$drawer_file = 'resources/views/backend/layouts/cart_drawer.blade.php';
$drawer_content = file_get_contents($drawer_file);

$search = 'shareReceiptPromise.then(() => {';
$replace = "shareReceiptPromise.then(() => {
                        window.posCart = [];
                        localStorage.removeItem('posCart');
                        window.saveCart && window.saveCart();";

if (strpos($drawer_content, $search) !== false && strpos($drawer_content, 'localStorage.removeItem') === false) {
    $drawer_content = str_replace($search, $replace, $drawer_content);
    file_put_contents($drawer_file, $drawer_content);
    echo "Added cart clearing logic to cart_drawer.blade.php\n";
} else {
    echo "Already added or search string not found in cart_drawer.blade.php\n";
}

// 2. Fix pos/index.blade.php (add missing bulkAddModal)
$pos_file = 'resources/views/backend/pos/index.blade.php';
$pos_content = file_get_contents($pos_file);

$modal_html = <<<HTML
<!-- Bulk Add Modal -->
<div class="modal fade" id="bulkAddModal" tabindex="-1" role="dialog" style="z-index: 10500;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-success text-white border-0 py-3">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-cart-plus mr-2"></i>Bulk Add to Cart</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-0" style="background: #f8fafc;">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="font-size: 0.95rem;">
                        <thead style="background: #e2e8f0; border-bottom: 2px solid #cbd5e1;">
                            <tr>
                                <th class="font-weight-bold text-dark border-0">Product</th>
                                <th class="text-center font-weight-bold text-dark border-0" width="120">Qty</th>
                                <th class="text-right font-weight-bold text-dark border-0" width="150">Price</th>
                                <th class="text-center font-weight-bold text-dark border-0" width="60"></th>
                            </tr>
                        </thead>
                        <tbody id="bulk-add-tbody" class="bg-white">
                            <!-- Items injected via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-top bg-light py-3">
                <button type="button" class="btn btn-outline-secondary font-weight-bold rounded-pill px-4" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success font-weight-bold rounded-pill px-5 shadow-sm" id="confirm-bulk-add">Confirm Add to Cart</button>
            </div>
        </div>
    </div>
</div>
HTML;

if (strpos($pos_content, 'id="bulkAddModal"') === false) {
    $search_modal = '<!-- Add Product Modal -->';
    $pos_content = str_replace($search_modal, $modal_html . "\n" . $search_modal, $pos_content);
    file_put_contents($pos_file, $pos_content);
    echo "Added bulkAddModal to pos/index.blade.php\n";
} else {
    echo "bulkAddModal already exists in pos/index.blade.php\n";
}

echo "Done!\n";
