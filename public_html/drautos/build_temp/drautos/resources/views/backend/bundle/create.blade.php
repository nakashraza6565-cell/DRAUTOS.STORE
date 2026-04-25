@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3" style="background: #f8fafc;">
            <h6 class="m-0 font-weight-bold text-primary">Create Product Bundle</h6>
        </div>
        <div class="card-body">
            <form method="post" action="{{route('bundles.store')}}">
                {{csrf_field()}}
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name" class="col-form-label font-weight-bold">Bundle Name <span class="text-danger">*</span></label>
                            <input id="name" type="text" name="name" placeholder="e.g. Engine Maintenance Kit" value="{{old('name')}}" class="form-control" required>
                            @error('name')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="sku" class="col-form-label font-weight-bold">Bundle SKU <span class="text-danger">*</span></label>
                            <input id="sku" type="text" name="sku" placeholder="BNDL-001" value="{{$auto_sku}}" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="price" class="col-form-label font-weight-bold">Bundle Price (Rs.) <span class="text-danger">*</span></label>
                            <input id="price" type="number" name="price" placeholder="Total selling price" value="{{old('price')}}" class="form-control" step="0.01" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="wholesale_price" class="col-form-label font-weight-bold">Wholesale Price</label>
                            <input id="wholesale_price" type="number" name="wholesale_price" placeholder="0.00" value="{{old('wholesale_price')}}" class="form-control" step="0.01">
                        </div>
                    </div>
                     <div class="col-md-3">
                        <div class="form-group">
                            <label for="retail_price" class="col-form-label font-weight-bold">Retail Price</label>
                            <input id="retail_price" type="number" name="retail_price" placeholder="0.00" value="{{old('retail_price')}}" class="form-control" step="0.01">
                        </div>
                    </div>
                     <div class="col-md-3">
                        <div class="form-group">
                            <label for="walkin_price" class="col-form-label font-weight-bold">Walk-in Price</label>
                            <input id="walkin_price" type="number" name="walkin_price" placeholder="0.00" value="{{old('walkin_price')}}" class="form-control" step="0.01">
                        </div>
                    </div>
                     <div class="col-md-3">
                        <div class="form-group">
                            <label for="salesman_price" class="col-form-label font-weight-bold">Salesman Price</label>
                            <input id="salesman_price" type="number" name="salesman_price" placeholder="0.00" value="{{old('salesman_price')}}" class="form-control" step="0.01">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description" class="col-form-label font-weight-bold">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="2">{{old('description')}}</textarea>
                </div>

                <hr class="my-4">
                <h6 class="font-weight-bold text-secondary mb-3">Included Products</h6>

                <div class="table-responsive">
                    <table class="table table-bordered" id="bundle-items-table">
                        <thead class="bg-light text-dark">
                            <tr>
                                <th style="width: 70%;">Product</th>
                                <th>Quantity</th>
                                <th style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="bundle-items-body">
                            <tr>
                                <td>
                                    <select name="product_id[]" class="form-control selectpicker" data-live-search="true" required>
                                        <option value="">-- Select Product --</option>
                                        @foreach($products as $product)
                                            <option value="{{$product->id}}">{{$product->title}} (SKU: {{$product->sku}})</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="quantity[]" class="form-control" min="1" value="1" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-item rounded-circle"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mb-4">
                    <button type="button" id="add-bundle-item" class="btn btn-info btn-sm rounded-pill"><i class="fas fa-plus mr-1"></i> Add Product to Bundle</button>
                </div>

                <div class="form-group mb-0 text-right">
                    <a href="{{route('bundles.index')}}" class="btn btn-light rounded-pill px-4 mr-2 border">Back</a>
                    <button class="btn btn-primary rounded-pill px-5 shadow-sm" type="submit">Create Bundle</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"></script>
<script>
    $(document).ready(function() {
        $('#add-bundle-item').click(function() {
            let row = $('#bundle-items-body tr:first').clone();
            row.find('input').val(1);
            row.find('.bootstrap-select').replaceWith(function() { return $('select', this); });
            $('#bundle-items-body').append(row);
            $('.selectpicker').selectpicker('render');
        });

        $(document).on('click', '.remove-item', function() {
            if ($('#bundle-items-body tr').length > 1) {
                $(this).closest('tr').remove();
            }
        });
    });
</script>
@endpush
