<!-- Global Floating Cart Button -->
@php
    $walkInId = $walkInId ?? \App\User::where('name', 'Walk-in Customer')->value('id') ?? 1;
    $customers = $customers ?? \App\User::where('role', 'user')->where('status', 'active')->orderBy('name', 'ASC')->get();
    $accounts = $accounts ?? \App\Models\FinancialAccount::where('status', 'active')->orderBy('type', 'ASC')->get();
    $activeAccountId = $activeAccountId ?? ($accounts->first() ? $accounts->first()->id : null);
@endphp
<button id="global-cart-btn" class="shadow-lg" data-is-pos="{{ Route::currentRouteName() === 'admin.pos' ? 'true' : 'false' }}" style="position: fixed !important; top: 50% !important; right: 0 !important; left: auto !important; bottom: auto !important; transform: translateY(-50%) !important; width: 44px; height: 80px; border-radius: 12px 0 0 12px !important; background: #facc15 !important; color: #083259 !important; display: {{ Route::currentRouteName() === 'admin.pos' ? 'flex' : 'none' }}; flex-direction: column; align-items: center; justify-content: center; z-index: 999999 !important; cursor: pointer; border: none; transition: all 0.3s ease;">
    <span class="badge badge-danger position-absolute shadow-sm" id="global-cart-badge" style="top: -6px; left: -6px; font-size: 11px; border: 2px solid #fff; border-radius: 50%; padding: 4px 6px;">0</span>
    <i class="fas fa-shopping-basket mb-1" style="font-size: 15px;"></i>
    <span style="writing-mode: vertical-rl; text-orientation: mixed; transform: rotate(180deg); font-size: 11px; font-weight: 800; letter-spacing: 1px;">CART</span>
</button>

<!-- Right: Checkout Sidebar (Offcanvas Style) -->
<div class="pos-sidebar bg-white border-left d-flex flex-column p-0 h-100 shadow-lg" id="checkout-sidebar">
    <!-- Sidebar Header -->
    <div class="p-3 bg-dark text-white d-flex align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold"><i class="fas fa-shopping-basket mr-2"></i> Current Order</h6>
        <button class="btn btn-sm btn-link text-white p-0" id="close-sidebar"><i class="fas fa-times fa-lg"></i></button>
    </div>

    <!-- Customer Section -->
    <div class="p-3 border-bottom" style="background: #f8fafc;">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="small font-weight-bold text-muted">Customer</span>
            <button class="btn btn-sm btn-link text-primary p-0" data-toggle="modal" data-target="#addCustomerModal"><i class="fas fa-plus-circle fa-lg"></i></button>
        </div>
        <select class="form-control select2" id="customer-select">
            <option value="{{$walkInId}}" data-type="walkin" data-phone="0000000000">Walk-in Customer</option>
            @foreach($customers as $customer)
            <option value="{{$customer->id}}" data-name="{{$customer->name}}" data-type="{{$customer->customer_type}}" data-balance="{{$customer->current_balance ?? 0}}" data-phone="{{$customer->phone}}">
                {{$customer->name}} ({{$customer->phone ?? 'N/A'}}) | Bal: Rs. {{ number_format($customer->current_balance ?? 0, 2) }}
            </option>
            @endforeach
        </select>
    </div>

    <!-- Current Order List -->
    <div class="flex-grow-1 overflow-auto p-2 custom-scrollbar bg-white" id="cart-items">
        <!-- Cart items here -->
        <div class="text-center py-5 text-muted opacity-5">
            <i class="fas fa-shopping-cart fa-3x mb-3"></i>
            <p class="small">Cart is empty</p>
        </div>
    </div>

    <!-- Summary & Actions -->
    <div class="p-2 bg-white border-top shadow-sm" style="margin-top: auto;">
        <div class="summary-box px-3 py-2 rounded-lg mb-2" style="background: #f8fafc; border: 1px solid #f1f5f9;">
            <div class="d-flex justify-content-between mb-1" style="font-size: 11px;">
                <span class="text-muted">Items: <span class="font-weight-bold text-dark" id="items-count">0</span></span>
                <span class="text-muted">Sub: <span class="font-weight-bold text-dark" id="subtotal-val">0.00</span></span>
                <span class="text-muted">Disc: <span class="font-weight-bold text-danger" id="discount-val">0.00</span></span>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-1 pt-1 border-top">
                <span class="small font-weight-bold">Payable</span>
                <span class="h6 m-0 text-success font-weight-bold" id="total-val">0.00</span>
            </div>
        </div>

        <div class="d-flex align-items-center" style="gap: 5px;">
            <button class="btn btn-light btn-sm px-3" id="park-order" title="Park Order" style="height: 38px; border: 1px solid #e2e8f0;"><i class="fas fa-pause text-muted"></i></button>
            <button class="btn btn-light btn-sm px-3" id="clear-cart" title="Clear Cart" style="height: 38px; border: 1px solid #e2e8f0;"><i class="fas fa-trash-alt text-danger"></i></button>
            <button class="btn btn-success btn-sm flex-grow-1 font-weight-bold shadow-sm animated-pulse" data-toggle="modal" data-target="#paymentModal" style="height: 38px; border-radius: 8px; font-size: 13px;">
                <i class="fas fa-check-circle mr-1"></i> CHECKOUT
            </button>
        </div>
    </div>
