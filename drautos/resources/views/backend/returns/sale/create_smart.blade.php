@extends('backend.layouts.master')

@section('title','New Sale Return')

@section('main-content')
<div class="container-fluid px-2 px-md-4 py-3">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h5 class="m-0 font-weight-bold text-primary"><i class="fas fa-undo mr-2"></i>New Sale Return</h5>
            <small class="text-muted">Select customer → Search products → Build basket → Submit</small>
        </div>
        <a href="{{ route('returns.sale.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i>Back</a>
    </div>

    @include('backend.layouts.notification')

    {{-- STEP 1: Customer Selection --}}
    <div class="card shadow-sm mb-3" id="step1Card">
        <div class="card-header py-2 d-flex align-items-center" style="background:#f8f9fc;">
            <span class="badge badge-primary mr-2" style="font-size:0.9rem;width:26px;height:26px;line-height:22px;border-radius:50%;">1</span>
            <span class="font-weight-bold">Select Customer</span>
            <span class="ml-auto text-success d-none" id="customerSelectedBadge"><i class="fas fa-check-circle mr-1"></i><span id="customerSelectedName"></span></span>
        </div>
        <div class="card-body py-2">
            <select id="customerSelect" class="form-control" style="width:100%">
                <option value="">-- Search customer by name or phone --</option>
                @foreach($customers as $c)
                    <option value="{{ $c->id }}" data-phone="{{ $c->phone }}">{{ $c->name }}{{ $c->phone ? ' — '.$c->phone : '' }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- STEP 2: Product Search --}}
    <div class="card shadow-sm mb-3" id="step2Card" style="opacity:0.45;pointer-events:none;">
        <div class="card-header py-2 d-flex align-items-center" style="background:#f8f9fc;">
            <span class="badge badge-secondary mr-2" id="step2Badge" style="font-size:0.9rem;width:26px;height:26px;line-height:22px;border-radius:50%;">2</span>
            <span class="font-weight-bold">Search Product to Return</span>
        </div>
        <div class="card-body py-2">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text bg-white"><i class="fas fa-search text-primary"></i></span>
                </div>
                <input type="text" id="productSearch" class="form-control border-left-0" placeholder="Type product name or SKU..." autocomplete="off">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" id="clearProductSearch" style="display:none;"><i class="fas fa-times"></i></button>
                </div>
            </div>
            {{-- Dropdown Results --}}
            <div id="searchResultsDropdown" class="border rounded mt-1 shadow-sm" style="display:none;max-height:280px;overflow-y:auto;background:#fff;z-index:999;position:relative;">
                <div id="searchResultsList"></div>
                <div id="searchNoResults" class="text-center text-muted py-3" style="display:none;">
                    <i class="fas fa-search-minus mr-1"></i> No returnable items found
                </div>
                <div id="searchLoading" class="text-center text-muted py-3" style="display:none;">
                    <i class="fas fa-spinner fa-spin mr-1"></i> Searching...
                </div>
            </div>
        </div>
    </div>

    {{-- STEP 3: Return Basket --}}
    <div class="card shadow-sm mb-3" id="step3Card" style="opacity:0.45;pointer-events:none;">
        <div class="card-header py-2 d-flex align-items-center justify-content-between" style="background:#f8f9fc;">
            <div class="d-flex align-items-center">
                <span class="badge badge-secondary mr-2" id="step3Badge" style="font-size:0.9rem;width:26px;height:26px;line-height:22px;border-radius:50%;">3</span>
                <span class="font-weight-bold">Return Basket</span>
                <span class="badge badge-danger ml-2" id="basketCountBadge" style="display:none;">0</span>
            </div>
            <button type="button" class="btn btn-outline-danger btn-sm" id="clearBasketBtn" style="display:none;" onclick="clearBasket()">
                <i class="fas fa-trash mr-1"></i>Clear All
            </button>
        </div>
        <div class="card-body p-0" id="basketBody">
            <div class="text-center text-muted py-4" id="basketEmpty">
                <i class="fas fa-shopping-basket fa-2x mb-2 text-gray-400"></i>
                <p class="mb-0">No items added yet. Search a product above.</p>
            </div>
            <div id="basketItemsList"></div>
            {{-- Basket Total --}}
            <div id="basketTotalRow" class="d-none px-3 py-2 border-top" style="background:#f8f9fc;">
                <div class="d-flex justify-content-between align-items-center">
                    <strong>Total Refund:</strong>
                    <span class="text-success font-weight-bold" style="font-size:1.2rem;" id="basketTotal">PKR 0.00</span>
                </div>
            </div>
        </div>
    </div>

    {{-- STEP 4: Return Details & Submit --}}
    <div class="card shadow-sm mb-3" id="step4Card" style="opacity:0.45;pointer-events:none;">
        <div class="card-header py-2 d-flex align-items-center" style="background:#f8f9fc;">
            <span class="badge badge-secondary mr-2" id="step4Badge" style="font-size:0.9rem;width:26px;height:26px;line-height:22px;border-radius:50%;">4</span>
            <span class="font-weight-bold">Return Details & Submit</span>
        </div>
        <div class="card-body">
            <form id="smartReturnForm" action="{{ route('returns.sale.store') }}" method="POST">
                @csrf
                <input type="hidden" name="smart_return" value="1">
                <input type="hidden" name="customer_id" id="hiddenCustomerId">
                <div id="hiddenBasketInputs"></div>

                <div class="row">
                    <div class="col-6 col-md-4">
                        <div class="form-group mb-2">
                            <label class="small font-weight-bold">Return Date <span class="text-danger">*</span></label>
                            <input type="date" name="return_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="form-group mb-2">
                            <label class="small font-weight-bold">Refund Method <span class="text-danger">*</span></label>
                            <select name="refund_method" class="form-control" required>
                                <option value="cash">Cash</option>
                                <option value="credit_note">Credit to Balance</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cheque">Cheque</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="form-group mb-2">
                            <label class="small font-weight-bold">Reference <small class="text-muted">(Optional)</small></label>
                            <input type="text" name="refund_reference" class="form-control" placeholder="Cheque #, Transaction ID...">
                        </div>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label class="small font-weight-bold">Reason <small class="text-muted">(Optional)</small></label>
                    <textarea name="reason" class="form-control" rows="2" placeholder="Reason for return..."></textarea>
                </div>

                <div id="noBasketWarning" class="alert alert-warning d-none">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Please add at least one product to the return basket.
                </div>
                <div id="noCustomerWarning" class="alert alert-warning d-none">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Please select a customer first.
                </div>

                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-2">
                    <div class="text-muted small" id="submitSummary"></div>
                    <button type="submit" class="btn btn-danger btn-lg px-4" id="submitBtn">
                        <i class="fas fa-undo mr-2"></i>Process Return
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* ---- Mobile-first styles ---- */
#searchResultsDropdown .result-item {
    padding: 10px 14px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    transition: background 0.15s;
}
#searchResultsDropdown .result-item:last-child { border-bottom: none; }
#searchResultsDropdown .result-item:active,
#searchResultsDropdown .result-item:hover { background: #e8f4fd; }
#searchResultsDropdown .result-item .order-badge {
    font-size: 0.72rem;
    background: #e9ecef;
    border-radius: 4px;
    padding: 1px 6px;
    color: #555;
}

