@extends('backend.layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary float-left">Expenses</h6>
      <a href="{{route('expenses.create')}}" class="btn btn-primary btn-sm float-right"><i class="fas fa-plus"></i> Add Expense</a>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="expense-dataTable" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>Date</th>
              <th>Title</th>
              <th>Description</th>
              <th>Amount</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($expenses as $expense)   
                <tr>
                    <td>{{$expense->date}}</td>
                    <td>{{$expense->title}}</td>
                    <td>{{$expense->description}}</td>
                    <td>Rs. {{number_format($expense->amount, 2)}}</td>
                    <td>
                        <form method="POST" action="{{route('expenses.destroy',[$expense->id])}}">
                          @csrf 
                          @method('delete')
                              <button class="btn btn-danger btn-sm dltBtn" style="height:30px; width:30px;border-radius:50%"><i class="fas fa-trash-alt"></i></button>
                        </form>
                    </td>
                </tr>  
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
</div>
@endsection
