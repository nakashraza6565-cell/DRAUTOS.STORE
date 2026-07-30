<!-- Quick Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        {{-- NOTE: NO overflow:hidden here — it blocks mobile scrolling --}}
        <div class="modal-content border-0 shadow-lg" style="border-radius: 25px;">
            <div class="modal-header py-3 border-0" style="background: #f97316; border-radius: 25px 25px 0 0; flex-shrink:0;">
                <h5 class="modal-title font-weight-bold text-white" style="font-size: 1.1rem;">Add Quick Product</h5>
                <button type="button" class="close text-white opacity-10" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
                </button>
            </div>
            <form id="quickAddProductForm">
                @csrf
                <div class="modal-body p-4 bg-white" style="overflow-y:auto; -webkit-overflow-scrolling:touch;">
                    <!-- PRODUCT TITLE -->
                    <div class="form-group mb-4">
                        <label class="premium-label">PRODUCT TITLE (SEARCH TO AVOID DUPLICATES) <span class="text-danger">*</span></label>
                        <select name="title" id="qa-title-select" class="premium-input form-control select2-tags" required>
                            <option value="">Search or Enter Product Name</option>
                        </select>
                    </div>

                    <div class="row">
                        <!-- CATEGORY -->
                        <div class="col-6 mb-3">
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
                        <div class="col-6 mb-3">
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
                        <div class="col-6 mb-3">
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
                        <div class="col-6 mb-3">
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
                        <!-- INITIAL STOCK -->
                        <div class="col-6 mb-3">
                            <label class="premium-label">INITIAL STOCK <span class="text-danger">*</span></label>
                            <input type="number" name="stock" class="premium-input form-control" value="0" required>
                        </div>

                        <!-- PURCHASE PRICE -->
                        <div class="col-6 mb-3">
                            <label class="premium-label">PURCHASE PRICE</label>
                            <input type="number" name="purchase_price" step="0.01" class="premium-input form-control" placeholder="0.00">
                        </div>
                    </div>

                    <div class="row">
                        <!-- SELLING PRICE -->
                        <div class="col-6 mb-3">
                            <label class="premium-label">SELLING PRICE <span class="text-danger">*</span></label>
                            <input type="number" name="price" step="0.01" class="premium-input form-control" placeholder="0.00" required>
                        </div>

                        <!-- PRIMARY SUPPLIER -->
                        <div class="col-6 mb-3">
                            <label class="premium-label">PRIMARY SUPPLIER</label>
                            <select name="supplier_id" id="qa-supplier-select" class="premium-input form-control">
                                <option value="">Select Supplier(s)</option>
                                @foreach(\App\Models\Supplier::where('status','active')->get() as $supplier)
                                    <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                                @endforeach
                            </select>
                            {{-- Inline Add Supplier — no nested modal needed --}}
                            <button type="button" id="qa-add-supplier-toggle" class="btn-action-plus mt-2" style="background: #22d3ee;" title="Add New Supplier">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    {{-- ══ Inline Quick-Add Supplier Panel ══ --}}
                    <div id="qa-supplier-panel" style="display:none; background:#f0fdf4; border:1.5px solid #22d3ee; border-radius:14px; padding:14px 16px; margin-bottom:10px;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span style="font-size:11px; font-weight:800; color:#0891b2; text-transform:uppercase; letter-spacing:0.8px;">
                                <i class="fas fa-truck mr-1"></i> Quick Add Supplier
                            </span>
                            <button type="button" id="qa-supplier-panel-close" style="background:none; border:none; color:#94a3b8; font-size:16px; line-height:1; padding:0;">&times;</button>
                        </div>
                        <div class="row">
                            <div class="col-7 pr-1">
                                <input type="text" id="qa-sup-name" class="form-control form-control-sm" placeholder="Supplier Name *" style="border-radius:8px; height:38px; font-size:13px;">
                            </div>
                            <div class="col-5 pl-1">
                                <input type="text" id="qa-sup-phone" class="form-control form-control-sm" placeholder="Phone" style="border-radius:8px; height:38px; font-size:13px;">
                            </div>
                        </div>
                        <button type="button" id="qa-sup-save" class="btn btn-sm mt-2 w-100 font-weight-700" style="background:#22d3ee; color:#fff; border-radius:10px; height:36px; font-weight:700; font-size:13px;">
                            <i class="fas fa-check mr-1"></i> Save Supplier
                        </button>
                    </div>
                </div>
                {{-- Sticky footer: always visible even when form is long --}}
                <div class="modal-footer border-0 bg-white justify-content-between" style="padding:12px 20px; position:sticky; bottom:0; z-index:10; border-top:1px solid #f1f5f9 !important; flex-shrink:0;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius:100px; height:46px; padding:0 24px; background:#94a3b8; border:none; font-weight:700; font-size:0.85rem;">Cancel</button>
                    <button type="submit" id="save-product-btn-qa" class="btn btn-orange shadow-lg" style="border-radius:100px; height:46px; padding:0 28px; font-weight:700; font-size:0.85rem;">
                        <i class="fas fa-lock mr-2"></i> SAVE PRODUCT
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* ── Base styles ─────────────────── */
    .premium-label {
        font-weight: 800;
        font-size: 0.7rem;
        color: #475569;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
        display: block;
        text-transform: uppercase;
    }
    .premium-input {
        border-radius: 12px !important;
        border: 1px solid #e2e8f0 !important;
        padding: 10px 16px !important;
        height: 46px !important;
        font-weight: 500 !important;
        color: #1e293b !important;
        background: #fff !important;
    }
    .btn-action-plus {
        width: 40px;
        height: 32px;
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

    /* ── Select2 Unification ─────────── */
    #addProductModal .select2-container--default .select2-selection--single {
        border-radius: 12px !important;
        height: 46px !important;
        border: 1px solid #e2e8f0 !important;
    }
    #addProductModal .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 46px !important;
        padding-left: 16px !important;
        font-size: 0.9rem;
    }
    #addProductModal .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 44px !important;
    }

    /* ── Mobile Fixes ────────────────── */
    @media (max-width: 767px) {
        /* Modal positioning */
        #addProductModal .modal-dialog {
            margin: 0 !important;
            max-width: 100% !important;
            width: 100% !important;
            position: fixed !important;
            bottom: 0 !important;
            top: auto !important;
            left: 0 !important;
            right: 0 !important;
        }
        #addProductModal .modal-content {
            border-radius: 20px 20px 0 0 !important;
            max-height: 92vh !important;
            display: flex !important;
            flex-direction: column !important;
        }
        #addProductModal .modal-header {
            border-radius: 20px 20px 0 0 !important;
            padding: 14px 18px !important;
        }
        /* Scrollable body */
        #addProductModal .modal-body {
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch !important;
            flex: 1 1 auto !important;
            padding: 14px 14px 6px !important;
        }
        /* Smaller inputs on mobile */
        #addProductModal .premium-input,
        #addProductModal .form-control {
            height: 42px !important;
            font-size: 0.88rem !important;
            padding: 8px 12px !important;
        }
        #addProductModal .select2-container--default .select2-selection--single {
            height: 42px !important;
        }
        #addProductModal .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 42px !important;
            font-size: 0.88rem !important;
        }
        #addProductModal .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
        }
        /* Compact labels */
        #addProductModal .premium-label {
            font-size: 0.65rem !important;
            margin-bottom: 4px !important;
        }
        /* Compact spacing */
        #addProductModal .mb-3 {
            margin-bottom: 10px !important;
        }
        #addProductModal .modal-body .form-group {
            margin-bottom: 10px !important;
        }
        /* Sticky footer buttons */
        #addProductModal .modal-footer {
            padding: 10px 14px !important;
            flex-shrink: 0 !important;
        }
        #addProductModal .modal-footer .btn {
            height: 44px !important;
            font-size: 0.85rem !important;
            padding: 0 20px !important;
        }
        /* Plus buttons */
        #addProductModal .btn-action-plus {
            width: 36px !important;
            height: 30px !important;
            margin-top: 6px !important;
        }
    }
