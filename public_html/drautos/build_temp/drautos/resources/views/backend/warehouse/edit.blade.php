@extends('backend.layouts.master')

@section('main-content')

<div class="card">
    <h5 class="card-header">Edit Warehouse</h5>
    <div class="card-body">
      <form method="post" action="{{route('warehouses.update',$warehouse->id)}}">
        @csrf 
        @method('PATCH')
        <div class="form-group">
          <label for="inputName" class="col-form-label">Name <span class="text-danger">*</span></label>
          <input id="inputName" type="text" name="name" placeholder="Enter name"  value="{{$warehouse->name}}" class="form-control">
          @error('name')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
            <label for="location" class="col-form-label">Location</label>
            <input id="location" type="text" name="location" placeholder="Enter location"  value="{{$warehouse->location}}" class="form-control">
        </div>

        <div class="form-group">
            <label for="contact_person" class="col-form-label">Contact Person</label>
            <input id="contact_person" type="text" name="contact_person" placeholder="Enter contact person"  value="{{$warehouse->contact_person}}" class="form-control">
        </div>

        <div class="form-group">
            <label for="phone_number" class="col-form-label">Phone</label>
            <input id="phone_number" type="text" name="phone_number" placeholder="Enter phone"  value="{{$warehouse->phone_number}}" class="form-control">
        </div>
        
        <div class="form-group">
          <label for="status" class="col-form-label">Status <span class="text-danger">*</span></label>
          <select name="status" class="form-control">
            <option value="active" {{(($warehouse->status=='active')? 'selected' : '')}}>Active</option>
            <option value="inactive" {{(($warehouse->status=='inactive')? 'selected' : '')}}>Inactive</option>
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