</div>

<div class="sidebar-overlay" id="pos-overlay"></div>

<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog" style="z-index: 105000;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Customer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="add-customer-form">
                    @csrf
                    <div class="form-group">
                        <label>Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Customer Type</label>
                        <select name="customer_type" class="form-control">
                            <option value="retail">Retail Customer</option>
                            <option value="wholesale">Wholesale Customer</option>
                            <option value="salesman">Salesman</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="save-customer-btn">Save Customer</button>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" style="z-index: 105000;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title font-weight-bold">Select Payment Method</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-0">
                <div class="row no-gutters">
                    <!-- Left Side: Order Summary -->
                    <div class="col-md-5 bg-light p-4 border-right">
                        <div class="text-center mb-4">
                            <i class="fas fa-receipt fa-3x text-muted mb-2"></i>
                            <h5 class="text-uppercase small font-weight-bold text-muted mb-1">Total Payable</h5>
                            <h2 class="font-weight-bold text-dark total-payable">Rs. 0.00</h2>
                        </div>

                        <div class="px-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Total Items</span>
                                <span class="font-weight-bold" id="modal-items-count">0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Ledger Balance</span>
                                <span class="font-weight-bold text-info" id="modal-ledger-balance">Rs. 0.00</span>
                            </div>
                            <hr>
                            <div class="form-group mb-0" id="due-date-wrapper" style="display: none;">
                                <label class="small font-weight-bold text-uppercase text-danger">Payment Due Date</label>
                                <input type="date" class="form-control form-control-sm border-0 shadow-none bg-white" id="payment-due-date" value="{{ date('Y-m-d', strtotime('+7 days')) }}">
                                <small class="text-muted" style="font-size: 10px;">For partial/credit payments</small>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side: Payment Methods -->
                    <div class="col-md-7 p-4 bg-white">
                        <label class="font-weight-bold text-uppercase small text-muted mb-2 d-block">Select Payment Method</label>

                        <div class="row no-gutters mb-4" id="payment-methods-grid">
                            @php
                                $activeReg = \App\Models\CashRegister::where('status', 'open')->where('user_id', auth()->id())->first();
                                $activeAccountId = $activeReg ? $activeReg->financial_account_id : null;
                            @endphp
                            
                            @foreach($accounts as $acc)
                                <div class="col-6 p-1 payment-method-item filter-all filter-{{$acc->type}}">
                                    <div class="payment-option p-3 border rounded text-center cursor-pointer position-relative transition-all {{ $acc->id == $activeAccountId ? 'active' : '' }}" 
                                         data-method="{{ $acc->id }}" 
                                         data-is-cash="{{ $acc->type == 'cash' ? 'yes' : 'no' }}">
                                        <div class="check-mark"><i class="fas fa-check-circle text-success"></i></div>
                                        <i class="fas fa-{{$acc->type == 'bank' ? 'university' : ($acc->type == 'wallet' ? 'mobile-alt' : 'money-bill-wave')}} fa-lg text-{{$acc->type == 'cash' ? 'success' : ($acc->type == 'wallet' ? 'warning' : 'primary')}} mb-2"></i>
                                        <div class="small font-weight-bold text-uppercase">{{$acc->name}}</div>
                                        <div style="font-size: 10px;" class="text-muted">Bal: Rs. {{ number_format($acc->current_balance, 0) }}</div>
                                    </div>
                                </div>
                            @endforeach

                            <div class="col-6 p-1 payment-method-item filter-all filter-credit">
                                <div class="payment-option p-3 border rounded text-center cursor-pointer position-relative transition-all" data-method="credit">
                                    <div class="check-mark"><i class="fas fa-check-circle text-success"></i></div>
                                    <i class="fas fa-user-clock fa-lg text-danger mb-2"></i>
                                    <div class="small font-weight-bold">CREDIT SALE</div>
                                </div>
                            </div>
                        </div>

                        <!-- Amount Received & Discount -->
                        <div id="amount-input-wrapper" style="display: none;" class="animated fadeIn">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-uppercase small text-success">Amount Received</label>
                                <div class="input-group input-group-lg border rounded overflow-hidden shadow-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-0">Rs.</span>
                                    </div>
                                    <input type="number" class="form-control border-0 shadow-none font-weight-bold" id="amount-received" placeholder="0.00">
                                </div>
                            </div>

                            <div class="form-group mb-0">
                                <label class="font-weight-bold text-uppercase small text-danger">Order Discount</label>
                                <div class="input-group input-group-lg border rounded overflow-hidden shadow-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-0">Rs.</span>
                                    </div>
                                    <input type="number" class="form-control border-0 shadow-none font-weight-bold text-danger" id="order-discount" placeholder="0.00" value="0">
                                </div>
                                <small class="text-muted" style="font-size: 11px;">Final discount applied to total payable.</small>
                                <div id="partial-info" class="mt-2 small text-warning font-weight-bold" style="display:none;">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Partial Payment
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4">
                <div class="custom-control custom-checkbox mr-auto d-flex flex-column align-items-start" style="gap: 4px; padding-left: 0;">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="print-receipt-toggle">
                        <label class="custom-control-label font-weight-bold text-success" for="print-receipt-toggle" style="cursor: pointer;">
                            <i class="fas fa-print mr-1"></i> Print Thermal Receipt
                        </label>
                    </div>
                    <div class="custom-control custom-checkbox mt-1" id="urdu-print-container" style="display: none; padding-left: 1.5rem;">
                        <input type="checkbox" class="custom-control-input" id="print-receipt-urdu">
                        <label class="custom-control-label font-weight-bold text-info" for="print-receipt-urdu" style="cursor: pointer;">
                            <i class="fas fa-language mr-1"></i> Translate to Urdu (اردو)
                        </label>
                    </div>
                    <div class="custom-control custom-checkbox mt-1">
                        <input type="checkbox" class="custom-control-input" id="share-receipt-toggle" checked>
                        <label class="custom-control-label font-weight-bold text-primary" for="share-receipt-toggle" style="cursor: pointer;">
                            <i class="fas fa-share-nodes mr-1"></i> Share Invoice
                        </label>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success btn-lg px-5 shadow" id="complete-order">SAVE ORDER</button>
            </div>
        </div>
    </div>