</style>

@push('scripts')
<script>
$(document).ready(function() {
    // Fix focus issue for Select2 in Bootstrap Modals
    $('#addProductModal').on('shown.bs.modal', function() {
        $(this).removeAttr('tabindex'); 
        
        // Re-initialize Select2 when modal is shown to ensure correct width and focus
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
                    return { query: params.term };
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

        $('#qa-cat-select, #qa-brand-select, #qa-model-select, #qa-unit-select, #qa-supplier-select').select2({
            theme: 'bootstrap4',
            width: '100%',
            dropdownParent: $('#addProductModal'),
            placeholder: "Select or Type",
            allowClear: true,
            minimumResultsForSearch: 0 // Force search bar visibility
        });
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
    // ── Inline Quick-Add Supplier Panel ─────────────────────────
    $('#qa-add-supplier-toggle').on('click', function() {
        var $panel = $('#qa-supplier-panel');
        $panel.slideToggle(200, function() {
            if ($panel.is(':visible')) {
                $('#qa-sup-name').focus();
                // Auto-scroll the modal body so the panel + Save button are fully visible
                setTimeout(function() {
                    $panel[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }, 50);
            }
        });
        // Toggle icon
        var $icon = $(this).find('i');
        $icon.toggleClass('fa-plus fa-times');
    });

    $('#qa-supplier-panel-close').on('click', function() {
        $('#qa-supplier-panel').slideUp(200);
        $('#qa-add-supplier-toggle').find('i').removeClass('fa-times').addClass('fa-plus');
    });

    $('#qa-sup-save').on('click', function() {
        var name = $('#qa-sup-name').val().trim();
        if (!name) {
            $('#qa-sup-name').focus().css('border-color', '#e74a3b');
            return;
        }
        $('#qa-sup-name').css('border-color', '');

        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

        $.ajax({
            url: "{{ route('supplier.quick-store') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                name: name,
                phone: $('#qa-sup-phone').val().trim(),
                status: 'active'
            },
            success: function(res) {
                if (res.status === 'success') {
                    var s = res.supplier;
                    var label = s.name + (s.phone ? ' (' + s.phone + ')' : '');

                    // Add to the qa-supplier-select inside product modal
                    var opt = new Option(label, s.id, true, true);
                    $('#qa-supplier-select').append(opt).trigger('change');

                    // Also add to main page supplier_id if it exists
                    if ($('#supplier_id').length) {
                        var mainOpt = new Option(label, s.id, false, false);
                        $('#supplier_id').append(mainOpt);
                    }

                    // Reset and close panel
                    $('#qa-sup-name').val('');
                    $('#qa-sup-phone').val('');
                    $('#qa-supplier-panel').slideUp(200);
                    $('#qa-add-supplier-toggle').find('i').removeClass('fa-times').addClass('fa-plus');

                    $btn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i> Save Supplier');

                    // Small success indicator
                    $btn.css('background', '#16a34a');
                    setTimeout(function() { $btn.css('background', '#22d3ee'); }, 1500);
                }
            },
            error: function(err) {
                $btn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i> Save Supplier');
                var msg = err.responseJSON && err.responseJSON.message ? err.responseJSON.message : 'Error saving supplier';
                alert(msg);
            }
        });
    });

    // Reset inline panel when product modal is closed
    $('#addProductModal').on('hidden.bs.modal', function() {
        $('#qa-supplier-panel').hide();
        $('#qa-add-supplier-toggle').find('i').removeClass('fa-times').addClass('fa-plus');
        $('#qa-sup-name, #qa-sup-phone').val('');
    });
});
</script>
@endpush
