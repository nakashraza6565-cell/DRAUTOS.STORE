@extends('backend.layouts.master')

@section('main-content')

<div class="card">
    <h5 class="card-header">Add Product</h5>
    <div class="card-body">
      <div class="row">
        <div class="col-md-12">
           @include('backend.layouts.notification')
        </div>
      </div>
      <form method="post" action="{{route('product.store')}}">
        {{csrf_field()}}
        <div class="form-group">
          <label for="inputTitle" class="col-form-label">Title <span class="text-danger">*</span></label>
          <input id="inputTitle" type="text" name="title" placeholder="Enter title"  value="{{old('title')}}" class="form-control">
          @error('title')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="summary" class="col-form-label">Summary</label>
          <textarea class="form-control" id="summary" name="summary">{{old('summary')}}</textarea>
          @error('summary')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="description" class="col-form-label">Description</label>
          <textarea class="form-control" id="description" name="description">{{old('description')}}</textarea>
          @error('description')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>


        <div class="form-group">
          <label for="is_featured">Is Featured</label><br>
          <input type="checkbox" name='is_featured' id='is_featured' value='1' checked> Yes                        
        </div>
              {{-- {{$categories}} --}}

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="sku" class="col-form-label">SKU (Stock Keeping Unit)</label>
                    <input id="sku" type="text" name="sku" placeholder="Auto-generated: 202185-XXX" value="{{old('sku')}}" class="form-control">
                    @error('sku')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="barcode" class="col-form-label">Barcode / UPC</label>
                    <input id="barcode" type="text" name="barcode" placeholder="Auto-generated: 202185-XXX" value="{{old('barcode')}}" class="form-control">
                    @error('barcode')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="price" class="col-form-label">Selling Price <span class="text-danger">*</span></label>
                    <input id="price" type="number" step="0.01" name="price" placeholder="Enter price" value="{{old('price')}}" class="form-control">
                    @error('price')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="purchase_price" class="col-form-label">Purchase Price</label>
                    <input id="purchase_price" type="number" step="0.01" name="purchase_price" placeholder="Enter purchase price" value="{{old('purchase_price')}}" class="form-control">
                    @error('purchase_price')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="packaging_cost" class="col-form-label">Packaging Cost</label>
                    <input id="packaging_cost" type="number" step="0.01" name="packaging_cost" placeholder="Enter packaging cost" value="{{old('packaging_cost')}}" class="form-control">
                    @error('packaging_cost')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-3">
                 <div class="form-group">
                    <label for="wholesale_price" class="col-form-label">Wholesale Price</label>
                    <input id="wholesale_price" type="number" step="0.01" name="wholesale_price" placeholder="Wholesale Price" value="{{old('wholesale_price')}}" class="form-control">
                    @error('wholesale_price')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
            </div>
            <div class="col-md-3">
                 <div class="form-group">
                    <label for="retail_price" class="col-form-label">Retail Price</label>
                    <input id="retail_price" type="number" step="0.01" name="retail_price" placeholder="Retail Price" value="{{old('retail_price')}}" class="form-control">
                    @error('retail_price')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
            </div>
            <div class="col-md-3">
                 <div class="form-group">
                    <label for="walkin_price" class="col-form-label">Walk-in Price</label>
                    <input id="walkin_price" type="number" step="0.01" name="walkin_price" placeholder="Walk-in Price" value="{{old('walkin_price')}}" class="form-control">
                    @error('walkin_price')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
            </div>
            <div class="col-md-3">
                 <div class="form-group">
                    <label for="salesman_price" class="col-form-label">Salesman Price</label>
                    <input id="salesman_price" type="number" step="0.01" name="salesman_price" placeholder="Salesman Price" value="{{old('salesman_price')}}" class="form-control">
                    @error('salesman_price')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="suppliers">Suppliers <span class="text-danger">*</span></label>
                    {{-- Use multiple select --}}
                    <select name="suppliers[]" class="form-control selectpicker" multiple data-live-search="true">
                        @foreach($suppliers as $supplier)
                            <option value="{{$supplier->id}}">{{$supplier->name}} @if($supplier->company_name) ({{$supplier->company_name}}) @endif</option>
                        @endforeach
                    </select>
                    @error('suppliers')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="warehouse_id">Warehouse / Location</label>
                    <select name="warehouse_id" class="form-control">
                        <option value="">--Select Warehouse--</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{$warehouse->id}}">{{$warehouse->name}} ({{$warehouse->location}})</option>
                        @endforeach
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" name="rack_number" class="form-control mt-2" placeholder="Rack Number" value="{{old('rack_number')}}">
                    </div>
                    <div class="col-md-6">
                         <input type="text" name="shelf_number" class="form-control mt-2" placeholder="Shelf Number" value="{{old('shelf_number')}}">
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="stock">Quantity <span class="text-danger">*</span></label>
                    <input id="quantity" type="number" name="stock" placeholder="Enter quantity" value="{{old('stock')}}" class="form-control">
                    @error('stock')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="unit">Variable Options (Unit)</label>
                    <div class="input-group">
                        <select name="unit" id="unit_select" class="form-control">
                            <option value="">--Select Unit--</option>
                            @if(isset($units))
                                @foreach($units as $u)
                                <option value="{{$u->name}}" {{old('unit') == $u->name ? 'selected' : ''}}>{{$u->name}}</option>
                                @endforeach
                            @endif
                        </select>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addUnitModal"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <input type="hidden" name="low_stock_threshold" value="5">
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="weight">Weight (per unit)</label>
                    <input id="weight" type="number" step="0.01" name="weight" value="{{old('weight')}}" class="form-control" placeholder="e.g. 0.5">
                    @error('weight')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                  <label for="cat_id">Category <span class="text-danger">*</span></label>
                  <select name="cat_id" id="cat_id" class="form-control">
                      <option value="">--Select any category--</option>
                      @foreach($categories as $key=>$cat_data)
                          <option value='{{$cat_data->id}}'>{{$cat_data->title}}</option>
                      @endforeach
                  </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group d-none" id="child_cat_div">
                  <label for="child_cat_id">Sub Category</label>
                  <select name="child_cat_id" id="child_cat_id" class="form-control">
                      <option value="">--Select any category--</option>
                  </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="brand_id">Brand</label>
                    <div class="input-group">
                        <select name="brand_id" id="brand_select" class="form-control">
                            <option value="">--Select Brand--</option>
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
            <div class="col-md-4">
                <div class="form-group">
                    <label for="model">Model</label>
                    <div class="input-group">
                        <select name="model" id="model_select" class="form-control">
                            <option value="">--Select Model--</option>
                            @if(isset($product_models))
                                @foreach($product_models as $m)
                                <option value="{{$m->name}}" {{old('model') == $m->name ? 'selected' : ''}}>{{$m->name}}</option>
                                @endforeach
                            @endif
                        </select>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#addModelModal"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="batch_number">Batch / Lot Number</label>
                    <input type="text" name="batch_number" value="{{old('batch_number')}}" class="form-control" placeholder="Enter Batch Number">
                </div>
            </div>
        </div>

        <div class="row">
             <div class="col-md-4">
                <div class="form-group">
                    <label for="size">Size</label>
                    <input type="text" name="size[]" class="form-control" placeholder="Size (e.g. S, M, L or separate by comma)">
                    <small class="text-muted">You can add multiple sizes</small>
                </div>
            </div>
            <div class="col-md-4">
                 <div class="form-group">
                    <label for="color">Color</label>
                    <input type="text" name="color" class="form-control" placeholder="Color" value="{{old('color')}}">
                </div>
            </div>
            <div class="col-md-4">
                 <div class="form-group">
                    <label for="type">Type</label>
                    <input type="text" name="type" class="form-control" placeholder="Type" value="{{old('type')}}">
                </div>
            </div>
        </div>

        <div class="form-group">
          <label for="discount" class="col-form-label">Discount(%)</label>
          <input id="discount" type="number" name="discount" min="0" max="100" placeholder="Enter discount"  value="{{old('discount')}}" class="form-control">
          @error('discount')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="condition">Condition</label>
          <select name="condition" class="form-control">
              <option value="">--Select Condition--</option>
              <option value="default">Default</option>
              <option value="new">New</option>
              <option value="hot">Hot</option>
          </select>
        </div>

        <div class="form-group">
          <label for="inputPhoto" class="col-form-label">Photo</label>
          <div class="input-group">
              <span class="input-group-btn">
                  <a id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-primary">
                  <i class="fa fa-picture-o"></i> Choose
                  </a>
              </span>
          <input id="thumbnail" class="form-control" type="text" name="photo" value="{{old('photo')}}">
        </div>
        <div id="holder" style="margin-top:15px;max-height:100px;"></div>
          @error('photo')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        
        <div class="form-group">
          <label for="status" class="col-form-label">Status</label>
          <select name="status" class="form-control">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
          </select>
          @error('status')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        <div class="form-group mb-3">
          <button type="reset" class="btn btn-warning">Reset</button>
           <button class="btn btn-success" type="submit">Submit</button>
        </div>
      </form>
    </div>
