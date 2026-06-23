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
            <div class="pos-header-wrap d-flex flex-wrap align-items-center mb-3 mt-1" style="gap: 10px;">
                <div class="flex-grow-1 min-width-mobile-100">
                    <div class="search-wrapper-sleek d-flex align-items-center px-3" style="background: #fff; border-radius: 100px; border: 1px solid #e2e8f0; height: 45px;">
                        <i class="fas fa-search search-icon-sleek mr-2 text-muted"></i>
                        <input type="text" id="product-search" class="form-control border-0 shadow-none p-0 bg-transparent" placeholder="Search products, SKU..." style="font-size: 14px;" autofocus autocomplete="off">
                    </div>
                </div>
                <div class="d-flex align-items-center ml-auto" style="gap: 8px;">
                    <div class="btn-group shadow-sm mr-1" role="group" style="border-radius: 100px; overflow: hidden; border: 1px solid #e2e8f0;">
                        <button type="button" class="btn btn-sm btn-white view-toggle-btn active" data-view="grid" style="height: 45px; padding: 0 15px; color: #4e73df;">
                            <i class="fas fa-th-large"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-white view-toggle-btn" data-view="list" style="height: 45px; padding: 0 15px; color: #94a3b8;">
                            <i class="fas fa-list"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-white border-left" id="toggle-multi-select" style="height: 45px; padding: 0 15px; color: #94a3b8;" title="Multi-Select Mode">
                            <i class="fas fa-check-double"></i>
                        </button>
                    </div>
                    <button type="button" data-toggle="modal" data-target="#addProductModal" class="btn btn-white btn-sm px-3 shadow-sm border d-flex align-items-center justify-content-center" style="border-radius: 100px; font-weight: 700; color: #475569; height: 45px; min-width: 45px;">
                        <i class="fas fa-plus text-primary mr-md-2"></i> <span class="d-none d-md-inline">NEW ITEM</span>
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
    </div>
</div>

<!-- Bulk Action Bar (Floating) -->
<div id="bulk-action-bar" class="d-none" style="position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%); z-index: 99999; background: #fff; padding: 12px 20px; border-radius: 50px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); border: 2px solid #4e73df; display: flex; align-items: center; gap: 15px;">
    <span class="font-weight-bold text-primary" id="bulk-count-text">0 items selected</span>
    <button class="btn btn-sm btn-success rounded-pill font-weight-bold px-3 shadow" onclick="openBulkModal()"><i class="fas fa-cart-plus mr-1"></i> Add to Cart</button>
    <button class="btn btn-sm btn-light border rounded-pill px-3" onclick="cancelMultiSelect()">Cancel</button>
</div><!-- Bulk Add Modal -->
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Purchase Price</label>
                                <input type="number" name="purchase_price" class="form-control" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Selling Price <span class="text-danger">*</span></label>
                                <input type="number" name="price" class="form-control" required placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Low Stock Alert <span class="text-danger">*</span></label>
                                <input type="number" name="low_stock_threshold" class="form-control" value="5" required>
                            </div>
                        </div>
                        <div class="col-md-3">
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

