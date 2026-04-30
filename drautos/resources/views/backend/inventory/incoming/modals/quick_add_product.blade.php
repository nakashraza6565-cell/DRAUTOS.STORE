<!-- Quick Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header py-4" style="background: #334155;">
                <h5 class="modal-title font-weight-bold text-white"><i class="fas fa-barcode mr-2"></i> QUICK ADD NEW PRODUCT</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="quickAddProductForm">
                @csrf
                <div class="modal-body p-4 bg-white">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group mb-4">
                                <label class="premium-label">PRODUCT TITLE <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="premium-input form-control" placeholder="e.g. Engine Oil 5W-30" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-4">
                                <label class="premium-label">SKU / BARCODE</label>
                                <input type="text" name="sku" class="premium-input form-control" placeholder="Unique identifier">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label class="premium-label">CATEGORY</label>
                                <select name="cat_id" class="premium-input form-control">
                                    <option value="">-- Select --</option>
                                    @foreach(\App\Models\Category::where('status','active')->get() as $cat)
                                        <option value="{{$cat->id}}">{{$cat->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label class="premium-label">BRAND</label>
                                <select name="brand_id" class="premium-input form-control">
                                    <option value="">-- Select --</option>
                                    @foreach(\App\Models\Brand::where('status','active')->get() as $brand)
                                        <option value="{{$brand->id}}">{{$brand->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label class="premium-label">PURCHASE PRICE</label>
                                <input type="number" name="purchase_price" step="0.01" class="premium-input form-control" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label class="premium-label">INITIAL RETAIL PRICE</label>
                                <input type="number" name="price" step="0.01" class="premium-input form-control" placeholder="Selling price">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 bg-light">
                    <button type="button" class="btn btn-light px-4" data-dismiss="modal" style="border-radius: 100px; font-weight: 700; color: #64748b;">CANCEL</button>
                    <button type="submit" class="btn btn-slate px-5 shadow-lg" style="border-radius: 100px; font-weight: 700;">CREATE PRODUCT</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .premium-label {
        font-weight: 700;
        font-size: 0.75rem;
        color: #64748b;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
        display: block;
    }
    .premium-input {
        border-radius: 12px !important;
        border: 1px solid #e2e8f0 !important;
        padding: 12px 18px !important;
        height: 50px !important;
        font-weight: 500 !important;
        color: #1e293b !important;
        transition: all 0.2s ease !important;
        background: #fdfdfd !important;
    }
    .premium-input:focus {
        border-color: #334155 !important;
        background: #fff !important;
        box-shadow: 0 4px 12px rgba(51, 65, 85, 0.08) !important;
    }
    .btn-slate {
        background: #334155 !important;
        color: #fff !important;
        border-radius: 12px !important;
        font-weight: 600;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .btn-slate:hover { background: #1e293b !important; }
</style>

<script>
$(document).ready(function() {
    $('#quickAddProductForm').on('submit', function(e) {
        e.preventDefault();
        let $form = $(this);
        let $btn = $form.find('button[type="submit"]');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Creating...');

        $.ajax({
            url: "{{ route('product.store') }}",
            type: "POST",
            data: $form.serialize() + "&status=active",
            success: function(response) {
                // Assuming response is the product object
                // Add to ALL existing and future product dropdowns
                window.lastAddedProduct = response;
                
                // Add to global product dropdowns (re-rendering rows might be easier but let's try updating)
                $('.product-select').each(function() {
                    let newOption = new Option(response.title + ' (' + (response.sku || '') + ')', response.id, false, false);
                    $(newOption).attr('data-cost', response.purchase_price || 0);
                    $(this).append(newOption);
                });

                $('#addProductModal').modal('hide');
                $form[0].reset();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Product Created',
                    text: response.title + ' is now available for entry.',
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function(err) {
                $btn.prop('disabled', false).text('Create Product');
                let msg = err.responseJSON && err.responseJSON.message ? err.responseJSON.message : 'Error creating product';
                Swal.fire('Error', msg, 'error');
            }
        });
    });
});
</script>
