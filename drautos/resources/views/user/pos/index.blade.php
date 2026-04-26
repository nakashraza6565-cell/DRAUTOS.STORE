@extends('user.layouts.master')
@section('title','Online Order || ' . (Settings::first()->title ?? 'Auto Store'))
@section('main-content')
<div class="container-fluid p-0" style="height: calc(100vh - 100px); overflow: hidden;">
    <div class="row m-0 h-100">
        <!-- Left: Product Selection -->
        <div class="col-lg-8 col-md-7 p-3 h-100 d-flex flex-column" style="background: #f1f5f9;">
            <!-- Search & Filters -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body p-2">
                    <div class="input-group input-group-lg">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                        </div>
                        <input type="text" id="product-search" class="form-control border-0 shadow-none px-0" placeholder="Search Products (Name, SKU)..." autofocus>
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
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
        </div>

        <!-- Right: Checkout Sidebar -->
        <div class="col-lg-4 col-md-5 bg-white shadow-sm d-flex flex-column p-0 h-100">
            <!-- Customer Section (Static for Logged-in User) -->
            <div class="p-4 border-bottom bg-light">
                <h6 class="m-0 font-weight-bold text-gray-800">Customer Details</h6>
                <div class="mt-2">
                    <p class="mb-1 text-dark font-weight-bold">{{ auth()->user()->name }}</p>
                    <p class="mb-0 text-muted small"><i class="fas fa-phone mr-1"></i> {{ auth()->user()->phone ?? 'N/A' }}</p>
                    <p class="mb-0 text-muted small"><i class="fas fa-tag mr-1"></i> Type: {{ ucfirst(auth()->user()->customer_type ?? 'Retail') }}</p>
                </div>
            </div>

            <!-- Current Order List -->
            <div class="flex-grow-1 overflow-auto p-3 custom-scrollbar" id="cart-items">
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-shopping-basket fa-3x mb-3 opacity-2"></i>
                    <p>Your order is empty</p>
                </div>
            </div>

            <!-- Summary & Actions -->
            <div class="p-3 bg-dark text-white rounded-top-lg shadow-lg">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-gray-400">Subtotal</span>
                    <span class="font-weight-bold" id="subtotal-val">Rs. 0.00</span>
                </div>
                <hr style="border-top: 1px solid rgba(255,255,255,0.1);">
                <div class="d-flex justify-content-between mb-4">
                    <span class="h5 m-0">Total</span>
                    <span class="h4 m-0 text-success font-weight-bold" id="total-val">Rs. 0.00</span>
                </div>

                <div class="row no-gutters mb-3">
                    <div class="col-12">
                        <button class="btn btn-outline-danger btn-block py-2 mb-3" id="clear-cart">
                            <i class="fas fa-trash-alt mr-2"></i> Clear Cart
                        </button>
                    </div>
                </div>

                <button class="btn btn-success btn-lg btn-block py-3 font-weight-bold shadow" id="submit-order">
                    <i class="fas fa-check mr-2"></i> PLACE ORDER
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .product-card:hover { transform: translateY(-3px); transition: 0.2s; }
</style>

@endsection

@push('scripts')
<script>
    let cart = [];
    let products = [];
    const customerType = "{{ auth()->user()->customer_type ?? 'retail' }}";

    $(document).ready(function() {
        fetchProducts('');
    });

    $('#product-search').on('input', function() {
        fetchProducts($(this).val());
    });

    $('.filter-cat').on('click', function() {
        $('.filter-cat').removeClass('active btn-primary').addClass('btn-white');
        $(this).addClass('active btn-primary').removeClass('btn-white');
        renderProducts();
    });

    function fetchProducts(query) {
        $.ajax({
            url: "{{ route('user.online-order.search') }}",
            data: { query: query },
            success: function(res) {
                products = res;
                renderProducts();
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
        let catId = $('.filter-cat.active').data('id');
        let html = '';
        
        let filtered = products;
        if(catId !== 'all') {
            filtered = products.filter(p => p.cat_id == catId);
        }

        filtered.forEach(p => {
            let displayPrice = getPriceForCustomer(p);
            let itemTypeBadge = p.item_type == 'bundle' ? '<span class="badge badge-info small mr-1">Bundle</span>' : '';
            
            html += `
                <div class="col-xl-3 col-lg-4 col-md-6 col-6 mb-4">
                    <div class="card product-card h-100 border-0 shadow-sm cursor-pointer" onclick="addToCart(${p.id}, '${p.item_type}')">
                        <img src="${p.photo ? p.photo.split(',')[0] : '{{asset('backend/img/thumbnail-default.jpg')}}'}" class="card-img-top" style="height: 120px; object-fit: cover; border-radius: 12px 12px 0 0;">
                        <div class="card-body p-2">
                            <h6 class="font-weight-bold mb-1 text-truncate">${itemTypeBadge}${p.title}</h6>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-success font-weight-bold small">Rs. ${displayPrice.toFixed(2)}${p.unit ? ' / ' + p.unit : ''}</span>
                                <span class="badge ${p.stock > 0 ? 'badge-light' : 'badge-danger'} small">${p.stock} in stock</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        $('#products-grid').html(html || '<div class="col-12 text-center py-5">No products found</div>');
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
    }

    function removeFromCart(index) {
        cart.splice(index, 1);
        renderCart();
    }

    function updateQty(index, val) {
        cart[index].qty = Math.max(1, parseInt(val));
        renderCart();
    }

    function renderCart() {
        let html = '';
        let total = 0;
        
        if(cart.length == 0) {
            $('#cart-items').html('<div class="text-center py-5 text-muted"><i class="fas fa-shopping-basket fa-3x mb-3 opacity-2"></i><p>Your order is empty</p></div>');
            $('#subtotal-val, #total-val, .total-payable').text('Rs. 0.00');
            return;
        }

        cart.forEach((item, index) => {
            let itemTotal = item.price * item.qty;
            total += itemTotal;
            html += `
                <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                    <div class="flex-grow-1">
                        <h6 class="font-weight-bold m-0 small">${item.title} ${item.unit ? '<span class="text-muted">('+item.unit+')</span>' : ''}</h6>
                        <span class="text-muted small">Rs. ${item.price} x ${item.qty}</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <input type="number" class="form-control form-control-sm text-center mx-2" value="${item.qty}" style="width: 50px;" onchange="updateQty(${index}, this.value)">
                        <button class="btn btn-sm text-danger" onclick="removeFromCart(${index})"><i class="fas fa-times"></i></button>
                    </div>
                </div>
            `;
        });
        $('#cart-items').html(html);
        $('#subtotal-val, #total-val, .total-payable').text('Rs. ' + total.toFixed(2));
    }

    $('#clear-cart').on('click', function() {
        cart = [];
        renderCart();
    });

    $('#submit-order').on('click', function() {
        if(cart.length == 0) return Swal.fire('Error', 'Your cart is empty', 'error');
        
        let total = parseFloat($('#total-val').text().replace('Rs. ', ''));
        let btn = $(this);
        
        // Immediate visual feedback
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Submitting Order...');
        
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
                    // Success notification
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
                    btn.prop('disabled', false).html('<i class="fas fa-check mr-2"></i> PLACE ORDER');
                }
            },
            error: function() {
                Swal.fire('Error', 'Something went wrong!', 'error');
                btn.prop('disabled', false).html('<i class="fas fa-check mr-2"></i> PLACE ORDER');
            }
        });
    });
</script>
@endpush
