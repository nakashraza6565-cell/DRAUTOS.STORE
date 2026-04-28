@extends('backend.layouts.master')
@section('title','POS || Danyal Autos')
@section('main-content')
<div style="position: relative; z-index: 999999;">
    @include('backend.layouts.notification')
</div>
<div class="container-fluid p-0" style="height: calc(100vh - 100px); overflow: hidden;">
    <div class="row m-0 h-100">
        <!-- Left: Product Selection -->
        <!-- Left: Product Selection -->
        <div class="col-12 p-3 h-100 d-flex flex-column pos-main-container" style="background: #f4f7f6;">
            <!-- Aura-Spotlight Header -->
            <div class="d-flex align-items-center mb-4 mt-1" style="gap: 15px;">
                <div class="flex-grow-1">
                    <div class="search-wrapper-sleek d-flex align-items-center">
                        <i class="fas fa-search search-icon-sleek mr-2"></i>
                        <input type="text" id="product-search" class="form-control border-0 shadow-none p-0" placeholder="Search products, SKU or scan barcode..." style="height: 38px; font-size: 15px;" autofocus autocomplete="off">
                        <!-- Google Style Suggestions -->
                        <div id="search-suggestions" class="position-absolute shadow-lg bg-white w-100 rounded-bottom d-none" style="top: 48px; left:0; z-index: 2000; border: 1px solid #e2e8f0; border-radius: 16px !important; overflow: hidden;">
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center" style="gap: 10px;">
                    <button type="button" data-toggle="modal" data-target="#addProductModal" class="btn btn-white btn-sm px-4 shadow-sm border" style="border-radius: 100px; font-weight: 700; color: #475569; height: 40px; display: flex; align-items: center;">
                        <i class="fas fa-plus-circle mr-2 text-primary"></i> NEW ITEM
                    </button>
                    <button class="btn btn-primary btn-sm px-4 shadow-primary position-relative" id="toggle-cart" style="border-radius: 100px; font-weight: 700; height: 40px; background-color: var(--primary) !important; border-color: var(--primary) !important; color: #fff !important;">
                        <i class="fas fa-shopping-bag mr-2"></i> VIEW CART
                        <span class="badge badge-danger position-absolute" id="cart-badge" style="top: -5px; right: 10px; font-size: 10px; border: 2px solid #fff;">0</span>
                    </button>
                </div>
            </div>

            <!-- Product Catalog Grid -->
            <div id="products-grid" class="row overflow-auto flex-grow-1 pr-2 custom-scrollbar">
                <!-- Products will be loaded here via AJAX -->
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
        </div>

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
                    <option value="1" data-type="walkin">Walk-in Customer</option>
                    @foreach($customers as $customer)
                    <option value="{{$customer->id}}" data-type="{{$customer->customer_type}}" data-balance="{{$customer->current_balance ?? 0}}">
                        {{$customer->name}} ({{$customer->phone}}) | Bal: Rs. {{number_format($customer->current_balance ?? 0, 2)}}
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
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>City</label>
                        <select name="city" id="customer-city-select" class="form-control" style="width: 100%;">
                            <option value="">Select or Type City</option>
                            @foreach($cities as $city)
                            <option value="{{$city}}">{{$city}}</option>
                            @endforeach
                        </select>
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
<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold">Add Quick Product</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body bg-light">
                <form id="add-product-form">
                    @csrf
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="small font-weight-bold">Product Title (Search to avoid duplicates) <span class="text-danger">*</span></label>
                                <select name="title" id="pos-title-select" class="form-control" required>
                                    <option value="">Search or Enter Product Name</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">Category <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <select name="cat_id" id="pos-cat-select" class="form-control" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $cat)
                                        <option value="{{$cat->id}}">{{$cat->title}}</option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addCategoryModal"><i class="fas fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Brand</label>
                                <div class="input-group">
                                    <select name="brand_id" id="pos-brand-select" class="form-control">
                                        <option value="">Select Brand</option>
                                        @foreach($brands as $brand)
                                        <option value="{{$brand->id}}">{{$brand->title}}</option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#addBrandModal"><i class="fas fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Model</label>
                                <div class="input-group">
                                    <select name="model" id="pos-model-select" class="form-control">
                                        <option value="">Select Model</option>
                                        @foreach($product_models as $m)
                                        <option value="{{$m->name}}">{{$m->name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#addModelModal"><i class="fas fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Unit / Packaging</label>
                                <div class="input-group">
                                    <select name="unit" id="pos-unit-select" class="form-control">
                                        <option value="piece">Piece</option>
                                        @foreach($units as $u)
                                        <option value="{{$u->name}}">{{$u->name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addUnitModal"><i class="fas fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Initial Stock <span class="text-danger">*</span></label>
                                <input type="number" name="stock" class="form-control" required value="0">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">Purchase Price</label>
                                <input type="number" name="purchase_price" class="form-control" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">Selling Price <span class="text-danger">*</span></label>
                                <input type="number" name="price" class="form-control" required placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">Primary Supplier</label>
                                <div class="input-group">
                                    <select name="suppliers[]" id="pos-supplier-select" class="form-control" multiple>
                                        @foreach($suppliers as $supplier)
                                        <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#addSupplierModal"><i class="fas fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary px-4 shadow" id="save-product-btn">
                    <i class="fas fa-save mr-1"></i> SAVE PRODUCT
                </button>
            </div>
        </div>
    </div>
</div>

@include('backend.product.partials.modals')

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
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Ledger Balance</span>
                                <span class="font-weight-bold text-info" id="modal-ledger-balance">Rs. 0.00</span>
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
    .pos-sidebar {
        position: fixed;
        top: 0;
        right: -400px;
        width: 400px;
        z-index: 20000 !important;
        transition: right 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100vh !important;
    }

    .pos-sidebar.active {
        right: 0;
    }

    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        z-index: 19999 !important;
        display: none;
    }

    .sidebar-overlay.active {
        display: block;
    }

    @media (max-width: 576px) {
        .pos-sidebar {
            width: 100%;
            right: -100%;
        }
    }

    .cursor-pointer {
        cursor: pointer;
    }

    .payment-option {
        transition: 0.2s;
        border: 2px solid #edf2f7 !important;
        border-radius: 12px !important;
    }

    .payment-option:hover {
        border-color: #4e73df !important;
        background: #f8f9fc;
        transform: translateY(-2px);
    }

    .payment-option.active {
        border-color: #4e73df !important;
        background: #f0f4ff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .payment-option .check-mark {
        position: absolute;
        top: 10px;
        right: 10px;
        opacity: 0;
        transform: scale(0.5);
        transition: 0.2s;
    }

    .payment-option.active .check-mark {
        opacity: 1;
        transform: scale(1);
    }

    .bg-soft-primary {
        background: #e0e7ff;
    }

    .bg-soft-danger {
        background: #fee2e2;
    }

    .text-primary {
        color: #4e73df !important;
    }

    .opacity-5 {
        opacity: 0.5;
    }

    /* Modern Industrial Design */
    body {
        background-color: #f4f7f6;
        font-family: 'Segoe UI', Roboto, sans-serif;
    }

    .pos-main-container {
        background: #f4f7f6;
    }

    /* Gallery Elite Grid Design */
    .product-grid-card {
        position: relative;
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        background: #000;
        aspect-ratio: 1/1;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .product-grid-card:hover {
        transform: scale(1.04) translateY(-8px);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4) !important;
        z-index: 10;
    }

    .product-grid-card .glass-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        background: rgba(0, 0, 0, 0.4);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        padding: 10px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        z-index: 3;
    }

    .product-grid-card .price-tag-elite {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #4e73df;
        color: #fff;
        padding: 2px 10px;
        border-radius: 8px;
        font-weight: 800;
        font-size: 11px;
        box-shadow: 0 4px 12px rgba(78, 115, 223, 0.4);
        z-index: 4;
    }

    .product-grid-card .stock-tag-elite {
        position: absolute;
        top: 10px;
        left: 10px;
        background: rgba(255, 255, 255, 0.9);
        color: #000;
        padding: 1px 8px;
        border-radius: 6px;
        font-size: 9px;
        font-weight: 700;
        z-index: 4;
    }

    .product-grid-card .thumbnail-elite {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s ease;
        opacity: 0.9;
    }

    .product-grid-card:hover .thumbnail-elite {
        transform: scale(1.15);
        opacity: 1;
    }

    /* Ideal-Density Grid */
    .col-xl-8-grid {
        flex: 0 0 12.5%;
        max-width: 12.5%;
    }

    @media (max-width: 1600px) {
        .col-xl-8-grid {
            flex: 0 0 16.66%;
            max-width: 16.66%;
        }
    }

    @media (max-width: 1200px) {
        .col-xl-8-grid {
            flex: 0 0 25%;
            max-width: 25%;
        }
    }

    @media (max-width: 768px) {
        .col-xl-8-grid {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }

    .elite-title {
        color: #fff;
        font-weight: 800;
        font-size: 13px;
        line-height: 1.2;
        margin-bottom: 2px;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
    }

    .elite-meta {
        color: rgba(255, 255, 255, 0.8);
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
        line-height: 1;
    }

    .price-tag-elite {
        position: absolute;
        top: 6px;
        right: 6px;
        background: #4e73df;
        color: #fff;
        padding: 2px 8px;
        border-radius: 8px;
        font-weight: 800;
        font-size: 11px;
        z-index: 4;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    }

    .stock-tag-elite {
        position: absolute;
        top: 6px;
        left: 6px;
        background: rgba(255, 255, 255, 0.95);
        color: #000;
        padding: 1px 7px;
        border-radius: 5px;
        font-size: 9px;
        font-weight: 800;
        z-index: 4;
    }

    .product-grid-card .glass-overlay {
        padding: 10px 12px;
        background: rgba(15, 23, 42, 0.85);
        /* Slightly more opaque for larger tiles */
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .product-grid-card {
        border-radius: 14px;
        overflow: hidden;
    }

    .filter-cat {
        transition: 0.2s;
        border-bottom: 2px solid transparent !important;
    }

    .filter-cat.active {
        color: #4e73df !important;
        border-bottom: 2px solid #4e73df !important;
    }

    .suggestion-item {
        padding: 8px 15px;
        cursor: pointer;
        transition: 0.2s;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
    }

    .suggestion-item:hover,
    .suggestion-item.active {
        background: #f0f7ff;
        color: #4e73df;
        padding-left: 22px;
        border-left: 3px solid #4e73df;
    }

    .suggestion-item:last-child {
        border-bottom: none;
    }

    .suggestion-item .match-highlight {
        font-weight: 800;
        color: #1a202c;
    }

    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }

    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
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

    .animated-pulse {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(78, 115, 223, 0.4);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(78, 115, 223, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(78, 115, 223, 0);
        }
    }


    .pos-sidebar {
        position: fixed !important;
        right: 0;
        top: 0;
        height: 100vh;
        width: 400px;
        z-index: 1040;
        transform: translateX(100%);
        transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1) !important;
        box-shadow: -15px 0 30px rgba(0, 0, 0, 0.3);
    }

    .pos-sidebar.active {
        transform: translateX(0);
    }

    /* Aura-Spotlight Search Bar */
    .search-wrapper-sleek {
        position: relative;
        z-index: 1000;
        background: #fff;
        border-radius: 100px;
        border: 1px solid #e2e8f0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        padding: 4px 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .search-wrapper-sleek:focus-within {
        border-color: var(--accent);
        box-shadow: 0 10px 25px -5px rgba(245, 158, 11, 0.2);
        transform: translateY(-1px);
    }

    #product-search {
        background: transparent;
        font-weight: 500;
        letter-spacing: -0.2px;
        color: #1e293b;
    }

    #product-search::placeholder {
        color: #94a3b8;
        font-weight: 400;
    }

    .search-icon-sleek {
        color: #94a3b8;
        transition: 0.3s;
    }

    .search-wrapper-sleek:focus-within .search-icon-sleek {
        color: var(--accent);
        transform: scale(1.1);
    }
