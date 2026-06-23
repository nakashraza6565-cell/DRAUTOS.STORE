@extends('backend.layouts.master')

@section('title','New Sale Return')

@section('main-content')
<style>
/* ======== MOBILE-FIRST RETURN PAGE ======== */
:root {
    --clr-primary: #2563eb;
    --clr-success: #16a34a;
    --clr-danger:  #dc2626;
    --clr-muted:   #6b7280;
    --radius:      12px;
}

#srPage { padding: 12px; max-width: 680px; margin: 0 auto; }

/* Step cards */
.sr-card {
    background: #fff;
    border-radius: var(--radius);
    border: 1.5px solid #e5e7eb;
    margin-bottom: 14px;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
    transition: opacity .25s, border-color .25s;
}
.sr-card.locked { opacity: .42; pointer-events: none; }
.sr-card-head {
    display: flex; align-items: center; gap: 10px;
    padding: 12px 14px;
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
}
.sr-step-num {
    width: 28px; height: 28px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: .85rem;
    background: #d1d5db; color: #fff; flex-shrink: 0;
    transition: background .2s;
}
.sr-card:not(.locked) .sr-step-num { background: var(--clr-primary); }
.sr-card-title { font-weight: 700; font-size: .95rem; flex-grow: 1; }
.sr-card-body { padding: 14px; }

/* Customer search */
#customerSearchBox {
    width: 100%; padding: 12px 14px;
    font-size: 1rem; border: 2px solid #d1d5db;
    border-radius: 10px; outline: none;
    transition: border-color .2s;
    -webkit-appearance: none;
}
#customerSearchBox:focus { border-color: var(--clr-primary); }
#customerDropdown {
    border: 1.5px solid #d1d5db; border-top: none;
    border-radius: 0 0 10px 10px;
    max-height: 220px; overflow-y: auto;
    background: #fff; display: none;
}
.cust-option {
    padding: 12px 14px;
    border-bottom: 1px solid #f3f4f6;
    cursor: pointer; font-size: .95rem;
}
.cust-option:last-child { border-bottom: none; }
.cust-option:active, .cust-option.highlighted { background: #eff6ff; }
.cust-option .cust-phone { font-size: .8rem; color: var(--clr-muted); }
#selectedCustomerPill {
    display: none;
    margin-top: 10px;
    background: #eff6ff;
    border: 1.5px solid #bfdbfe;
    border-radius: 8px;
    padding: 10px 14px;
    display: none;
    align-items: center;
    gap: 8px;
}
#selectedCustomerPill .pill-name { font-weight: 600; font-size: .95rem; flex-grow: 1; }
#clearCustomerBtn {
    background: none; border: none;
    color: var(--clr-danger); font-size: 1.2rem;
    cursor: pointer; padding: 2px 4px; line-height: 1;
}

/* Product search */
#productSearchBox {
    width: 100%; padding: 12px 14px;
    font-size: 1rem; border: 2px solid #d1d5db;
    border-radius: 10px; outline: none;
    transition: border-color .2s;
    -webkit-appearance: none;
}
#productSearchBox:focus { border-color: var(--clr-primary); }
#searchSpinner { display: none; margin-top: 8px; text-align: center; color: var(--clr-muted); font-size: .9rem; }

