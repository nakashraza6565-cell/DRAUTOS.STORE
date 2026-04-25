@extends('backend.layouts.master')
@section('title','POS || Danyal Autos')
@section('main-content')
<div class="container-fluid p-0" style="height: calc(100vh - 100px); overflow: hidden;">
    <div class="row m-0 h-100">
        <!-- Left: Product Selection -->
        <div class="col-lg-9 col-md-8 p-3 h-100 d-flex flex-column" style="background: #f1f5f9;">
            <!-- Search & Filters -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body p-2">
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <div class="input-group input-group-lg">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                                </div>
                                <input type="text" id="product-search" class="form-control border-0 shadow-none px-0" placeholder="Scan Barcode or Search Products..." autofocus>
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="{{route('product.create')}}" target="_blank" class="btn btn-primary btn-lg shadow-sm rounded-pill px-4">
                                <i class="fas fa-plus-circle mr-2"></i> Add Product
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Categories Bar -->
            <div class="d-flex overflow-auto mb-3 pb-2 no-scrollbar" style="gap: 10px;">
                <button class="btn btn-white shadow-sm px-4 py-2 text-nowrap filter-cat active" data-id="all">All Items</button>
                @foreach($categories as $cat)
                    <button class="btn btn-white shadow-sm px-4 py-2 text-nowrap filter-cat" data-id="{{$cat->id}}">{{$cat->title}}</button>
                @endforeach
            </div>

            <!-- Products Grid -->
            <div id="products-grid" class="row overflow-auto flex-grow-1 pr-2 custom-scrollbar">
                <!-- Products will be loaded here via AJAX -->
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
        </div>

        <!-- Right: Checkout Sidebar -->
        <div class="col-lg-3 col-md-4 bg-white shadow-sm d-flex flex-column p-0 h-100">
            <!-- Customer Section -->
            <div class="p-3 border-bottom bg-light">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="m-0 font-weight-bold text-gray-800">Customer</h6>
                    <button class="btn btn-sm btn-outline-info rounded-circle" data-toggle="modal" data-target="#addCustomerModal"><i class="fas fa-plus"></i></button>
                </div>
                <select class="form-control select2 shadow-none" id="customer-select">
                    <option value="1" data-type="walkin">Walk-in Customer</option>
                    @foreach($customers as $customer)
                        <option value="{{$customer->id}}" data-type="{{$customer->customer_type}}">{{$customer->name}} ({{$customer->phone}})</option>
                    @endforeach
                </select>
            </div>

            <!-- Current Order List -->
            <div class="flex-grow-1 overflow-auto p-3 custom-scrollbar" id="cart-items">
                <!-- Cart items here -->
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-shopping-basket fa-3x mb-3 opacity-2"></i>
                    <p>Current order is empty</p>
                </div>
            </div>

            <!-- Summary & Actions -->
            <div class="p-3 bg-dark text-white rounded-top-lg shadow-lg" style="margin-top: auto;">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-gray-400">Items Selected</span>
                    <span class="font-weight-bold" id="items-count">0</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-gray-400">Subtotal</span>
                    <span class="font-weight-bold" id="subtotal-val">Rs. 0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-2 border-bottom border-secondary pb-2">
                    <span class="text-gray-400">Discount</span>
                    <span class="text-danger font-weight-bold" id="discount-val">Rs. 0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-3 mt-2">
                    <span class="h5 m-0 font-weight-bold">Total</span>
                    <span class="h4 m-0 text-success font-weight-bold" id="total-val">Rs. 0.00</span>
                </div>

                <div class="row no-gutters mb-3">
                    <div class="col-6 pr-1">
                        <button class="btn btn-outline-light btn-block py-3" id="park-order">
                            <i class="fas fa-pause mr-2"></i> Park
                        </button>
                    </div>
                    <div class="col-6 pl-1">
                        <button class="btn btn-outline-danger btn-block py-3" id="clear-cart">
                            <i class="fas fa-trash-alt mr-2"></i> Clear
                        </button>
                    </div>
                </div>

                <button class="btn btn-success btn-lg btn-block py-3 font-weight-bold shadow" data-toggle="modal" data-target="#paymentModal">
                    <i class="fas fa-money-bill-wave mr-2"></i> PROCESS CHECKOUT
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog">
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
                            <option value="walkin">Walk-in Customer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" name="city" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" class="form-control" rows="2"></textarea>
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
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog">
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
                            <hr>
                            <div class="form-group mb-0">
                                <label class="small font-weight-bold text-uppercase text-danger">Payment Due Date</label>
                                <input type="date" class="form-control form-control-sm border-0 shadow-none bg-white" id="payment-due-date" value="{{ date('Y-m-d', strtotime('+7 days')) }}">
                                <small class="text-muted" style="font-size: 10px;">For partial/credit payments</small>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side: Payment Methods -->
                    <div class="col-md-7 p-4 bg-white">
                        <label class="font-weight-bold text-uppercase small text-muted mb-3 d-block">Select Payment Method</label>
                        
                        <div class="row no-gutters mb-4" id="payment-methods-grid">
                            <div class="col-6 p-1">
                                <div class="payment-option p-3 border rounded text-center cursor-pointer position-relative transition-all" data-method="cash">
                                    <div class="check-mark"><i class="fas fa-check-circle text-success"></i></div>
                                    <i class="fas fa-money-bill-wave fa-lg text-success mb-2"></i>
                                    <div class="small font-weight-bold">CASH</div>
                                </div>
                            </div>
                            <div class="col-6 p-1">
                                <div class="payment-option p-3 border rounded text-center cursor-pointer position-relative transition-all" data-method="wallet">
                                    <div class="check-mark"><i class="fas fa-check-circle text-success"></i></div>
                                    <i class="fas fa-wallet fa-lg text-warning mb-2"></i>
                                    <div class="small font-weight-bold">WALLET TRANSFER</div>
                                </div>
                            </div>
                            <div class="col-6 p-1">
                                <div class="payment-option p-3 border rounded text-center cursor-pointer position-relative transition-all" data-method="bank">
                                    <div class="check-mark"><i class="fas fa-check-circle text-success"></i></div>
                                    <i class="fas fa-university fa-lg text-primary mb-2"></i>
                                    <div class="small font-weight-bold">BANK / CARD</div>
                                </div>
                            </div>
                            <div class="col-6 p-1">
                                <div class="payment-option p-3 border rounded text-center cursor-pointer position-relative transition-all" data-method="credit">
                                    <div class="check-mark"><i class="fas fa-check-circle text-success"></i></div>
                                    <i class="fas fa-user-clock fa-lg text-danger mb-2"></i>
                                    <div class="small font-weight-bold">CREDIT SALE</div>
                                </div>
                            </div>
                        </div>

                        <!-- Amount Received (Hidden until method selected) -->
                        <div id="amount-input-wrapper" style="display: none;" class="animated fadeIn">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold text-uppercase small text-success">Amount Received</label>
                                <div class="input-group input-group-lg border rounded overflow-hidden">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-0">Rs.</span>
                                    </div>
                                    <input type="number" class="form-control border-0 shadow-none font-weight-bold" id="amount-received" placeholder="0.00">
                                </div>
                                <div id="partial-info" class="mt-2 small text-warning font-weight-bold" style="display:none;">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Partial Payment
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4">
                <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success btn-lg px-5 shadow" id="complete-order">SAVE & PRINT INVOICE</button>
            </div>
        </div>
    </div>