</div>

<iframe id="print-iframe" style="display:none;"></iframe>

<style>
    .pos-sidebar {
        position: fixed !important;
        right: -400px;
        top: 0;
        height: 100vh !important;
        width: 400px;
        z-index: 104000;
        transition: right 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: -15px 0 30px rgba(0, 0, 0, 0.3);
    }
    .pos-sidebar.active {
        right: 0;
    }
    @media (max-width: 576px) {
        .pos-sidebar {
            width: 100%;
            right: -100%;
        }
        .pos-sidebar.active {
            right: 0;
        }
    }
    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        z-index: 103999;
        display: none;
    }
    .sidebar-overlay.active {
        display: block;
    }
    
    

    .animated-pulse {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(249, 115, 22, 0.6); }
        70% { box-shadow: 0 0 0 15px rgba(249, 115, 22, 0); }
        100% { box-shadow: 0 0 0 0 rgba(249, 115, 22, 0); }
    }

    .payment-option.active {
        border-color: #1cc88a !important;
        background-color: #f8f9fc;
        box-shadow: 0 0 15px rgba(28,200,138,0.2);
        transform: translateY(-2px);
    }
    .payment-option .check-mark {
        position: absolute;
        top: 5px;
        right: 5px;
        display: none;
    }
    .payment-option.active .check-mark {
        display: block;
    }

    .cart-item {
        transition: all 0.2s ease;
        border-radius: 8px;
        margin-bottom: 8px;
        border: 1px solid transparent;
    }
    .cart-item:hover {
        background: #f8fafc;
        border-color: #e2e8f0;
    }

    /* Purchase history toast styling */
    .purchase-history-toast {
        font-size: 14px !important;
        padding: 8px 12px !important;
        border-radius: 10px !important;
        max-width: 380px !important;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12) !important;
        border: 1px solid #bae6fd !important;
        background-color: #f0f9ff !important;
    }
    .purchase-history-toast .swal2-title {
        font-size: 13.5px !important;
        font-weight: 800 !important;
        color: #0369a1 !important;
        margin: 0 !important;
        padding: 0 0 4px 0 !important;
        border-bottom: 1px solid #e0f2fe;
        text-align: left !important;
    }
    .purchase-history-toast .swal2-html-container {
        font-size: 12.5px !important;
        margin: 6px 0 0 0 !important;
        color: #334155 !important;
        text-align: left !important;
        line-height: 1.4 !important;
    }
    .purchase-history-toast .swal2-icon {
        scale: 0.7 !important;
        margin: 0 8px 0 0 !important;
    }

    /* Mobile view: size should change to much smaller */
    @media (max-width: 576px) {
        .purchase-history-toast {
            font-size: 11px !important;
            padding: 6px 10px !important;
            max-width: 280px !important;
        }
        .purchase-history-toast .swal2-title {
            font-size: 11px !important;
            padding-bottom: 2px !important;
        }
        .purchase-history-toast .swal2-html-container {
            font-size: 10px !important;
            margin-top: 4px !important;
        }
        .purchase-history-toast .swal2-icon {
            scale: 0.55 !important;
            margin-right: 4px !important;
        }
    }
