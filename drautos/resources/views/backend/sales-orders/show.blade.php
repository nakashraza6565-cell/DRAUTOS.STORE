@extends('backend.layouts.master')

@section('main-content')
<style>
    .sleek-input {
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 4px 8px;
        font-weight: 700;
        color: #1e293b;
        background: #f8fafc;
        box-shadow: none;
        transition: all 0.2s;
    }
    .sleek-input:focus {
        border-color: #4e73df;
        background: #fff;
        outline: none;
        box-shadow: 0 0 0 2px rgba(78, 115, 223, 0.2);
    }
    .sleek-input-group {
        display: flex;
        align-items: center;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        overflow: hidden;
        transition: all 0.2s;
    }
    .sleek-input-group:focus-within {
        border-color: #4e73df;
        background: #fff;
        box-shadow: 0 0 0 2px rgba(78, 115, 223, 0.2);
    }
    .sleek-input-group .prefix {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        padding: 4px 8px;
    }
    .sleek-input-group input {
        border: none;
        background: transparent;
        font-weight: 700;
        color: #1e293b;
        padding: 4px 8px 4px 0;
        width: 100%;
    }
    .sleek-input-group input:focus {
        outline: none;
    }
</style>

<div class="row">
    <div class="col-md-12">
        @include('backend.layouts.notification')
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex align-items-center justify-content-between flex-wrap" style="gap:8px;">
        <h6 class="m-0 font-weight-bold text-primary">
            @if($salesOrder->is_priority)
                <i class="fas fa-star text-warning mr-1"></i>
            @endif
            Sale Order: {{$salesOrder->order_number}}
        </h6>
        <div class="d-flex" style="gap:6px; flex-wrap:wrap;">
            <div class="btn-group">
                <button type="button" class="btn btn-info btn-sm dropdown-toggle shadow-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-print mr-1"></i> Print
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item font-weight-bold" href="{{route('sales-orders.thermal', $salesOrder->id)}}" target="_blank">English</a>
                    <a class="dropdown-item font-weight-bold text-info" href="{{route('sales-orders.thermal', $salesOrder->id)}}?lang=ur" target="_blank">Urdu (اردو)</a>
                </div>
            </div>
            <a href="{{route('sales-orders.index')}}" class="btn btn-secondary btn-sm">Back</a>
        </div>
    </div>

    <div class="card-body">

        {{-- Top Info Row --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="p-3 border rounded bg-light h-100">
                    <h6 class="font-weight-bold mb-2 small text-uppercase text-muted">Customer</h6>
                    <div class="font-weight-bold">{{$salesOrder->user->name ?? 'Guest'}}</div>
                    <div class="text-muted small">{{$salesOrder->user->phone ?? 'N/A'}}</div>
                    @if($salesOrder->user && $salesOrder->user->city)
                        <div class="mt-1"><i class="fas fa-map-marker-alt text-danger mr-1"></i><small>{{$salesOrder->user->city}}</small></div>
                    @endif
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 border rounded bg-light h-100">
                    <h6 class="font-weight-bold mb-2 small text-uppercase text-muted">Order Info</h6>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small text-muted">Status</span>
                        @if($salesOrder->status=='photo_pending') <span class="badge badge-warning"><i class="fas fa-camera mr-1"></i>Photo Pending</span>
                        @elseif($salesOrder->status=='pending') <span class="badge badge-warning">Pending</span>
                        @elseif($salesOrder->status=='partially_delivered') <span class="badge badge-info">Partial</span>
                        @elseif($salesOrder->status=='delivered') <span class="badge badge-success">Delivered</span>
                        @else <span class="badge badge-secondary">{{$salesOrder->status}}</span>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small text-muted">Date</span>
                        <span class="small">{{$salesOrder->created_at->format('d M Y, h:i A')}}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="small text-muted font-weight-bold">Assign Staff</span>
                        <form action="{{route('sales-orders.assign-staff', $salesOrder->id)}}" method="POST">
                            @csrf
                            <select name="staff_id" class="form-control form-control-sm" onchange="this.form.submit()" style="min-width:140px;">
                                @foreach($allStaff as $staff)
                                    <option value="{{$staff->id}}" {{$salesOrder->staff_id == $staff->id ? 'selected' : ''}}>
                                        {{$staff->name}} ({{ucfirst($staff->role)}})
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- ADD ITEM FORM --}}
        <div class="card border-0 bg-light mb-3">
            <div class="card-body py-3">
                <h6 class="font-weight-bold mb-3">
                    <i class="fas fa-plus-circle text-primary mr-1"></i> Add Item to Order
                </h6>
                <form id="add-item-form" action="{{route('sales-orders.add-item', $salesOrder->id)}}" method="POST">
                    @csrf
                    <div class="row align-items-end">
                        <div class="col-md-5">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="small font-weight-bold mb-0">Product</label>
                                <button type="button" class="btn btn-link btn-sm p-0 text-primary font-weight-bold" data-toggle="modal" data-target="#addProductModal">
                                    <i class="fas fa-plus-circle"></i> New Product
                                </button>
                            </div>
                            <select name="product_id" id="add-product-select" class="form-control select2-product" required>
                                <option value="">-- Select Product --</option>
                                @foreach($products as $product)
                                    <option value="{{$product->id}}" data-price="{{$product->price}}">
                                        {{$product->title}} 
                                        @if($product->brand) | {{$product->brand->title}} @endif
                                        @if($product->model) | {{$product->model}} @endif
                                        @if($product->sku) ({{$product->sku}}) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="small font-weight-bold">Qty</label>
                            <input type="number" name="quantity" id="add-qty" class="form-control" value="1" min="0.01" step="any" required>
                        </div>
                        <div class="col-md-3">
                            <label class="small font-weight-bold">Price</label>
                            <input type="number" name="price" id="add-price" class="form-control" value="0" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-plus mr-1"></i> Add
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ITEMS TABLE --}}
        <form id="fulfill-form" action="{{route('sales-orders.fulfill', $salesOrder->id)}}" method="POST">
            @csrf
            <!-- Global Select All / Header -->
            <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
                <div class="font-weight-bold text-dark text-right ml-auto" style="font-size: 1.1rem;">
                    Total: <span id="grand-total-display" class="text-success">Rs. {{number_format($salesOrder->total_amount, 2)}}</span>
                </div>
            </div>

            <div id="items-table-container" class="table-responsive">
                <table class="table table-bordered table-hover order-table-to-cards sale-order-detail-to-cards" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th width="40" class="text-center">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="select-all">
                                    <label class="custom-control-label" style="cursor: pointer;" for="select-all"></label>
                                </div>
                            </th>
                            <th>Product</th>
                            <th class="text-center">Ordered</th>
                            <th class="text-center">Delivered</th>
                            <th class="text-center">Remaining</th>
                            <th class="text-center" width="120">Fulfill Qty</th>
                            <th class="text-center" width="150">Unit Price</th>
                            <th class="text-right">Total</th>
                            <th class="text-center" width="50">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salesOrder->items as $item)
                        @php $remaining = $item->quantity - $item->delivered_quantity; @endphp
                        <tr style="{{ $remaining <= 0 ? 'background-color: #f0fdf4; opacity:0.8;' : '' }}">
                            <td class="align-middle text-center" data-title="Select">
                                @if($remaining > 0)
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input item-checkbox" name="selected_items[]" value="{{$item->id}}" id="check-{{$item->id}}">
                                        <label class="custom-control-label" style="cursor: pointer;" for="check-{{$item->id}}"></label>
                                    </div>
                                @else
                                    <i class="fas fa-check-circle text-success" style="font-size: 1.2rem;"></i>
                                @endif
                            </td>
                            <td class="align-middle" data-title="Product">
                                <div class="font-weight-bold text-dark text-truncate" style="font-size: 0.95rem;" title="{{$item->product->title ?? 'Deleted Product'}}">{{$item->product->title ?? 'Deleted Product'}}</div>
                                <div class="small text-muted text-truncate">
                                    SKU: {{$item->product->sku ?? 'N/A'}}
                                    @if($item->product && $item->product->brand)
                                        | {{$item->product->brand->title}}
                                    @endif
                                </div>
                            </td>
                            <td class="align-middle text-center font-weight-bold text-dark" data-title="Ordered">{{$item->quantity}}</td>
                            <td class="align-middle text-center font-weight-bold text-info" data-title="Delivered">{{$item->delivered_quantity}}</td>
                            <td class="align-middle text-center font-weight-bold {{ $remaining > 0 ? 'text-danger' : 'text-success' }}" data-title="Remaining">{{$remaining}}</td>
                            <td class="align-middle" data-title="Fulfill Qty">
                                @if($remaining > 0)
                                    <input type="number" name="deliver[{{$item->id}}]" class="sleek-input deliver-qty text-center w-100" value="{{$remaining}}" min="0" step="any" style="font-size: 13px;">
                                @else
                                    <div class="badge badge-success w-100 py-2 d-flex align-items-center justify-content-center" style="font-size: 12px; height: 32px;"><i class="fas fa-check mr-1"></i> FULFILLED</div>
                                @endif
                            </td>
                            <td class="align-middle" data-title="Unit Price">
                                <div class="sleek-input-group w-100">
                                    <span class="prefix">Rs</span>
                                    <input type="number" step="0.01" class="text-right item-price-input" data-id="{{$item->id}}" value="{{$item->price}}" style="font-size: 13px;">
                                </div>
                            </td>
                            <td class="align-middle text-right font-weight-bold text-primary" data-title="Total" id="item-total-{{$item->id}}">Rs. {{number_format($item->price * $item->quantity, 2)}}</td>
                            <td class="align-middle text-center" data-title="Action">
                                @if($remaining > 0)
                                    <button type="button" class="btn btn-sm btn-outline-danger shadow-sm flex-shrink-0 act-delete" 
                                            style="border-radius: 8px; padding: 4px 8px;"
                                            onclick="removeItem('{{route('sales-orders.remove-item', [$salesOrder->id, $item->id])}}')"
                                            title="Remove item">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted bg-white">
                                <i class="fas fa-box-open fa-3x mb-3 d-block opacity-50"></i>
                                <h5 class="font-weight-bold">No items in this order yet.</h5>
                                <p class="small mb-0">Use the "Add Item" form above to build this order.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($salesOrder->status != 'delivered' && $salesOrder->status != 'cancelled')
            <div class="mt-3 p-3 border rounded bg-light d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="mb-1 font-weight-bold">Fulfillment Action</h6>
                    <p class="small text-muted mb-0">Check items above and send to POS for delivery.</p>
                </div>
                <button type="submit" class="btn btn-primary btn-lg px-5 shadow" id="fulfill-btn" disabled>
                    <i class="fas fa-desktop mr-2"></i> SEND SELECTED TO POS
                </button>
            </div>
            @endif
        </form>

        @if($salesOrder->note)
        <div class="mt-4">
            <h6 class="font-weight-bold">Order Note:</h6>
            <div class="p-3 bg-light border rounded">{{$salesOrder->note}}</div>
        </div>
        @endif

        {{-- Linked POS Bills --}}
        <div class="mt-4" id="linked-bills-container">
            <h6 class="font-weight-bold text-primary"><i class="fas fa-file-invoice-dollar mr-1"></i> Linked POS Bills (Fulfillment History)</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered bg-light">
                    <thead>
                        <tr>
                            <th>Bill Number</th>
                            <th>Date</th>
                            <th>Total Amount</th>
                            <th>Payment Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salesOrder->orders as $linkedOrder)
                        <tr>
                            <td><span class="font-weight-bold">{{$linkedOrder->order_number}}</span></td>
                            <td>{{$linkedOrder->created_at->format('d M Y, h:i A')}}</td>
                            <td>Rs. {{number_format($linkedOrder->total_amount, 2)}}</td>
                            <td>
                                @if($linkedOrder->payment_status == 'paid') <span class="badge badge-success">Paid</span>
                                @elseif($linkedOrder->payment_status == 'partial') <span class="badge badge-warning">Partial</span>
                                @else <span class="badge badge-danger">Unpaid</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{route('order.show', $linkedOrder->id)}}" class="btn btn-primary btn-sm px-3" target="_blank">
                                    <i class="fas fa-eye mr-1"></i> View Bill
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted small py-3">No bills generated for this sale order yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

