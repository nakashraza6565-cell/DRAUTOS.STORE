@extends('backend.layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Edit Factor: {{$factor->name}}</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="{{route('manufacturing.production-factors.update', $factor->id)}}">
            @csrf
            @method('PATCH')
            <div class="form-group">
                <label for="name">Name <span class="text-danger">*</span></label>
                <input id="name" type="text" name="name" value="{{$factor->name}}" class="form-control" required>
                @error('name')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="type">Type <span class="text-danger">*</span></label>
                <select name="type" id="type" class="form-control" required>
                    <option value="material" {{(($factor->type=='material') ? 'selected' : '')}}>Raw Material</option>
                    <option value="labor" {{(($factor->type=='labor') ? 'selected' : '')}}>Labor / Subcontractor Procedure</option>
                    <option value="overhead" {{(($factor->type=='overhead') ? 'selected' : '')}}>Overhead</option>
                </select>
            </div>

            <div class="form-group">
                <label for="unit">Unit <span class="text-danger">*</span></label>
                <input id="unit" type="text" name="unit" value="{{$factor->unit}}" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="cost_price">Default Cost Price (Optional)</label>
                <input id="cost_price" type="number" step="0.01" name="cost_price" value="{{$factor->cost_price}}" class="form-control">
            </div>

            <div class="form-group">
                <label for="stock_quantity">Stock Quantity (Current Stock)</label>
                <input id="stock_quantity" type="number" step="0.01" name="stock_quantity" value="{{$factor->stock_quantity}}" class="form-control">
            </div>

            <div class="form-group">
                <label for="status">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-control">
                    <option value="active" {{(($factor->status=='active') ? 'selected' : '')}}>Active</option>
                    <option value="inactive" {{(($factor->status=='inactive') ? 'selected' : '')}}>Inactive</option>
                </select>
            </div>

            <div class="form-group mb-3">
                <button class="btn btn-success" type="submit">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
