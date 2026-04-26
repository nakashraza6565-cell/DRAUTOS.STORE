@extends('backend.layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Add New Packaging Material</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="{{route('packaging.store')}}">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="type">Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-control" required>
                            <option value="sticker">Sticker</option>
                            <option value="box">Box</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{old('name')}}" required placeholder="e.g. Standard Box, Logo Sticker">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="size">Size / Dimensions</label>
                        <input type="text" name="size" class="form-control" value="{{old('size')}}" placeholder="e.g. 10x12 inch, 2cm">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="cost">Base Cost (per unit) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="cost" class="form-control" value="{{old('cost', 0)}}" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="supplier_id">Preferred Supplier</label>
                        <select name="supplier_id" class="form-control">
                            <option value="">-- Select Supplier --</option>
                            @foreach($suppliers as $supplier)
                            <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="stock">Initial Stock</label>
                        <input type="number" step="0.01" name="stock" class="form-control" value="{{old('stock', 0)}}">
                    </div>
                </div>
            </div>

            <div class="form-group mb-3">
                <button type="reset" class="btn btn-warning">Reset</button>
                <button class="btn btn-success" type="submit">Submit</button>
            </div>
        </form>
    </div>
</div>
@endsection