{{-- Add Product Quick Modal --}}
<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index:9999;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg">
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
                                <label class="small font-weight-bold">Product Title <span class="text-danger">*</span></label>
                                <select name="title" id="pos-title-select" class="form-control" required></select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">Category <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <select name="cat_id" id="qp-cat-select" class="form-control" required>
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
                                    <select name="brand_id" id="qp-brand-select" class="form-control">
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
                                    <select name="model" id="qp-model-select" class="form-control">
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
                                <label class="small font-weight-bold">Unit</label>
                                <div class="input-group">
                                    <select name="unit" id="qp-unit-select" class="form-control">
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
                                <input type="number" name="price" id="qp-price" class="form-control" required placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">Primary Supplier</label>
                                <div class="input-group">
                                    <select name="suppliers[]" id="qp-supplier-select" class="form-control" multiple>
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
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary px-4 shadow" id="save-product-btn">
                    <i class="fas fa-save mr-1"></i> SAVE PRODUCT
                </button>
            </div>
        </div>
    </div>
</div>

@include('backend.product.partials.modals')

{{-- ===============================================================
     FLOATING PHOTO PANEL (fixed to right side of screen)
================================================================ --}}

{{-- Floating trigger button --}}
<button id="photo-panel-toggle" onclick="togglePhotoPanel()"
    style="position:fixed; right:0; top:50%; transform:translateY(-50%);
           background: linear-gradient(135deg,#ffc107,#ff8800); color:#fff; border:none;
           border-radius:12px 0 0 12px; padding:14px 10px; cursor:pointer;
           box-shadow:-3px 3px 12px rgba(0,0,0,0.2); z-index:1050;
           display:flex; flex-direction:column; align-items:center; gap:6px;
           transition: all 0.2s; min-width:44px;">
    <i class="fas fa-camera" style="font-size:18px;"></i>
    <span id="photo-panel-badge" style="background:#fff; color:#ff8800; border-radius:50%; width:22px; height:22px;
          font-size:11px; font-weight:700; display:flex; align-items:center; justify-content:center;">
        {{ $salesOrder->photos->count() }}
    </span>
    <span style="font-size:9px; letter-spacing:0.5px; writing-mode:vertical-rl; text-orientation:mixed;">PHOTOS</span>
</button>

{{-- Side Panel --}}
<div id="so-photo-panel"
     style="position:fixed; right:-360px; top:0; height:100vh; width:350px;
            background:#fff; box-shadow:-4px 0 20px rgba(0,0,0,0.15);
            z-index:1049; transition:right 0.3s ease; display:flex; flex-direction:column;">

    {{-- Panel Header --}}
    <div style="background:linear-gradient(135deg,#ffc107,#ff8800); color:#fff; padding:16px; display:flex; justify-content:space-between; align-items:center; flex-shrink:0;">
        <div>
            <div style="font-weight:700; font-size:15px;"><i class="fas fa-camera mr-2"></i>Order Photos</div>
            <div style="font-size:11px; opacity:0.85;">Evidence &amp; Reference — Internal Only</div>
        </div>
        <button onclick="togglePhotoPanel()" style="background:rgba(255,255,255,0.2); border:none; border-radius:50%; width:32px; height:32px; color:#fff; cursor:pointer; font-size:16px;">✕</button>
    </div>

    {{-- Upload area --}}
    <div style="padding:12px; background:#fffdf0; border-bottom:1px solid #ffc10730; flex-shrink:0;">
        <div id="panel-dropzone" onclick="document.getElementById('panel-photo-input').click()"
             style="border:2px dashed #ffc107; border-radius:8px; padding:14px; text-align:center; cursor:pointer; transition:background 0.2s;">
            <i class="fas fa-cloud-upload-alt text-warning"></i>
            <span class="small font-weight-bold text-muted ml-2">Upload More Photos</span>
            <input type="file" id="panel-photo-input" multiple accept="image/*,.pdf" style="display:none;"
                   onchange="panelUploadPhotos(this)">
        </div>
        <div id="panel-upload-progress" style="display:none;" class="mt-2 text-center small text-muted">
            <i class="fas fa-spinner fa-spin mr-1"></i>Uploading...
        </div>
    </div>

    {{-- Scrollable photo grid --}}
    <div id="panel-photo-grid" style="flex:1; overflow-y:auto; padding:12px;">
        @if($salesOrder->photos->count() > 0)
            <div class="row" id="panel-photos-row" style="margin:0 -4px;">
                @foreach($salesOrder->photos as $photo)
                <div class="col-6 px-1 mb-2 panel-photo-col" id="panel-photo-col-{{$photo->id}}">
                    <div class="card border shadow-sm" style="border-radius:8px; overflow:hidden; cursor:pointer;"
                         onclick="openLightbox('{{ route('sales-orders.photos.view', [$salesOrder->id, $photo->id]) }}','{{addslashes($photo->original_name)}}')">
                        <div style="height:90px; display:flex; align-items:center; justify-content:center; background:#f8f9fa; position:relative;">
                            @if(str_contains($photo->mime_type ?? '', 'pdf'))
                                <i class="fas fa-file-pdf fa-2x text-danger"></i>
                            @else
                                <img src="{{ route('sales-orders.photos.view', [$salesOrder->id, $photo->id]) }}"
                                     style="max-height:90px; max-width:100%; object-fit:cover; width:100%;" loading="lazy"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block'">
                                <i class="fas fa-image fa-2x text-muted" style="display:none;"></i>
                            @endif
                            <button onclick="event.stopPropagation(); panelDeletePhoto({{$photo->id}}, '{{ route('sales-orders.photos.delete', [$salesOrder->id, $photo->id]) }}')"
                                style="position:absolute;top:3px;right:3px;background:rgba(220,53,69,0.85);border:none;border-radius:50%;width:20px;height:20px;color:white;font-size:10px;line-height:1;cursor:pointer;">
                                &times;
                            </button>
                        </div>
                        <div class="p-1 text-center" style="background:#fff;">
                            <p class="mb-0 text-truncate" style="font-size:9px;max-width:100%;" title="{{$photo->original_name}}">{{$photo->original_name}}</p>
                            <p class="mb-0 text-muted" style="font-size:8px;">{{$photo->human_file_size}} &bull; {{$photo->created_at->format('d M')}}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-4 text-muted" id="panel-empty-msg">
                <i class="fas fa-camera fa-2x mb-2 d-block opacity-50"></i>
                <p class="small mb-0">No photos yet.</p>
                <p class="small">Tap "Upload More Photos" above to add evidence.</p>
            </div>
        @endif
    </div>

    {{-- Panel Footer --}}
    <div style="padding:10px 14px; border-top:1px solid #eee; background:#f8f9fa; flex-shrink:0; font-size:10px; color:#94a3b8; text-align:center;">
        Photos are for internal reference only &bull; Not shown on invoices
    </div>
</div>

{{-- Lightbox overlay --}}
<div id="so-lightbox" onclick="closeLightbox()"
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.92); z-index:9999; flex-direction:column; align-items:center; justify-content:center;">
    <button onclick="closeLightbox()" style="position:fixed;top:16px;right:20px;background:rgba(255,255,255,0.15);border:none;border-radius:50%;width:40px;height:40px;color:#fff;font-size:20px;cursor:pointer;z-index:10000;">✕</button>
    <p id="lightbox-caption" style="color:#ccc;font-size:12px;margin-bottom:8px;"></p>
    <img id="lightbox-img" src="" style="max-width:95vw;max-height:88vh;border-radius:8px;box-shadow:0 8px 40px rgba(0,0,0,0.6); object-fit:contain;" onclick="event.stopPropagation()">
    <p style="color:#888;font-size:11px;margin-top:8px;">Click anywhere to close</p>
