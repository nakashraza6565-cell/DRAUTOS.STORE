@extends('backend.layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Edit Packaging Material: {{$item->name}}</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="{{route('packaging.update', $item->id)}}">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="type">Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-control" required>
                            <option value="sticker" {{$item->type == 'sticker' ? 'selected' : ''}}>Sticker</option>
                            <option value="box" {{$item->type == 'box' ? 'selected' : ''}}>Box</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{$item->name}}" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="size">Size / Dimensions</label>
                        <input type="text" name="size" class="form-control" value="{{$item->size}}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="cost">Base Cost (per unit) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="cost" class="form-control" value="{{$item->cost}}" required>
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
                            <option value="{{$supplier->id}}" {{$item->supplier_id == $supplier->id ? 'selected' : ''}}>{{$supplier->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="stock">Current Stock (Direct Override)</label>
                        <input type="number" step="0.01" name="stock" class="form-control" value="{{$item->stock}}">
                        <small class="text-muted text-warning">Warning: Manually changing stock may bypass inventory history.</small>
                    </div>
                </div>
            </div>

            <div class="form-group mb-3">
                <button class="btn btn-success" type="submit">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
