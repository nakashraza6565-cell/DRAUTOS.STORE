<!-- Quick Add Product Modal -->
<div class="modal" id="addProductModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-barcode mr-2"></i>Quick Add New Product</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="quickAddProductForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold">Product Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" placeholder="e.g. Engine Oil 5W-30" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold">SKU / Barcode</label>
                                <input type="text" name="sku" class="form-control" placeholder="Unique identifier">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">Category</label>
                                <select name="cat_id" class="form-control">
                                    <option value="">-- Select --</option>
                                    @foreach(\App\Models\Category::where('status','active')->get() as $cat)
                                        <option value="{{$cat->id}}">{{$cat->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">Brand</label>
                                <select name="brand_id" class="form-control">
                                    <option value="">-- Select --</option>
                                    @foreach(\App\Models\Brand::where('status','active')->get() as $brand)
                                        <option value="{{$brand->id}}">{{$brand->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">Initial Retail Price</label>
                                <input type="number" name="price" class="form-control" placeholder="Selling price">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark font-weight-bold px-4">Create Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Modal-specific logic can go here, but submission is handled by the parent view
});
</script>
