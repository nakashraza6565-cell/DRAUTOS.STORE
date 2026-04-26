@extends('backend.layouts.master')

@section('main-content')

<div class="card">
    <h5 class="card-header">Edit Die</h5>
    <div class="card-body">
      <form method="post" action="{{route('die-management.update',$die->id)}}">
        @csrf 
        @method('PATCH')
        <div class="form-group">
          <label for="inputName" class="col-form-label">Name <span class="text-danger">*</span></label>
          <input id="inputName" type="text" name="name" placeholder="Enter name"  value="{{$die->name}}" class="form-control">
          @error('name')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
            <label for="rack_number" class="col-form-label">Rack Number</label>
            <input id="rack_number" type="text" name="rack_number" placeholder="Enter rack number"  value="{{$die->rack_number}}" class="form-control">
        </div>

        <div class="form-group">
            <label for="maker" class="col-form-label">Maker</label>
            <input id="maker" type="text" name="maker" placeholder="Enter maker name"  value="{{$die->maker}}" class="form-control">
        </div>

        <div class="form-group">
            <label for="maker_phone" class="col-form-label">Maker Phone Number</label>
            <input id="maker_phone" type="text" name="maker_phone" placeholder="Enter maker phone number"  value="{{$die->maker_phone}}" class="form-control">
        </div>

        <div class="form-group">
            <label for="die_type" class="col-form-label">Die Type</label>
            <input id="die_type" type="text" name="die_type" placeholder="e.g. Casting, Molding"  value="{{$die->die_type}}" class="form-control">
        </div>

        <div class="form-group">
            <label for="custody_of" class="col-form-label">Custody Of</label>
            <input id="custody_of" type="text" name="custody_of" placeholder="Enter person name"  value="{{$die->custody_of}}" class="form-control">
        </div>

        <div class="form-group">
            <label for="custody_phone" class="col-form-label">Custody Phone Number</label>
            <input id="custody_phone" type="text" name="custody_phone" placeholder="Enter custody phone number"  value="{{$die->custody_phone}}" class="form-control">
        </div>

        <div class="form-group">
            <label for="quality_status" class="col-form-label">Quality Status</label>
            <select name="quality_status" class="form-control">
                <option value="good" {{(($die->quality_status=='good')? 'selected' : '')}}>Good</option>
                <option value="maintenance_required" {{(($die->quality_status=='maintenance_required')? 'selected' : '')}}>Maintenance Required</option>
                <option value="damaged" {{(($die->quality_status=='damaged')? 'selected' : '')}}>Damaged</option>
            </select>
        </div>
        
        <div class="form-group">
          <label for="inputPhoto" class="col-form-label">Photo</label>
          <div class="input-group">
              <span class="input-group-btn">
                  <a id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-primary text-white">
                  <i class="fa fa-picture-o"></i> Choose
                  </a>
              </span>
          <input id="thumbnail" class="form-control" type="text" name="photo" value="{{$die->photo}}">
        </div>
        <div id="holder" style="margin-top:15px;max-height:100px;">
          @if($die->photo)
              <img src="{{$die->photo}}" style="max-height:100px;">
          @endif
        </div>
          @error('photo')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="status" class="col-form-label">Status <span class="text-danger">*</span></label>
          <select name="status" class="form-control">
            <option value="active" {{(($die->status=='active')? 'selected' : '')}}>Active</option>
            <option value="inactive" {{(($die->status=='inactive')? 'selected' : '')}}>Inactive</option>
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

@push('scripts')
<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script>
    $('#lfm').filemanager('image');
</script>
@endpush
