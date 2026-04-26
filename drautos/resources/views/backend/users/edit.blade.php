@extends('backend.layouts.master')

@section('main-content')

<div class="card">
    <h5 class="card-header">Edit User</h5>
    <div class="card-body">
      <form method="post" action="{{route('users.update',$user->id)}}">
        @csrf 
        @method('PATCH')
        <div class="form-group">
          <label for="inputTitle" class="col-form-label">Name</label>
        <input id="inputTitle" type="text" name="name" placeholder="Enter name"  value="{{$user->name}}" class="form-control">
        @error('name')
        <span class="text-danger">{{$message}}</span>
        @enderror
        </div>

        <div class="form-group">
            <label for="inputEmail" class="col-form-label">Email</label>
          <input id="inputEmail" type="email" name="email" placeholder="Enter email"  value="{{$user->email}}" class="form-control">
          @error('email')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
            <label for="inputPassword" class="col-form-label">Password (Leave blank to keep current)</label>
          <input id="inputPassword" type="password" name="password" placeholder="Enter new password" value="" class="form-control">
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
            <input id="thumbnail" class="form-control" type="text" name="photo" value="{{$user->photo}}">
        </div>
        <img id="holder" style="margin-top:15px;max-height:100px;">
          @error('photo')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        @php 
        $roles=DB::table('users')->select('role')->where('id',$user->id)->get();
        // dd($roles);
        @endphp
        <div class="form-group">
            <label for="role" class="col-form-label">Role</label>
            <select name="role" class="form-control">
                <option value="">-----Select Role-----</option>
                @foreach($roles as $role)
                    <option value="{{$role->role}}" {{(($role->role=='admin') ? 'selected' : '')}}>Admin</option>
                    <option value="{{$role->role}}" {{(($role->role=='user') ? 'selected' : '')}}>User</option>
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
                <option value="wholesale" {{$user->customer_type=='wholesale' ? 'selected' : ''}}>Wholesale Customer</option>
                <option value="retail" {{$user->customer_type=='retail' ? 'selected' : ''}}>Retail Customer</option>
                <option value="walkin" {{$user->customer_type=='walkin' ? 'selected' : ''}}>Walk-in Customer</option>
                <option value="salesman" {{$user->customer_type=='salesman' ? 'selected' : ''}}>Salesman Customer</option>
            </select>
            @error('customer_type')
            <span class="text-danger">{{$message}}</span>
            @enderror
        </div>
          <div class="form-group">
            <label for="status" class="col-form-label">Status</label>
            <select name="status" class="form-control">
                <option value="active" {{(($user->status=='active') ? 'selected' : '')}}>Active</option>
                <option value="inactive" {{(($user->status=='inactive') ? 'selected' : '')}}>Inactive</option>
                <option value="pending" {{(($user->status=='pending') ? 'selected' : '')}}>Pending</option>
            </select>
          @error('status')
          <span class="text-danger">{{$message}}</span>
          @enderror
          </div>

          <div class="form-group">
              <label for="phone" class="col-form-label">Phone</label>
              <input id="phone" type="text" name="phone" placeholder="Enter phone number" value="{{$user->phone}}" class="form-control">
              @error('phone')
              <span class="text-danger">{{$message}}</span>
              @enderror
          </div>

          <div class="form-group">
              <label for="address" class="col-form-label">Address</label>
              <textarea id="address" name="address" placeholder="Enter address" class="form-control">{{$user->address}}</textarea>
              @error('address')
              <span class="text-danger">{{$message}}</span>
              @enderror
          </div>

          <div class="form-group">
              <label for="city" class="col-form-label">City</label>
              <input id="city" type="text" name="city" placeholder="Enter city" value="{{$user->city}}" class="form-control">
              @error('city')
              <span class="text-danger">{{$message}}</span>
              @enderror
          </div>

          <div class="form-group">
              <label for="shipping_address" class="col-form-label">Shipping Address</label>
              <textarea id="shipping_address" name="shipping_address" placeholder="Enter shipping address" class="form-control">{{$user->shipping_address}}</textarea>
              @error('shipping_address')
              <span class="text-danger">{{$message}}</span>
              @enderror
          </div>

          <div class="form-group">
              <label for="shipping_city" class="col-form-label">Shipping City</label>
              <input id="shipping_city" type="text" name="shipping_city" placeholder="Enter shipping city" value="{{$user->shipping_city}}" class="form-control">
              @error('shipping_city')
              <span class="text-danger">{{$message}}</span>
              @enderror
          </div>

          <div class="form-group">
              <label for="courier_company" class="col-form-label">Courier Company</label>
              <input id="courier_company" type="text" name="courier_company" placeholder="Enter courier company" value="{{$user->courier_company}}" class="form-control">
              @error('courier_company')
              <span class="text-danger">{{$message}}</span>
              @enderror
          </div>

          <div class="form-group">
              <label for="courier_number" class="col-form-label">Courier Number</label>
              <input id="courier_number" type="text" name="courier_number" placeholder="Enter courier number" value="{{$user->courier_number}}" class="form-control">
              @error('courier_number')
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