</div>

@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Move modals to body to avoid z-index issues
    $('#addProductModal, #addCategoryModal, #addBrandModal, #addSupplierModal, #addUnitModal, #addModelModal').appendTo('body');

    // Select2 for add item dropdown
    $('.select2-product').select2({ placeholder: 'Search product...', width: '100%' });

    // Auto-fill price when product selected
    $('#add-product-select').on('change', function() {
        let price = $(this).find(':selected').data('price') || 0;
        $('#add-price').val(price);
    });

    // Fulfill checkboxes logic
    const updateFulfillButton = () => {
        const checkedCount = $('.item-checkbox:checked').length;
        $('#fulfill-btn').prop('disabled', checkedCount === 0);
    };

    // Use event delegation for items that might be added/refreshed via AJAX
    $(document).on('change', '.item-checkbox', function() {
        updateFulfillButton();
    });

    $(document).on('change', '#select-all', function() {
        $('.item-checkbox').prop('checked', $(this).prop('checked')).trigger('change');
    });

    // Run once on load to set initial state
    updateFulfillButton();

    $(document).on('input', '.deliver-qty', function() {
        let val = parseFloat($(this).val());
        if (val < 0) $(this).val(0);
    });

    // Init modal Select2 when opened
    $('#addProductModal').on('shown.bs.modal', function () {
        $('#qp-model-select, #qp-unit-select, #qp-cat-select, #qp-brand-select').select2({ tags: true, width: '100%', allowClear: true, dropdownParent: $('#addProductModal') });
        $('#qp-supplier-select').select2({ width: '100%', allowClear: true, dropdownParent: $('#addProductModal') });
        $('#pos-title-select').select2({
            placeholder: 'Search or Enter Product Name',
            allowClear: true, tags: true, width: '100%',
            dropdownParent: $('#addProductModal'),
            minimumInputLength: 2,
            ajax: {
                url: "{{route('admin.product.search-simple')}}",
                dataType: 'json', delay: 250,
                data: function(p) { return { q: p.term }; },
                processResults: function(data) { return { results: data }; },
                cache: true
            }
        });
    });

    // Sub-modal AJAX handlers
    $(document).on('submit', '#quickAddCategoryForm', function(e) {
        e.preventDefault();
        $.post("{{route('category.quick-store')}}", $(this).serialize() + "&_token={{csrf_token()}}&is_parent=1", function(res) {
            if(res.status == 'success') {
                $('#qp-cat-select').append(new Option(res.category.title, res.category.id, false, true)).trigger('change');
                $('#addCategoryModal').modal('hide');
            }
        });
    });

    $(document).on('submit', '#quickAddSupplierForm', function(e) {
        e.preventDefault();
        $.post("{{route('supplier.quick-store')}}", $(this).serialize() + "&_token={{csrf_token()}}", function(res) {
            if(res.status == 'success') {
                $('#qp-supplier-select').append(new Option(res.supplier.name + ' (' + (res.supplier.company_name || '') + ')', res.supplier.id, false, true)).trigger('change');
                $('#addSupplierModal').modal('hide');
            }
        });
    });

    $(document).on('submit', '#quickAddBrandForm', function(e) {
        e.preventDefault();
        $.post("{{route('brand.quick-store')}}", $(this).serialize() + "&_token={{csrf_token()}}", function(res) {
            if(res.status == 'success') {
                $('#qp-brand-select').append(new Option(res.brand.title, res.brand.id, false, true)).trigger('change');
                $('#addBrandModal').modal('hide');
            }
        });
    });

    $(document).on('submit', '#quickAddUnitForm', function(e) {
        e.preventDefault();
        $.post("{{route('product.store-unit')}}", $(this).serialize() + "&_token={{csrf_token()}}", function(res) {
            if(res.status == 'success') {
                $('#qp-unit-select').append(new Option(res.unit.name, res.unit.name, false, true)).trigger('change');
                $('#addUnitModal').modal('hide');
            }
        });
    });

    $(document).on('submit', '#quickAddModelForm', function(e) {
        e.preventDefault();
        $.post("{{route('product.store-model')}}", $(this).serialize() + "&_token={{csrf_token()}}", function(res) {
            if(res.status == 'success') {
                $('#qp-model-select').append(new Option(res.model.name, res.model.name, false, true)).trigger('change');
                $('#addModelModal').modal('hide');
            }
        });
    });

    // Update Item Price AJAX
    $(document).on('change', '.item-price-input', function() {
        let $input = $(this);
        let itemId = $input.data('id');
        let newPrice = $input.val();
        let $row = $input.closest('tr');

        $input.addClass('border-warning');

        $.ajax({
            url: "/admin/sales-orders/item/" + itemId + "/update-price",
            type: 'POST',
            data: {
                _token: "{{csrf_token()}}",
                price: newPrice
            },
            success: function(res) {
                if (res.status === 'success') {
                    $input.removeClass('border-warning').addClass('border-success');
                    setTimeout(() => $input.removeClass('border-success'), 2000);
                    
                    // Update item total display
                    $('#item-total-' + itemId).text('Rs. ' + parseFloat(res.item_total).toLocaleString(undefined, {minimumFractionDigits: 2}));
                    
                    // Update grand total display
                    $('#grand-total-display').text('Rs. ' + parseFloat(res.total_amount).toLocaleString(undefined, {minimumFractionDigits: 2}));
                    
                    Swal.fire({
                        icon: 'success', title: 'Price Updated', toast: true,
                        position: 'top-end', showConfirmButton: false, timer: 2000
                    });
                }
            },
            error: function(err) {
                $input.removeClass('border-warning').addClass('border-danger');
                let msg = err.responseJSON ? err.responseJSON.message : 'Error updating price';
                Swal.fire('Error', msg, 'error');
            }
        });
    });

    // Save new product
    $('#save-product-btn').on('click', function() {
        let $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> SAVING...');
        $.ajax({
            url: "{{route('product.quick-store')}}",
            type: 'POST',
            data: $('#add-product-form').serialize(),
            success: function(res) {
                if (res.status === 'success') {
                    $('#addProductModal').modal('hide');
                    $('#add-product-form')[0].reset();
                    Swal.fire('Success', 'Product added!', 'success');
                    // Add to the add-item dropdown
                    let opt = new Option(res.product.title + ' (' + (res.product.sku || 'N/A') + ')', res.product.id, true, true);
                    $(opt).attr('data-price', res.product.price);
                    $('#add-product-select').append(opt).trigger('change');
                    $('#add-price').val(res.product.price);
                }
                $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> SAVE PRODUCT');
            },
            error: function(err) {
                let msg = err.responseJSON ? err.responseJSON.message : 'Error saving product';
                Swal.fire('Error', msg, 'error');
                $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> SAVE PRODUCT');
            }
        });
    });

    // AJAX Add Item
    $('#add-item-form').on('submit', function(e) {
        e.preventDefault();
        let $form = $(this);
        let $btn = $form.find('button[type="submit"]');
        let originalHtml = $btn.html();

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> ADDING...');

        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: $form.serialize(),
            success: function(res) {
                if (res.status === 'success') {
                    // Refresh only the table container
                    let fetchUrl = window.location.href.split('#')[0];
                    $('#items-table-container').load(fetchUrl + ' #items-table-container > *', function() {
                        // Reset fulfillment button state based on new content
                        updateFulfillButton();
                        $('#select-all').prop('checked', false);
                        
                        // Show success ONLY after the table has actually updated on screen
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: res.message,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    });
                    
                    // Refresh linked POS bills history too
                    $('#linked-bills-container').load(fetchUrl + ' #linked-bills-container > *');

                    // Update Grand Total in view
                    $('#grand-total-display').text('Rs. ' + parseFloat(res.total_amount).toLocaleString(undefined, {minimumFractionDigits: 2}));
                    
                    // Reset form select
                    $('#add-product-select').val('').trigger('change');
                    $('#add-qty').val(1);
                    $('#add-price').val(0);
                }
            },
            error: function(err) {
                let msg = err.responseJSON ? err.responseJSON.message : 'Error adding item';
                Swal.fire('Error', msg, 'error');
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalHtml);
            }
        });
    });
});