</style>

<script>
    window.posCart = JSON.parse(localStorage.getItem('posCart')) || [];
    
    

    $(document).ready(function() {
        if($('#customer-select').length) {
            $('#customer-select').select2({ width: '100%' });
        }
        
        renderCart();

        $('#global-cart-btn, #toggle-cart').on('click', function(e) {
            
            $('#checkout-sidebar').addClass('active');
            $('#pos-overlay').addClass('active');
        });

        $('#pos-overlay, #close-sidebar').on('click', function() {
            $('#checkout-sidebar').removeClass('active');
            $('#pos-overlay').removeClass('active');
        });

        $('#clear-cart').on('click', function() {
            Swal.fire({
                title: 'Clear Cart?',
                text: "All items will be removed!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, clear it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.posCart = [];
                    saveCart();
                }
            });
        });
        // Clear Cart
        $(document).on('click', '#clear-cart', function() {
            if (window.posCart.length == 0) return;
            Swal.fire({
                title: 'Clear Cart?',
                text: "This will remove all items from the current order.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74a3b',
                cancelButtonColor: '#858796',
                confirmButtonText: 'Yes, clear it'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.posCart = [];
                    window.saveCart();
                }
            });
        });
    });

    window.saveCart = function() {
        localStorage.setItem('posCart', JSON.stringify(window.posCart));
        renderCart();
    };

    window.removeFromCart = function(index) {
        window.posCart.splice(index, 1);
        saveCart();
    };

    window.updatePrice = function(index, val) {
        let p = parseFloat(val);
        p = isNaN(p) ? 0 : p;
        window.posCart[index].price = p;

        if (p > window.posCart[index].base_price) {
            window.posCart[index].original_price = p;
        } else {
            window.posCart[index].original_price = window.posCart[index].base_price;
        }
        saveCart();
    };

    window.updateQty = function(index, val) {
        window.posCart[index].qty = Math.max(1, parseInt(val));
        saveCart();
    };

    window.renderCart = function() {
        let html = '';
        let subtotal = 0;
        let totalDiscount = 0;

        if (window.posCart.length == 0) {
            $('#cart-items').html('<div class="text-center py-5 text-muted"><i class="fas fa-shopping-basket fa-3x mb-3 opacity-2"></i><p>Current order is empty</p></div>');
            updateSummary(0, 0, 0);
            if ($('#global-cart-btn').data('is-pos') === true) {
                $('#global-cart-btn').css('display', 'flex');
            } else {
                $('#global-cart-btn').hide();
            }
            return;
        }

        $('#global-cart-btn').css('display', 'flex');

        window.posCart.forEach((item, index) => {
            let lineOriginalTotal = item.original_price * item.qty;
            let lineActualTotal = item.price * item.qty;
            subtotal += lineOriginalTotal;
            totalDiscount += (lineOriginalTotal - lineActualTotal);

            html += `
                <div class="cart-item d-flex align-items-center p-2 mb-1 border-bottom" style="background: #fff; min-height: 45px;">
                    <div class="flex-grow-1 min-width-0">
                        <div class="d-flex align-items-center flex-wrap overflow-hidden">
                            <div class="d-flex justify-content-between align-items-center mb-1 w-100">
                                <h6 class="font-weight-bold m-0 text-dark text-truncate" style="font-size: 13px; line-height: 1.1; max-width: 85%;">${item.title}</h6>
                                <button class="btn btn-sm btn-info p-0 d-flex align-items-center justify-content-center shadow-sm" 
                                    style="width: 18px; height: 18px; border-radius: 4px; font-size: 10px;" 
                                    onclick="showProductHistoryGlobal(${item.id}, '${item.type}')" title="Selling History">
                                    <i class="fas fa-info-circle text-white" style="font-size: 10px;"></i>
                                </button>
                            </div>
                            ${item.last_purchase ? `<div class="w-100 mt-1 mb-1"><span class="badge badge-soft-info" style="font-size: 10px; background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; padding: 2px 6px; border-radius: 4px;"><i class="fas fa-history mr-1"></i>${item.last_purchase}</span></div>` : ''}
                        </div>
                        <div class="d-flex align-items-center mt-1" style="gap: 4px;">
                            <div class="price-cell-sleek d-flex align-items-center border rounded px-1 bg-light-soft" style="border-color: #e2e8f0 !important; height: 22px;">
                                <span class="text-muted" style="font-size: 9px; margin-right: 2px;">Rs.</span>
                                <input type="number" step="0.01" class="border-0 bg-transparent p-0 font-weight-bold text-dark" value="${item.price}" style="width: 52px; font-size: 12px; outline: none; box-shadow: none;" onchange="updatePrice(${index}, this.value)">
                            </div>
                            <span class="text-muted small ml-1" style="font-size: 10px; opacity: 0.8;">x ${item.qty} ${item.unit || ''}</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center" style="gap: 8px;">
                        <div class="qty-cell-sleek d-flex align-items-center border rounded bg-light-soft" style="border-color: #e2e8f0 !important; height: 22px; padding: 0 2px;">
                            <button class="btn btn-link p-0 text-muted" onclick="updateQty(${index}, ${item.qty - 1})" style="width: 16px;"><i class="fas fa-minus fa-xs"></i></button>
                            <input type="number" class="border-0 bg-transparent text-center font-weight-bold p-0 mx-1 text-dark" value="${item.qty}" style="width: 30px; font-size: 12px; outline: none; box-shadow: none;" onchange="updateQty(${index}, this.value)">
                            <button class="btn btn-link p-0 text-muted" onclick="updateQty(${index}, ${item.qty + 1})" style="width: 16px;"><i class="fas fa-plus fa-xs"></i></button>
                        </div>
                        <div class="text-right" style="min-width: 65px;">
                            <span class="font-weight-bold text-success" style="font-size: 13.5px;">Rs.${lineActualTotal.toLocaleString()}</span>
                        </div>
                        <button class="btn btn-link text-danger p-0 border-0" onclick="removeFromCart(${index})" style="font-size: 14px; opacity: 0.7;">
                            <i class="fas fa-times-circle"></i>
                        </button>
                    </div>
                </div>
            `;
        });
        $('#cart-items').html(html);
        updateSummary(subtotal, totalDiscount, window.posCart.length);
    };

    function updateSummary(subtotal, discount, count) {
        let total = subtotal - discount;
        $('#items-count').text(count);
        $('#modal-items-count').text(count);
        $('#subtotal-val').text('Rs. ' + subtotal.toFixed(2));
        $('#discount-val').text('Rs. ' + discount.toFixed(2));
        $('#total-val').text('Rs. ' + total.toFixed(2));
        $('.total-payable').text('Rs. ' + total.toFixed(2));

        if (count > 0) {
            $('#cart-badge').text(count).show();
            $('#global-cart-badge').text(count);
            $('#toggle-cart').addClass('animated-pulse');
        } else {
            $('#cart-badge').hide();
            $('#global-cart-badge').text('0');
            $('#toggle-cart').removeClass('animated-pulse');
        }
    }

    // Payment Logic
    $(document).on('click', '.payment-option', function() {
        $('.payment-option').removeClass('active');
        $(this).addClass('active');

        let method = $(this).data('method');
        
        if (method === 'credit') {
            $('#due-date-wrapper').fadeIn();
            $('#amount-received').val(0).trigger('input');
        } else {
            $('#due-date-wrapper').fadeOut();
            if ($('#amount-received').val() == 0) {
                $('#amount-received').val('').trigger('input');
            }
        }
        $('#amount-input-wrapper').show();
        $('#amount-received').focus();
    });

    $(document).on('input', '#amount-received, #order-discount', function() {
        let subtotal = parseFloat($('#subtotal-val').text().replace('Rs. ', '')) || 0;
        let lineDiscount = parseFloat($('#discount-val').text().replace('Rs. ', '')) || 0;
        let originalTotal = subtotal - lineDiscount;

        let globalDiscount = parseFloat($('#order-discount').val()) || 0;
        let newTotal = originalTotal - globalDiscount;

        $('.total-payable').text('Rs. ' + newTotal.toFixed(2));
        $('#total-val').text('Rs. ' + newTotal.toFixed(2));

        let received = parseFloat($('#amount-received').val()) || 0;
        if (received > 0 && received < newTotal) {
            $('#partial-info').show();
        } else {
            $('#partial-info').hide();
        }
    });

    $(document).on('click', '#complete-order', function() {
        if (window.posCart.length == 0) {
            Swal.fire('Error', 'Cart is empty!', 'error');
            return;
        }

        let customer_id = $('#customer-select').val();
        let total_amount = parseFloat($('#total-val').text().replace('Rs. ', ''));
        let payment_method = $('.payment-option.active').data('method');

        if (!payment_method) {
            Swal.fire('Error', 'Please select a payment method!', 'warning');
            return;
        }
        let amount_received_raw = $('#amount-received').val();
        let amount_received = (amount_received_raw === "" || amount_received_raw === null) ? 0 : parseFloat(amount_received_raw);
        if (isNaN(amount_received)) amount_received = 0;
        
        let order_discount = parseFloat($('#order-discount').val()) || 0;
        let due_date = $('#payment-due-date').val();

        // Prepare data
        let payload = {
            customer_id: customer_id,
            total_amount: total_amount,
            discount: order_discount,
            payment_method: payment_method,
            payment_status: (amount_received >= total_amount) ? 'paid' : 'partial',
            amount_paid: amount_received,
            due_date: due_date,
            cart: window.posCart,
            sales_order_id: window.salesOrderId,
            _token: "{{csrf_token()}}"
        };

        $(this).prop('disabled', true).text('Processing...');

        $.ajax({
            url: "{{route('pos.store-order')}}",
            type: "POST",
            data: payload,
            success: function(response) {
                if (response.status == 'success') {
                    // Handle Printing via hidden iframe only if toggled ON
                    if ($('#print-receipt-toggle').is(':checked') && response.thermal_url) {
                        let printUrl = response.thermal_url;
                        if ($('#print-receipt-urdu').is(':checked')) {
                            printUrl += (printUrl.indexOf('?') >= 0 ? '&' : '?') + 'lang=ur';
                        }
                        $('#print-iframe').attr('src', printUrl);
                    }

                    // Share Receipt / Image logic
                    let shareReceiptPromise = Promise.resolve();
                    if ($('#share-receipt-toggle').is(':checked') && response.invoice_url) {
                        shareReceiptPromise = new Promise((resolveOuter) => {
                            Swal.fire({
                                title: '<i class="fas fa-share-nodes text-primary mr-1"></i> Share Invoice / Image',
                                html: `
                                    <div class="p-2 text-center">
                                        <p class="text-muted small mb-4">Choose your invoice language.<br><b>First Tap:</b> Generates & downloads invoice image.<br><b>Second Tap:</b> Opens share menu!</p>
                                        <div class="d-flex flex-column" style="gap: 12px;">
                                            <button id="pos-share-en" class="btn btn-outline-primary btn-block py-3 font-weight-bold d-flex align-items-center justify-content-center" style="border-radius: 12px; font-size: 0.95rem; border-width: 2px;">
                                                <i class="fas fa-file-invoice mr-2"></i> Share in English (EN)
                                            </button>
                                            <button id="pos-share-ur" class="btn btn-outline-success btn-block py-3 font-weight-bold text-success d-flex align-items-center justify-content-center" style="border-radius: 12px; font-size: 0.95rem; border-width: 2px;">
                                                <i class="fas fa-language mr-2"></i> Share in Urdu (اردو)
                                            </button>
                                        </div>
                                    </div>
                                `,
                                showConfirmButton: false,
                                showCancelButton: true,
                                cancelButtonText: 'Close',
                                cancelButtonColor: '#6c757d',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    let enBlob = null;
                                    let urBlob = null;

                                    async function handleLanguageTap(lang, $btn) {
                                        let currentBlob = lang === 'ur' ? urBlob : enBlob;

                                        // SECOND TAP: Open share menu
                                        if (currentBlob) {
                                            const filename = 'Invoice_' + (response.order_number || Date.now()) + (lang === 'ur' ? '_Urdu' : '') + '.png';
                                            const file = new File([currentBlob], filename, { type: 'image/png' });
                                            let text = lang === 'ur' ? "السلام علیکم، دانیال آٹوز سے آپ کا بل یہاں ہے:" : "Assalam-o-Alaikum, here is your receipt from Danyal Autos:";

                                            if (navigator.share && navigator.canShare && navigator.canShare({ files: [file] })) {
                                                try {
                                                    await navigator.share({
                                                        files: [file],
                                                        title: 'Danyal Autos Invoice',
                                                        text: text
                                                    });
                                                } catch (err) {
                                                    if (err.name !== 'AbortError') {
                                                        console.log('Native Share Failed:', err);
                                                    }
                                                }
                                            } else {
                                                // Fallback to text url share via WhatsApp
                                                let shareUrl = response.invoice_url;
                                                if (lang === 'ur') {
                                                    shareUrl += (shareUrl.indexOf('?') >= 0 ? '&' : '?') + 'lang=ur';
                                                }
                                                let customerPhone = $('#customer-select option:selected').data('phone') || '';
                                                let cleanedPhone = customerPhone.toString().replace(/[^0-9]/g, '');
                                                if (cleanedPhone && !cleanedPhone.startsWith('92')) {
                                                    if (cleanedPhone.startsWith('0')) {
                                                        cleanedPhone = '92' + cleanedPhone.substring(1);
                                                    } else {
                                                        cleanedPhone = '92' + cleanedPhone;
                                                    }
                                                }
                                                let shareText = encodeURIComponent(text + "\n" + shareUrl);
                                                let waUrl = cleanedPhone ? `https://api.whatsapp.com/send?phone=${cleanedPhone}&text=${shareText}` : `https://api.whatsapp.com/send?text=${shareText}`;
                                                window.open(waUrl, '_blank');
                                            }
                                            return;
                                        }

                                        // FIRST TAP: Generate standard invoice screenshot & download
                                        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Generating & Downloading...');

                                        try {
                                            let printUrl = response.invoice_url.replace('/pdf/', '/print/') + '?type=standard';
                                            if (lang === 'ur') {
                                                printUrl += '&lang=ur';
                                            }

                                            const printResponse = await fetch(printUrl);
                                            let htmlText = await printResponse.text();

                                            // Strip auto-print scripts
                                            htmlText = htmlText.replace(/onload\s*=\s*['"]window\.print\(\)['"]/gi, '');
                                            htmlText = htmlText.replace(/window\.onload\s*=\s*function\(\)\s*\{\s*window\.print\(\);\s*\}/gi, '');

                                            // Render inside iframe
                                            const iframe = document.createElement('iframe');
                                            iframe.style.position = 'fixed';
                                            iframe.style.right = '-9999px';
                                            iframe.style.width = '800px';
                                            iframe.style.height = '2500px';
                                            document.body.appendChild(iframe);

                                            const iframeDoc = iframe.contentWindow.document;
                                            iframeDoc.open();
                                            iframeDoc.write(htmlText);
                                            iframeDoc.close();

                                            // Wait for assets/fonts to load
                                            await new Promise(r => setTimeout(r, 1000));

                                            // Auto-resize
                                            iframe.style.height = (iframeDoc.documentElement.scrollHeight + 100) + 'px';

                                            // Load html2canvas if needed
                                            if (typeof html2canvas === 'undefined') {
                                                await new Promise((resolveScript) => {
                                                    const script = document.createElement('script');
                                                    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
                                                    script.onload = resolveScript;
                                                    document.head.appendChild(script);
                                                });
                                            }

                                            const wrapper = iframeDoc.getElementById('invoice-wrapper') || iframeDoc.body;
                                            const canvas = await html2canvas(wrapper, {
                                                scale: 2,
                                                useCORS: true,
                                                backgroundColor: '#ffffff'
                                            });

                                            const blob = await new Promise(r => canvas.toBlob(r, 'image/png'));
                                            document.body.removeChild(iframe);

                                            // Save blob
                                            if (lang === 'ur') {
                                                urBlob = blob;
                                            } else {
                                                enBlob = blob;
                                            }

                                            // Trigger automatic download
                                            const downloadLink = document.createElement('a');
                                            downloadLink.href = URL.createObjectURL(blob);
                                            const downloadFilename = 'Invoice_' + (response.order_number || Date.now()) + (lang === 'ur' ? '_Urdu' : '') + '.png';
                                            downloadLink.download = downloadFilename;
                                            document.body.appendChild(downloadLink);
                                            downloadLink.click();
                                            document.body.removeChild(downloadLink);

                                            // Update button UI for the SECOND TAP
                                            $btn.prop('disabled', false)
                                                .removeClass('btn-outline-primary btn-outline-success text-success')
                                                .addClass('btn-warning text-dark')
                                                .html('<i class="fas fa-share-alt mr-2"></i> Tap to Share ' + (lang === 'ur' ? 'Urdu (اردو)' : 'English (EN)'));

                                        } catch (err) {
                                            console.error('POS Image share generation failed:', err);
                                            $btn.prop('disabled', false).html(lang === 'ur' ? '<i class="fas fa-language mr-2"></i> Share in Urdu (اردو)' : '<i class="fas fa-file-invoice mr-2"></i> Share in English (EN)');
                                            alert('Failed to generate standard printed image screenshot.');
                                        }
                                    }

                                    $('#pos-share-en').on('click', function() {
                                        handleLanguageTap('en', $(this));
                                    });

                                    $('#pos-share-ur').on('click', function() {
                                        handleLanguageTap('ur', $(this));
                                    });
                                }
                            }).then(() => {
                                resolveOuter();
                            });
                        });
                    }

                    shareReceiptPromise.then(() => {
                        window.posCart = [];
                        localStorage.removeItem('posCart');
                        window.saveCart && window.saveCart();
                        if (response.wa_sent) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Order saved' + ($('#print-receipt-toggle').is(':checked') ? ' and Receipt Printed.' : '.'),
                                icon: 'success',
                                timer: 4000
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Order Saved',
                                text: 'Order created' + ($('#print-receipt-toggle').is(':checked') ? ' and Receipt Sent to Printer' : '') + ', but WhatsApp could not be sent.',
                                icon: 'warning',
                                timer: 5000
                            }).then(() => {
                                location.reload();
                            });
                        }
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
                    $('#complete-order').prop('disabled', false).text('SAVE ORDER');
                }
            },
            error: function(err) {
                console.log(err);
                if (err.status === 422) {
                    let errors = err.responseJSON.errors;
                    let msg = '';
                    $.each(errors, function(key, value) {
                        msg += value[0] + '\n';
                    });
                    alert('Validation Error:\n' + msg);
                } else {
                    alert('Something went wrong! Check console.');
                }
                $('#complete-order').prop('disabled', false).text('SAVE ORDER');
            }
        });
    });
