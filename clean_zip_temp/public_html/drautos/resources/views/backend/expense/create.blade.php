@extends('backend.layouts.master')

@section('main-content')
<div class="card">
    <h5 class="card-header">Add Expense</h5>
    <div class="card-body">
      <form method="post" action="{{route('expenses.store')}}">
        {{csrf_field()}}
        <div class="form-group">
          <label>Title <span class="text-danger">*</span></label>
          <input type="text" name="title" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Amount <span class="text-danger">*</span></label>
            <input type="number" step="0.01" name="amount" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Date <span class="text-danger">*</span></label>
            <input type="date" name="date" class="form-control" value="{{date('Y-m-d')}}" required>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="3"></textarea>
        </div>
        <button class="btn btn-success" type="submit">Submit</button>
      </form>
    </div>
</div>
@endsection