function removeItem(url) {
    if (confirm('Remove this item from the order?')) {
        $('#remove-item-form').attr('action', url).submit();
    }
}
</script>

<form id="remove-item-form" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

<script>
/* ================================================================
   PHOTO PANEL & LIGHTBOX JS
================================================================ */
let panelOpen = false;

function togglePhotoPanel() {
    panelOpen = !panelOpen;
    document.getElementById('so-photo-panel').style.right = panelOpen ? '0' : '-360px';
    // shift panel toggle button slightly
    document.getElementById('photo-panel-toggle').style.right = panelOpen ? '350px' : '0';
}

function openLightbox(url, caption) {
    const lb = document.getElementById('so-lightbox');
    document.getElementById('lightbox-img').src = url;
    document.getElementById('lightbox-caption').textContent = caption;
    lb.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    document.getElementById('so-lightbox').style.display = 'none';
    document.getElementById('lightbox-img').src = '';
    document.body.style.overflow = '';
}

// Escape key closes lightbox
document.addEventListener('keydown', e => { if(e.key === 'Escape') closeLightbox(); });

// Drag over panel dropzone
const panelDz = document.getElementById('panel-dropzone');
if (panelDz) {
    panelDz.addEventListener('dragover', e => { e.preventDefault(); panelDz.style.background = '#fff3cd'; });
    panelDz.addEventListener('dragleave', () => { panelDz.style.background = 'transparent'; });
    panelDz.addEventListener('drop', e => {
        e.preventDefault();
        panelDz.style.background = 'transparent';
        panelUploadPhotos({ files: e.dataTransfer.files });
    });
}

