@extends('backend.layouts.master')

@section('main-content')

<div class="card">
    <h5 class="card-header">Edit Supplier</h5>
    <div class="card-body">
      <form method="post" action="{{route('suppliers.update',$supplier->id)}}">
        @csrf 
        @method('PATCH')
        <div class="form-group">
          <label for="inputName" class="col-form-label">Name <span class="text-danger">*</span></label>
          <input id="inputName" type="text" name="name" placeholder="Enter name"  value="{{$supplier->name}}" class="form-control">
          @error('name')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
            <label for="company_name" class="col-form-label">Company Name</label>
            <input id="company_name" type="text" name="company_name" placeholder="Enter company name"  value="{{$supplier->company_name}}" class="form-control">
        </div>

        <div class="form-group">
            <label for="email" class="col-form-label">Email</label>
            <input id="email" type="email" name="email" placeholder="Enter email"  value="{{$supplier->email}}" class="form-control">
        </div>

        <div class="form-group">
            <label for="phone" class="col-form-label">Phone</label>
            <input id="phone" type="text" name="phone" placeholder="Enter phone"  value="{{$supplier->phone}}" class="form-control">
        </div>

        <div class="form-group">
            <label for="address" class="col-form-label">Address</label>
            <textarea name="address" class="form-control">{{$supplier->address}}</textarea>
        </div>
        
        <div class="form-group">
          <label for="status" class="col-form-label">Status <span class="text-danger">*</span></label>
          <select name="status" class="form-control">
            <option value="active" {{(($supplier->status=='active')? 'selected' : '')}}>Active</option>
            <option value="inactive" {{(($supplier->status=='inactive')? 'selected' : '')}}>Inactive</option>
        </select>
          @error('status')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        <div class="form-group mb-3">
           <button class="btn btn-success" type="submit">Update</button>
        </div>
      </form>
    </div>
</div>

@endsection