.basket-item {
    border-bottom: 1px solid #eee;
    padding: 10px 14px;
}
.basket-item:last-child { border-bottom: none; }
.basket-item .basket-item-title { font-weight: 600; font-size: 0.92rem; }
.basket-item .basket-item-meta { font-size: 0.78rem; color: #888; }
.basket-qty-control { display: flex; align-items: center; gap: 6px; }
.basket-qty-control button {
    width: 30px; height: 30px;
    border-radius: 6px; border: 1px solid #ddd;
    background: #f8f9fc; font-size: 1rem; line-height: 1;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
}
.basket-qty-control input {
    width: 52px; text-align: center;
    border: 1px solid #ddd; border-radius: 6px;
    padding: 4px; font-size: 0.9rem;
}
.condition-select-mobile {
    font-size: 0.82rem;
    padding: 3px 6px;
    border: 1px solid #ddd;
    border-radius: 6px;
    background: #fff;
    max-width: 140px;
}
.remove-basket-item {
    background: none; border: none; color: #dc3545;
    font-size: 1.1rem; cursor: pointer; padding: 4px 6px;
}
/* Step activation animation */
.step-active { opacity:1 !important; pointer-events:auto !important; transition: opacity 0.3s; }
.step-active .badge-secondary { background:#007bff !important; }

@media (max-width: 575px) {
    .basket-item { padding: 10px; }
    #step1Card .card-body, #step2Card .card-body,
    #step3Card .card-body, #step4Card .card-body { padding: 10px; }
    .btn-lg { font-size: 1rem; padding: .55rem 1.2rem; }
}
</style>
@endsection

@push('scripts')
<script>
$(function () {

    // ====================================================
    // STATE
    // ====================================================
    var selectedCustomerId   = null;
    var selectedCustomerName = '';
    var basket = []; // [{product_id, order_id, order_number, product_title, unit_price, qty, max_qty, condition, notes}]
    var searchTimer = null;

    // ====================================================
    // STEP 1 — Customer Select2
    // ====================================================
    $('#customerSelect').select2({
        placeholder: '-- Search customer by name or phone --',
        allowClear: true,
        width: '100%'
    });

    $('#customerSelect').on('change', function () {
        selectedCustomerId   = $(this).val();
        selectedCustomerName = $(this).find('option:selected').text();

        if (selectedCustomerId) {
            // Show badge
            $('#customerSelectedName').text(selectedCustomerName.split(' — ')[0]);
            $('#customerSelectedBadge').removeClass('d-none');
            $('#hiddenCustomerId').val(selectedCustomerId);
            activateStep('step2Card', 'step2Badge');
            activateStep('step3Card', 'step3Badge');
            activateStep('step4Card', 'step4Badge');
        } else {
            selectedCustomerId = null;
            $('#customerSelectedBadge').addClass('d-none');
            $('#hiddenCustomerId').val('');
            deactivateStep('step2Card', 'step2Badge');
            deactivateStep('step3Card', 'step3Badge');
            deactivateStep('step4Card', 'step4Badge');
            clearBasket();
        }
    });

    // ====================================================
    // STEP 2 — Product Search (AJAX with debounce)
    // ====================================================
    $('#productSearch').on('input', function () {
        var q = $(this).val().trim();
        $('#clearProductSearch').toggle(q.length > 0);

        clearTimeout(searchTimer);
        if (!selectedCustomerId) return;

        if (q.length === 0) {
            hideDropdown();
            return;
        }
        showLoading();
        searchTimer = setTimeout(function () {
            doSearch(q);
        }, 300);
    });

    $('#clearProductSearch').on('click', function () {
        $('#productSearch').val('').trigger('input');
    });

    // Close dropdown when clicking outside
    $(document).on('click', function (e) {
        if (!$(e.target).closest('#step2Card').length) {
            hideDropdown();
        }
    });

    function doSearch(q) {
        $.ajax({
            url: '{{ route("returns.sale.search-products") }}',
            data: { customer_id: selectedCustomerId, q: q },
            success: function (data) {
                renderResults(data);
            },
            error: function () {
                hideDropdown();
            }
        });
    }

    function renderResults(data) {
        var $list = $('#searchResultsList').empty();
        $('#searchLoading').hide();
        $('#searchResultsDropdown').show();

        if (!data || data.length === 0) {
            $('#searchNoResults').show();
            return;
        }
        $('#searchNoResults').hide();

        data.forEach(function (item) {
            var alreadyInBasket = basket.find(function(b) {
                return b.product_id === item.product_id && b.order_id === item.order_id;
            });

            var html = '<div class="result-item d-flex align-items-start" data-item=\'' + JSON.stringify(item).replace(/'/g, "&#39;") + '\'>' +
                '<div class="flex-grow-1">' +
                    '<div class="font-weight-bold" style="font-size:0.9rem;">' + escapeHtml(item.product_title) + '</div>' +
                    '<div class="mt-1">' +
                        '<span class="order-badge mr-1">' + escapeHtml(item.order_number) + '</span>' +
                        '<span class="order-badge mr-1">' + escapeHtml(item.order_date) + '</span>' +
                        (item.product_sku ? '<span class="order-badge">SKU: ' + escapeHtml(item.product_sku) + '</span>' : '') +
                    '</div>' +
                    '<div class="mt-1" style="font-size:0.8rem;color:#555;">' +
                        'Sold: <strong>' + item.qty_sold + '</strong> &nbsp;|&nbsp; ' +
                        'Returnable: <strong class="text-success">' + item.returnable_qty + '</strong> &nbsp;|&nbsp; ' +
                        'PKR <strong>' + formatNum(item.unit_price) + '</strong>/ea' +
                    '</div>' +
                '</div>' +
                '<div class="ml-2">' +
                    (alreadyInBasket
                        ? '<span class="badge badge-success" style="font-size:0.75rem;"><i class="fas fa-check"></i> Added</span>'
                        : '<button class="btn btn-primary btn-sm px-2 py-1 add-to-basket-btn"><i class="fas fa-plus"></i> Add</button>'
                    ) +
                '</div>' +
            '</div>';
            $list.append(html);
        });
    }

    // Add to basket click
    $(document).on('click', '.add-to-basket-btn', function () {
        var $item = $(this).closest('.result-item');
        var data  = JSON.parse($item.attr('data-item'));
        addToBasket(data);
        // Update button to "Added"
        $(this).replaceWith('<span class="badge badge-success" style="font-size:0.75rem;"><i class="fas fa-check"></i> Added</span>');
    });

    function showLoading() {
        $('#searchResultsList').empty();
        $('#searchNoResults').hide();
        $('#searchLoading').show();
        $('#searchResultsDropdown').show();
    }
    function hideDropdown() {
        $('#searchResultsDropdown').hide();
    }

    // ====================================================
    // STEP 3 — Basket Logic
    // ====================================================
    function addToBasket(item) {
        // Prevent duplicate
        var exists = basket.find(function(b) { return b.product_id === item.product_id && b.order_id === item.order_id; });
        if (exists) return;

        basket.push({
            product_id:    item.product_id,
            order_id:      item.order_id,
            order_number:  item.order_number,
            product_title: item.product_title,
            product_sku:   item.product_sku,
            unit_price:    item.unit_price,
            qty:           item.returnable_qty,
            max_qty:       item.returnable_qty,
            condition:     'good',
            notes:         ''
        });
        renderBasket();
    }

    window.clearBasket = function () {
        basket = [];
        renderBasket();
    };

    function renderBasket() {
        var $list  = $('#basketItemsList').empty();
        var $empty = $('#basketEmpty');
        var $total = $('#basketTotalRow');
        var $badge = $('#basketCountBadge');
        var $clrBtn = $('#clearBasketBtn');

        if (basket.length === 0) {
            $empty.show();
            $total.addClass('d-none');
            $badge.hide();
            $clrBtn.hide();
            updateSubmitSummary();
            rebuildHiddenInputs();
            return;
        }

        $empty.hide();
        $total.removeClass('d-none');
        $badge.text(basket.length).show();
        $clrBtn.show();

        var grandTotal = 0;
        basket.forEach(function (item, idx) {
            var rowTotal = item.qty * item.unit_price;
            grandTotal += rowTotal;

            var html =
                '<div class="basket-item" data-idx="' + idx + '">' +
                    '<div class="d-flex align-items-start justify-content-between">' +
                        '<div class="flex-grow-1">' +
                            '<div class="basket-item-title">' + escapeHtml(item.product_title) + '</div>' +
                            '<div class="basket-item-meta">' +
                                'Order: ' + escapeHtml(item.order_number) +
                                (item.product_sku ? ' &nbsp;|&nbsp; SKU: ' + escapeHtml(item.product_sku) : '') +
                                ' &nbsp;|&nbsp; PKR ' + formatNum(item.unit_price) + '/ea' +
                            '</div>' +
                        '</div>' +
                        '<button class="remove-basket-item" onclick="removeFromBasket(' + idx + ')" title="Remove"><i class="fas fa-times-circle"></i></button>' +
                    '</div>' +
                    '<div class="d-flex align-items-center justify-content-between mt-2 flex-wrap gap-2">' +
                        '<div class="basket-qty-control">' +
                            '<button type="button" onclick="changeQty(' + idx + ', -1)"><i class="fas fa-minus" style="font-size:0.7rem;"></i></button>' +
                            '<input type="number" class="basket-qty-input" data-idx="' + idx + '" min="1" max="' + item.max_qty + '" value="' + item.qty + '">' +
                            '<button type="button" onclick="changeQty(' + idx + ', 1)"><i class="fas fa-plus" style="font-size:0.7rem;"></i></button>' +
                            '<span class="small text-muted ml-1">/ ' + item.max_qty + '</span>' +
                        '</div>' +
                        '<select class="condition-select-mobile" data-idx="' + idx + '">' +
                            '<option value="good"' + (item.condition === 'good' ? ' selected' : '') + '>✅ Good (Restock)</option>' +
                            '<option value="damaged"' + (item.condition === 'damaged' ? ' selected' : '') + '>⚠️ Damaged</option>' +
                            '<option value="defective"' + (item.condition === 'defective' ? ' selected' : '') + '>❌ Defective</option>' +
                        '</select>' +
                        '<span class="font-weight-bold text-success" style="font-size:0.95rem;">PKR ' + formatNum(rowTotal) + '</span>' +
                    '</div>' +
                    '<input type="text" class="form-control form-control-sm mt-2 basket-notes-input" data-idx="' + idx + '" placeholder="Optional note..." value="' + escapeHtml(item.notes) + '">' +
                '</div>';

            $list.append(html);
        });

        $('#basketTotal').text('PKR ' + formatNum(grandTotal));
        updateSubmitSummary();
        rebuildHiddenInputs();
    }

    // Qty change via +/- buttons
    window.changeQty = function (idx, delta) {
        var item = basket[idx];
        if (!item) return;
        var newQty = item.qty + delta;
        if (newQty < 1) newQty = 1;
        if (newQty > item.max_qty) newQty = item.max_qty;
        item.qty = newQty;
        renderBasket();
    };

    // Qty change via direct input
    $(document).on('change', '.basket-qty-input', function () {
        var idx = parseInt($(this).data('idx'));
        var val = parseInt($(this).val()) || 1;
        if (val < 1) val = 1;
        if (val > basket[idx].max_qty) val = basket[idx].max_qty;
        basket[idx].qty = val;
        renderBasket();
    });

    // Condition change
    $(document).on('change', '.condition-select-mobile', function () {
        var idx = parseInt($(this).data('idx'));
        basket[idx].condition = $(this).val();
        rebuildHiddenInputs();
    });

    // Notes change
    $(document).on('input', '.basket-notes-input', function () {
        var idx = parseInt($(this).data('idx'));
        basket[idx].notes = $(this).val();
        rebuildHiddenInputs();
    });

    window.removeFromBasket = function (idx) {
        basket.splice(idx, 1);
        renderBasket();
    };

    function rebuildHiddenInputs() {
        var $container = $('#hiddenBasketInputs').empty();
        basket.forEach(function (item, idx) {
            $container.append('<input type="hidden" name="items[' + idx + '][product_id]" value="' + item.product_id + '">');
            $container.append('<input type="hidden" name="items[' + idx + '][order_id]" value="' + item.order_id + '">');
            $container.append('<input type="hidden" name="items[' + idx + '][quantity]" value="' + item.qty + '">');
            $container.append('<input type="hidden" name="items[' + idx + '][unit_price]" value="' + item.unit_price + '">');
            $container.append('<input type="hidden" name="items[' + idx + '][condition]" value="' + item.condition + '">');
            $container.append('<input type="hidden" name="items[' + idx + '][notes]" value="' + escapeHtml(item.notes) + '">');
        });
    }

    function updateSubmitSummary() {
        if (basket.length === 0) {
            $('#submitSummary').text('');
            return;
        }
        var total = basket.reduce(function(s, i) { return s + (i.qty * i.unit_price); }, 0);
        $('#submitSummary').html(basket.length + ' item(s) &nbsp;|&nbsp; Total: <strong>PKR ' + formatNum(total) + '</strong>');
    }

    // ====================================================
    // STEP 4 — Form Submission
    // ====================================================
    $('#smartReturnForm').on('submit', function (e) {
        $('#noBasketWarning').addClass('d-none');
        $('#noCustomerWarning').addClass('d-none');

        if (!selectedCustomerId) {
            e.preventDefault();
            $('#noCustomerWarning').removeClass('d-none');
            $('html,body').animate({ scrollTop: $('#noCustomerWarning').offset().top - 80 }, 400);
            return false;
        }
        if (basket.length === 0) {
            e.preventDefault();
            $('#noBasketWarning').removeClass('d-none');
            $('html,body').animate({ scrollTop: $('#noBasketWarning').offset().top - 80 }, 400);
            return false;
        }

        rebuildHiddenInputs();
        $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Processing...');
    });

    // ====================================================
    // HELPERS
    // ====================================================
    function activateStep(cardId, badgeId) {
        $('#' + cardId).addClass('step-active');
        $('#' + badgeId).removeClass('badge-secondary').addClass('badge-primary');
    }
    function deactivateStep(cardId, badgeId) {
        $('#' + cardId).removeClass('step-active');
        $('#' + badgeId).removeClass('badge-primary').addClass('badge-secondary');
    }
    function formatNum(n) {
        return parseFloat(n).toLocaleString('en-PK', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    function escapeHtml(text) {
        if (!text) return '';
        return String(text).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    }
});
</script>
@endpush