$(document).on('click', '#save-customer-btn', function() {
        let form = $('#add-customer-form');
        $.ajax({
            url: "{{route('users.direct-store')}}",
            type: "POST",
            data: form.serialize() + "&role=user&status=active&password=password123",
            dataType: "json",
            success: function(response) {
                if (typeof response === 'string') {
                    try { response = JSON.parse(response); } catch (e) {}
                }
                let user = response.user || response.data || response;
                let displayText = (user.name || 'Unknown') + ' (' + (user.phone || 'N/A') + ') | Bal: Rs. 0.00';
                let newOption = new Option(displayText, user.id, true, true);
                $(newOption).attr('data-type', user.customer_type || 'retail');
                $(newOption).attr('data-balance', 0);
                $('#customer-select').append(newOption).trigger('change');
                $('#addCustomerModal').modal('hide');
                form[0].reset();
                Swal.fire('Success', 'Customer Added', 'success');
            },
            error: function(err) {
                Swal.fire('Error', 'Failed to add customer', 'error');
            }
        });
    });
    window.fetchLastPurchase = function(cartItem) {
        let customer_id = $('#customer-select').val();
        if (!customer_id || customer_id == 1) return; // Skip walk-in

        $.ajax({
            url: "{{route('pos.last-purchase')}}",
            data: {
                customer_id: customer_id,
                item_type: cartItem.type,
                item_id: cartItem.id
            },
            success: function(res) {
                if (res.found) {
                    cartItem.last_purchase = `Bought ${res.quantity} at Rs.${res.price} on ${res.date}`;
                    renderCart();

                    Swal.fire({
                        title: 'Purchase History Found!',
                        html: `Customer previously bought this item.<br>
                               Date: <b>${res.date}</b> | Qty: <b>${res.quantity}</b> | Price Paid: <b style="color: green;">Rs. ${res.price}</b>`,
                        icon: 'info',
                        position: 'top',
                        toast: true,
                        showConfirmButton: false,
                        timer: 5000,
                        customClass: {
                            popup: 'purchase-history-toast',
                        }
                    });
                }
            }
        });
    };

    window.showProductHistoryGlobal = function(pid, type) {
        Swal.fire({
            title: '<i class="fas fa-info-circle mr-2 text-info"></i> Selling History',
            html: '<div class="text-center py-3"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i></div>',
            showConfirmButton: false,
            showCloseButton: true,
            width: '450px',
            didOpen: () => {
                $.get("{{ route('admin.product-selling-history') }}", { product_id: pid, item_type: type }, function(res) {
                    if (res.success) {
                        let historyHtml = `
                            <div class="text-left px-1">
                                <div class="alert alert-light border mb-3 p-2 d-flex justify-content-between align-items-center">
                                    <div class="small font-weight-bold text-uppercase text-muted">Price Range</div>
                                    <div class="font-weight-bold text-primary">Rs. ${res.min_price.toLocaleString()} - Rs. ${res.max_price.toLocaleString()}</div>
                                </div>
                                
                                <label class="small font-weight-bold text-uppercase text-muted mb-2">Last 5 Sales</label>
                                <div class="table-responsive">
                                    <table class="table table-sm table-borderless mb-0" style="font-size: 12px;">
                                        <thead>
                                            <tr class="border-bottom">
                                                <th>Customer</th>
                                                <th class="text-center">Qty</th>
                                                <th class="text-right">Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${res.history.length > 0 ? res.history.map(s => `
                                                <tr class="border-bottom">
                                                    <td>
                                                        <div class="font-weight-bold">${s.customer}</div>
                                                        <div class="text-muted" style="font-size: 10px;">${s.date}</div>
                                                    </td>
                                                    <td class="text-center align-middle">${s.qty}</td>
                                                    <td class="text-right align-middle font-weight-bold text-success">Rs. ${s.price.toLocaleString()}</td>
                                                </tr>
                                            `).join('') : '<tr><td colspan="3" class="text-center py-3 text-muted">No sales history found</td></tr>'}
                                        </tbody>
                                        ${res.history.length > 0 ? `
                                        <tfoot style="position: sticky; bottom: 0; background: #f8fafc; box-shadow: 0 -1px 0 #e2e8f0;">
                                            <tr>
                                                <td colspan="3" class="p-2 text-center">
                                                    <a href="{{ route('admin.pos') }}?product_id=${pid}" class="btn btn-sm btn-outline-primary btn-block">View All History</a>
                                                </td>
                                            </tr>
                                        </tfoot>
                                        ` : ''}
                                    </table>
                                </div>
                            </div>
                        `;
                        Swal.update({ html: historyHtml });
                    } else {
                        Swal.update({ html: '<div class="text-danger py-3">Failed to load history</div>' });
                    }
                }).fail(function() {
                    Swal.update({ html: '<div class="text-danger py-3">Error fetching data</div>' });
                });
            }
        });
    };
</script>