</style>
@endsection

@push('styles')
<link rel="stylesheet" href="{{asset('frontend/js/select2/css/select2.min.css')}}">
<style>
    .select2-dropdown {
        border: 1px solid #d1d3e2 !important;
        box-shadow: 0 .15rem 1.75rem 0 rgba(58, 59, 69, .15) !important;
        z-index: 100001 !important;
    }

    .select2-container {
        z-index: 100001 !important;
    }

    .swal2-container {
        z-index: 100002 !important;
    }

    /* Sleek Modal for POS */
    #addProductModal .modal-content {
        border-radius: 12px;
        overflow: hidden;
        border: none;
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
    }

    #addProductModal .modal-header {
        background: #4e73df;
        padding: 1rem 1.5rem;
    }

    #addProductModal .modal-body {
        padding: 1.5rem;
    }

    #addProductModal .form-group label {
        font-size: 0.75rem;
        color: #4e73df;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    #addProductModal .input-group-append .btn {
        padding: 0.2rem 0.5rem;
        font-size: 0.75rem;
        border-radius: 0 0.35rem 0.35rem 0 !important;
    }

    #addProductModal .input-group>.form-control {
        border-radius: 0.35rem 0 0 0.35rem !important;
        height: 35px !important;
        font-size: 0.85rem;
    }
