@extends('backend.layouts.master')

@section('main-content')

<div class="card">
    <h5 class="card-header">Edit Staff Member</h5>
    <div class="card-body">
      <form method="post" action="{{route('staff.update',$staff->id)}}">
        @csrf 
        @method('PATCH')
        <div class="form-group">
          <label for="inputTitle" class="col-form-label">Name</label>
        <input id="inputTitle" type="text" name="name" placeholder="Enter name"  value="{{$staff->name}}" class="form-control">
        @error('name')
        <span class="text-danger">{{$message}}</span>
        @enderror
        </div>

        <div class="form-group">
            <label for="inputEmail" class="col-form-label">Email</label>
          <input id="inputEmail" type="email" name="email" placeholder="Enter email"  value="{{$staff->email}}" class="form-control">
          @error('email')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
            <label for="inputPassword" class="col-form-label">Password <small>(Leave blank to keep current)</small></label>
          <input id="inputPassword" type="password" name="password" placeholder="Enter password (optional)" class="form-control">
          @error('password')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
            <label for="inputPhone" class="col-form-label">Phone</label>
            <input id="inputPhone" type="text" name="phone" placeholder="Enter phone"  value="{{$staff->phone}}" class="form-control">
            @error('phone')
            <span class="text-danger">{{$message}}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="role" class="col-form-label">Role <span class="text-danger">*</span></label>
            <select name="role" class="form-control" required>
                <option value="">--Select Role--</option>
                @foreach($roles as $role)
                    <option value="{{$role->name}}" {{ $staff->hasRole($role->name) ? 'selected' : '' }}>{{ucfirst($role->name)}}</option>
                @endforeach
            </select>
          @error('role')
          <span class="text-danger">{{$message}}</span>
          @enderror
          </div>
          <div class="form-group">
            <label for="status" class="col-form-label">Status</label>
            <select name="status" class="form-control">
                <option value="active" {{(($staff->status=='active') ? 'selected' : '')}}>Active</option>
                <option value="inactive" {{(($staff->status=='inactive') ? 'selected' : '')}}>Inactive</option>
            </select>
          @error('status')
          <span class="text-danger">{{$message}}</span>
          @enderror
          </div>

          <div class="form-row">
            <div class="form-group col-md-4">
                <label for="salary_type" class="col-form-label">Salary Structure</label>
                <select name="salary_type" id="salary_type" class="form-control">
                    <option value="monthly" {{(($staff->salary_type=='monthly') ? 'selected' : '')}}>Monthly</option>
                    <option value="daily" {{(($staff->salary_type=='daily') ? 'selected' : '')}}>Daily Wage</option>
                    <option value="weekly" {{(($staff->salary_type=='weekly') ? 'selected' : '')}}>Weekly</option>
                </select>
                @error('salary_type')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="form-group col-md-4">
                <label for="base_salary" class="col-form-label">Base Monthly Salary (Rs.)</label>
                <input id="base_salary" type="number" step="0.01" name="base_salary" placeholder="Enter base salary" value="{{$staff->base_salary}}" class="form-control">
                @error('base_salary')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="form-group col-md-4">
                <label for="daily_wage" class="col-form-label">Daily Wage (Rs.)</label>
                <input id="daily_wage" type="number" step="0.01" name="daily_wage" placeholder="Enter daily wage" value="{{$staff->daily_wage}}" class="form-control">
                @error('daily_wage')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="form-group col-md-6">
                <label for="overtime_rate" class="col-form-label">Overtime Rate (Per Hour Rs.)</label>
                <input id="overtime_rate" type="number" step="0.01" name="overtime_rate" placeholder="Enter overtime rate" value="{{$staff->overtime_rate}}" class="form-control">
                @error('overtime_rate')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
          </div>
        <div class="form-group mb-3">
           <button class="btn btn-success" type="submit">Update</button>
        </div>
      </form>
    </div>
</div>

@endsection
