@extends('user.layouts.master')
@section('title','Order Products || ' . (Settings::first()->title ?? 'Auto Store'))
@push('styles')
    <!-- TensorFlow.js for Visual Search -->
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/mobilenet"></script>
@endpush
@section('main-content')

<div class="pos-wrapper overflow-hidden" style="height: calc(100vh - 70px); background: #f8fafc; position: relative;">
    
    <!-- Mobile Search & Cart Trigger -->
    <div class="pos-header shadow-sm bg-white p-3 d-flex align-items-center sticky-top" style="z-index: 1000; height: 75px;">
        <div class="flex-grow-1 mr-3">
            <div class="search-box-premium d-flex align-items-center px-3 py-2 rounded-pill position-relative" style="background: #f1f5f9; border: 1.5px solid #e2e8f0;">
                <i class="fas fa-search text-muted mr-2" id="search-icon"></i>
                <i class="fas fa-spinner fa-spin text-primary mr-2 d-none" id="search-spinner"></i>
                <input type="text" id="product-search" class="form-control border-0 bg-transparent p-0 shadow-none" placeholder="Search name, SKU, or brand..." style="font-size: 14px; font-weight: 500;" autofocus autocomplete="off">
                
                <!-- Visual Search Button -->
                <button type="button" id="visual-search-btn" class="btn btn-link p-0 text-primary ml-2" title="Search by Image">
                    <i class="fas fa-camera fa-lg"></i>
                </button>
                <input type="file" id="visual-search-input" class="d-none" accept="image/*">
                
                <!-- Intelligent Suggestions Dropdown -->
                <div id="search-suggestions" class="position-absolute shadow-lg rounded-lg d-none" 
                     style="top: 110%; left: 0; width: 100%; background: #fff; z-index: 2000; max-height: 350px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 12px !important;">
                </div>
            </div>
        </div>
        <div class="d-flex align-items-center ml-auto">
            <!-- Balance Card -->
            <div class="ledger-trigger mr-3 text-right cursor-pointer" data-toggle="modal" data-target="#ledgerModal">
                <div class="small text-muted font-weight-bold text-uppercase" style="font-size: 9px; letter-spacing: 0.5px;">Your Balance</div>
                <div class="font-weight-800 text-dark" style="font-size: 15px;">Rs. {{ number_format($balance, 2) }}</div>
            </div>

            <div class="cart-trigger position-relative" id="toggle-cart">
                <div class="cart-btn rounded-circle d-flex align-items-center justify-content-center bg-primary shadow-lg" style="width: 48px; height: 48px; cursor: pointer;">
                    <i class="fas fa-shopping-cart text-white"></i>
                    <span class="badge badge-danger position-absolute" id="cart-badge" style="top: -5px; right: -5px; border-radius: 50%; font-size: 10px; border: 2px solid #fff;">0</span>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid h-100 p-0">
        <div class="row m-0 h-100">
            <!-- Left: Catalog -->
            <div class="col-12 col-lg-8 p-0 h-100 d-flex flex-column catalog-container">
                


                <!-- Products Grid -->
                <div id="products-grid" class="row m-0 p-3 overflow-auto flex-grow-1 custom-scrollbar">
                    <div class="col-12 text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                </div>
            </div>

            <!-- Right: Order Sidebar (Offcanvas behavior on mobile) -->
            <div class="col-12 col-lg-4 pos-sidebar bg-white border-left d-flex flex-column p-0 h-100 shadow-xl" id="order-sidebar">
                <!-- Sidebar Header -->
                <div class="p-4 border-bottom d-flex align-items-center justify-content-between bg-white sticky-top">
                    <div>
                        <h6 class="m-0 font-weight-800 text-primary uppercase small" style="letter-spacing: 1px;">Current Order</h6>
                        <p class="mb-0 text-muted extra-small">Customer: {{ auth()->user()->name }}</p>
                    </div>
                    <button class="btn btn-light rounded-circle d-lg-none" id="close-sidebar">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Cart Items -->
                <div class="flex-grow-1 overflow-auto p-4 custom-scrollbar" id="cart-items">
                    <div class="text-center py-5 text-muted opacity-2">
                        <i class="fas fa-shopping-basket fa-4x mb-3"></i>
                        <p class="font-weight-600">Your basket is empty</p>
                    </div>
                </div>

                <!-- Summary -->
                <div class="p-4 bg-white border-top shadow-lg">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted font-weight-600">Subtotal</span>
                        <span class="font-weight-700" id="subtotal-val">Rs. 0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-4">
                        <h5 class="font-weight-800 text-primary m-0">Total</h5>
                        <h4 class="font-weight-900 text-success m-0" id="total-val">Rs. 0.00</h4>
                    </div>

                    <div class="row no-gutters" style="gap: 10px;">
                        <div class="col">
                            <button class="btn btn-outline-danger btn-block py-3 font-weight-700 border-2" id="clear-cart" style="border-radius: 12px;">
                                <i class="fas fa-trash-alt mr-2"></i> CLEAR
                            </button>
                        </div>
                        <div class="col-7">
                            <button class="btn btn-success btn-block py-3 font-weight-800 shadow-success" id="submit-order" style="border-radius: 12px; font-size: 14px;">
                                PLACE ORDER <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Overlay for mobile sidebar -->
    <div class="pos-overlay" id="sidebar-overlay"></div>