</div>
<!-- Hidden Iframe for Printing -->
<iframe id="print-iframe" style="display:none;"></iframe>

<style>
    .cursor-pointer { cursor: pointer; }
    .payment-option { transition: 0.2s; border: 2px solid #edf2f7 !important; }
    .payment-option:hover { border-color: #4e73df !important; background: #f8f9fc; }
    .payment-option.active { border-color: #1cc88a !important; background: #f0fff4; }
    .payment-option .check-mark { position: absolute; top: 5px; right: 5px; opacity: 0; transform: scale(0.5); transition: 0.2s; }
    .payment-option.active .check-mark { opacity: 1; transform: scale(1); }
    
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .product-card:hover { transform: translateY(-3px); transition: 0.2s; }
    
    .animated { animation-duration: 0.3s; }
    .fadeIn { animation-name: fadeIn; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    
    /* Select2 POS Styles */
    .select2-container--default .select2-selection--single {
        height: 45px !important;
        border: 1px solid #d1d3e2 !important;
        border-radius: 5px !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container {
        width: 100% !important;
    }
</style>
@endsection

@push('styles')
<link rel="stylesheet" href="{{asset('frontend/js/select2/css/select2.min.css')}}">
<style>
    .select2-dropdown {
        border: 1px solid #d1d3e2 !important;
        box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15) !important;
    }
</style>
@endpush

@push('scripts')
<script src="{{asset('frontend/js/select2/js/select2.min.js')}}"></script>
<script>
    let cart = [];
    let products = [];

    // Initial Load
    $(document).ready(function() {
        $('#customer-select, #brand-filter, #model-filter').select2({
            placeholder: "Select",
            allowClear: false
        });
        fetchProducts();
    });

    $('#product-search').on('input', function() {
        fetchProducts();
    });



    $('.filter-cat').on('click', function() {
        $('.filter-cat').removeClass('active');
        $(this).addClass('active');
        fetchProducts();
    });

    function fetchProducts() {
        let query = $('#product-search').val();
        let cat_id = $('.filter-cat.active').data('id');


        $.ajax({
            url: "{{route('pos.search-products')}}",
            data: { 
                query: query,
                cat_id: cat_id,
            },
            success: function(res) {
                products = res;
                renderProducts();
            }
        });
    }

    function getPriceForCustomer(product) {
        let type = $('#customer-select').find(':selected').data('type') || 'retail'; // Default to retail or base price
        
        let price = parseFloat(product.price); // Default Selling Price
        
        if(type == 'wholesale' && product.wholesale_price) price = parseFloat(product.wholesale_price);
        else if(type == 'retail' && product.retail_price) price = parseFloat(product.retail_price);
        else if(type == 'walkin' && product.walkin_price) price = parseFloat(product.walkin_price);
        else if(type == 'salesman' && product.salesman_price) price = parseFloat(product.salesman_price);
        
        return price || 0;
    }

    function renderProducts() {
        let html = '';
        products.forEach(p => {
            let displayPrice = getPriceForCustomer(p);
            let itemTypeBadge = p.item_type == 'bundle' ? '<span class="badge badge-info small mr-1">Bundle</span>' : '';
            let brandName = p.brand ? p.brand.title : '';
            let modelName = p.model || '';
            
            html += `
                <div class="col-xl-3 col-lg-4 col-md-6 col-6 mb-3">
                    <div class="card product-card h-100 border-0 shadow-sm cursor-pointer" onclick="addToCart(${p.id}, '${p.item_type}')" style="border-radius: 12px; overflow: hidden;">
                        <img src="${p.photo ? p.photo.split(',')[0] : '{{asset('backend/img/thumbnail-default.jpg')}}'}" class="card-img-top" style="height: 100px; object-fit: cover;">
                        <div class="card-body p-2">
                            <h6 class="font-weight-bold mb-1 text-truncate" style="font-size: 13px;">${itemTypeBadge}${p.title}</h6>
                            <div class="small text-muted mb-1 text-truncate">
                                ${brandName ? '<span class="mr-1"><i class="fas fa-tag fa-xs"></i> '+brandName+'</span>' : ''}
                                ${modelName ? '<span><i class="fas fa-car fa-xs"></i> '+modelName+'</span>' : ''}
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-success font-weight-bold" style="font-size: 14px;">Rs. ${displayPrice.toFixed(2)}</span>
                                <span class="badge ${p.stock > 0 ? 'badge-light' : 'badge-danger'} px-1" style="font-size: 10px;">${p.stock} In</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        $('#products-grid').html(html || '<div class="col-12 text-center py-5">No products found</div>');
    }

    function addToCart(pid, type) {
        // Logic to handle bundles and products with same ID? 
        // IDs are from different tables, so they might collide!
        // PID alone is not enough if we mix products and bundles. 
        // AdminController searchProducts returns results from 2 tables.
        // It's possible Product ID 1 exists and Bundle ID 1 exists.
        // We passed 'type' ('product' or 'bundle') to onclick.
        
        // However, the `products` array contains mixed items. find() will return first match.
        // We really should use a unique ID logic, or filter by type too.
        // Let's rely on type.
        
        let product = products.find(p => p.id == pid && p.item_type == type);
        if(!product) return;
        
        // Use unique ID for cart to avoid collision? 
        // Cart Logic currently uses `id`. 
        // We need to differentiate in Cart too.
        // Or we can prefix ID? 'p-1', 'b-1'.
        
        let cartId = type + '-' + pid; 
        
        let item = cart.find(i => i.unique_id == cartId);
        if(item) {
            item.qty++;
        } else {
            let finalPrice = getPriceForCustomer(product);
            
            let cartItem = {
                unique_id: cartId,
                id: product.id,
                type: type,
                title: product.title,
                brand: product.brand ? product.brand.title : '',
                model: product.model || '',
                base_price: finalPrice, // Store the pricing strategy price
                original_price: finalPrice,
                price: finalPrice,
                qty: 1,
                unit: product.unit,
                last_purchase: null
            };
            cart.push(cartItem);
            
            // Async fetch last purchase history
            fetchLastPurchase(cartItem);
        }
        renderCart();
    }

    function fetchLastPurchase(cartItem) {
        let customer_id = $('#customer-select').val();
        if(!customer_id || customer_id == 1) return; // Skip walk-in
        
        $.ajax({
            url: "{{route('pos.last-purchase')}}",
            data: {
                customer_id: customer_id,
                item_type: cartItem.type,
                item_id: cartItem.id
            },
            success: function(res) {
                if(res.found) {
                    cartItem.last_purchase = `Last bought: ${res.date} | Qty: ${res.quantity} | Price: Rs.${res.price}`;
                    renderCart();
                    
                    Swal.fire({
                        title: 'Purchase History Found!',
                        html: `<b>Customer previously bought this item.</b><br><br>
                               Date: <b>${res.date}</b><br>
                               Quantity: <b>${res.quantity}</b><br>
                               Price Paid: <b style="color: green;">Rs. ${res.price}</b>`,
                        icon: 'info',
                        position: 'top',
                        toast: true,
                        showConfirmButton: false,
                        timer: 5000
                    });
                }
            }
        });
    }

    function removeFromCart(index) {
        cart.splice(index, 1);
        renderCart();
    }

    window.updatePrice = function(index, val) {
        let p = parseFloat(val);
        p = isNaN(p) ? 0 : p;
        cart[index].price = p;
        
        // If the new price is higher than the customer's pricing strategy base price,
        // we override the original_price so there is no negative discount.
        // If it's lower, we keep the base_price as original so it reflects as a discount.
        if (p > cart[index].base_price) {
            cart[index].original_price = p;
        } else {
            cart[index].original_price = cart[index].base_price;
        }
        
        renderCart();
    };

    function updateQty(index, val) {
        cart[index].qty = Math.max(1, parseInt(val));
        renderCart();
    }

    function renderCart() {
        let html = '';
        let subtotal = 0;
        let totalDiscount = 0;
        
        if(cart.length == 0) {
            $('#cart-items').html('<div class="text-center py-5 text-muted"><i class="fas fa-shopping-basket fa-3x mb-3 opacity-2"></i><p>Current order is empty</p></div>');
            updateSummary(0, 0, 0);
            return;
        }

        cart.forEach((item, index) => {
            let lineOriginalTotal = item.original_price * item.qty;
            let lineActualTotal = item.price * item.qty;
            subtotal += lineOriginalTotal;
            totalDiscount += (lineOriginalTotal - lineActualTotal);


            html += `
                <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                    <div class="flex-grow-1 pr-2">
                        <h6 class="font-weight-bold m-0" style="font-size: 12px; line-height: 1.2;">${item.title}</h6>
                        <div class="small text-muted" style="font-size: 10px;">
                            ${item.brand ? item.brand : ''} ${item.model ? '| ' + item.model : ''}
                        </div>
                        <span class="text-muted" style="font-size: 10px;">MSRP: Rs. ${item.original_price}</span>
                        ${item.last_purchase ? `<div class="mt-1 text-info font-weight-bold" style="font-size: 9px;"><i class="fas fa-history mr-1"></i>${item.last_purchase}</div>` : ''}
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="mr-1 text-center">
                            <span class="d-block text-muted" style="font-size: 9px; line-height:1;">Price</span>
                            <input type="number" step="0.01" class="form-control form-control-sm text-center p-0" value="${item.price}" style="width: 55px; height: 24px; font-size: 11px;" onchange="updatePrice(${index}, this.value)">
                        </div>
                        <div class="mr-1 text-center">
                            <span class="d-block text-muted" style="font-size: 9px; line-height:1;">Qty</span>
                            <input type="number" class="form-control form-control-sm text-center p-0" value="${item.qty}" style="width: 35px; height: 24px; font-size: 11px;" onchange="updateQty(${index}, this.value)">
                        </div>
                        <div class="mr-1 text-right" style="min-width: 60px;">
                            <span class="d-block text-muted" style="font-size: 9px; line-height:1;">Total</span>
                            <span class="font-weight-bold" style="font-size: 11px;">Rs.${lineActualTotal.toFixed(0)}</span>
                        </div>
                        <button class="btn btn-sm text-danger p-0" onclick="removeFromCart(${index})"><i class="fas fa-times fa-xs"></i></button>
                    </div>
                </div>
            `;
        });
        $('#cart-items').html(html);
        updateSummary(subtotal, totalDiscount, cart.length);
    }

    function updateSummary(subtotal, discount, count) {
        let total = subtotal - discount;
        $('#items-count').text(count);
        $('#modal-items-count').text(count); // NEW
        $('#subtotal-val').text('Rs. ' + subtotal.toFixed(2));
        $('#discount-val').text('Rs. ' + discount.toFixed(2));
        $('#total-val').text('Rs. ' + total.toFixed(2));
        $('.total-payable').text('Rs. ' + total.toFixed(2));
    }

    $('.payment-option').on('click', function() {
        $('.payment-option').removeClass('active');
        $(this).addClass('active');
        
        let method = $(this).data('method');
        if (method === 'credit') {
            $('#amount-received').val(0).trigger('input');
        } else if ($('#amount-received').val() == 0) {
            $('#amount-received').val('').trigger('input');
        }

        // Show amount received input with animation
        $('#amount-input-wrapper').show();
        $('#amount-received').focus();
    });

    $('#amount-received').on('input', function() {
        let total = parseFloat($('#total-val').text().replace('Rs. ', ''));
        let received = parseFloat($(this).val()) || 0;
        
        if (received > 0 && received < total) {
            $('#partial-info').show();
        } else {
            $('#partial-info').hide();
        }
    });

    $('#save-customer-btn').on('click', function() {
        let form = $('#add-customer-form');
        $.ajax({
            url: "{{route('users.store')}}", 
            type: "POST",
            data: form.serialize() + "&role=user&status=active&password=password123", 
            success: function(response) {
                // Add new option with data-type
                let newOption = new Option(response.name + ' (' + response.phone + ')', response.id, true, true);
                $(newOption).data('type', response.customer_type); // Set data attribute
                $('#customer-select').append(newOption).trigger('change');
                $('#addCustomerModal').modal('hide');
                form[0].reset();
                Swal.fire('Success', 'Customer Added', 'success');
            },
            error: function(err) {
               console.log(err);
               let errorMsg = 'Failed to add customer';
               if(err.status === 422) {
                   let errors = err.responseJSON.errors;
                   errorMsg = Object.values(errors).flat().join('\n');
               }
               Swal.fire('Error', errorMsg, 'error');
            }
        });
    });

    $('#complete-order').on('click', function() {
        if(cart.length == 0) {
            Swal.fire('Error', 'Cart is empty!', 'error');
            return;
        }

        let customer_id = $('#customer-select').val();
        let total_amount = parseFloat($('#total-val').text().replace('Rs. ', ''));
        let payment_method = $('.payment-option.active').data('method');
        
        if(!payment_method) {
            Swal.fire('Error', 'Please select a payment method!', 'warning');
            return;
        }
        let amount_received_raw = $('#amount-received').val();
        let amount_received = (amount_received_raw === "" || amount_received_raw === null) ? 0 : parseFloat(amount_received_raw);
        if (isNaN(amount_received)) amount_received = 0;
        let due_date = $('#payment-due-date').val();

        // Prepare data
        let payload = {
            customer_id: customer_id,
            total_amount: total_amount,
            payment_method: payment_method,
            payment_status: (amount_received >= total_amount) ? 'paid' : 'partial',
            amount_paid: amount_received,
            due_date: due_date,
            cart: cart,
            _token: "{{csrf_token()}}"
        };

        $(this).prop('disabled', true).text('Processing...');

        $.ajax({
            url: "{{route('pos.store-order')}}",
            type: "POST",
            data: payload,
            success: function(response) {
                    if(response.status == 'success') {
                        // Handle Printing via hidden iframe
                        if(response.thermal_url) {
                            $('#print-iframe').attr('src', response.thermal_url);
                        }

                        if(response.wa_sent) {

                        Swal.fire({
                            title: 'Success!',
                            text: 'Order saved and Receipt Printed.',
                            icon: 'success',
                            timer: 2000
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Order Saved',
                            text: 'Order created and Receipt Sent to Printer, but WhatsApp could not be sent.',
                            icon: 'warning'
                        }).then(() => {
                            location.reload();
                        });
                    }
                } else {
                    Swal.fire('Error', response.message, 'error');
                    $('#complete-order').prop('disabled', false).text('SAVE & PRINT INVOICE');
                }
            },
            error: function(err) {
                console.log(err);
                if(err.status === 422) {
                     let errors = err.responseJSON.errors;
                     let msg = '';
                     $.each(errors, function(key, value) {
                         msg += value[0] + '\n';
                     });
                     alert('Validation Error:\n' + msg);
                } else {
                    alert('Something went wrong! Check console.');
                }
                $('#complete-order').prop('disabled', false).text('SAVE & PRINT INVOICE');
            }
        });
    });
</script>
@endpush
