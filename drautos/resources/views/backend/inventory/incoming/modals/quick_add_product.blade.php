<!-- Quick Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 25px; overflow: hidden;">
            <div class="modal-header py-4 border-0" style="background: #f97316;">
                <h5 class="modal-title font-weight-bold text-white" style="font-size: 1.2rem;">Add Quick Product</h5>
                <button type="button" class="close text-white opacity-10" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
                </button>
            </div>
            <form id="quickAddProductForm">
                @csrf
                <div class="modal-body p-4 bg-white">
                    <!-- PRODUCT TITLE -->
                    <div class="form-group mb-4">
                        <label class="premium-label">PRODUCT TITLE (SEARCH TO AVOID DUPLICATES) <span class="text-danger">*</span></label>
                        <select name="title" id="qa-title-select" class="premium-input form-control select2-tags" required>
                            <option value="">Search or Enter Product Name</option>
                        </select>
                    </div>

                    <div class="row">
                        <!-- CATEGORY -->
                        <div class="col-md-6 mb-4">
                            <label class="premium-label">CATEGORY <span class="text-danger">*</span></label>
                            <select name="cat_id" id="qa-cat-select" class="premium-input form-control" required>
                                <option value="">Select or Type</option>
                                @foreach(\App\Models\Category::where('status','active')->get() as $cat)
                                    <option value="{{$cat->id}}">{{$cat->title}}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn-action-plus mt-2" style="background: #f97316;" data-toggle="modal" data-target="#addCategoryModal">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>

                        <!-- BRAND -->
                        <div class="col-md-6 mb-4">
                            <label class="premium-label">BRAND</label>
                            <select name="brand_id" id="qa-brand-select" class="premium-input form-control">
                                <option value="">Select or Type</option>
                                @foreach(\App\Models\Brand::where('status','active')->get() as $brand)
                                    <option value="{{$brand->id}}">{{$brand->title}}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn-action-plus mt-2" style="background: #22d3ee;" data-toggle="modal" data-target="#addBrandModal">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row">
                        <!-- MODEL -->
                        <div class="col-md-6 mb-4">
                            <label class="premium-label">MODEL</label>
                            <select name="model" id="qa-model-select" class="premium-input form-control">
                                <option value="">Select or Type</option>
                                @foreach(\App\Models\ProductModel::all() as $m)
                                    <option value="{{$m->name}}">{{$m->name}}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn-action-plus mt-2" style="background: #fbbf24;" data-toggle="modal" data-target="#addModelModal">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>

                        <!-- UNIT -->
                        <div class="col-md-6 mb-4">
                            <label class="premium-label">UNIT / PACKAGING</label>
                            <select name="unit" id="qa-unit-select" class="premium-input form-control">
                                <option value="piece">Piece</option>
                                @foreach(\App\Models\Unit::all() as $u)
                                    <option value="{{$u->name}}">{{$u->name}}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn-action-plus mt-2" style="background: #f97316;" data-toggle="modal" data-target="#addUnitModal">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="premium-label">PURCHASE PRICE</label>
                            <input type="number" name="purchase_price" step="0.01" class="premium-input form-control" placeholder="0.00">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="premium-label">RETAIL PRICE</label>
                            <input type="number" name="price" step="0.01" class="premium-input form-control" placeholder="0.00">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 bg-white justify-content-between">
                    <button type="button" class="btn btn-secondary px-5" data-dismiss="modal" style="border-radius: 100px; height: 55px; background: #94a3b8; border: none; font-weight: 700;">Cancel</button>
                    <button type="submit" id="save-product-btn-qa" class="btn btn-orange px-5 shadow-lg" style="border-radius: 100px; height: 55px; font-weight: 700;">
                        <i class="fas fa-lock mr-2"></i> SAVE PRODUCT
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .premium-label {
        font-weight: 800;
        font-size: 0.7rem;
        color: #475569;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
        display: block;
        text-transform: uppercase;
    }
    .premium-input {
        border-radius: 12px !important;
        border: 1px solid #e2e8f0 !important;
        padding: 12px 18px !important;
        height: 50px !important;
        font-weight: 500 !important;
        color: #1e293b !important;
        background: #fff !important;
    }
    .btn-action-plus {
        width: 45px;
        height: 35px;
        border: none;
        border-radius: 8px;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        transition: all 0.2s ease;
    }
    .btn-action-plus:hover { transform: scale(1.05); filter: brightness(1.1); }
    .btn-orange {
        background: #f97316 !important;
        color: #fff !important;
        border: none;
    }
    .btn-orange:hover { background: #ea580c !important; }
    
    /* Select2 Unification */
    .select2-container--default .select2-selection--single {
        border-radius: 12px !important;
        height: 50px !important;
        border: 1px solid #e2e8f0 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 50px !important;
        padding-left: 18px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 48px !important;
    }
</style>

<script>
$(document).ready(function() {
    // Initialize Select2 with tags for Title
    $('#qa-title-select').select2({
        tags: true,
        placeholder: "Search or Enter Product Name",
        width: '100%',
        dropdownParent: $('#addProductModal'),
        ajax: {
            url: "{{ route('pos.search-products') }}",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { q: params.term };
            },
            processResults: function (data) {
                return {
                    results: data.map(function (item) {
                        return { id: item.title, text: item.title };
                    })
                };
            },
            cache: true
        }
    });

    $('#quickAddProductForm').on('submit', function(e) {
        e.preventDefault();
        let $form = $(this);
        let $btn = $('#save-product-btn-qa');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> SAVING...');

        $.ajax({
            url: "{{ route('product.quick-store') }}",
            type: "POST",
            data: $form.serialize() + "&status=active",
            success: function(res) {
                if(res.status === 'success') {
                    let response = res.product;
                    // Add to dropdowns in the parent page
                    $('.product-select').each(function() {
                        let newOption = new Option(response.title + ' (' + (response.sku || '') + ')', response.id, false, false);
                        $(newOption).attr('data-cost', response.purchase_price || 0);
                        $(this).append(newOption).trigger('change');
                    });

                    $('#addProductModal').modal('hide');
                    $form[0].reset();
                    Swal.fire('Success', 'Product Added Successfully!', 'success');
                }
                $btn.prop('disabled', false).html('<i class="fas fa-lock mr-2"></i> SAVE PRODUCT');
            },
            error: function(err) {
                $btn.prop('disabled', false).html('<i class="fas fa-lock mr-2"></i> SAVE PRODUCT');
                let msg = err.responseJSON && err.responseJSON.message ? err.responseJSON.message : 'Error creating product';
                Swal.fire('Error', msg, 'error');
            }
        });
    });
});
</script>
