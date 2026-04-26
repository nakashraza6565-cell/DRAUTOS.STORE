@extends('backend.layouts.master')

@section('main-content')

<div class="card">
    <h5 class="card-header">Add User</h5>
    <div class="card-body">
      <form method="post" action="{{route('users.store')}}">
        {{csrf_field()}}
        <div class="form-group">
          <label for="inputTitle" class="col-form-label">Name</label>
        <input id="inputTitle" type="text" name="name" placeholder="Enter name"  value="{{old('name')}}" class="form-control">
        @error('name')
        <span class="text-danger">{{$message}}</span>
        @enderror
        </div>

        <div class="form-group">
            <label for="inputEmail" class="col-form-label">Email</label>
          <input id="inputEmail" type="email" name="email" placeholder="Enter email"  value="{{old('email')}}" class="form-control">
          @error('email')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
            <label for="inputPassword" class="col-form-label">Password</label>
          <input id="inputPassword" type="password" name="password" placeholder="Enter password"  value="{{old('password')}}" class="form-control">
          @error('password')
          <span class="text-danger">{{$message}}</span>
          @enderror
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
        <img id="holder" style="margin-top:15px;max-height:100px;">
          @error('photo')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        @php 
        $roles=DB::table('users')->select('role')->get();
        @endphp
        <div class="form-group">
            <label for="role" class="col-form-label">Role</label>
            <select name="role" class="form-control">
                <option value="">-----Select Role-----</option>
                @foreach($roles as $role)
                    <option value="{{$role->role}}">{{$role->role}}</option>
                @endforeach
            </select>
          @error('role')
          <span class="text-danger">{{$message}}</span>
          @enderror
          </div>
          
        <div class="form-group">
            <label for="customer_type" class="col-form-label">Customer Type</label>
            <select name="customer_type" class="form-control">
                <option value="">-----Select Type-----</option>
                <option value="wholesale" {{old('customer_type')=='wholesale' ? 'selected' : ''}}>Wholesale Customer</option>
                <option value="retail" {{old('customer_type')=='retail' ? 'selected' : ''}}>Retail Customer</option>
                <option value="walkin" {{old('customer_type')=='walkin' ? 'selected' : ''}}>Walk-in Customer</option>
                <option value="salesman" {{old('customer_type')=='salesman' ? 'selected' : ''}}>Salesman Customer</option>
            </select>
            @error('customer_type')
            <span class="text-danger">{{$message}}</span>
            @enderror
        </div>
          <div class="form-group">
            <label for="status" class="col-form-label">Status</label>
            <select name="status" class="form-control">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="pending">Pending</option>
            </select>
          @error('status')
          <span class="text-danger">{{$message}}</span>
          @enderror
          </div>

          <div class="form-group">
              <label for="phone" class="col-form-label">Phone</label>
              <input id="phone" type="text" name="phone" placeholder="Enter phone number" value="{{old('phone')}}" class="form-control">
              @error('phone')
              <span class="text-danger">{{$message}}</span>
              @enderror
          </div>

          <div class="form-group">
              <label for="courier_company" class="col-form-label">Courier Company</label>
              <input id="courier_company" type="text" name="courier_company" placeholder="Enter courier company" value="{{old('courier_company')}}" class="form-control">
              @error('courier_company')
              <span class="text-danger">{{$message}}</span>
              @enderror
          </div>

          <div class="form-group">
              <label for="courier_number" class="col-form-label">Courier Number</label>
              <input id="courier_number" type="text" name="courier_number" placeholder="Enter courier number" value="{{old('courier_number')}}" class="form-control">
              @error('courier_number')
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