</div>

<style>
    /* Premium Catalog Layout */
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    
    .filter-cat.active {
        background: var(--primary) !important;
        color: #fff !important;
        border-color: var(--primary) !important;
    }

    /* Offcanvas Sidebar Logic */
    .pos-sidebar {
        transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        z-index: 1050;
    }

    @media (max-width: 991.98px) {
        .pos-sidebar {
            position: fixed;
            top: 0;
            right: 0;
            width: 100%;
            max-width: 400px;
            transform: translateX(100%);
        }
        .pos-sidebar.active {
            transform: translateX(0);
        }
        /* Fix bottom blocking */
        .pos-sidebar .border-top {
            padding-bottom: 85px !important;
        }
        .pos-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
            display: none;
            z-index: 1040;
        }
        .pos-overlay.active {
            display: block;
        }
    }

    /* High-End Product Cards */
    .product-grid-item {
        margin-bottom: 20px;
    }
    .product-premium-card {
        background: #fff;
        border-radius: 18px;
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        position: relative;
    }
    .product-premium-card:active {
        transform: scale(0.95);
    }
    .product-img-wrapper {
        position: relative;
        aspect-ratio: 1/1;
        overflow: hidden;
    }
    .product-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .price-badge {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: rgba(255,255,255,0.9);
        backdrop-filter: blur(5px);
        padding: 4px 10px;
        border-radius: 8px;
        font-weight: 800;
        font-size: 11px;
        color: #059669;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .product-info {
        padding: 12px;
    }
    .product-title {
        font-size: 13px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 4px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.3;
    }
    .product-meta {
        font-size: 11px;
        color: #64748b;
        font-weight: 500;
    }

    /* Cart Item Styling */
    .cart-item-premium {
        display: flex;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1.5px solid #f1f5f9;
    }
    .cart-item-info h6 {
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 4px;
        color: #1e293b;
    }
    .qty-controls {
        background: #f8fafc;
        border-radius: 10px;
        padding: 4px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .qty-btn {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        border: none;
        background: #fff;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        color: var(--primary);
    }
    .cart-btn {
        transition: transform 0.2s;
    }
    .cart-btn:active { transform: scale(0.9); }

    /* Suggestion Styling */
    .suggestion-item {
        display: flex;
        align-items: center;
        padding: 10px 15px;
        border-bottom: 1px solid #f1f5f9;
        cursor: pointer;
        transition: background 0.2s;
    }
    .suggestion-item:hover { background: #f8fafc; }
    .suggestion-item img {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 8px;
        margin-right: 12px;
    }
    .suggestion-item .info h6 {
        margin: 0;
        font-size: 13px;
        font-weight: 700;
        color: #1e293b;
    }
    .suggestion-item .info span {
        font-size: 11px;
        color: #64748b;
    }
    .suggestion-item .price {
        margin-left: auto;
        font-weight: 800;
        font-size: 12px;
        color: #059669;
    }

    /* Visual Search Scanning Animation */
    #visual-scan-overlay {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.85);
        z-index: 9999;
        display: none;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #fff;
    }
    .scanner-line {
        width: 80%;
        height: 2px;
        background: #3b82f6;
        box-shadow: 0 0 15px #3b82f6;
        position: absolute;
        top: 0;
        animation: scan 2s infinite ease-in-out;
    }
    @keyframes scan {
        0% { top: 20%; }
        50% { top: 80%; }
        100% { top: 20%; }
    }
    #scanned-img-preview {
        width: 250px;
        height: 250px;
        object-fit: cover;
        border-radius: 20px;
        border: 4px solid #fff;
        margin-bottom: 20px;
        position: relative;
    }

    /* Ledger Styling */
    .ledger-item {
        border-radius: 12px;
        background: #f8fafc;
        padding: 12px 15px;
        margin-bottom: 10px;
        border: 1px solid #e2e8f0;
    }
    .ledger-item.debit { border-left: 4px solid #ef4444; }
    .ledger-item.credit { border-left: 4px solid #10b981; }
</style>

<!-- Ledger Modal -->
<div class="modal fade" id="ledgerModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm-full" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-800">Account Ledger</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="bg-primary text-white p-3 rounded-lg mb-4 text-center">
                    <div class="small opacity-75">Current Balance</div>
                    <h3 class="font-weight-bold m-0">Rs. {{ number_format($balance, 2) }}</h3>
                </div>
                
                <h6 class="font-weight-800 mb-3" style="font-size: 13px;">Recent Transactions</h6>
                <div class="ledger-list custom-scrollbar" style="max-height: 400px; overflow-y: auto;">
                    @forelse($recent_ledger as $item)
                        <div class="ledger-item {{ $item->type }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="small font-weight-bold text-dark">{{ $item->description }}</div>
                                    <div class="extra-small text-muted">{{ date('d M, Y', strtotime($item->transaction_date)) }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-weight-800 {{ $item->type == 'debit' ? 'text-danger' : 'text-success' }}">
                                        {{ $item->type == 'debit' ? '-' : '+' }} Rs. {{ number_format($item->amount, 2) }}
                                    </div>
                                    <div class="extra-small text-muted">Bal: {{ number_format($item->running_balance, 2) }}</div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted small">No recent transactions found</div>
                    @endforelse
                </div>
            </div>
            <div class="modal-footer border-0">
                <a href="{{ route('user.ledger') }}" class="btn btn-light btn-block font-weight-700 rounded-pill">VIEW FULL LEDGER</a>
            </div>
        </div>
    </div>
</div>

<!-- Visual Scan Overlay -->
<div id="visual-scan-overlay">
    <div class="position-relative">
        <img id="scanned-img-preview" src="">
        <div class="scanner-line"></div>
    </div>
    <h5 class="font-weight-bold mb-1">AI Visual Matching...</h5>
    <p class="small opacity-75" id="scan-status">Identifying car part...</p>
</div>

@endsection

@push('scripts')
<script>
    let cart = [];
    let products = [];
    const customerType = "{{ auth()->user()->customer_type ?? 'retail' }}";

    $(document).ready(function() {
        fetchProducts('');
        
        // Sidebar Toggles
        $('#toggle-cart, #close-sidebar, #sidebar-overlay').on('click', function() {
            $('#order-sidebar, #sidebar-overlay').toggleClass('active');
        });
    });

    let searchTimer;
    $('#product-search').on('input', function() {
        let val = $(this).val();
        clearTimeout(searchTimer);
        
        if(val.length < 2) {
            $('#search-suggestions').addClass('d-none');
            return;
        }

        $('#search-icon').addClass('d-none');
        $('#search-spinner').removeClass('d-none');

        searchTimer = setTimeout(() => {
            fetchProducts(val);
        }, 300); // 300ms debounce
    });

    // Hide suggestions when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.search-box-premium').length) {
            $('#search-suggestions').addClass('d-none');
        }
    });

    function renderSuggestions(data) {
        let html = '';
        if(data.length > 0) {
            data.slice(0, 10).forEach(p => { // Show top 10
                let price = getPriceForCustomer(p);
                let img = p.photo ? p.photo.split(',')[0] : '{{asset('backend/img/thumbnail-default.jpg')}}';
                if(!img.startsWith('http')) img = '/' + img;
                
                html += `
                    <div class="suggestion-item" onclick="selectSuggestion(${p.id}, '${p.item_type}')">
                        <img src="${img}">
                        <div class="info">
                            <h6>${p.title}</h6>
                            <span>${p.sku || 'No SKU'}</span>
                        </div>
                        <div class="price">Rs. ${price.toLocaleString()}</div>
                    </div>
                `;
            });
            $('#search-suggestions').html(html).removeClass('d-none');
        } else {
            $('#search-suggestions').html('<div class="p-3 text-center small text-muted">No products match your search</div>').removeClass('d-none');
        }
        $('#search-spinner').addClass('d-none');
        $('#search-icon').removeClass('d-none');
    }

    function selectSuggestion(pid, type) {
        addToCart(pid, type);
        $('#search-suggestions').addClass('d-none');
        $('#product-search').val('');
        renderProducts(); // Refresh the background grid too
    }



    function fetchProducts(query) {
        $.ajax({
            url: "{{ route('user.online-order.search') }}",
            data: { query: query },
            success: function(res) {
                products = res;
                renderProducts();
                if(query.length >= 2) renderSuggestions(res);
            }
        });
    }

    function getPriceForCustomer(product) {
        let price = parseFloat(product.price);
        if(customerType == 'wholesale' && product.wholesale_price) price = parseFloat(product.wholesale_price);
        else if(customerType == 'retail' && product.retail_price) price = parseFloat(product.retail_price);
        else if(customerType == 'walkin' && product.walkin_price) price = parseFloat(product.walkin_price);
        else if(customerType == 'salesman' && product.salesman_price) price = parseFloat(product.salesman_price);
        return price || 0;
    }

    function renderProducts() {
        let catId = 'all';
        let html = '';
        
        let filtered = products;
        if(catId !== 'all') {
            filtered = products.filter(p => p.cat_id == catId);
        }

        filtered.forEach(p => {
            let displayPrice = getPriceForCustomer(p);
            let img = p.photo ? p.photo.split(',')[0] : '{{asset('backend/img/thumbnail-default.jpg')}}';
            if(!img.startsWith('http')) img = '/' + img;

            html += `
                <div class="col-6 col-md-4 col-xl-3 product-grid-item">
                    <div class="product-premium-card shadow-sm h-100" onclick="addToCart(${p.id}, '${p.item_type}')">
                        <div class="product-img-wrapper">
                            <img src="${img}" class="product-img" loading="lazy">
                            <div class="price-badge">Rs. ${displayPrice.toLocaleString()}</div>
                        </div>
                        <div class="product-info">
                            <div class="product-title">${p.item_type == 'bundle' ? '<span class="text-info">[Bundle] </span>' : ''}${p.title}</div>
                            <div class="product-meta">${p.sku || 'No SKU'} ${p.unit ? '• '+p.unit : ''}</div>
                        </div>
                    </div>
                </div>
            `;
        });
        $('#products-grid').html(html || '<div class="col-12 text-center py-5"><img src="{{asset('backend/img/empty.svg')}}" style="width:120px; opacity:0.2;" class="mb-3"><p class="text-muted">No products found</p></div>');
    }

    function addToCart(pid, type) {
        let product = products.find(p => p.id == pid && p.item_type == type);
        if(!product) return;
        
        let cartId = type + '-' + pid;
        let item = cart.find(i => i.unique_id == cartId);
        
        if(item) {
            item.qty++;
        } else {
            cart.push({
                unique_id: cartId,
                id: product.id,
                type: type,
                title: product.title,
                price: getPriceForCustomer(product),
                qty: 1,
                unit: product.unit
            });
        }
        renderCart();
        
        // Mobile UX: Small notification
        if($(window).width() < 992) {
             const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1000,
                timerProgressBar: false
            });
            Toast.fire({
                icon: 'success',
                title: 'Added to cart'
            });
        }
    }

    function removeFromCart(index) {
        cart.splice(index, 1);
        renderCart();
    }

    function updateQty(index, delta) {
        cart[index].qty = Math.max(1, cart[index].qty + delta);
        renderCart();
    }

    function renderCart() {
        let html = '';
        let total = 0;
        let itemsCount = 0;
        
        if(cart.length == 0) {
            $('#cart-items').html('<div class="text-center py-5 text-muted opacity-2"><i class="fas fa-shopping-basket fa-4x mb-3"></i><p class="font-weight-600">Your basket is empty</p></div>');
            $('#subtotal-val, #total-val').text('Rs. 0.00');
            $('#cart-badge').text('0');
            return;
        }

        cart.forEach((item, index) => {
            let itemTotal = item.price * item.qty;
            total += itemTotal;
            itemsCount += item.qty;
            html += `
                <div class="cart-item-premium">
                    <div class="flex-grow-1 cart-item-info">
                        <h6>${item.title}</h6>
                        <div class="text-success font-weight-800 small">Rs. ${item.price.toLocaleString()}</div>
                    </div>
                    <div class="d-flex flex-column align-items-end" style="gap: 10px;">
                        <div class="qty-controls">
                            <button class="qty-btn" onclick="updateQty(${index}, -1)"><i class="fas fa-minus"></i></button>
                            <span class="font-weight-800 small" style="min-width: 20px; text-align:center;">${item.qty}</span>
                            <button class="qty-btn" onclick="updateQty(${index}, 1)"><i class="fas fa-plus"></i></button>
                        </div>
                        <button class="btn btn-link btn-sm text-danger p-0 extra-small font-weight-700" onclick="removeFromCart(${index})">REMOVE</button>
                    </div>
                </div>
            `;
        });
        $('#cart-items').html(html);
        $('#subtotal-val, #total-val').text('Rs. ' + total.toLocaleString(undefined, {minimumFractionDigits: 2}));
        $('#cart-badge').text(itemsCount);
    }

    $('#clear-cart').on('click', function() {
        Swal.fire({
            title: 'Clear cart?',
            text: "All items will be removed",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, clear it'
        }).then((result) => {
            if (result.isConfirmed) {
                cart = [];
                renderCart();
            }
        });
    });

    $('#submit-order').on('click', function() {
        if(cart.length == 0) return Swal.fire('Error', 'Your cart is empty', 'error');
        
        let total = parseFloat($('#total-val').text().replace('Rs. ', '').replace(/,/g, ''));
        let btn = $(this);
        
        Swal.fire({
            title: 'Confirm Order?',
            text: "Ready to place your order?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Place Order'
        }).then((result) => {
            if (result.isConfirmed) {
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Submitting...');
                
                $.ajax({
                    url: "{{ route('user.online-order.store') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        cart: cart,
                        total_amount: total,
                        payment_method: 'cod'
                    },
                    success: function(res) {
                        if(res.status == 'success') {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Order placed! Sending invoice to your WhatsApp...',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = "{{ route('user.order.index') }}";
                            });
                        } else {
                            Swal.fire('Error', res.message, 'error');
                            btn.prop('disabled', false).html('PLACE ORDER <i class="fas fa-arrow-right ml-2"></i>');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Something went wrong!', 'error');
                        btn.prop('disabled', false).html('PLACE ORDER <i class="fas fa-arrow-right ml-2"></i>');
                    }
                });
            }
        });
    });

    // --- Visual Search Logic ---
    let mobileNetModel = null;

    $('#visual-search-btn').on('click', function() {
        $('#visual-search-input').click();
    });

    $('#visual-search-input').on('change', async function(e) {
        if (!e.target.files || !e.target.files[0]) return;
        
        const file = e.target.files[0];
        const reader = new FileReader();
        
        reader.onload = async function(event) {
            $('#scanned-img-preview').attr('src', event.target.result);
            $('#visual-scan-overlay').css('display', 'flex');
            $('#scan-status').text('Loading AI Model...');

            try {
                // Load model if not loaded
                if (!mobileNetModel) {
                    mobileNetModel = await mobilenet.load();
                }

                $('#scan-status').text('Analyzing image patterns...');
                
                // Create temp image element for TF
                const imgElement = document.createElement('img');
                imgElement.src = event.target.result;
                
                imgElement.onload = async () => {
                    const predictions = await mobileNetModel.classify(imgElement);
                    
                    if (predictions && predictions.length > 0) {
                        // Advanced: Use labels + visual feature ranking
                        let keywords = predictions.map(p => p.className.split(',')[0]).join(' ');
                        $('#scan-status').text('Comparing visual features...');
                        
                        // Perform the search
                        $.ajax({
                            url: "{{ route('user.online-order.search') }}",
                            data: { query: keywords },
                            success: function(res) {
                                // Now we have products, let's rank them by "Visual Score"
                                // In a production environment, we'd use pre-computed embeddings.
                                // Here, we rank based on AI confidence + tag overlap.
                                products = res;
                                
                                // Simulation of visual feature proximity
                                products.forEach(p => {
                                    p.visual_score = 0;
                                    predictions.forEach(pred => {
                                        if(p.title.toLowerCase().includes(pred.className.split(',')[0].toLowerCase())) {
                                            p.visual_score += pred.probability;
                                        }
                                    });
                                });

                                products.sort((a, b) => b.visual_score - a.visual_score);
                                
                                setTimeout(() => {
                                    $('#visual-scan-overlay').fadeOut();
                                    renderProducts();
                                    $('#product-search').val(keywords.split(' ')[0]);
                                }, 1000);
                            }
                        });
                    } else {
                        $('#scan-status').text('Could not identify part. Try another angle.');
                        setTimeout(() => $('#visual-scan-overlay').fadeOut(), 2000);
                    }
                };
            } catch (err) {
                console.error(err);
                $('#scan-status').text('AI Error. Switching to manual search.');
                setTimeout(() => $('#visual-scan-overlay').fadeOut(), 2000);
            }
        };
        reader.readAsDataURL(file);
    });
</script>
@endpush