</style>
@endpush

@push('scripts')
<script src="{{asset('frontend/js/select2/js/select2.min.js')}}"></script>
<script>
    let cart = [];
    let products = [];

    // Sales Order Integration
    window.salesOrderId = @json(session('pos_payload.sales_order_id') ?? null);
    const soPayload = @json(session('pos_payload.items') ?? null);
    const soCustomerId = @json(session('pos_payload.customer_id') ?? null);

    $(document).ready(function() {
        // Clear payload from session so it doesn't reappear on reload
        @if(session('pos_payload'))
        @php session()->forget('pos_payload'); @endphp
        @endif

        // Auto-load items if coming from Sales Order
        if (soPayload && soPayload.length > 0) {
            soPayload.forEach(item => {
                let cartId = 'product-' + item.id;
                let cartItem = {
                    unique_id: cartId,
                    id: item.id,
                    type: 'product',
                    title: item.title,
                    brand: item.brand || '',
                    model: item.model || '',
                    base_price: parseFloat(item.price),
                    original_price: parseFloat(item.price),
                    price: parseFloat(item.price),
                    qty: parseFloat(item.qty),
                    unit: item.unit || 'piece',
                    last_purchase: null,
                    so_item_id: item.so_item_id
                };

                // Add to cart if not already there (though for SO we usually just push)
                cart.push(cartItem);
            });

            renderCart();

            if (soCustomerId) {
                $('#customer-select').val(soCustomerId).trigger('change');
            }

            // Open the sidebar automatically to show the loaded items
            $('#checkout-sidebar').addClass('active');
            $('#pos-overlay').addClass('active');
        }


        // Toggle Cart (Sticky Mode)
        $('#toggle-cart').on('click', function() {
            $('#checkout-sidebar').toggleClass('active');
            $('#pos-overlay').toggleClass('active');
        });

        // Close logic
        $('#pos-overlay, #close-sidebar').on('click', function() {
            $('#checkout-sidebar').removeClass('active');
            $('#pos-overlay').removeClass('active');
        });

        // Create Sidebar Overlay if not exists
        if ($('.sidebar-overlay').length == 0) {
            $('body').append('<div class="sidebar-overlay" id="pos-overlay"></div>');
        }

        // Select2 for Main POS Filters
        $('#brand-filter, #model-filter').select2({
            placeholder: "Select",
            allowClear: true,
            tags: true
        });

        // Initialize Select2 for Add Product Modal when it's shown
        $('#addProductModal').on('shown.bs.modal', function() {
            // Move modals to body to avoid z-index issues
            $('#addCategoryModal, #addBrandModal, #addSupplierModal, #addUnitModal, #addModelModal').appendTo('body');

            $('#pos-model-select, #pos-cat-select, #pos-brand-select, #pos-unit-select').select2({
                placeholder: "Select or Type",
                allowClear: true,
                tags: true,
                width: '100%',
                dropdownParent: $('#addProductModal')
            });

            $('#pos-supplier-select').select2({
                placeholder: "Select Supplier(s)",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#addProductModal')
            });

            $('#pos-title-select').select2({
                placeholder: "Search or Enter Product Name",
                allowClear: true,
                tags: true,
                width: '100%',
                dropdownParent: $('#addProductModal'),
                minimumInputLength: 2,
                ajax: {
                    url: "{{route('admin.product.search-simple')}}", // Need to create this
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                }
            });

            // If an existing product is selected, warn the user
            $('#pos-title-select').on('select2:select', function(e) {
                var data = e.params.data;
                if (data.is_existing) {
                    Swal.fire({
                        title: 'Product Exists!',
                        text: '"' + data.text + '" is already in your inventory. Are you sure you want to add it again?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, add as new',
                        cancelButtonText: 'No, cancel'
                    }).then((result) => {
                        if (!result.isConfirmed) {
                            $('#pos-title-select').val(null).trigger('change');
                        }
                    });
                }
            });
        });

        // Sub-modal AJAX handlers for POS
        $(document).on('submit', '#quickAddCategoryForm', function(e) {
            e.preventDefault();
            $.post("{{route('category.quick-store')}}", $(this).serialize() + "&_token={{csrf_token()}}&is_parent=1", function(res) {
                if (res.status == 'success') {
                    $('#pos-cat-select').append(new Option(res.category.title, res.category.id, false, true)).trigger('change');
                    $('#addCategoryModal').modal('hide');
                }
            });
        });

        $(document).on('submit', '#quickAddSupplierForm', function(e) {
            e.preventDefault();
            $.post("{{route('supplier.quick-store')}}", $(this).serialize() + "&_token={{csrf_token()}}", function(res) {
                if (res.status == 'success') {
                    $('#pos-supplier-select').append(new Option(res.supplier.name + ' (' + (res.supplier.company_name || '') + ')', res.supplier.id, false, true)).trigger('change');
                    $('#addSupplierModal').modal('hide');
                }
            });
        });

        $(document).on('submit', '#quickAddBrandForm', function(e) {
            e.preventDefault();
            $.post("{{route('brand.quick-store')}}", $(this).serialize() + "&_token={{csrf_token()}}", function(res) {
                if (res.status == 'success') {
                    $('#pos-brand-select').append(new Option(res.brand.title, res.brand.id, false, true)).trigger('change');
                    $('#addBrandModal').modal('hide');
                }
            });
        });

        $(document).on('submit', '#quickAddUnitForm', function(e) {
            e.preventDefault();
            $.post("{{route('product.store-unit')}}", $(this).serialize() + "&_token={{csrf_token()}}", function(res) {
                if (res.status == 'success') {
                    $('#pos-unit-select').append(new Option(res.unit.name, res.unit.name, false, true)).trigger('change');
                    $('#addUnitModal').modal('hide');
                }
            });
        });

        $(document).on('submit', '#quickAddModelForm', function(e) {
            e.preventDefault();
            $.post("{{route('product.store-model')}}", $(this).serialize() + "&_token={{csrf_token()}}", function(res) {
                if (res.status == 'success') {
                    $('#pos-model-select').append(new Option(res.model.name, res.model.name, false, true)).trigger('change');
                    $('#addModelModal').modal('hide');
                }
            });
        });

        // Initialize Select2 for Add Customer Modal when it's shown
        $('#addCustomerModal').on('shown.bs.modal', function() {
            $('#customer-city-select').select2({
                placeholder: "Select City",
                allowClear: true,
                tags: true,
                width: '100%',
                dropdownParent: $('#addCustomerModal')
            });
        });

        $('#customer-select').select2({
            placeholder: "Select Customer",
            allowClear: false,
            dropdownParent: $('body') // Ensure it's appended to body for correct z-index handling
        });

        fetchProducts();

        // Customer Change Logic
        $('#customer-select').on('change', function() {
            let customer_id = $(this).val();
            let balance = parseFloat($(this).find(':selected').data('balance')) || 0;
            $('#modal-ledger-balance').text('Rs. ' + balance.toFixed(2));

            // Re-render products if pricing depends on customer type
            renderProducts();

            // Update cart items if customer changes (prices might change)
            if (cart.length > 0) {
                cart.forEach(item => {
                    let product = products.find(p => p.id == item.id && p.item_type == item.type);
                    if (product) {
                        let newPrice = getPriceForCustomer(product);
                        item.base_price = newPrice;
                        item.original_price = newPrice;
                        item.price = newPrice;
                    }
                    if (customer_id == 1) {
                        item.last_purchase = null;
                    } else {
                        fetchLastPurchase(item);
                    }
                });
                renderCart();
            }
        });

        // Park Order - Open New POS in another tab
        $('#park-order').on('click', function() {
            window.open("{{route('admin.pos')}}", '_blank');
        });

        // Clear Cart
        $('#clear-cart').on('click', function() {
            if (cart.length == 0) return;
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
                    cart = [];
                    renderCart();
                }
            });
        });
    });

    let searchTimer;
    let currentFocus = -1;

    $('#product-search').on('keydown', function(e) {
        let x = $('#search-suggestions div.suggestion-item');
        if (e.keyCode == 40) { // Down
            currentFocus++;
            addActive(x);
        } else if (e.keyCode == 38) { // Up
            currentFocus--;
            addActive(x);
        } else if (e.keyCode == 13) { // Enter
            e.preventDefault();
            if (currentFocus > -1) {
                if (x) x[currentFocus].click();
            }
        }
    });

    function addActive(x) {
        if (!x) return false;
        removeActive(x);
        if (currentFocus >= x.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = (x.length - 1);
        $(x[currentFocus]).addClass("active");
        // Scroll into view if needed
        x[currentFocus].scrollIntoView({
            block: 'nearest'
        });
    }

    function removeActive(x) {
        for (let i = 0; i < x.length; i++) {
            $(x[i]).removeClass("active");
        }
    }

    $('#product-search').on('input', function() {
        let val = $(this).val();
        clearTimeout(searchTimer);
        currentFocus = -1; // Reset focus on input

        if (val.length > 0) {
            searchTimer = setTimeout(() => {
                fetchProducts(val, true);
            }, 150);
        } else {
            $('#search-suggestions').addClass('d-none');
            fetchProducts('', false);
        }
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.input-group').length) {
            $('#search-suggestions').addClass('d-none');
        }
    });

    function showSuggestions(query, matches) {
        if (matches.length > 0) {
            let html = '';
            matches.slice(0, 10).forEach(m => {
                let title = m.title;
                // Escape query for regex
                let escapedQuery = query.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                let regex = new RegExp('(' + escapedQuery + ')', 'gi');
                let highlightedTitle = title.replace(regex, '<span class="match-highlight">$1</span>');

                html += `
                    <div class="suggestion-item d-flex align-items-center" onclick="selectSuggestion('${m.id}', '${m.item_type}')">
                        <i class="fas fa-search mr-3 text-muted" style="font-size: 11px; opacity: 0.5;"></i>
                        <div class="flex-grow-1">${highlightedTitle}</div>
                        <div class="text-primary x-small font-weight-bold" style="font-size: 10px; opacity: 0.7;">${m.sku || 'VIEW'}</div>
                    </div>
                `;
            });
            $('#search-suggestions').html(html).removeClass('d-none');
        } else {
            $('#search-suggestions').addClass('d-none');
        }
    }

    window.selectSuggestion = function(pid, type) {
        let product = products.find(p => p.id == pid && p.item_type == type);
        if (product) {
            $('#product-search').val(product.title);
            $('#search-suggestions').addClass('d-none');
            fetchProducts(product.title, false);
            addToCart(pid, type);
        }
    };



    $('.filter-cat').on('click', function() {
        $('.filter-cat').removeClass('active');
        $(this).addClass('active');
        fetchProducts();
    });

    function fetchProducts(query = null, triggerSuggestions = false) {
        if (query === null) query = $('#product-search').val();
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
                if (triggerSuggestions && query.length > 0) {
                    showSuggestions(query, res);
                }
            }
        });
    }

    function getPriceForCustomer(product) {
        let type = $('#customer-select').find(':selected').data('type') || 'retail'; // Default to retail or base price

        let price = parseFloat(product.price); // Default Selling Price

        if (type == 'wholesale' && product.wholesale_price) price = parseFloat(product.wholesale_price);
        else if (type == 'retail' && product.retail_price) price = parseFloat(product.retail_price);
        else if (type == 'walkin' && product.walkin_price) price = parseFloat(product.walkin_price);
        else if (type == 'salesman' && product.salesman_price) price = parseFloat(product.salesman_price);

        return price || 0;
    }

    function renderProducts() {
        let html = '';
        products.forEach(p => {
            let displayPrice = getPriceForCustomer(p);
            let itemTypeBadge = p.item_type == 'bundle' ? '<span class="badge badge-warning mb-1" style="font-size:8px; padding:1px 4px;">BUNDLE</span>' : '';
            let brandName = p.brand ? p.brand.title : 'GENERIC';
            let modelName = p.model || 'N/A';

            html += `
                <div class="col-xl-8-grid mb-3 px-2">
                    <div class="card product-grid-card shadow-sm cursor-pointer" onclick="addToCart(${p.id}, '${p.item_type}', event)">
                        <div class="price-tag-elite">Rs. ${Math.round(displayPrice).toLocaleString()}</div>
                        <div class="stock-tag-elite ${p.stock <= 5 ? 'text-danger' : ''}">${p.stock}</div>
                        
                        <img src="${p.photo ? p.photo.split(',')[0] : '{{asset('backend/img/thumbnail-default.jpg')}}'}" class="thumbnail-elite">
                        
                        <div class="glass-overlay">
                            ${itemTypeBadge}
                            <div class="elite-title text-truncate" title="${p.title}">${p.title}</div>
                            <div class="elite-meta text-truncate">${brandName} | ${modelName}</div>
                        </div>
                    </div>
                </div>
            `;
        });
        $('#products-grid').html(html || '<div class="col-12 text-center py-5"><h5 class="text-muted">No items match your search</h5></div>');
    }

    function addToCart(pid, type, event) {
        let product = products.find(p => p.id == pid && p.item_type == type);
        if (!product) return;

        let defaultPrice = getPriceForCustomer(product);

        Swal.fire({
            title: `<span style="font-size: 16px; font-weight: 800;">${product.title}</span>`,
            html: `
                <div class="text-left px-2">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold small text-uppercase text-muted">Quantity</label>
                                <input type="number" id="swal-qty" class="form-control form-control-lg text-center font-weight-bold" value="1" min="1" step="1" style="border-radius: 12px; border: 2px solid #e2e8f0;">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold small text-uppercase text-muted">Unit Price</label>
                                <input type="number" id="swal-price" class="form-control form-control-lg text-center font-weight-bold text-success" value="${defaultPrice}" step="0.01" style="border-radius: 12px; border: 2px solid #e2e8f0;">
                            </div>
                        </div>
                    </div>
                    ${product.stock ? `<div class="text-center small font-weight-bold mt-2">Available: <span class="${product.stock <= 5 ? 'text-danger' : 'text-primary'}">${product.stock} ${product.unit || 'pcs'}</span></div>` : ''}
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'ADD TO CART',
            cancelButtonText: 'CANCEL',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            focusConfirm: false,
            width: '400px',
            preConfirm: () => {
                const qty = document.getElementById('swal-qty').value;
                const price = document.getElementById('swal-price').value;
                if (!qty || qty < 1) {
                    Swal.showValidationMessage(`Please enter a valid quantity`);
                    return false;
                }
                return {
                    qty: parseFloat(qty),
                    price: parseFloat(price)
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let {
                    qty,
                    price
                } = result.value;
                let cartId = type + '-' + pid;
                let item = cart.find(i => i.unique_id == cartId);

                if (item) {
                    item.qty += qty;
                    item.price = price;
                    item.original_price = Math.max(price, item.base_price);
                } else {
                    let cartItem = {
                        unique_id: cartId,
                        id: product.id,
                        type: type,
                        title: product.title,
                        brand: product.brand ? product.brand.title : '',
                        model: product.model || '',
                        base_price: defaultPrice,
                        original_price: Math.max(price, defaultPrice),
                        price: price,
                        qty: qty,
                        unit: product.unit,
                        last_purchase: null
                    };
                    cart.push(cartItem);
                    fetchLastPurchase(cartItem);
                }
                renderCart();

                // Success Toast
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true
                });
                Toast.fire({
                    icon: 'success',
                    title: 'Added to cart'
                });
            }
        });
    }

    function fetchLastPurchase(cartItem) {
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

        if (cart.length == 0) {
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
                <div class="cart-item d-flex align-items-center p-2 mb-1 border-bottom" style="background: #fff; min-height: 45px;">
                    <div class="flex-grow-1 min-width-0">
                        <div class="d-flex align-items-center flex-wrap overflow-hidden">
                            <h6 class="font-weight-bold m-0 text-dark text-truncate" style="font-size: 13px; line-height: 1.1;">${item.title}</h6>
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

        // Update Toggle Badge
        if (count > 0) {
            $('#cart-badge').text(count).show();
            $('#toggle-cart').addClass('animated-pulse');
        } else {
            $('#cart-badge').hide();
            $('#toggle-cart').removeClass('animated-pulse');
        }
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
            url: "{{route('users.direct-store')}}",
            type: "POST",
            data: form.serialize() + "&role=user&status=active&password=password123",
            dataType: "json",
            success: function(response) {
                if (typeof response === 'string') {
                    try {
                        response = JSON.parse(response);
                    } catch (e) {}
                }
                let user = response.user || response.data || response;
                let name = user.name || 'Unknown';
                let phone = user.phone || 'N/A';

                // Add new option with data-type and data-balance
                let displayText = name + ' (' + phone + ') | Bal: Rs. 0.00';
                let newOption = new Option(displayText, user.id, true, true);
                $(newOption).attr('data-type', user.customer_type || 'retail');
                $(newOption).attr('data-balance', 0);
                $('#customer-select').append(newOption).trigger('change');
                $('#addCustomerModal').modal('hide');
                form[0].reset();
                Swal.fire('Success', 'Customer Added', 'success');
            },
            error: function(err) {
                console.log(err);
                let errorMsg = 'Failed to add customer';
                if (err.status === 422) {
                    let errors = err.responseJSON.errors;
                    errorMsg = Object.values(errors).flat().join('\n');
                } else if (err.responseJSON && err.responseJSON.message) {
                    errorMsg = err.responseJSON.message;
                } else if (err.responseText) {
                    errorMsg = "Server Error: " + err.responseText.substring(0, 150);
                } else {
                    errorMsg = "Server Error " + err.status;
                }
                Swal.fire('Error', errorMsg, 'error');
            }
        });
    });

    // Save New Product logic
    $('#save-product-btn').on('click', function() {
        var $btn = $(this);
        var formData = $('#add-product-form').serialize();

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> SAVING...');

        $.ajax({
            url: "{{route('product.quick-store')}}",
            type: "POST",
            data: formData,
            success: function(res) {
                if (res.status === 'success') {
                    $('#addProductModal').modal('hide');
                    $('#add-product-form')[0].reset();
                    Swal.fire('Success', 'Product added successfully!', 'success');
                    // Refresh grid
                    fetchProducts();
                }
                $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> SAVE PRODUCT');
            },
            error: function(err) {
                var msg = err.responseJSON ? err.responseJSON.message : 'Error adding product';
                Swal.fire('Error', msg, 'error');
                $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> SAVE PRODUCT');
            }
        });
    });

    $('#complete-order').on('click', function() {
        if (cart.length == 0) {
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
                    // Handle Printing via hidden iframe
                    if (response.thermal_url) {
                        $('#print-iframe').attr('src', response.thermal_url);
                    }

                    if (response.wa_sent) {

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
                $('#complete-order').prop('disabled', false).text('SAVE & PRINT INVOICE');
            }
        });
    });
</script>
@endpush