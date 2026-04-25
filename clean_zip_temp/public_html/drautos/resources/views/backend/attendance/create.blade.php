@extends('backend.layouts.master')

@section('main-content')

<div class="card">
    <h5 class="card-header">Add Attendance Entry</h5>
    <div class="card-body">
      <form method="post" action="{{route('attendance.store')}}">
        {{csrf_field()}}
        
        <div class="form-group">
            <label for="user_id">Select Employee <span class="text-danger">*</span></label>
            <select name="user_id" class="form-control select2">
                @foreach($staff as $user)
                    <option value="{{$user->id}}">{{$user->name}} ({{$user->role}})</option>
                @endforeach
            </select>
            @error('user_id')
            <span class="text-danger">{{$message}}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="date">Date <span class="text-danger">*</span></label>
            <input type="date" name="date" value="{{date('Y-m-d')}}" class="form-control">
            @error('date')
            <span class="text-danger">{{$message}}</span>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="clock_in">Clock In Time</label>
                    <input type="time" name="clock_in" class="form-control">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="clock_out">Clock Out Time</label>
                    <input type="time" name="clock_out" class="form-control">
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="status">Attendance Status <span class="text-danger">*</span></label>
            <select name="status" class="form-control">
                <option value="present">Present</option>
                <option value="late">Late</option>
                <option value="absent">Absent</option>
                <option value="on_leave">On Leave</option>
            </select>
            @error('status')
            <span class="text-danger">{{$message}}</span>
            @enderror
        </div>

        <div class="form-group mb-3">
           <button class="btn btn-success" type="submit">Save Attendance</button>
        </div>
      </form>
    </div>
</div>

@endsection