</div>

<!-- Add Unit Modal -->
<div class="modal fade" id="addUnitModal" tabindex="-1" role="dialog" aria-labelledby="addUnitModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addUnitModalLabel">Add New Unit Option</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
            <label for="new_unit_name">Unit Name (e.g., per carton, per gram)</label>
            <input type="text" id="new_unit_name" class="form-control" placeholder="Enter unit name">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="saveUnitBtn">Save Unit</button>
      </div>
    </div>
  </div>
</div>

<!-- Add Model Modal -->
...
<!-- Add Brand Modal -->
<div class="modal fade" id="addBrandModal" tabindex="-1" role="dialog" aria-labelledby="addBrandModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addBrandModalLabel">Quick Add Brand (Company)</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
            <label for="new_brand_title">Brand Name <span class="text-danger">*</span></label>
            <input type="text" id="new_brand_title" class="form-control" placeholder="Enter brand name">
        </div>
        <div class="form-group">
            <label for="new_brand_company">Company Name</label>
            <input type="text" id="new_brand_company" class="form-control" placeholder="Enter company name">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-info" id="saveBrandBtn">Save Brand</button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{asset('backend/summernote/summernote.min.css')}}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
@endpush
@push('scripts')
<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script src="{{asset('backend/summernote/summernote.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>

