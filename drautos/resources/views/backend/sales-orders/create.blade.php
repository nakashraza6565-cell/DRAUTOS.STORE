@extends('backend.layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Create New Sale Order</h6>
    </div>
    <div class="card-body">
        @include('backend.layouts.notification')
        <form method="post" action="{{route('sales-orders.store')}}" enctype="multipart/form-data" id="so-create-form">
            {{csrf_field()}}

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="user_id" class="col-form-label font-weight-bold mb-0">Customer <span class="text-danger">*</span></label>
                            <button type="button" class="btn btn-link btn-sm p-0 text-primary font-weight-bold" data-toggle="modal" data-target="#addCustomerModal">
                                <i class="fas fa-plus-circle"></i> New Customer
                            </button>
                        </div>
                        <select name="user_id" id="user_id" class="form-control select2" required>
                            <option value="">--Select Customer--</option>
                            @foreach($customers as $customer)
                            <option value="{{$customer->id}}" data-phone="{{$customer->phone}}" data-balance="{{number_format($customer->current_balance, 2)}}" data-name="{{$customer->name}}">
                                {{$customer->name}} ({{$customer->phone}})
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6" id="customer-info-card" style="display:none;">
                    <div class="p-2 border rounded bg-white shadow-sm d-flex justify-content-between align-items-center mt-2">
                        <div>
                            <div class="small text-muted font-weight-bold text-uppercase">Customer Record</div>
                            <div id="c-name" class="font-weight-bold text-primary"></div>
                            <div id="c-phone" class="small text-muted"></div>
                        </div>
                        <div class="text-right">
                            <div class="small text-muted font-weight-bold text-uppercase">Ledger Balance</div>
                            <div id="c-balance" class="h5 mb-0 font-weight-bold text-danger"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 bg-light mb-4">
                <div class="card-body">
                    <h6 class="font-weight-bold mb-3">Order Items & Consolidation</h6>
                    <div id="items-container">
                        <!-- Add Item Row -->
                        <div class="item-row row mb-2 align-items-end">
                            <div class="col-md-5">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="small font-weight-bold mb-0">New Item Selection</label>
                                    <button type="button" class="btn btn-link btn-sm p-0 text-primary font-weight-bold" data-toggle="modal" data-target="#addProductModal">
                                        <i class="fas fa-plus-circle"></i> New Product
                                    </button>
                                </div>
                                <select class="form-control select2 product-select">
                                    <option value="">--Select Product--</option>
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
                                <label class="small font-weight-bold">Quantity</label>
                                <input type="number" class="form-control qty-input" value="1" min="0.1" step="any">
                            </div>
                            <div class="col-md-3">
                                <label class="small font-weight-bold">Price</label>
                                <input type="number" class="form-control price-input" value="0" min="0" step="0.01">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-primary btn-block add-item"><i class="fas fa-plus"></i> ADD</button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mt-3">
                        <table class="table table-sm table-white bg-white rounded shadow-sm" id="added-items-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>Product / Description</th>
                                    <th width="100">Qty</th>
                                    <th width="150">Price</th>
                                    <th width="150">Total</th>
                                    <th width="50"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Items will be populated here -->
                            </tbody>
                            <tfoot>
                                <tr class="font-weight-bold bg-light">
                                    <td colspan="3" class="text-right border-0">Consolidated Grand Total:</td>
                                    <td id="grand-total" class="border-0">Rs. 0.00</td>
                                    <td class="border-0"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>


            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="note" class="col-form-label">Internal Order Note</label>
                        <textarea class="form-control" id="note" name="note" rows="2" placeholder="Add any special instructions for this order...">{{old('note')}}</textarea>
                    </div>
                </div>
            </div>

            {{-- ===== PHOTO UPLOAD SECTION ===== --}}
            <div class="card border-warning mb-4" id="photo-upload-section">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center py-2">
                    <span class="font-weight-bold"><i class="fas fa-camera mr-2"></i>Order Photos <small class="text-muted font-weight-normal">(Evidence / Reference — max 10 photos, 20MB each)</small></span>
                    <span class="badge badge-dark" id="photo-count-badge">0 / 10</span>
                </div>
                <div class="card-body p-3">
                    {{-- Dropzone --}}
                    <div id="so-photo-dropzone"
                         style="border: 2px dashed #ffc107; border-radius: 10px; padding: 28px 20px; text-align: center; cursor: pointer; background: #fffdf0; transition: background 0.2s;"
                         onclick="document.getElementById('order_photos_input').click()">
                        <i class="fas fa-cloud-upload-alt fa-2x text-warning mb-2"></i>
                        <p class="mb-1 font-weight-bold text-muted">Tap to upload photos</p>
                        <p class="small text-muted mb-0">JPEG, PNG, WEBP, HEIC or PDF &bull; Up to 10 files &bull; 20 MB each</p>
                        <input type="file" id="order_photos_input" name="order_photos[]" multiple accept="image/*,.pdf"
                               style="display:none;" onchange="handlePhotoSelect(this)">
                    </div>

                    {{-- Preview grid --}}
                    <div id="photo-preview-grid" class="row mt-3" style="display:none;">
                        {{-- Thumbnails added by JS --}}
                    </div>
                    <p id="photo-hint" class="small text-success mt-2 mb-0" style="display:none;">
                        <i class="fas fa-check-circle mr-1"></i>
                        Photos attached — you can save the order with just these photos and add items later.
                    </p>
                </div>
            </div>

            <div class="form-group mb-3">
                <button type="submit" class="btn btn-success px-5 shadow-sm font-weight-bold" id="submit-order" disabled>
                    <i class="fas fa-save mr-1"></i> SAVE SALE ORDER
                </button>
                <a href="{{route('sales-orders.index')}}" class="btn btn-secondary px-4 ml-2">Back</a>
                <span id="submit-hint" class="small text-muted ml-3">Add items or upload photos to enable save</span>
            </div>
        </form>

    </div>
</div>

<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog" style="z-index: 9999;">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold">Add New Customer</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body bg-light">
                <form id="add-customer-form">
                    @csrf
                    <div class="form-group">
                        <label class="small font-weight-bold">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-bold">Customer Type</label>
                        <select name="customer_type" class="form-control">
                            <option value="retail">Retail Customer</option>
                            <option value="wholesale">Wholesale Customer</option>
                            <option value="salesman">Salesman</option>
                            <option value="walkin">Walk-in Customer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-bold">Phone</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-bold">City</label>
                        <select name="city" id="pos-city-select" class="form-control">
                            <option value="">Select or Type City</option>
                            @foreach($cities as $c)
                                <option value="{{$c}}">{{$c}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-bold">Address</label>
                        <textarea name="address" class="form-control" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 p-3 bg-light">
                <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary px-4 shadow" id="save-customer-btn">Save Customer</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 9999;">
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
            <div class="modal-footer border-0 p-3 bg-light">
                <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary px-4 shadow" id="save-product-btn">
                    <i class="fas fa-save mr-1"></i> SAVE PRODUCT
                </button>
            </div>
        </div>
    </div>
</div>

@include('backend.product.partials.modals')

@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Fix for modal backdrop issue: move modals to body
        $('#addProductModal, #addCategoryModal, #addBrandModal, #addSupplierModal, #addUnitModal, #addModelModal').appendTo('body');

        $('.select2').select2();

        @if(isset($selectedUserId))
        $('#user_id').val('{{$selectedUserId}}').trigger('change');
        @endif

        let itemsCount = 0;

        $('.product-select').on('change', function() {
            let productId = $(this).val();
            let userId = $('#user_id').val();
            let $priceInput = $('.price-input');

            if (productId && userId) {
                // Smart Price Fetching for B2B
                $.get("{{ route('sales-orders.get-price') }}", { customer_id: userId, product_id: productId }, function(res) {
                    if (res.success) {
                        $priceInput.val(res.price);
                        if (res.source === 'history') {
                            $priceInput.parent().find('.small').remove();
                            $priceInput.after('<div class="small text-success"><i class="fas fa-history mr-1"></i> Historical Customer Price</div>');
                        } else {
                            $priceInput.parent().find('.small').remove();
                            $priceInput.after('<div class="small text-muted"><i class="fas fa-tag mr-1"></i> Default Selling Price</div>');
                        }
                    }
                });
            } else {
                let price = $(this).find(':selected').data('price') || 0;
                $priceInput.val(price);
                $priceInput.parent().find('.small').remove();
            }
        });

        $('.add-item').on('click', function() {
            let productSelect = $('.product-select');
            let productId = productSelect.val();
            let productName = productSelect.find(':selected').text();
            let qty = parseFloat($('.qty-input').val());
            let price = parseFloat($('.price-input').val());

            if (!productId || qty <= 0) {
                alert('Please select a product and valid quantity');
                return;
            }

            addItemToTable(productId, productName, qty, price);

            // Reset inputs
            productSelect.val('').trigger('change');
            $('.qty-input').val(1);
            $('.price-input').val(0);
        });

        $('#user_id').on('change', function() {
            let userId = $(this).val();
            let $opt = $(this).find(':selected');

            if (!userId) {
                $('#customer-info-card').hide();
                return;
            }

            // Update Customer Card
            $('#c-name').text($opt.data('name'));
            $('#c-phone').text($opt.data('phone'));
            $('#c-balance').text('Rs. ' + $opt.data('balance'));
            $('#customer-info-card').fadeIn();

            // Clear existing items when customer changes to avoid mismatch
            $('#added-items-table tbody').empty();
            itemsCount = 0;
            updateGrandTotal();
            $('#submit-order').prop('disabled', true);

            Swal.fire({
                title: 'Checking pending items...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.get("/admin/sales-orders/pending-items/" + userId, function(data) {
                Swal.close();
                if (data.length > 0) {
                    let addedIds = [];
                    data.forEach(function(item) {
                        // Avoid duplicates if somehow triggered twice
                        if (!addedIds.includes(item.product_id)) {
                            addItemToTable(item.product_id, item.product_title, item.quantity, item.price, true);
                            addedIds.push(item.product_id);
                        }
                    });

                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 4000
                    });
                    Toast.fire({
                        icon: 'info',
                        title: data.length + ' pending items consolidated'
                    });
                }
            });
        });

        function addItemToTable(productId, productName, qty, price, isCarryForward = false) {
            let total = qty * price;
            let row = `
                <tr class="item-added-row">
                    <td class="align-middle">
                        <input type="hidden" name="items[${itemsCount}][product_id]" value="${productId}">
                        <div class="font-weight-bold">${productName}</div>
                        ${isCarryForward ? '<span class="badge badge-warning"><i class="fas fa-history mr-1"></i> Carried Forward from previous order</span>' : ''}
                    </td>
                    <td class="align-middle">
                        <input type="hidden" name="items[${itemsCount}][quantity]" value="${qty}">
                        ${qty}
                    </td>
                    <td class="align-middle">
                        <input type="hidden" name="items[${itemsCount}][price]" value="${price}">
                        Rs. ${parseFloat(price).toFixed(2)}
                    </td>
                    <td class="row-total align-middle font-weight-bold" data-value="${total}">
                        Rs. ${total.toFixed(2)}
                    </td>
                    <td class="align-middle text-center">
                        <button type="button" class="btn btn-link text-danger p-0 remove-item"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;

            $('#added-items-table tbody').append(row);
            itemsCount++;
            updateGrandTotal();
            checkSubmitEnable();
        }

        $(document).on('click', '.remove-item', function() {
            $(this).closest('tr').remove();
            updateGrandTotal();
            checkSubmitEnable();
        });

        function updateGrandTotal() {
            let grandTotal = 0;
            $('.row-total').each(function() {
                grandTotal += parseFloat($(this).data('value'));
            });
            $('#grand-total').text('Rs. ' + grandTotal.toLocaleString(undefined, {
                minimumFractionDigits: 2
            }));
        }

        /* ================================================================
           PHOTO SELECTION & PREVIEW
        ================================================================ */
        let selectedPhotoFiles = [];   // track DataTransfer-style array

        window.handlePhotoSelect = function(input) {
            const MAX = 10;
            const newFiles = Array.from(input.files);
            const remaining = MAX - selectedPhotoFiles.length;
            const toAdd = newFiles.slice(0, remaining);

            if (newFiles.length > remaining) {
                alert('Maximum 10 photos allowed. Only the first ' + remaining + ' photo(s) were added.');
            }

            toAdd.forEach(file => {
                selectedPhotoFiles.push(file);
                renderPhotoThumb(file, selectedPhotoFiles.length - 1);
            });

            syncFileInputToForm();
            refreshPhotoUI();
        };

        function renderPhotoThumb(file, idx) {
            const grid = document.getElementById('photo-preview-grid');
            const isPDF = file.type === 'application/pdf';
            const sizeKB = (file.size / 1024).toFixed(0);

            const col = document.createElement('div');
            col.className = 'col-6 col-sm-4 col-md-3 mb-3';
            col.id = 'photo-thumb-' + idx;
            col.innerHTML = `
                <div class="card border shadow-sm h-100" style="border-radius:8px; overflow:hidden;">
                    <div style="height:100px; display:flex; align-items:center; justify-content:center; background:#f8f9fa; position:relative;">
                        ${isPDF
                            ? '<i class="fas fa-file-pdf fa-3x text-danger"></i>'
                            : '<img src="" class="photo-preview-img" style="max-height:100px;max-width:100%;object-fit:contain;" />'
                        }
                        <button type="button" onclick="removePhoto(${idx})"
                            style="position:absolute;top:4px;right:4px;background:rgba(220,53,69,0.85);border:none;border-radius:50%;width:22px;height:22px;color:white;font-size:11px;line-height:1;cursor:pointer;">
                            &times;
                        </button>
                    </div>
                    <div class="card-body p-1 text-center">
                        <p class="mb-0 small text-truncate" style="max-width:100%;font-size:10px;" title="${file.name}">${file.name}</p>
                        <p class="mb-0 text-muted" style="font-size:9px;">${sizeKB} KB</p>
                    </div>
                </div>`;

            if (!isPDF) {
                const reader = new FileReader();
                reader.onload = e => {
                    const img = col.querySelector('.photo-preview-img');
                    if (img) img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }

            grid.appendChild(col);
        }

        window.removePhoto = function(idx) {
            selectedPhotoFiles.splice(idx, 1);
            // Rebuild entire preview grid
            const grid = document.getElementById('photo-preview-grid');
            grid.innerHTML = '';
            selectedPhotoFiles.forEach((f, i) => renderPhotoThumb(f, i));
            syncFileInputToForm();
            refreshPhotoUI();
        };

        // Re-build the file input using DataTransfer so the form submits real files
        function syncFileInputToForm() {
            try {
                const dt = new DataTransfer();
                selectedPhotoFiles.forEach(f => dt.items.add(f));
                document.getElementById('order_photos_input').files = dt.files;
            } catch(e) {
                // DataTransfer not supported in older browsers; files submitted anyway
            }
        }

        function refreshPhotoUI() {
            const count = selectedPhotoFiles.length;
            $('#photo-count-badge').text(count + ' / 10');
            $('#photo-preview-grid').toggle(count > 0);
            $('#photo-hint').toggle(count > 0);

            // Dropzone highlight color
            if (count >= 10) {
                $('#so-photo-dropzone').css('border-color', '#28a745').css('background', '#f0fff4');
            } else {
                $('#so-photo-dropzone').css('border-color', '#ffc107').css('background', '#fffdf0');
            }

            checkSubmitEnable();
        }

        // Drag-and-drop support for the dropzone
        const dz = document.getElementById('so-photo-dropzone');
        if (dz) {
            dz.addEventListener('dragover', e => { e.preventDefault(); dz.style.background = '#fff3cd'; });
            dz.addEventListener('dragleave', () => { dz.style.background = '#fffdf0'; });
            dz.addEventListener('drop', e => {
                e.preventDefault();
                dz.style.background = '#fffdf0';
                const fake = { files: e.dataTransfer.files };
                handlePhotoSelect(fake);
            });
        }

        /* Enable submit when items > 0 OR photos > 0 */
        function checkSubmitEnable() {
            const hasItems  = $('#added-items-table tbody tr').length > 0;
            const hasPhotos = selectedPhotoFiles.length > 0;
            const canSubmit = hasItems || hasPhotos;
            $('#submit-order').prop('disabled', !canSubmit);

            if (canSubmit) {
                $('#submit-hint').hide();
            } else {
                $('#submit-hint').show();
            }
        }

        // --- Quick Add Product Logic ---

        // Initialize Select2 for Add Customer Modal
        $('#addCustomerModal').on('shown.bs.modal', function() {
            $('#pos-city-select').select2({
                placeholder: "Select or Type City",
                allowClear: true,
                tags: true,
                width: '100%',
                dropdownParent: $('#addCustomerModal')
            });
        });

        // Initialize Select2 for Add Product Modal
        $('#addProductModal').on('shown.bs.modal', function() {
            $('#pos-model-select, #pos-unit-select, #pos-cat-select, #pos-brand-select').select2({
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
                    url: "{{route('admin.product.search-simple')}}",
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
        });

        // Sub-modal AJAX handlers
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

        $('#save-product-btn').on('click', function() {
            let form = $('#add-product-form');
            let formData = form.serialize();
            let $btn = $(this);

            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> SAVING...');

            $.ajax({
                url: "{{route('product.quick-store')}}",
                type: "POST",
                data: formData,
                success: function(res) {
                    if (res.status === 'success') {
                        $('#addProductModal').modal('hide');
                        form[0].reset();
                        Swal.fire('Success', 'Product added successfully!', 'success');

                        // Add to the main product-select dropdown
                        let newOption = new Option(res.product.title + ' (SKU: ' + (res.product.sku || 'N/A') + ')', res.product.id, false, true);
                        $(newOption).attr('data-price', res.product.price);
                        $('.product-select').append(newOption).trigger('change');
                    }
                    $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> SAVE PRODUCT');
                },
                error: function(err) {
                    let msg = err.responseJSON ? err.responseJSON.message : 'Error adding product';
                    Swal.fire('Error', msg, 'error');
                    $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> SAVE PRODUCT');
                }
            });
        });
        $('#save-customer-btn').on('click', function() {
            let form = $('#add-customer-form');
            let $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');
            
            $.ajax({
                url: "{{route('users.direct-store')}}", 
                type: "POST",
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                data: form.serialize() + "&role=user&status=active&password=password123", 
                dataType: "json",
                success: function(response) {
                    if (typeof response === 'string') {
                        try { response = JSON.parse(response); } catch(e) {}
                    }
                    let user = response.user || response.data || response;
                    let name = user.name || 'Unknown';
                    let phone = user.phone || 'N/A';
                    let balance = 0.00;

                    let displayText = name + ' (' + phone + ')';
                    let newOption = new Option(displayText, user.id, true, true);
                    $(newOption).attr('data-phone', phone);
                    $(newOption).attr('data-balance', balance.toFixed(2));
                    $(newOption).attr('data-name', name);
                    
                    $('#user_id').append(newOption).trigger('change');
                    $('#addCustomerModal').modal('hide');
                    form[0].reset();
                    Swal.fire('Success', 'Customer Added Successfully!', 'success');
                    $btn.prop('disabled', false).html('Save Customer');
                },
                error: function(err) {
                   console.log(err);
                   let errorMsg = 'Unknown Error';
                   if(err.status === 422) {
                       let errors = err.responseJSON.errors;
                       errorMsg = Object.values(errors).flat().join('\n');
                   } else if(err.responseJSON && err.responseJSON.message) {
                       errorMsg = err.responseJSON.message;
                   } else if(err.responseText) {
                       errorMsg = "Server Error: " + err.responseText.substring(0, 150);
                   } else {
                       errorMsg = "HTTP Status: " + err.status;
                   }
                   Swal.fire('Error', errorMsg, 'error');
                   $btn.prop('disabled', false).html('Save Customer');
                }
            });
        });
    });
</script>
@endpush
