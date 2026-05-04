@extends('backend.layouts.master')

@section('main-content')

<div class="card">
    <h5 class="card-header font-weight-bold text-primary">Edit Product</h5>
    <div class="card-body">
      <div class="row">
        <div class="col-md-12">
           @include('backend.layouts.notification')
        </div>
      </div>
      <form method="post" action="{{route('product.update',$product->id)}}">
        @csrf 
        @method('PATCH')
        
        <!-- SECTION 1: Mandatory Information -->
        <div class="bg-light p-3 border rounded mb-4">
            <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-star text-danger"></i> Mandatory Information</h6>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                      <label for="inputTitle" class="col-form-label">Title <span class="text-danger">*</span></label>
                      <input id="inputTitle" type="text" name="title" placeholder="Enter title"  value="{{$product->title}}" class="form-control form-control-lg" required>
                      @error('title')
                      <span class="text-danger">{{$message}}</span>
                      @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                      <label for="cat_id">Category <span class="text-danger">*</span></label>
                      <div class="input-group">
                          <select name="cat_id" id="cat_id" class="form-control" required>
                              <option value="">--Select any category--</option>
                              @foreach($categories as $key=>$cat_data)
                                  <option value='{{$cat_data->id}}' {{(($product->cat_id==$cat_data->id)? 'selected' : '')}}>{{$cat_data->title}}</option>
                              @endforeach
                          </select>
                          <div class="input-group-append">
                              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addCategoryModal"><i class="fas fa-plus"></i></button>
                          </div>
                      </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="suppliers">Suppliers <span class="text-danger">*</span></label>
                        <div class="input-group">
                            @php 
                                $supplier_ids = $product->suppliers ? $product->suppliers->pluck('id')->toArray() : [];
                                if(empty($supplier_ids) && $product->supplier_id) { $supplier_ids[] = $product->supplier_id; }
                            @endphp
                            <select name="suppliers[]" class="form-control selectpicker" multiple data-live-search="true" id="supplier_select" required>
                                @foreach($suppliers as $supplier)
                                    <option value="{{$supplier->id}}" {{ (in_array($supplier->id, $supplier_ids) ? 'selected' : '') }}>{{$supplier->name}} @if($supplier->company_name) ({{$supplier->company_name}}) @endif</option>
                                @endforeach
                            </select>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#addSupplierModal"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="price" class="col-form-label">Selling Price <span class="text-danger">*</span></label>
                        <input id="price" type="number" step="0.01" name="price" placeholder="0.00" value="{{$product->price}}" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="stock">Quantity <span class="text-danger">*</span></label>
                        <input id="quantity" type="number" name="stock" placeholder="0" value="{{$product->stock}}" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="unit">Unit / Packaging <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <select name="unit" id="unit_select" class="form-control" required>
                                <option value="">--Select Unit--</option>
                                @if(isset($units))
                                    @foreach($units as $u)
                                    <option value="{{$u->name}}" {{(($product->unit==$u->name)? 'selected' : '')}}>{{$u->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addUnitModal"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="brand_id">Brand</label>
                        <div class="input-group">
                            <select name="brand_id" id="brand_select" class="form-control">
                                <option value="">--Select Brand--</option>
                               @foreach($brands as $brand)
                                <option value="{{$brand->id}}" {{(($product->brand_id==$brand->id)? 'selected' : '')}}>{{$brand->title}}</option>
                               @endforeach
                            </select>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#addBrandModal"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="model">Model</label>
                        <div class="input-group">
                            <select name="model" id="model_select" class="form-control">
                                <option value="">--Select Model--</option>
                                @if(isset($product_models))
                                    @foreach($product_models as $m)
                                    <option value="{{$m->name}}" {{(($product->model==$m->name)? 'selected' : '')}}>{{$m->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#addModelModal"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 2: Secondary / Optional Details -->
        <div class="p-3">
            <h6 class="font-weight-bold text-muted mb-3">Optional Details</h6>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group {{($product->child_cat_id ? '' : 'd-none')}}" id="child_cat_div">
                      <label for="child_cat_id">Sub Category</label>
                      <div class="input-group">
                          <select name="child_cat_id" id="child_cat_id" class="form-control">
                              <option value="">--Select subcategory--</option>
                              @if($product->child_cat_id && $product->cat_info)
                                  @foreach($product->cat_info->child_cat as $child_cat)
                                      <option value="{{$child_cat->id}}" {{($product->child_cat_id == $child_cat->id ? 'selected' : '')}}>{{$child_cat->title}}</option>
                                  @endforeach
                              @endif
                          </select>
                          <div class="input-group-append">
                              <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#addSubCategoryModal"><i class="fas fa-plus"></i></button>
                          </div>
                      </div>
                    </div>
                </div>
            </div>

            <div class="form-group mb-0">
                <label for="inputPhoto" class="col-form-label">Product Photo</label>
                <div class="input-group">
                    <span class="input-group-btn">
                        <a id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-primary">
                        <i class="fa fa-picture-o"></i> Choose Photo
                        </a>
                    </span>
                    <input id="thumbnail" class="form-control" type="text" name="photo" value="{{$product->photo}}">
                </div>
                <div id="holder" style="margin-top:15px;max-height:100px;">
                    @if($product->photo)
                        <img src="{{$product->photo}}" style="height: 100px;">
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="purchase_price">Purchase Price</label>
                        <input id="purchase_price" type="number" step="0.01" name="purchase_price" placeholder="0.00" value="{{$product->purchase_price}}" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                     <div class="form-group">
                        <label for="wholesale_price">Wholesale Price</label>
                        <input id="wholesale_price" type="number" step="0.01" name="wholesale_price" placeholder="0.00" value="{{$product->wholesale_price}}" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                     <div class="form-group">
                        <label for="retail_price">Retail Price</label>
                        <input id="retail_price" type="number" step="0.01" name="retail_price" placeholder="0.00" value="{{$product->retail_price}}" class="form-control">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="sku">SKU</label>
                        <input id="sku" type="text" name="sku" placeholder="Auto-generated" value="{{$product->sku}}" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="barcode">Barcode</label>
                        <input id="barcode" type="text" name="barcode" placeholder="Auto-generated" value="{{$product->barcode}}" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="low_stock_threshold">Low Stock Alert</label>
                        <input id="low_stock_threshold" type="number" name="low_stock_threshold" placeholder="e.g. 5" value="{{$product->low_stock_threshold}}" class="form-control">
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Summary</label>
                    <div class="input-group">
                        <div class="form-control bg-light text-truncate" id="summary_preview">{{$product->summary ? strip_tags($product->summary) : 'No summary added'}}</div>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#summaryModal">Edit</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label>Description</label>
                    <div class="input-group">
                        <div class="form-control bg-light text-truncate" id="description_preview">{{$product->description ? strip_tags($product->description) : 'No description added'}}</div>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#descriptionModal">Edit</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="warehouse_id">Warehouse</label>
                        <select name="warehouse_id" class="form-control">
                            <option value="">--Select Warehouse--</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{$warehouse->id}}" {{(($product->warehouse_id==$warehouse->id)? 'selected' : '')}}>{{$warehouse->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="batch_number">Batch Number</label>
                        <input type="text" name="batch_number" value="{{$product->batch_number}}" class="form-control" placeholder="Batch #">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="weight">Weight</label>
                        <input id="weight" type="number" step="0.01" name="weight" value="{{$product->weight}}" class="form-control" placeholder="0.00">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                     <div class="form-group">
                        <label for="size">Size</label>
                        <input type="text" name="size[]" value="{{ is_array($product->size) ? implode(',', $product->size) : $product->size }}" class="form-control" placeholder="Size">
                    </div>
                </div>
                <div class="col-md-3">
                     <div class="form-group">
                        <label for="color">Color</label>
                        <input type="text" name="color" class="form-control" placeholder="Color" value="{{$product->color}}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                      <label for="condition">Condition</label>
                      <select name="condition" class="form-control">
                          <option value="default" {{(($product->condition=='default')? 'selected' : '')}}>Default</option>
                          <option value="new" {{(($product->condition=='new')? 'selected' : '')}}>New</option>
                          <option value="hot" {{(($product->condition=='hot')? 'selected' : '')}}>Hot</option>
                      </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                      <label for="status">Status</label>
                      <select name="status" class="form-control">
                          <option value="active" {{(($product->status=='active')? 'selected' : '')}}>Active</option>
                          <option value="inactive" {{(($product->status=='inactive')? 'selected' : '')}}>Inactive</option>
                      </select>
                    </div>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-3">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" name='is_featured' id='is_featured' value='1' {{(($product->is_featured)? 'checked' : '')}} class="custom-control-input">
                      <label class="custom-control-label" for="is_featured">Is Featured</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group mb-3 text-center mt-4">
           <hr>
           <button class="btn btn-success btn-lg px-5" type="submit">Update Product</button>
        </div>

        <!-- Summary & Description Modals -->
        <div class="modal fade" id="summaryModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white"><h5 class="modal-title">Edit Summary</h5><button type="button" class="close text-white" data-dismiss="modal">&times;</button></div>
                    <div class="modal-body"><textarea class="form-control" id="summary" name="summary">{{$product->summary}}</textarea></div>
                    <div class="modal-footer"><button type="button" class="btn btn-primary" data-dismiss="modal">Save Changes</button></div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="descriptionModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white"><h5 class="modal-title">Edit Description</h5><button type="button" class="close text-white" data-dismiss="modal">&times;</button></div>
                    <div class="modal-body"><textarea class="form-control" id="description" name="description">{{$product->description}}</textarea></div>
                    <div class="modal-footer"><button type="button" class="btn btn-primary" data-dismiss="modal">Save Changes</button></div>
                </div>
            </div>
        </div>
      </form>
    </div>
</div>

@include('backend.product.partials.modals')

@endsection

@push('styles')
<link rel="stylesheet" href="{{asset('backend/summernote/summernote.min.css')}}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
<style>
    .selectpicker + .btn { background: #fff; border: 1px solid #ced4da; }
    .input-group-append .btn { z-index: 4; }
</style>
@endpush

@push('scripts')
<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script src="{{asset('backend/summernote/summernote.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>

<script>
    $('#lfm').filemanager('image');

    $(document).ready(function() {
      $('#summary').summernote({
          placeholder: "Brief summary...",
          tabsize: 2,
          height: 200,
          callbacks: { onChange: function(contents) { $('#summary_preview').text($('<div>').html(contents).text() || 'No summary added'); } }
      });
      $('#description').summernote({
          placeholder: "Detailed description...",
          tabsize: 2,
          height: 350,
          callbacks: { onChange: function(contents) { $('#description_preview').text($('<div>').html(contents).text() || 'No description added'); } }
      });

      $(document).on('change', '#cat_id', function() {
          var cat_id = $(this).val();
          if (cat_id) {
              $.ajax({
                  url: "/admin/category/" + cat_id + "/child",
                  type: "POST",
                  data: { _token: "{{csrf_token()}}" },
                  success: function(response) {
                      if (typeof(response) != 'object') { response = $.parseJSON(response); }
                      var html_option = "<option value=''>--Select subcategory--</option>";
                      if (response.status && response.data) {
                          $('#child_cat_div').removeClass('d-none');
                          $.each(response.data, function(id, title) { html_option += "<option value='" + id + "'>" + title + "</option>"; });
                      } else { $('#child_cat_div').addClass('d-none'); }
                      $('#child_cat_id').html(html_option);
                  }
              });
          }
      });

      // Quick Store AJAX handlers (Delegated)
      $(document).on('submit', '#quickAddCategoryForm', function(e) {
          e.preventDefault();
          $.post("{{route('category.quick-store')}}", $(this).serialize() + "&_token={{csrf_token()}}&is_parent=1", function(res) {
              if(res.status == 'success') {
                  $('#cat_id').append(new Option(res.category.title, res.category.id, false, true)).trigger('change');
                  $('#addCategoryModal').modal('hide');
              }
          });
      });

      $(document).on('submit', '#quickAddSubCategoryForm', function(e) {
          e.preventDefault();
          var pId = $('#cat_id').val();
          if(!pId) { alert('Select parent category first'); return; }
          $.post("{{route('category.quick-store')}}", $(this).serialize() + "&_token={{csrf_token()}}&is_parent=0&parent_id=" + pId, function(res) {
              if(res.status == 'success') {
                  $('#child_cat_id').append(new Option(res.category.title, res.category.id, false, true)).trigger('change');
                  $('#addSubCategoryModal').modal('hide');
              }
          });
      });

      $(document).on('submit', '#quickAddSupplierForm', function(e) {
          e.preventDefault();
          $.post("{{route('supplier.quick-store')}}", $(this).serialize() + "&_token={{csrf_token()}}", function(res) {
              if(res.status == 'success') {
                  $('#supplier_select').append(new Option(res.supplier.name + ' (' + (res.supplier.company_name || '') + ')', res.supplier.id, false, true)).selectpicker('refresh');
                  $('#addSupplierModal').modal('hide');
              }
          });
      });

      $(document).on('submit', '#quickAddBrandForm', function(e) {
          e.preventDefault();
          $.post("{{route('brand.quick-store')}}", $(this).serialize() + "&_token={{csrf_token()}}", function(res) {
              if(res.status == 'success') {
                  $('#brand_select').append(new Option(res.brand.title, res.brand.id, false, true));
                  $('#addBrandModal').modal('hide');
              }
          });
      });

      $(document).on('submit', '#quickAddUnitForm', function(e) {
          e.preventDefault();
          $.post("{{route('product.store-unit')}}", $(this).serialize() + "&_token={{csrf_token()}}", function(res) {
              if(res.status == 'success') {
                  $('#unit_select').append(new Option(res.unit.name, res.unit.name, false, true));
                  $('#addUnitModal').modal('hide');
              }
          });
      });

      $(document).on('submit', '#quickAddModelForm', function(e) {
          e.preventDefault();
          $.post("{{route('product.store-model')}}", $(this).serialize() + "&_token={{csrf_token()}}", function(res) {
              if(res.status == 'success') {
                  $('#model_select').append(new Option(res.model.name, res.model.name, false, true));
                  $('#addModelModal').modal('hide');
              }
          });
      });

    });
</script>
@endpush
