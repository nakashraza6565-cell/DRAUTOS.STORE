@extends('backend.layouts.master')

@section('main-content')

<div class="card">
    <h5 class="card-header">Add Die</h5>
    <div class="card-body">
      <form method="post" action="{{route('die-management.store')}}">
        {{csrf_field()}}
        <div class="form-group">
          <label for="inputName" class="col-form-label">Name <span class="text-danger">*</span></label>
          <input id="inputName" type="text" name="name" placeholder="Enter name"  value="{{old('name')}}" class="form-control">
          @error('name')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
            <label for="rack_number" class="col-form-label">Rack Number</label>
            <input id="rack_number" type="text" name="rack_number" placeholder="Enter rack number"  value="{{old('rack_number')}}" class="form-control">
        </div>

        <div class="form-group">
            <label for="maker" class="col-form-label">Maker</label>
            <input id="maker" type="text" name="maker" placeholder="Enter maker name"  value="{{old('maker')}}" class="form-control">
        </div>

        <div class="form-group">
            <label for="maker_phone" class="col-form-label">Maker Phone Number</label>
            <input id="maker_phone" type="text" name="maker_phone" placeholder="Enter maker phone number"  value="{{old('maker_phone')}}" class="form-control">
        </div>

        <div class="form-group">
            <label for="die_type" class="col-form-label">Die Type</label>
            <input id="die_type" type="text" name="die_type" placeholder="e.g. Casting, Molding"  value="{{old('die_type')}}" class="form-control">
        </div>

        <div class="form-group">
            <label for="custody_of" class="col-form-label">Custody Of</label>
            <input id="custody_of" type="text" name="custody_of" placeholder="Enter person name"  value="{{old('custody_of')}}" class="form-control">
        </div>

        <div class="form-group">
            <label for="custody_phone" class="col-form-label">Custody Phone Number</label>
            <input id="custody_phone" type="text" name="custody_phone" placeholder="Enter custody phone number"  value="{{old('custody_phone')}}" class="form-control">
        </div>

        <div class="form-group">
            <label for="quality_status" class="col-form-label">Quality Status</label>
            <select name="quality_status" class="form-control">
                <option value="good">Good</option>
                <option value="maintenance_required">Maintenance Required</option>
                <option value="damaged">Damaged</option>
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
          <input id="thumbnail" class="form-control" type="text" name="photo" value="{{old('photo')}}">
        </div>
        <div id="holder" style="margin-top:15px;max-height:100px;"></div>
          @error('photo')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="status" class="col-form-label">Status <span class="text-danger">*</span></label>
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

@endsection

@push('scripts')
<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script>
    $('#lfm').filemanager('image');
</script>
@endpush
