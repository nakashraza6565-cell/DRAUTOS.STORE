@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0 mb-5" style="border-radius: 20px; overflow: hidden;">
                <div class="card-header py-4 d-flex align-items-center justify-content-between" style="background: #334155; border: none;">
                    <h5 class="m-0 font-weight-bold text-white"><i class="fas fa-barcode mr-2"></i> ADD NEW PRODUCT</h5>
                    <a href="{{route('product.index')}}" class="btn btn-sm btn-light px-3" style="border-radius: 100px; font-weight: 600; color: #334155;">
                        <i class="fas fa-arrow-left mr-1"></i> Back to List
                    </a>
                </div>
                <div class="card-body p-4 p-md-5 bg-white">
                    @include('backend.layouts.notification')
                    
                    <form method="post" action="{{route('product.store')}}">
                        {{csrf_field()}}
                        
                        <!-- PRIMARY INFO -->
                        <div class="mb-5">
                            <h6 class="premium-section-title mb-4">PRIMARY INFORMATION</h6>
                            
                            <div class="form-group mb-4">
                                <label class="premium-label">PRODUCT TITLE <span class="text-danger">*</span></label>
                                <input type="text" name="title" placeholder="e.g. Engine Oil 5W-30" value="{{old('title')}}" class="premium-input form-control" required>
                                @error('title') <span class="text-danger small">{{$message}}</span> @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label class="premium-label">CATEGORY <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <select name="cat_id" id="cat_id" class="premium-input form-control" required>
                                                <option value="">-- Select Category --</option>
                                                @foreach($categories as $cat_data)
                                                    <option value='{{$cat_data->id}}'>{{$cat_data->title}}</option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-slate px-3" data-toggle="modal" data-target="#addCategoryModal"><i class="fas fa-plus"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label class="premium-label">SUPPLIERS <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <select name="suppliers[]" class="premium-input form-control selectpicker" multiple data-live-search="true" id="supplier_select" required>
                                                @foreach($suppliers as $supplier)
                                                    <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-slate px-3" data-toggle="modal" data-target="#addSupplierModal"><i class="fas fa-plus"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group mb-4">
                                        <label class="premium-label">PURCHASE PRICE</label>
                                        <input type="number" step="0.01" name="purchase_price" placeholder="0.00" value="{{old('purchase_price')}}" class="premium-input form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-4">
                                        <label class="premium-label">RETAIL PRICE (SELLING) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" name="price" placeholder="Selling price" value="{{old('price')}}" class="premium-input form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-4">
                                        <label class="premium-label">INITIAL STOCK <span class="text-danger">*</span></label>
                                        <input type="number" name="stock" placeholder="0" value="{{old('stock')}}" class="premium-input form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-4">
                                        <label class="premium-label">UNIT / PACKAGING <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <select name="unit" id="unit_select" class="premium-input form-control" required>
                                                <option value="">-- Select Unit --</option>
                                                @foreach($units as $u)
                                                    <option value="{{$u->name}}">{{$u->name}}</option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-slate px-3" data-toggle="modal" data-target="#addUnitModal"><i class="fas fa-plus"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SPECIFICATIONS -->
                        <div class="mb-5">
                            <h6 class="premium-section-title mb-4">SPECIFICATIONS & INVENTORY</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label class="premium-label">BRAND</label>
                                        <div class="input-group">
                                            <select name="brand_id" id="brand_select" class="premium-input form-control">
                                                <option value="">-- Select Brand --</option>
                                                @foreach($brands as $brand)
                                                    <option value="{{$brand->id}}">{{$brand->title}}</option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-slate px-3" data-toggle="modal" data-target="#addBrandModal"><i class="fas fa-plus"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label class="premium-label">MODEL</label>
                                        <div class="input-group">
                                            <select name="model" id="model_select" class="premium-input form-control">
                                                <option value="">-- Select Model --</option>
                                                @foreach($product_models as $m)
                                                    <option value="{{$m->name}}">{{$m->name}}</option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-slate px-3" data-toggle="modal" data-target="#addModelModal"><i class="fas fa-plus"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label class="premium-label">SKU / BARCODE</label>
                                        <input type="text" name="sku" placeholder="Unique identifier" value="{{old('sku')}}" class="premium-input form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label class="premium-label">BARCODE STRING</label>
                                        <input type="text" name="barcode" placeholder="Scan or enter barcode" value="{{old('barcode')}}" class="premium-input form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label class="premium-label">SUMMARY</label>
                                        <textarea name="summary" class="premium-input form-control" rows="2" placeholder="Brief product summary...">{{old('summary')}}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label class="premium-label">PHOTO</label>
                                        <div class="input-group">
                                            <span class="input-group-btn">
                                                <a id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-slate" style="border-radius: 12px 0 0 12px !important; height: 50px; display: flex; align-items: center;">
                                                    <i class="fa fa-picture-o mr-1"></i> Choose
                                                </a>
                                            </span>
                                            <input id="thumbnail" class="premium-input form-control" type="text" name="photo" value="{{old('photo')}}" style="border-radius: 0 12px 12px 0 !important;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 pt-4 border-top text-center">
                            <button type="reset" class="btn btn-light btn-lg px-5 mr-3" style="border-radius: 100px; font-weight: 700; color: #64748b; height: 55px;">CANCEL</button>
                            <button type="submit" class="btn btn-slate btn-lg px-5 shadow-lg" style="border-radius: 100px; font-weight: 700; height: 55px;">CREATE PRODUCT</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('backend.product.partials.modals')

@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
<style>
    body { background-color: #f8fafc; }
    .premium-section-title {
        color: #1e293b;
        font-weight: 800;
        letter-spacing: 1px;
        font-size: 0.8rem;
        padding-bottom: 8px;
        border-bottom: 2px solid #f1f5f9;
        display: inline-block;
    }
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
    textarea.premium-input { height: auto !important; }
    .btn-slate {
        background: #334155 !important;
        color: #fff !important;
        border-radius: 12px !important;
        font-weight: 600;
    }
    .btn-slate:hover { background: #1e293b !important; }
    .input-group-append .btn { 
        border-radius: 0 12px 12px 0 !important;
        height: 50px;
    }
    .selectpicker + .btn { 
        background: #fdfdfd !important; 
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        height: 50px !important;
        display: flex !important;
        align-items: center !important;
    }
</style>
@endpush

@push('scripts')
<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
<script>
    $('#lfm').filemanager('image');
    $(document).ready(function() {
        $('.selectpicker').selectpicker();
    });
</script>
@endpush
