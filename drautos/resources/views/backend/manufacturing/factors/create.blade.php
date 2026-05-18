@extends('backend.layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Add New Factor (Material/Labor)</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="{{route('manufacturing.production-factors.store')}}">
            {{csrf_field()}}
            <div class="form-group">
                <label for="name">Name <span class="text-danger">*</span></label>
                <input id="name" type="text" name="name" placeholder="e.g. Raw Steel, Turning Labor" value="{{old('name')}}" class="form-control" required>
                @error('name')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="type">Type <span class="text-danger">*</span></label>
                <select name="type" id="type" class="form-control" required>
                    <option value="material" {{old('type')=='material' ? 'selected' : ''}}>Raw Material</option>
                    <option value="labor" {{old('type')=='labor' ? 'selected' : ''}}>Labor / Subcontractor Procedure</option>
                    <option value="overhead" {{old('type')=='overhead' ? 'selected' : ''}}>Overhead</option>
                </select>
            </div>

            <div class="form-group">
                <label for="unit">Unit <span class="text-danger">*</span></label>
                <input id="unit" type="text" name="unit" placeholder="e.g. kg, pcs, piece, hr" value="{{old('unit')}}" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="cost_price">Default Cost Price (Optional)</label>
                <input id="cost_price" type="number" step="0.01" name="cost_price" placeholder="0.00" value="{{old('cost_price')}}" class="form-control">
                <small class="text-muted">You can set a default cost here, or enter the exact cost during the production run.</small>
            </div>

            <div class="form-group">
                <label for="stock_quantity">Starting Stock Quantity (Optional)</label>
                <input id="stock_quantity" type="number" step="0.01" name="stock_quantity" placeholder="0.00" value="{{old('stock_quantity', '0.00')}}" class="form-control">
                <small class="text-muted">For raw materials, you can initialize the stock here.</small>
            </div>

            <div class="form-group">
                <label for="status">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-control">
                    <option value="active" {{old('status')=='active' ? 'selected' : ''}}>Active</option>
                    <option value="inactive" {{old('status')=='inactive' ? 'selected' : ''}}>Inactive</option>
                </select>
            </div>

            <div class="form-group mb-3">
                <button type="reset" class="btn btn-warning">Reset</button>
                <button class="btn btn-success" type="submit">Submit</button>
            </div>
        </form>
    </div>
</div>
@endsection