{{-- Edit Product Modal --}}
<div class="modal fade" id="editProductModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index:10000;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold">Quick Edit Item</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body bg-light">
                <form id="edit-product-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit-product-id" name="id">
                    <input type="hidden" id="edit-product-type">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="small font-weight-bold">Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="edit-title" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">Category <span class="text-danger">*</span></label>
                                <select name="cat_id" id="edit-cat-id" class="form-control" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $cat)
                                    <option value="{{$cat->id}}">{{$cat->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Brand</label>
                                <select name="brand_id" id="edit-brand-id" class="form-control">
                                    <option value="">Select Brand</option>
                                    @foreach($brands as $brand)
                                    <option value="{{$brand->id}}">{{$brand->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Model</label>
                                <select name="model" id="edit-model" class="form-control">
                                    <option value="">Select Model</option>
                                    @foreach($product_models as $m)
                                    <option value="{{$m->name}}">{{$m->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Unit</label>
                                <select name="unit" id="edit-unit" class="form-control">
                                    <option value="piece">Piece</option>
                                    @foreach($units as $u)
                                    <option value="{{$u->name}}">{{$u->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Available Stock <span class="text-danger">*</span></label>
                                <input type="number" name="stock" id="edit-stock" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Purchase Price</label>
                                <input type="number" name="purchase_price" id="edit-purchase-price" class="form-control" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Selling Price <span class="text-danger">*</span></label>
                                <input type="number" name="price" id="edit-price" class="form-control" required step="0.01">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Low Stock Alert <span class="text-danger">*</span></label>
                                <input type="number" name="low_stock_threshold" id="edit-low-stock" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Suppliers <span class="text-danger">*</span></label>
                                <select name="suppliers[]" id="edit-supplier-select" class="form-control" multiple required>
                                    @foreach($suppliers as $supplier)
                                    <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="alert alert-warning small mb-0 mt-2 d-none" id="bundle-edit-warning">
                    <i class="fas fa-info-circle mr-1"></i> Full bundle editing must be done via the Admin Dashboard. Only stock/price are editable here.
                </div>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary px-4 shadow" id="update-product-btn">
                    <i class="fas fa-save mr-1"></i> SAVE CHANGES
                </button>
            </div>
        </div>
    </div>
</div>

@include('backend.product.partials.modals')

<!-- Bulk Add Modal -->

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

    .filter-btn {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        font-size: 12px !important;
        white-space: nowrap;
        border-radius: 20px !important;
        padding: 4px 15px !important;
        font-weight: 700 !important;
        border-width: 1px !important;
        border-style: solid !important;
        background: #ffffff !important;
    }
    .filter-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    }
    
    .pos-list-item {
        transition: all 0.2s ease;
        border: 1px solid rgba(0,0,0,0.05) !important;
        border-radius: 8px;
    }
    .pos-list-item:hover {
        border-color: #4e73df !important;
        transform: translateX(4px);
        background-color: #f8f9fc;
    }
    .view-toggle-btn {
        transition: all 0.2s ease;
        background: #fff;
    }
    .view-toggle-btn.active {
        background-color: #f8f9fc;
        color: #4e73df !important;
    }
    .view-toggle-btn:not(.active) {
        color: #94a3b8 !important;
    }

    .selectable-item {
        user-select: none;
        -webkit-user-select: none;
        -webkit-touch-callout: none;
    }
    .multi-select-mode .product-grid-card, .multi-select-mode .pos-list-item {
        cursor: pointer !important;
    }
    .product-grid-card.selected-for-bulk, .pos-list-item.selected-for-bulk {
        border: 3px solid #4e73df !important;
        box-shadow: 0 0 15px rgba(78,115,223,0.4) !important;
        transform: scale(0.98);
    }
    .selected-checkmark {
        position: absolute;
        top: -10px;
        right: -10px;
        background: #fff;
        color: #1cc88a;
        border-radius: 50%;
        font-size: 24px;
        z-index: 50;
        display: none;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    .pos-list-item .selected-checkmark {
        top: 50%;
        right: 15px;
        transform: translateY(-50%);
    }
    .selected-for-bulk .selected-checkmark {
        display: block;
    }
    #toggle-multi-select.active {
        background-color: #4e73df;
        color: #fff !important;
    }

    /* Explicitly color inactive outline buttons to prevent theme white-out */
    .filter-btn.btn-outline-success {
        color: #2e7d32 !important;
        border-color: #81c784 !important;
    }
    .filter-btn.btn-outline-primary {
        color: #1565c0 !important;
        border-color: #90caf9 !important;
    }
    .filter-btn.btn-outline-warning {
        color: #ef6c00 !important;
        border-color: #ffe082 !important;
    }
    .filter-btn.btn-outline-danger {
        color: #c62828 !important;
        border-color: #ef9a9a !important;
    }
    /* Dynamic active styles */
    .filter-btn.active.btn-success {
        background-color: #2e7d32 !important;
        color: #ffffff !important;
        border-color: #2e7d32 !important;
    }
    .filter-btn.active.btn-primary {
        background-color: #1565c0 !important;
        color: #ffffff !important;
        border-color: #1565c0 !important;
    }
    .filter-btn.active.btn-warning {
        background-color: #f9a825 !important;
        color: #ffffff !important;
        border-color: #f9a825 !important;
    }
    .filter-btn.active.btn-danger {
        background-color: #c62828 !important;
        color: #ffffff !important;
        border-color: #c62828 !important;
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
                window.posCart.push(cartItem);
            });

            window.saveCart();

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

        function formatCustomer(state) {
            if (!state.id) return state.text;
            var $el = $(state.element);
            
            if (state.id == '1') {
                return $(`<span style="font-size: 13px; font-weight: 600;"><i class="fas fa-walking text-primary mr-1"></i> Walk-in Customer</span>`);
            }

            var name = $el.data('name') || state.text;
            var phone = $el.data('phone') || '';
            var balance = parseFloat($el.data('balance')) || 0;
            var balClass = balance > 0 ? 'badge-success' : (balance < 0 ? 'badge-danger' : 'badge-secondary');
            var balText = 'Rs. ' + Math.abs(balance).toLocaleString('en-US', {minimumFractionDigits: 0});

            return $(`
                <div class="d-flex align-items-center justify-content-between w-100" style="font-size: 13px;">
                    <div class="text-truncate" style="max-width: 45%; font-weight: 600;">
                        ${name}
                    </div>
                    <div class="d-flex align-items-center" style="gap: 6px; padding-right: 5px;">
                        <span class="text-muted d-none d-sm-inline" style="font-size: 11px;"><i class="fas fa-phone-alt mr-1"></i>${phone}</span>
                        <span class="badge ${balClass}" style="font-size: 11px; padding: 4px 6px;">Bal: ${balText}</span>
                    </div>
                </div>
            `);
        }

        $('#customer-select').select2({
            placeholder: "Select Customer",
            allowClear: true,
            dropdownParent: $('body'), // Ensure it's appended to body for correct z-index handling
            templateResult: formatCustomer,
            templateSelection: formatCustomer
        });

        fetchProducts();

        // Customer Change Logic
        $('#customer-select').on('change', function() {
            let id = $(this).val();
            let balance = parseFloat($(this).find(':selected').data('balance')) || 0;
            
            // Force only Cash for Walk-in Customer (ID 1)
            if (id == 1) {
                $('.payment-option[data-method="credit"]').parent().hide();
                $('.payment-option[data-method="cod"]').parent().hide();
                
                // Force select Cash if Credit or COD was active
                let activeMethod = $('.payment-option.active').data('method');
                if (activeMethod == 'credit' || activeMethod == 'cod') {
                    $('.payment-option[data-method="cash"]').trigger('click');
                }
                // Clear payment amount for walk-in
                $('#amount-received').val('');
            } else {
                $('.payment-option[data-method="credit"]').parent().show();
                $('.payment-option[data-method="cod"]').parent().show();
            }

            $('#modal-ledger-balance').text('Rs. ' + balance.toFixed(2));

            // Re-fetch products from server to smartly sort by this customer's history
            fetchProducts();

            // Update cart items if customer changes (prices might change)
            if (window.posCart.length > 0) {
                window.posCart.forEach(item => {
                    let product = products.find(p => p.id == item.id && p.item_type == item.type);
                    if (product) {
                        let newPrice = getPriceForCustomer(product);
                        if (item.price === item.base_price) {
                            item.price = newPrice;
                        }
                        item.base_price = newPrice;
                        item.original_price = Math.max(item.price, newPrice);
                    }
                    if (id == 1) {
                        item.last_purchase = null;
                    } else {
                        fetchLastPurchase(item);
                    }
                });
                window.saveCart();
            }
        });

        // Ensure walk-in restrictions are applied when payment modal opens
        $('#paymentModal').on('show.bs.modal', function() {
            let id = $('#customer-select').val();
            
            // Reset filter button styles and trigger Cash category by default to keep layout clean and uncrowded
            $('.filter-btn').removeClass('active btn-success btn-primary btn-warning btn-danger text-white');
            $('.payment-method-item').show();
            $('.filter-btn[data-filter="cash"]').trigger('click');
            
            if (id == 1) {
                $('.payment-option[data-method="credit"]').parent().hide();
                $('.payment-option[data-method="cod"]').parent().hide();
                
                let activeMethod = $('.payment-option.active').data('method');
                if (activeMethod == 'credit' || activeMethod == 'cod') {
                    $('.payment-option[data-method="cash"]').trigger('click');
                }
            } else {
                $('.payment-option[data-method="credit"]').parent().show();
                $('.payment-option[data-method="cod"]').parent().show();
            }
        });

        // Park Order - Open New POS in another tab
        $('#park-order').on('click', function() {
            window.open("{{route('admin.pos')}}", '_blank');
        });

        // Clear Cart
        $('#clear-cart').on('click', function() {
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
                    cart = [];
                    window.saveCart();
                }
            });
        });
    });

    let searchTimer;
    let currentFocus = -1;

    $('#product-search').on('keydown', function(e) {
        let viewMode = localStorage.getItem('pos_view_mode') || 'grid';
        let x;
        
        if (viewMode === 'list') {
            x = $('#products-grid .pos-list-item');
            if (e.keyCode == 40 || e.keyCode == 38) {
                $('#search-suggestions').addClass('d-none'); // Hide suggestions when navigating list
            }
        } else {
            x = $('#search-suggestions div.suggestion-item');
        }

        if (e.keyCode == 40) { // Down
            e.preventDefault();
            currentFocus++;
            addActive(x);
        } else if (e.keyCode == 38) { // Up
            e.preventDefault();
            currentFocus--;
            addActive(x);
        } else if (e.keyCode == 13) { // Enter
            e.preventDefault();
            if (currentFocus > -1 && x && x.length > 0) {
                x[currentFocus].click();
            } else {
                // Pressing Enter without selecting
                $('#search-suggestions').addClass('d-none');
                fetchProducts($(this).val(), false, true);
            }
        }
    });

    function addActive(x) {
        if (!x || x.length === 0) return false;
        removeActive(x);
        if (currentFocus >= x.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = (x.length - 1);
        
        let el = $(x[currentFocus]);
        let viewMode = localStorage.getItem('pos_view_mode') || 'grid';
        
        if (viewMode === 'list' && el.hasClass('pos-list-item')) {
            el.addClass("border border-primary bg-light");
            el.css('transform', 'scale(1.01)');
            el.css('transition', 'all 0.1s ease-in-out');
        } else {
            el.addClass("active");
        }
        
        // Scroll into view if needed
        x[currentFocus].scrollIntoView({
            block: 'center',
            behavior: 'smooth'
        });
    }

    function removeActive(x) {
        if (!x) return;
        let viewMode = localStorage.getItem('pos_view_mode') || 'grid';
        for (let i = 0; i < x.length; i++) {
            let el = $(x[i]);
            if (viewMode === 'list' && el.hasClass('pos-list-item')) {
                el.removeClass("border border-primary bg-light");
                el.css('transform', 'scale(1)');
            } else {
                el.removeClass("active");
            }
        }
    }

    $('#product-search').on('input', function() {
        let val = $(this).val();
        clearTimeout(searchTimer);
        currentFocus = -1; // Reset focus on input

        if (val.length > 0) {
            searchTimer = setTimeout(() => {
                // Live update the grid while typing (real-time filtering) AND show suggestions
                fetchProducts(val, false, true);
            }, 150);
        } else {
            $('#search-suggestions').addClass('d-none');
            // Clear search results and reset grid to default
            fetchProducts('', false, true);
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
            // Now update the grid to show the selected product
            fetchProducts(product.title, false, true);
            // Optionally add to cart - keeping original behavior
            addToCart(pid, type);
        }
    };



    $('.filter-cat').on('click', function() {
        $('.filter-cat').removeClass('active');
        $(this).addClass('active');
        fetchProducts();
    });

    $(document).ready(function() {
        let currentMode = localStorage.getItem('pos_view_mode') || 'grid';
        $('.view-toggle-btn').removeClass('active');
        $(`.view-toggle-btn[data-view="${currentMode}"]`).addClass('active');

        $('.view-toggle-btn').click(function() {
            let mode = $(this).data('view');
            localStorage.setItem('pos_view_mode', mode);
            $('.view-toggle-btn').removeClass('active');
            $(this).addClass('active');
            renderProducts();
        });

        $('#toggle-multi-select').click(function() {
            toggleMultiSelectMode();
        });
    });

    let isMultiSelectMode = false;
    let selectedProducts = [];
    let pressTimer;

    function toggleMultiSelectMode(forceState = null) {
        if (forceState !== null) {
            isMultiSelectMode = forceState;
        } else {
            isMultiSelectMode = !isMultiSelectMode;
        }
        
        if (isMultiSelectMode) {
            $('body').addClass('multi-select-mode');
            $('#toggle-multi-select').addClass('active');
            if (selectedProducts.length > 0) {
                $('#bulk-action-bar').removeClass('d-none');
            }
        } else {
            cancelMultiSelect();
        }
    }

    function toggleProductSelection(pid, type) {
        let index = selectedProducts.findIndex(p => p.id == pid && p.type == type);
        if (index > -1) {
            selectedProducts.splice(index, 1);
            $(`.selectable-item[data-pid="${pid}"][data-ptype="${type}"]`).removeClass('selected-for-bulk');
        } else {
            selectedProducts.push({id: pid, type: type});
            $(`.selectable-item[data-pid="${pid}"][data-ptype="${type}"]`).addClass('selected-for-bulk');
        }
        
        if (selectedProducts.length > 0) {
            $('#bulk-count-text').text(`${selectedProducts.length} item${selectedProducts.length>1?'s':''} selected`);
            $('#bulk-action-bar').removeClass('d-none');
        } else {
            $('#bulk-action-bar').addClass('d-none');
        }
    }

    window.cancelMultiSelect = function() {
        isMultiSelectMode = false;
        selectedProducts = [];
        $('body').removeClass('multi-select-mode');
        $('#toggle-multi-select').removeClass('active');
        $('.selectable-item').removeClass('selected-for-bulk');
        $('#bulk-action-bar').addClass('d-none');
    };

    window.openBulkModal = function() {
        if (selectedProducts.length === 0) return;
        
        let html = '';
        selectedProducts.forEach(sel => {
            let product = products.find(p => p.id == sel.id && p.item_type == sel.type);
            if (!product) return;
            let defaultPrice = getPriceForCustomer(product);
            
            html += `
            <tr data-pid="${sel.id}" data-ptype="${sel.type}">
                <td class="align-middle">
                    <div class="font-weight-bold text-truncate" style="max-width: 250px;" title="${product.title}">${product.title}</div>
                    <small class="text-muted">Stock: ${product.stock || 'N/A'}</small>
                </td>
                <td class="align-middle">
                    <input type="number" class="form-control form-control-sm text-center bulk-qty-input font-weight-bold" value="1" min="1" step="1">
                </td>
                <td class="align-middle">
                    <input type="number" class="form-control form-control-sm text-right bulk-price-input text-success font-weight-bold" value="${defaultPrice}" step="0.01">
                </td>
                <td class="align-middle text-center">
                    <button type="button" class="btn btn-sm btn-link text-danger" onclick="removeBulkItem(${sel.id}, '${sel.type}', this)"><i class="fas fa-times"></i></button>
                </td>
            </tr>
            `;
        });
        
        $('#bulk-add-tbody').html(html);
        $('#bulkAddModal').modal('show');
    };

    window.removeBulkItem = function(pid, type, btnElement) {
        let index = selectedProducts.findIndex(p => p.id == pid && p.type == type);
        if (index > -1) {
            selectedProducts.splice(index, 1);
            $(`.selectable-item[data-pid="${pid}"][data-ptype="${type}"]`).removeClass('selected-for-bulk');
        }
        $(btnElement).closest('tr').remove();
        
        $('#bulk-count-text').text(`${selectedProducts.length} item${selectedProducts.length>1?'s':''} selected`);
        if (selectedProducts.length === 0) {
            $('#bulkAddModal').modal('hide');
            $('#bulk-action-bar').addClass('d-none');
        }
    };

    $('#confirm-bulk-add').click(function() {
        let addedCount = 0;
        $('#bulk-add-tbody tr').each(function() {
            let pid = $(this).data('pid');
            let type = $(this).data('ptype');
            let qty = parseFloat($(this).find('.bulk-qty-input').val());
            let price = parseFloat($(this).find('.bulk-price-input').val());
            
            if (qty > 0) {
                let product = products.find(p => p.id == pid && p.item_type == type);
                if (product) {
                    let cartId = type + '-' + pid;
                    let item = window.posCart.find(i => i.unique_id == cartId);
                    let defaultPrice = getPriceForCustomer(product);

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
                        window.posCart.push(cartItem);
                        fetchLastPurchase(cartItem);
                    }
                    addedCount++;
                }
            }
        });
        
        if (addedCount > 0) {
            window.saveCart();
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
            Toast.fire({
                icon: 'success',
                title: `Added ${addedCount} items to cart`
            });
        }
        
        $('#bulkAddModal').modal('hide');
        cancelMultiSelect();
    });

    window.handleProductClick = function(pid, type, event) {
        if (isMultiSelectMode) {
            toggleProductSelection(pid, type);
        } else {
            addToCart(pid, type, event);
        }
    };

    function fetchProducts(query = null, triggerSuggestions = false, updateGrid = true) {
        if (query === null) query = $('#product-search').val();
        let cat_id = $('.filter-cat.active').data('id');
        let customer_id = $('#customer-select').val();

        $.ajax({
            url: "{{route('pos.search-products')}}",
            data: {
                query: query,
                cat_id: cat_id,
                customer_id: customer_id
            },
            success: function(res) {
                products = res;
                if (updateGrid) {
                    renderProducts();
                }
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
        let viewMode = localStorage.getItem('pos_view_mode') || 'grid';
        let currentQuery = $('#product-search').val().trim();
        let safeQuery = currentQuery.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        let regex = safeQuery ? new RegExp(`(${safeQuery})`, 'gi') : null;

        let html = '';
        products.forEach(p => {
            let displayPrice = getPriceForCustomer(p);
            let itemTypeBadge = p.item_type == 'bundle' ? '<span class="badge badge-warning mb-1" style="font-size:8px; padding:1px 4px;">BUNDLE</span>' : '';
            let brandName = p.brand ? p.brand.title : 'GENERIC';
            let modelName = p.model || 'N/A';
            
            let displayTitle = p.title;
            let displaySku = p.sku ? p.sku : '';
            
            if (regex && currentQuery.length > 0) {
                displayTitle = p.title.replace(regex, '<mark class="bg-warning px-1 rounded text-dark" style="padding: 2px 0;">$1</mark>');
                if (displaySku) displaySku = displaySku.replace(regex, '<mark class="bg-warning px-1 rounded text-dark" style="padding: 2px 0;">$1</mark>');
            }

            let photoSrc = p.photo ? p.photo.split(',')[0].trim() : '';
            if (!photoSrc) {
                photoSrc = "{{asset('backend/img/thumbnail-default.jpg')}}";
            } else if (!photoSrc.startsWith('http') && !photoSrc.startsWith('/')) {
                photoSrc = '/' + photoSrc;
            }

            let editRoute = p.item_type === 'bundle' 
                ? '/admin/product-bundles/' + p.id + '/edit'
                : '/admin/product/' + p.id + '/edit';

            let isSelected = selectedProducts.find(s => s.id == p.id && s.type == p.item_type) ? 'selected-for-bulk' : '';

            if (viewMode === 'list') {
                html += `
                <div class="col-12 mb-1 px-1">
                    <div class="card pos-list-item shadow-sm selectable-item cursor-pointer ${isSelected}" data-pid="${p.id}" data-ptype="${p.item_type}" onclick="handleProductClick(${p.id}, '${p.item_type}', event)" style="border-radius: 6px;">
                        <i class="fas fa-check-circle selected-checkmark"></i>
                        <div class="card-body p-2 d-flex align-items-center m-0" style="gap: 8px; overflow: hidden;">
                            ${itemTypeBadge}
                            <div class="font-weight-bold text-dark" style="font-size: 13px; flex: 3; min-width: 100px; line-height: 1.3;" title="${p.title}">
                                ${displayTitle}
                                <div class="text-muted mt-1" style="font-size: 10px; font-weight: normal;">
                                    ${brandName} ${displaySku ? '| ' + displaySku : ''}
                                </div>
                            </div>
                            <div class="text-muted text-center" style="font-size: 12px; width: 70px;">
                                <span class="${p.stock <= 5 ? 'text-danger font-weight-bold' : 'font-weight-bold'}">${p.stock}</span>
                            </div>
                            <div class="font-weight-bold text-success text-right" style="font-size: 14px; width: 90px;">
                                ${Math.round(displayPrice).toLocaleString()}
                            </div>
                            <div class="d-flex align-items-center" style="gap: 4px;">
                                <button class="btn btn-sm btn-light border shadow-sm" style="width: 24px; height: 24px; padding: 0; display: flex; align-items: center; justify-content: center;" onclick="event.stopPropagation(); openEditModal(${p.id}, '${p.item_type}');" title="Quick Edit">
                                    <i class="fas fa-edit text-primary" style="font-size: 10px;"></i>
                                </button>
                                <button class="btn btn-sm btn-info border-0 shadow-sm" style="width: 24px; height: 24px; padding: 0; display: flex; align-items: center; justify-content: center;" onclick="event.stopPropagation(); showProductHistory(${p.id}, '${p.item_type}');" title="Selling History">
                                    <i class="fas fa-info text-white" style="font-size: 10px;"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>`;
            } else {
                html += `
                    <div class="col-xl-8-grid mb-3 px-2">
                        <div class="card product-grid-card shadow-sm selectable-item cursor-pointer position-relative ${isSelected}" data-pid="${p.id}" data-ptype="${p.item_type}" onclick="handleProductClick(${p.id}, '${p.item_type}', event)">
                            <i class="fas fa-check-circle selected-checkmark"></i>
                            <div class="price-tag-elite">Rs. ${Math.round(displayPrice).toLocaleString()}</div>
                            <div class="stock-tag-elite ${p.stock <= 5 ? 'text-danger' : ''}">${p.stock}</div>
                            <img src="${photoSrc}" class="thumbnail-elite" alt="Product Image" onerror="this.src='{{asset('backend/img/thumbnail-default.jpg')}}'">
                            
                            <div class="glass-overlay">
                                ${itemTypeBadge}
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="elite-title text-truncate" title="${p.title}" style="max-width: 85%; margin-bottom: 0;">${displayTitle}</div>
                                    <div class="d-flex" style="gap: 4px;">
                                        <button class="btn btn-sm btn-light shadow-sm" 
                                            style="padding: 2px 6px; border-radius: 4px; font-size: 10px; background: rgba(255,255,255,0.9); z-index: 20;" 
                                            onclick="event.stopPropagation(); openEditModal(${p.id}, '${p.item_type}');" title="Quick Edit">
                                            <i class="fas fa-edit text-primary"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="elite-meta text-truncate">${brandName} | ${modelName}</div>
                            </div>
                            <button class="btn btn-info shadow-sm position-absolute" 
                                style="top: 35px; left: 6px; width: 24px; height: 24px; border-radius: 50%; padding: 0; display: flex; align-items: center; justify-content: center; font-size: 11px; z-index: 20; border: 2px solid #fff;" 
                                onclick="event.stopPropagation(); showProductHistory(${p.id}, '${p.item_type}');" title="Selling History">
                                <i class="fas fa-info text-white"></i>
                            </button>
                        </div>
                    </div>
                `;
            }
        });
        $('#products-grid').html(html || '<div class="col-12 text-center py-5"><h5 class="text-muted">No items match your search</h5></div>');
        bindLongPress();
    }

    function bindLongPress() {
        $('.selectable-item').off('touchstart mousedown touchend mouseup mouseleave');
        $('.selectable-item').on('touchstart mousedown', function(e) {
            let pid = $(this).data('pid');
            let type = $(this).data('ptype');
            pressTimer = window.setTimeout(function() {
                if (!isMultiSelectMode) {
                    toggleMultiSelectMode(true);
                }
                toggleProductSelection(pid, type);
            }, 500);
        }).on('touchend mouseup mouseleave', function(e) {
            clearTimeout(pressTimer);
        });
    }

    function showProductHistory(pid, type) {
        let product = products.find(p => p.id == pid && p.item_type == type);
        if (!product) return;

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
                                                <td class="font-weight-bold text-dark text-right" style="padding: 10px 8px;">Total:</td>
                                                <td class="text-center font-weight-bold text-primary" style="padding: 10px 8px;">${res.total_qty}</td>
                                                <td class="text-right font-weight-bold text-primary" style="padding: 10px 8px;">Rs. ${res.total_amount.toLocaleString()}</td>
                                            </tr>
                                        </tfoot>
                                        ` : ''}
                                    </table>
                                </div>
                            </div>
                        `;
                        Swal.update({
                            html: historyHtml
                        });
                    } else {
                        Swal.update({
                            html: '<div class="text-center py-3 text-danger">Failed to load history</div>'
                        });
                    }
                });
            }
        });
    }

    function addToCart(pid, type, event) {
        let product = products.find(p => p.id == pid && p.item_type == type);
        if (!product) return;

        let defaultPrice = getPriceForCustomer(product);
        let cartId = type + '-' + pid;
        let existingItem = window.posCart ? window.posCart.find(i => i.unique_id == cartId) : null;
        if (existingItem) {
            defaultPrice = existingItem.price;
        }

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
            didOpen: () => {
                const qtyInput = document.getElementById('swal-qty');
                const priceInput = document.getElementById('swal-price');
                
                if (qtyInput) {
                    setTimeout(() => {
                        qtyInput.focus();
                        qtyInput.select();
                    }, 50);
                    
                    qtyInput.addEventListener('focus', function() {
                        this.select();
                    });
                    qtyInput.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            Swal.clickConfirm();
                        }
                    });
                }
                
                if (priceInput) {
                    priceInput.addEventListener('focus', function() {
                        this.select();
                    });
                    priceInput.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            Swal.clickConfirm();
                        }
                    });
                }
            },
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
                let item = window.posCart.find(i => i.unique_id == cartId);

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
                    window.posCart.push(cartItem);
                    fetchLastPurchase(cartItem); // from global
                }
                saveCart(); // from global

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

    // Save Edit Product logic
    $('#update-product-btn').on('click', function() {
        let $btn = $(this);
        let id = $('#edit-product-id').val();
        let type = $('#edit-product-type').val();
        
        if(type === 'bundle') {
            window.open('/admin/product-bundles/' + id + '/edit', '_blank');
            return;
        }

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> SAVING...');
        
        $.ajax({
            url: "/admin/product/" + id,
            type: 'POST',
            data: $('#edit-product-form').serialize(),
            success: function(res) {
                if (res.status === 'success') {
                    $('#editProductModal').modal('hide');
                    Swal.fire('Success', 'Item updated!', 'success');
                    fetchProducts(null, false, true); // Refresh grid silently
                }
            },
            error: function(err) {
                let msg = err.responseJSON ? err.responseJSON.message : 'Error updating item';
                Swal.fire('Error', msg, 'error');
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> SAVE CHANGES');
            }
        });
    });

    window.openEditModal = function(pid, type) {
        if(type === 'bundle') {
            // Bundles are complex (has many products), open in new tab for now
            window.open('/admin/product-bundles/' + pid + '/edit', '_blank');
            return;
        }

        let product = products.find(p => p.id == pid && p.item_type == type);
        if (!product) return;

        $('#edit-product-id').val(product.id);
        $('#edit-product-type').val(type);
        $('#edit-title').val(product.title);
        $('#edit-stock').val(product.stock);
        $('#edit-price').val(product.price);
        $('#edit-purchase-price').val(product.purchase_price || 0);
        $('#edit-cat-id').val(product.cat_id || 1);
        $('#edit-brand-id').val(product.brand_id || '');
        $('#edit-model').val(product.model || '');
        $('#edit-unit').val(product.unit || 'piece');
        $('#edit-low-stock').val(product.low_stock_threshold || 5);
        
        // Handle Select2 for Edit Modal
        if ($('#edit-supplier-select').hasClass('select2-hidden-accessible')) {
            $('#edit-supplier-select').select2('destroy');
        }
        
        $('#edit-supplier-select').select2({
            placeholder: "Select Supplier(s)",
            width: '100%',
            dropdownParent: $('#editProductModal')
        });

        // Set Suppliers if available, otherwise clear
        if (product.suppliers && product.suppliers.length > 0) {
            let sIds = product.suppliers.map(s => s.id);
            $('#edit-supplier-select').val(sIds).trigger('change');
        } else {
            $('#edit-supplier-select').val(null).trigger('change');
        }
        
        $('#bundle-edit-warning').addClass('d-none');
        
        $('#editProductModal').modal('show');
    };

    // Show/hide Urdu print option based on standard print checkbox
    $('#print-receipt-toggle').on('change', function() {
        if ($(this).is(':checked')) {
            $('#urdu-print-container').slideDown(200);
        } else {
            $('#urdu-print-container').slideUp(200);
            $('#print-receipt-urdu').prop('checked', false);
        }
    });

    // Payment method category filtering with dynamic toggle-off support
    $('.filter-btn').on('click', function() {
        let isAlreadyActive = $(this).hasClass('active');
        
        // Reset all buttons to default state
        $('.filter-btn').removeClass('active btn-success btn-primary btn-warning btn-danger text-white');
        
        let filter = $(this).data('filter');
        
        if (isAlreadyActive) {
            // Toggle off: Show all payment methods
            $('.payment-method-item').show();
            // Re-apply Walk-in restrictions if customer ID is 1 (Walk-in)
            let customerId = $('#customer-select').val();
            if (customerId == 1) {
                $('.payment-option[data-method="credit"]').parent().hide();
                $('.payment-option[data-method="cod"]').parent().hide();
            }
            return;
        }
        
        // Toggle on: Style active button and filter
        $(this).addClass('active');
        if (filter === 'cash') {
            $(this).addClass('btn-success text-white');
        } else if (filter === 'bank') {
            $(this).addClass('btn-primary text-white');
        } else if (filter === 'wallet') {
            $(this).addClass('btn-warning text-white');
        } else if (filter === 'credit') {
            $(this).addClass('btn-danger text-white');
        }
        
        $('.payment-method-item').hide();
        $('.filter-' + filter).show();

        // Re-apply Walk-in restrictions if customer ID is 1 (Walk-in)
        let customerId = $('#customer-select').val();
        if (customerId == 1) {
            $('.payment-option[data-method="credit"]').parent().hide();
            $('.payment-option[data-method="cod"]').parent().hide();
        }
    });

    </script>
@endpush