<script>
    $('#lfm').filemanager('image');

    $(document).ready(function() {
      $('#summary').summernote({
        placeholder: "Write short description.....",
          tabsize: 2,
          height: 100
      });
    });

    $(document).ready(function() {
      $('#description').summernote({
        placeholder: "Write detail description.....",
          tabsize: 2,
          height: 150
      });
    });
    // $('select').selectpicker();

</script>

<script>
  $('#cat_id').change(function(){
    var cat_id=$(this).val();
    // alert(cat_id);
    if(cat_id !=null){
      // Ajax call
      $.ajax({
        url:"/admin/category/"+cat_id+"/child",
        data:{
          _token:"{{csrf_token()}}",
          id:cat_id
        },
        type:"POST",
        success:function(response){
          if(typeof(response) !='object'){
            response=$.parseJSON(response)
          }
          // console.log(response);
          var html_option="<option value=''>----Select sub category----</option>"
          if(response.status){
            var data=response.data;
            // alert(data);
            if(response.data){
              $('#child_cat_div').removeClass('d-none');
              $.each(data,function(id,title){
                html_option +="<option value='"+id+"'>"+title+"</option>"
              });
            }
            else{
            }
          }
          else{
            $('#child_cat_div').addClass('d-none');
          }
          $('#child_cat_id').html(html_option);
        }
      });
    }
    else{
    }
  });

  $('#saveUnitBtn').click(function(e) {
      e.preventDefault();
      var unitName = $('#new_unit_name').val();
      if(unitName) {
          $.ajax({
              url: "{{route('product.store-unit')}}",
              type: "POST",
              data: {
                  _token: "{{csrf_token()}}",
                  name: unitName
              },
              success: function(response) {
                  if(response.status == 'success') {
                      var newOption = new Option(response.unit.name, response.unit.name, false, true);
                      $('#unit_select').append(newOption).trigger('change');
                      $('#addUnitModal').modal('hide');
                      $('#new_unit_name').val('');
                      alert('Unit added successfully');
                  }
              },
              error: function(err) {
                  let msg = "Failed to save unit.";
                  if(err.responseJSON && err.responseJSON.message) {
                      msg += " Error: " + err.responseJSON.message;
                  }
                  alert(msg);
                  console.log(err);
              }
          });
      }
  });

  $('#saveModelBtn').click(function(e) {
      ...
  });

  $('#saveBrandBtn').click(function(e) {
      e.preventDefault();
      var brandTitle = $('#new_brand_title').val();
      var brandCompany = $('#new_brand_company').val();
      if(brandTitle) {
          $.ajax({
              url: "{{route('brand.quick-store')}}",
              type: "POST",
              data: {
                  _token: "{{csrf_token()}}",
                  title: brandTitle,
                  company_name: brandCompany
              },
              success: function(response) {
                  if(response.status == 'success') {
                      var newOption = new Option(response.brand.title, response.brand.id, false, true);
                      $('#brand_select').append(newOption).trigger('change');
                      $('#addBrandModal').modal('hide');
                      $('#new_brand_title').val('');
                      $('#new_brand_company').val('');
                      alert('Brand added successfully');
                  }
              },
              error: function(err) {
                  alert('Failed to save brand.');
              }
          });
      }
  });
</script>
@endpush
