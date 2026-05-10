@extends('backend.layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3" style="background: #f8fafc;">
        <h6 class="m-0 font-weight-bold text-primary">Create Purchase Order (Request)</h6>
    </div>
    <div class="card-body">
        @include('backend.layouts.notification')
        <form method="post" action="{{route('purchase-orders.store')}}">
            {{csrf_field()}}

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="supplier_id" class="col-form-label font-weight-bold">Supplier <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <select name="supplier_id" id="supplier_id" class="form-control select2" required>
                                <option value="">-- Select Supplier --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{$supplier->id}}">{{$supplier->name}} ({{$supplier->company_name}})</option>
                                @endforeach
                            </select>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addSupplierModal"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="order_date" class="col-form-label font-weight-bold">Order Date <span class="text-danger">*</span></label>
                        <input id="order_date" type="date" name="order_date" value="{{date('Y-m-d')}}" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="card border-0 bg-light mb-4 mt-3">
                <div class="card-body">
                    <h6 class="font-weight-bold mb-3">Add Products to Request</h6>
                    <div id="items-container">
                        <div class="item-row row mb-2 align-items-end">
                            <div class="col-md-7">
                                <label class="small font-weight-bold mb-1">Search Product</label>
                                <div class="input-group">
                                    <select class="form-control select2 product-select">
                                        <option value="">--Select Product--</option>
                                        @foreach($products as $product)
                                        <option value="{{$product->id}}" data-unit="{{$product->unit}}">
                                            {{$product->title}} 
                                            @if($product->brand) | {{$product->brand->title}} @endif
                                            | Rs. {{number_format($product->purchase_price, 0)}}
                                            @if($product->sku) ({{$product->sku}}) @endif
                                        </option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#addProductModal"><i class="fas fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="small font-weight-bold mb-1">Quantity</label>
                                <div class="input-group">
                                    <input type="number" class="form-control qty-input" value="1" min="0.1" step="any">
                                    <div class="input-group-append">
                                        <span class="input-group-text unit-display" style="min-width: 60px;">Unit</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-primary btn-block add-item"><i class="fas fa-plus"></i> ADD</button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mt-3">
                        <table class="table table-sm bg-white rounded shadow-sm" id="added-items-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>Product / Description</th>
                                    <th width="150" class="text-right">Quantity</th>
                                    <th width="50"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Items populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="notes" class="col-form-label font-weight-bold">Notes / Special Instructions</label>
                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Enter any specific details about this order..."></textarea>
            </div>

            <div class="form-group mb-0 text-right">
                <button type="submit" class="btn btn-success px-5 shadow-sm font-weight-bold" id="submit-order" disabled>SAVE PURCHASE ORDER</button>
                <a href="{{route('purchase-orders.index')}}" class="btn btn-secondary px-4 ml-2">Cancel</a>
            </div>
        </form>
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
                                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addCategoryModal"><i class="fas fa-plus"></i></button>
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
                                        <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#addBrandModal"><i class="fas fa-plus"></i></button>
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
                                        <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#addModelModal"><i class="fas fa-plus"></i></button>
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
                                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addUnitModal"><i class="fas fa-plus"></i></button>
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold">Purchase Price</label>
                                <input type="number" name="purchase_price" class="form-control" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold">Selling Price <span class="text-danger">*</span></label>
                                <input type="number" name="price" class="form-control" required placeholder="0.00">
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
<style>
    .select2-container .select2-selection--single { height: 38px !important; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 38px !important; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px !important; }
    .modal-backdrop { z-index: 1040 !important; }
    .modal { z-index: 1050 !important; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Fix for modal backdrop issue: move modals to body
        $('#addProductModal, #addCategoryModal, #addBrandModal, #addSupplierModal, #addUnitModal, #addModelModal').appendTo('body');

        $('.select2').select2({ width: '100%' });

        let itemsCount = 0;

        $('.product-select').on('change', function() {
            let unit = $(this).find(':selected').data('unit') || 'Unit';
            $('.unit-display').text(unit);
        });

        $('.add-item').on('click', function() {
            let productSelect = $('.product-select');
            let productId = productSelect.val();
            let productName = productSelect.find(':selected').text();
            let unit = productSelect.find(':selected').data('unit') || '';
            let qty = parseFloat($('.qty-input').val());

            if (!productId || qty <= 0) {
                alert('Please select a product and valid quantity');
                return;
            }

            // Check if item already exists
            let existing = false;
            $('#added-items-table tbody tr').each(function() {
                if($(this).find('input[name*="product_id"]').val() == productId) {
                    existing = true;
                    return false;
                }
            });

            if(existing) {
                alert('Product already added to list');
                return;
            }

            addItemToTable(productId, productName, qty, unit);

            // Reset inputs
            productSelect.val('').trigger('change');
            $('.qty-input').val(1);
            $('.unit-display').text('Unit');
        });

        function addItemToTable(productId, productName, qty, unit) {
            let row = `
                <tr class="item-added-row">
                    <td class="align-middle">
                        <input type="hidden" name="product_id[]" value="${productId}">
                        <div class="font-weight-bold text-gray-900">${productName}</div>
                    </td>
                    <td class="align-middle" width="180">
                        <div class="input-group input-group-sm">
                            <input type="number" name="quantity[]" value="${qty}" class="form-control font-weight-bold text-right" min="0.1" step="any" required>
                            <div class="input-group-append">
                                <span class="input-group-text small">${unit}</span>
                            </div>
                        </div>
                    </td>
                    <td class="align-middle text-center">
                        <button type="button" class="btn btn-link text-danger p-0 remove-item"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;

            $('#added-items-table tbody').append(row);
            itemsCount++;
            $('#submit-order').prop('disabled', false);
        }

        $(document).on('click', '.remove-item', function() {
            $(this).closest('tr').remove();
            if ($('#added-items-table tbody tr').length === 0) {
                $('#submit-order').prop('disabled', true);
            }
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
                        return { q: params.term };
                    },
                    processResults: function(data) {
                        return { results: data };
                    },
                    cache: true
                }
            });
        });

        // AJAX handlers for modals
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
                    // Update the main supplier dropdown
                    let newOption = new Option(res.supplier.name + ' (' + (res.supplier.company_name || '') + ')', res.supplier.id, true, true);
                    $('#supplier_id').append(newOption).trigger('change');
                    
                    // Update the modal's supplier dropdown if it exists
                    $('#pos-supplier-select').append(new Option(res.supplier.name + ' (' + (res.supplier.company_name || '') + ')', res.supplier.id, false, true)).trigger('change');
                    
                    $('#addSupplierModal').modal('hide');
                    Swal.fire('Success', 'Supplier Added Successfully!', 'success');
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
                        let displayText = res.product.title;
                        if(res.product.brand_name) displayText += ' | ' + res.product.brand_name;
                        displayText += ' | Rs. ' + parseFloat(res.product.purchase_price || 0).toLocaleString();
                        if(res.product.sku) displayText += ' (' + res.product.sku + ')';

                        let newOption = new Option(displayText, res.product.id, false, true);
                        $(newOption).attr('data-unit', res.product.unit);
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
    });
</script>
@endpush