function panelUploadPhotos(input) {
    const files = Array.from(input.files);
    if (files.length === 0) return;

    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    files.forEach(f => formData.append('order_photos[]', f));

    document.getElementById('panel-upload-progress').style.display = 'block';
    document.getElementById('panel-dropzone').style.opacity = '0.5';

    fetch('{{ route("sales-orders.photos.upload", $salesOrder->id) }}', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') {
            // Re-render photo grid from server response
            const grid = document.getElementById('panel-photos-row');
            const emptyMsg = document.getElementById('panel-empty-msg');

            if (emptyMsg) emptyMsg.remove();
            if (!grid) {
                const container = document.getElementById('panel-photo-grid');
                container.innerHTML = '<div class="row" id="panel-photos-row" style="margin:0 -4px;"></div>';
            }

            res.photos.forEach(photo => {
                if (!document.getElementById('panel-photo-col-' + photo.id)) {
                    const col = document.createElement('div');
                    col.className = 'col-6 px-1 mb-2 panel-photo-col';
                    col.id = 'panel-photo-col-' + photo.id;
                    const isPdf = photo.url.endsWith('.pdf');
                    col.innerHTML = `
                        <div class="card border shadow-sm" style="border-radius:8px;overflow:hidden;cursor:pointer;"
                             onclick="openLightbox('${photo.url}','${photo.original_name}')">
                            <div style="height:90px;display:flex;align-items:center;justify-content:center;background:#f8f9fa;position:relative;">
                                ${isPdf
                                    ? '<i class="fas fa-file-pdf fa-2x text-danger"></i>'
                                    : `<img src="${photo.url}" style="max-height:90px;max-width:100%;object-fit:cover;width:100%;" loading="lazy">`
                                }
                                <button onclick="event.stopPropagation(); panelDeletePhoto(${photo.id},'');"
                                    style="position:absolute;top:3px;right:3px;background:rgba(220,53,69,0.85);border:none;border-radius:50%;width:20px;height:20px;color:white;font-size:10px;line-height:1;cursor:pointer;">
                                    &times;
                                </button>
                            </div>
                            <div class="p-1 text-center" style="background:#fff;">
                                <p class="mb-0 text-truncate" style="font-size:9px;" title="${photo.original_name}">${photo.original_name}</p>
                                <p class="mb-0 text-muted" style="font-size:8px;">${photo.human_size}</p>
                            </div>
                        </div>`;
                    document.getElementById('panel-photos-row').appendChild(col);
                }
            });

            // Update badge count
            document.getElementById('photo-panel-badge').textContent = res.photos.length;

            Swal.fire({ icon:'success', title:'Photos Uploaded', toast:true, position:'top-end', showConfirmButton:false, timer:2500 });
        } else {
            Swal.fire('Error', res.message || 'Upload failed', 'error');
        }
    })
    .catch(() => Swal.fire('Error', 'Network error during upload', 'error'))
    .finally(() => {
        document.getElementById('panel-upload-progress').style.display = 'none';
        document.getElementById('panel-dropzone').style.opacity = '1';
        document.getElementById('panel-photo-input').value = '';
    });
}