/* Search result cards */
.result-card {
    border: 1.5px solid #e5e7eb;
    border-radius: 10px;
    padding: 12px;
    margin-top: 10px;
    background: #fff;
    display: flex;
    gap: 10px;
    align-items: flex-start;
}
.result-card .rc-info { flex-grow: 1; min-width: 0; }
.result-card .rc-title { font-weight: 700; font-size: .95rem; word-break: break-word; }
.result-card .rc-meta { font-size: .78rem; color: var(--clr-muted); margin-top: 3px; }
.result-card .rc-price { font-size: .9rem; color: var(--clr-primary); font-weight: 600; margin-top: 4px; }
.rc-returnable { display: inline-block; background: #dcfce7; color: #15803d; border-radius: 6px; padding: 1px 7px; font-size: .78rem; font-weight: 600; }
.btn-add-result {
    background: var(--clr-primary); color: #fff;
    border: none; border-radius: 8px;
    padding: 10px 14px; font-size: .9rem; font-weight: 600;
    cursor: pointer; white-space: nowrap; flex-shrink: 0;
    min-width: 64px; text-align: center;
    transition: background .15s;
    -webkit-tap-highlight-color: transparent;
}
.btn-add-result:active { background: #1d4ed8; }
.btn-add-result.added { background: var(--clr-success); cursor: default; }
#searchResultsContainer { margin-top: 4px; }

/* Basket items */
.basket-entry {
    border: 1.5px solid #e5e7eb;
    border-radius: 10px;
    padding: 12px;
    margin-bottom: 10px;
    background: #fff;
}
.basket-entry .be-title { font-weight: 700; font-size: .95rem; }
.basket-entry .be-meta { font-size: .78rem; color: var(--clr-muted); margin-top: 2px; }
.be-controls {
    display: flex; align-items: center; gap: 10px;
    margin-top: 10px; flex-wrap: wrap;
}
.qty-box {
    display: flex; align-items: center;
    border: 1.5px solid #d1d5db; border-radius: 8px;
    overflow: hidden;
}
.qty-btn {
    width: 42px; height: 42px;
    background: #f3f4f6; border: none;
    font-size: 1.2rem; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    -webkit-tap-highlight-color: transparent;
    transition: background .12s;
    flex-shrink: 0;
}
.qty-btn:active { background: #e5e7eb; }
.qty-val {
    width: 48px; text-align: center;
    font-size: 1rem; font-weight: 700;
    border: none; border-left: 1.5px solid #d1d5db; border-right: 1.5px solid #d1d5db;
    height: 42px; outline: none;
    -webkit-appearance: none; -moz-appearance: textfield;
}
.qty-val::-webkit-inner-spin-button,
.qty-val::-webkit-outer-spin-button { -webkit-appearance: none; }
.be-condition {
    flex-grow: 1; min-width: 120px;
    padding: 10px 12px; font-size: .88rem;
    border: 1.5px solid #d1d5db; border-radius: 8px;
    background: #fff; outline: none; height: 42px;
    -webkit-appearance: auto;
}
.be-total { font-weight: 700; color: var(--clr-success); font-size: 1rem; min-width: 80px; text-align: right; }
.be-remove {
    background: none; border: none;
    color: var(--clr-danger); font-size: 1.4rem;
    cursor: pointer; padding: 4px; line-height: 1;
    -webkit-tap-highlight-color: transparent;
}
.be-notes {
    margin-top: 8px; width: 100%;
    padding: 9px 12px; font-size: .88rem;
    border: 1.5px solid #e5e7eb; border-radius: 8px;
    outline: none;
}
.be-notes:focus { border-color: var(--clr-primary); }

/* Basket total bar */
#basketTotalBar {
    background: #f0fdf4; border: 1.5px solid #bbf7d0;
    border-radius: 10px; padding: 12px 14px;
    display: flex; justify-content: space-between; align-items: center;
    margin-top: 4px; display: none;
}
#basketTotalBar .bt-label { font-weight: 600; font-size: .95rem; }
#basketTotalBar .bt-amount { font-weight: 800; font-size: 1.2rem; color: var(--clr-success); }

/* Form fields */
.sr-field-group { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px; }
@media (max-width: 480px) { .sr-field-group { grid-template-columns: 1fr; } }
.sr-field label { display: block; font-size: .82rem; font-weight: 700; color: #374151; margin-bottom: 4px; }
.sr-field input, .sr-field select, .sr-field textarea {
    width: 100%; padding: 11px 12px;
    font-size: .95rem; border: 1.5px solid #d1d5db;
    border-radius: 8px; outline: none;
    transition: border-color .2s; background: #fff;
    -webkit-appearance: none; appearance: none;
}
.sr-field select { -webkit-appearance: auto; appearance: auto; }
.sr-field input:focus, .sr-field select:focus, .sr-field textarea:focus { border-color: var(--clr-primary); }

/* Submit */
#submitBtn {
    width: 100%; padding: 16px;
    background: var(--clr-danger); color: #fff;
    border: none; border-radius: 10px;
    font-size: 1.1rem; font-weight: 700;
    cursor: pointer; margin-top: 14px;
    transition: background .15s;
    -webkit-tap-highlight-color: transparent;
    display: flex; align-items: center; justify-content: center; gap: 8px;
}
#submitBtn:active { background: #b91c1c; }
#submitBtn:disabled { opacity: .65; cursor: not-allowed; }

/* Misc */
.sr-empty-msg { text-align: center; padding: 24px; color: var(--clr-muted); font-size: .9rem; }
.alert-inline { background: #fef9c3; border: 1.5px solid #fde047; border-radius: 8px; padding: 10px 14px; font-size: .88rem; color: #713f12; display: none; margin-top: 10px; }
.sr-basket-count { background: var(--clr-danger); color: #fff; border-radius: 50%; width: 22px; height: 22px; font-size: .75rem; font-weight: 700; display: none; align-items: center; justify-content: center; flex-shrink: 0; }
</style>

<div id="srPage">
    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
        <div>
            <div style="font-size:1.15rem;font-weight:800;color:#111;">↩ New Sale Return</div>
            <div style="font-size:.78rem;color:var(--clr-muted);">Select customer → search product → submit</div>
        </div>
        <a href="{{ route('returns.sale.index') }}" style="padding:8px 14px;background:#f3f4f6;border-radius:8px;font-size:.85rem;color:#374151;font-weight:600;text-decoration:none;">← Back</a>
    </div>

    @include('backend.layouts.notification')

    {{-- STEP 1: Customer --}}
    <div class="sr-card" id="step1Card">
        <div class="sr-card-head">
            <div class="sr-step-num">1</div>
            <div class="sr-card-title">Select Customer</div>
            <div id="custDoneIcon" style="display:none;color:var(--clr-success);font-size:1.1rem;">✓</div>
        </div>
        <div class="sr-card-body">
            <input type="text" id="customerSearchBox" placeholder="Search by name or phone..." autocomplete="off" autocorrect="off" spellcheck="false">
            <div id="customerDropdown">
                @foreach($customers as $c)
                    <div class="cust-option" data-id="{{ $c->id }}" data-name="{{ $c->name }}" data-phone="{{ $c->phone ?? '' }}">
                        {{ $c->name }}
                        @if($c->phone)<div class="cust-phone">{{ $c->phone }}</div>@endif
                    </div>
                @endforeach
            </div>
            <div id="selectedCustomerPill">
                <i class="fas fa-user-check" style="color:var(--clr-primary);"></i>
                <span class="pill-name" id="pillCustomerName"></span>
                <button id="clearCustomerBtn" type="button" title="Change customer">×</button>
            </div>
        </div>
    </div>

    {{-- STEP 2: Product Search --}}
    <div class="sr-card locked" id="step2Card">
        <div class="sr-card-head">
            <div class="sr-step-num">2</div>
            <div class="sr-card-title">Search Product to Return</div>
        </div>
        <div class="sr-card-body">
            <input type="text" id="productSearchBox" placeholder="Type product name or SKU..." autocomplete="off" autocorrect="off" spellcheck="false">
            <div id="searchSpinner"><i class="fas fa-spinner fa-spin"></i> Searching...</div>
            <div id="searchResultsContainer"></div>
        </div>
    </div>

    {{-- STEP 3: Basket --}}
    <div class="sr-card locked" id="step3Card">
        <div class="sr-card-head">
            <div class="sr-step-num">3</div>
            <div class="sr-card-title">Return Basket</div>
            <div class="sr-basket-count" id="basketCountBadge">0</div>
            <button type="button" id="clearBasketBtn" onclick="clearBasket()" style="display:none;margin-left:auto;background:none;border:none;color:var(--clr-danger);font-size:.8rem;font-weight:600;cursor:pointer;">Clear</button>
        </div>
        <div class="sr-card-body" id="basketBody">
            <div class="sr-empty-msg" id="basketEmpty">
                <div style="font-size:2rem;">🛒</div>
                No items yet. Search a product above.
            </div>
            <div id="basketItemsList"></div>
            <div id="basketTotalBar">
                <span class="bt-label">Total Refund</span>
                <span class="bt-amount" id="basketTotalAmt">PKR 0.00</span>
            </div>
        </div>
    </div>

    {{-- STEP 4: Details & Submit --}}
    <div class="sr-card locked" id="step4Card">
        <div class="sr-card-head">
            <div class="sr-step-num">4</div>
            <div class="sr-card-title">Details & Submit</div>
        </div>
        <div class="sr-card-body">
            <form id="smartReturnForm" action="{{ route('returns.sale.store') }}" method="POST">
                @csrf
                <input type="hidden" name="smart_return" value="1">
                <input type="hidden" name="customer_id" id="hiddenCustomerId">
                <div id="hiddenBasketInputs"></div>

                <div class="sr-field-group">
                    <div class="sr-field">
                        <label>Return Date *</label>
                        <input type="date" name="return_date" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="sr-field">
                        <label>Refund Method *</label>
                        <select name="refund_method" required>
                            <option value="cash">💵 Cash Refund</option>
                            <option value="credit_note">💳 Credit to Account</option>
                        </select>
                    </div>
                </div>
                <div class="sr-field" style="margin-bottom:10px;">
                    <label>Reference <span style="font-weight:400;color:var(--clr-muted);">(optional)</span></label>
                    <input type="text" name="refund_reference" placeholder="Cheque #, transaction ID...">
                </div>
                <div class="sr-field" style="margin-bottom:10px;">
                    <label>Reason <span style="font-weight:400;color:var(--clr-muted);">(optional)</span></label>
                    <textarea name="reason" rows="2" placeholder="Reason for return..." style="resize:vertical;"></textarea>
                </div>

                <div class="alert-inline" id="noBasketWarning">⚠️ Please add at least one product to the basket.</div>
                <div class="alert-inline" id="noCustomerWarning">⚠️ Please select a customer first.</div>

                <div id="submitSummary" style="font-size:.85rem;color:var(--clr-muted);margin-top:6px;"></div>

                <button type="submit" id="submitBtn">
                    <i class="fas fa-undo"></i> Process Return
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function () {

    /* ===============================================
       STATE
    =============================================== */
    var selectedCustomerId   = null;
    var allCustomers = [];
    var basket = [];
    var searchTimer = null;

    // Build customer list from DOM
    document.querySelectorAll('.cust-option').forEach(function(el) {
        allCustomers.push({
            id:    el.dataset.id,
            name:  el.dataset.name,
            phone: el.dataset.phone
        });
    });

    /* ===============================================
       STEP 1 – CUSTOMER SEARCH (native, no Select2)
    =============================================== */
    var $csBox = $('#customerSearchBox');
    var $csDrop = $('#customerDropdown');
    var $pill = $('#selectedCustomerPill');

    $csBox.on('input', function () {
        var q = this.value.toLowerCase().trim();
        var $opts = $csDrop.find('.cust-option');
        var shown = 0;
        $opts.each(function () {
            var match = (this.dataset.name + ' ' + this.dataset.phone).toLowerCase().includes(q);
            $(this).toggle(match);
            if (match) shown++;
        });
        $csDrop.show();
    });

    $csBox.on('focus', function () {
        if (!selectedCustomerId) $csDrop.show();
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#step1Card').length) $csDrop.hide();
    });

    $(document).on('click', '.cust-option', function () {
        selectCustomer(this.dataset.id, this.dataset.name, this.dataset.phone);
    });

    $('#clearCustomerBtn').on('click', function () {
        clearCustomer();
    });

    function selectCustomer(id, name, phone) {
        selectedCustomerId = id;
        $('#hiddenCustomerId').val(id);
        $csBox.val('').hide();
        $csDrop.hide();
        $('#pillCustomerName').text(name + (phone ? ' — ' + phone : ''));
        $pill.css('display', 'flex');
        $('#custDoneIcon').show();
        // unlock steps
        ['step2Card','step3Card','step4Card'].forEach(function(id) { $('#'+id).removeClass('locked'); });
        // clear basket if customer changed
        clearBasket();
    }

    function clearCustomer() {
        selectedCustomerId = null;
        $('#hiddenCustomerId').val('');
        $pill.hide();
        $csBox.val('').show().focus();
        $('#custDoneIcon').hide();
        ['step2Card','step3Card','step4Card'].forEach(function(id) { $('#'+id).addClass('locked'); });
        clearBasket();
    }

    /* ===============================================
       STEP 2 – PRODUCT SEARCH
    =============================================== */
    var $psBox = $('#productSearchBox');

    $psBox.on('input', function () {
        var q = this.value.trim();
        clearTimeout(searchTimer);
        $('#searchResultsContainer').empty();
        if (!selectedCustomerId || q.length < 1) { $('#searchSpinner').hide(); return; }
        $('#searchSpinner').show();
        searchTimer = setTimeout(function () { doSearch(q); }, 350);
    });

    // Close results on outside click
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#step2Card').length) {
            $('#searchResultsContainer').empty();
        }
    });

    function doSearch(q) {
        $.ajax({
            url: '{{ route("returns.sale.search-products") }}',
            data: { customer_id: selectedCustomerId, q: q },
            success: function (data) {
                $('#searchSpinner').hide();
                renderResults(data);
            },
            error: function () {
                $('#searchSpinner').hide();
                $('#searchResultsContainer').html('<div class="sr-empty-msg">Search failed. Try again.</div>');
            }
        });
    }

    function renderResults(data) {
        var $c = $('#searchResultsContainer').empty();
        if (!data || data.length === 0) {
            $c.html('<div class="sr-empty-msg">No returnable items found for this search.</div>');
            return;
        }
        data.forEach(function (item) {
            var inBasket = basket.find(function(b){ return b.product_id===item.product_id && b.order_id===item.order_id; });
            var btnHtml = inBasket
                ? '<button class="btn-add-result added" disabled>✓ Added</button>'
                : '<button class="btn-add-result add-to-basket-btn" data-item=\'' + JSON.stringify(item).replace(/'/g,"&#39;") + '\'>+ Add</button>';

            $c.append(
                '<div class="result-card">' +
                    '<div class="rc-info">' +
                        '<div class="rc-title">' + esc(item.product_title) + '</div>' +
                        '<div class="rc-meta">Order: <strong>' + esc(item.order_number) + '</strong> &nbsp;·&nbsp; ' + esc(item.order_date) + (item.product_sku ? ' &nbsp;·&nbsp; SKU: '+esc(item.product_sku) : '') + '</div>' +
                        '<div class="rc-price">PKR ' + fmt(item.unit_price) + '/ea &nbsp; <span class="rc-returnable">' + item.returnable_qty + ' returnable</span></div>' +
                    '</div>' +
                    btnHtml +
                '</div>'
            );
        });
    }

    $(document).on('click', '.add-to-basket-btn', function () {
        var data = JSON.parse($(this).attr('data-item'));
        addToBasket(data);
        $(this).addClass('added').prop('disabled', true).text('✓ Added');
        // Scroll to basket
        setTimeout(function(){ $('html,body').animate({scrollTop: $('#step3Card').offset().top - 60}, 300); }, 100);
    });

    /* ===============================================
       STEP 3 – BASKET
    =============================================== */
    function addToBasket(item) {
        var exists = basket.find(function(b){ return b.product_id===item.product_id && b.order_id===item.order_id; });
        if (exists) return;
        basket.push({
            product_id: item.product_id, order_id: item.order_id,
            order_number: item.order_number, product_title: item.product_title,
            product_sku: item.product_sku, unit_price: item.unit_price,
            qty: item.returnable_qty, max_qty: item.returnable_qty,
            condition: 'good', notes: ''
        });
        renderBasket();
    }

    window.clearBasket = function () { basket = []; renderBasket(); };

    function renderBasket() {
        var $list = $('#basketItemsList').empty();
        var $empty = $('#basketEmpty');
        var $total = $('#basketTotalBar');
        var $badge = $('#basketCountBadge');
        var $clrBtn = $('#clearBasketBtn');

        if (basket.length === 0) {
            $empty.show(); $total.hide();
            $badge.css('display','none'); $clrBtn.hide();
            rebuildHiddenInputs(); updateSubmitSummary(); return;
        }
        $empty.hide();
        $badge.text(basket.length).css('display','flex');
        $clrBtn.show();

        var grand = 0;
        basket.forEach(function (item, idx) {
            var rowTotal = item.qty * item.unit_price;
            grand += rowTotal;

            $list.append(
                '<div class="basket-entry">' +
                    '<div style="display:flex;justify-content:space-between;align-items:flex-start;">' +
                        '<div style="flex-grow:1;min-width:0;">' +
                            '<div class="be-title">' + esc(item.product_title) + '</div>' +
                            '<div class="be-meta">Order: ' + esc(item.order_number) + (item.product_sku ? ' · SKU: '+esc(item.product_sku) : '') + '</div>' +
                        '</div>' +
                        '<button class="be-remove" onclick="removeFromBasket('+idx+')" title="Remove">×</button>' +
                    '</div>' +
                    '<div class="be-controls">' +
                        '<div class="qty-box">' +
                            '<button class="qty-btn" type="button" onclick="changeQty('+idx+',-1)">−</button>' +
                            '<input class="qty-val basket-qty-input" type="number" data-idx="'+idx+'" min="1" max="'+item.max_qty+'" value="'+item.qty+'">' +
                            '<button class="qty-btn" type="button" onclick="changeQty('+idx+',1)">+</button>' +
                        '</div>' +
                        '<span style="font-size:.78rem;color:var(--clr-muted);">max '+item.max_qty+'</span>' +
                        '<select class="be-condition" data-idx="'+idx+'">' +
                            '<option value="good"'+(item.condition==='good'?' selected':'')+'>✅ Good (Restock)</option>' +
                            '<option value="damaged"'+(item.condition==='damaged'?' selected':'')+'>⚠️ Damaged</option>' +
                            '<option value="defective"'+(item.condition==='defective'?' selected':'')+'>❌ Defective</option>' +
                        '</select>' +
                        '<span class="be-total">PKR '+fmt(rowTotal)+'</span>' +
                    '</div>' +
                    '<input class="be-notes basket-notes-input" type="text" data-idx="'+idx+'" placeholder="Note (optional)..." value="'+esc(item.notes)+'">' +
                '</div>'
            );
        });

        $('#basketTotalAmt').text('PKR ' + fmt(grand));
        $total.show();
        rebuildHiddenInputs();
        updateSubmitSummary();
    }

    window.changeQty = function (idx, delta) {
        var item = basket[idx]; if (!item) return;
        item.qty = Math.min(item.max_qty, Math.max(1, item.qty + delta));
        renderBasket();
    };
    window.removeFromBasket = function (idx) { basket.splice(idx, 1); renderBasket(); };

    $(document).on('change', '.basket-qty-input', function () {
        var idx = +$(this).data('idx');
        basket[idx].qty = Math.min(basket[idx].max_qty, Math.max(1, +$(this).val() || 1));
        renderBasket();
    });
    $(document).on('change', '.be-condition', function () {
        basket[+$(this).data('idx')].condition = $(this).val();
        rebuildHiddenInputs();
    });
    $(document).on('input', '.basket-notes-input', function () {
        basket[+$(this).data('idx')].notes = $(this).val();
        rebuildHiddenInputs();
    });

    function rebuildHiddenInputs() {
        var $c = $('#hiddenBasketInputs').empty();
        basket.forEach(function (item, idx) {
            $c.append('<input type="hidden" name="items['+idx+'][product_id]" value="'+item.product_id+'">');
            $c.append('<input type="hidden" name="items['+idx+'][order_id]" value="'+item.order_id+'">');
            $c.append('<input type="hidden" name="items['+idx+'][quantity]" value="'+item.qty+'">');
            $c.append('<input type="hidden" name="items['+idx+'][unit_price]" value="'+item.unit_price+'">');
            $c.append('<input type="hidden" name="items['+idx+'][condition]" value="'+item.condition+'">');
            $c.append('<input type="hidden" name="items['+idx+'][notes]" value="'+esc(item.notes)+'">');
        });
    }

    function updateSubmitSummary() {
        if (basket.length === 0) { $('#submitSummary').text(''); return; }
        var total = basket.reduce(function(s,i){ return s + i.qty*i.unit_price; }, 0);
        $('#submitSummary').html(basket.length + ' item(s) &nbsp;·&nbsp; <strong>PKR ' + fmt(total) + '</strong>');
    }

    /* ===============================================
       STEP 4 – SUBMIT
    =============================================== */
    $('#smartReturnForm').on('submit', function (e) {
        $('#noBasketWarning, #noCustomerWarning').hide();
        if (!selectedCustomerId) {
            e.preventDefault();
            $('#noCustomerWarning').show();
            $('html,body').animate({scrollTop: $('#step1Card').offset().top - 60}, 300);
            return false;
        }
        if (basket.length === 0) {
            e.preventDefault();
            $('#noBasketWarning').show();
            $('html,body').animate({scrollTop: $('#step3Card').offset().top - 60}, 300);
            return false;
        }
        rebuildHiddenInputs();
        $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
    });

    /* ===============================================
       HELPERS
    =============================================== */
    function fmt(n) { return parseFloat(n).toLocaleString('en-PK',{minimumFractionDigits:2,maximumFractionDigits:2}); }
    function esc(t) { if(!t) return ''; return String(t).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }
});
</script>
@endpush
@endsection