function panelDeletePhoto(photoId, deleteUrl) {
    if (!confirm('Delete this photo? This cannot be undone.')) return;

    // Build delete URL if not provided (for dynamically added photos)
    if (!deleteUrl) {
        deleteUrl = `/admin/sales-orders/{{ $salesOrder->id }}/photos/${photoId}`;
    }

    fetch(deleteUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: '_token={{ csrf_token() }}&_method=DELETE'
    })
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') {
            const col = document.getElementById('panel-photo-col-' + photoId);
            if (col) col.remove();

            // Update count
            const remaining = document.querySelectorAll('.panel-photo-col').length;
            document.getElementById('photo-panel-badge').textContent = remaining;

            if (remaining === 0) {
                document.getElementById('panel-photo-grid').innerHTML =
                    '<div class="text-center py-4 text-muted" id="panel-empty-msg">' +
                    '<i class="fas fa-camera fa-2x mb-2 d-block opacity-50"></i>' +
                    '<p class="small mb-0">No photos yet.</p></div>';
            }

            Swal.fire({ icon:'success', title:'Photo Deleted', toast:true, position:'top-end', showConfirmButton:false, timer:2000 });
        } else {
            Swal.fire('Error', 'Could not delete photo', 'error');
        }
    })
    .catch(() => Swal.fire('Error', 'Network error', 'error'));
}
</script>
@endpush
