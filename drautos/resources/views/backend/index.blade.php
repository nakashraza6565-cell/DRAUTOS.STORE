@extends('backend.layouts.master')
@section('title','Danyal Autos || DASHBOARD')
@section('main-content')
<div class="container-fluid" style="padding: 1.5rem;">
    @include('backend.layouts.notification')
    
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Overview</h1>
        <button data-toggle="modal" data-target="#quickExpenseModal" class="btn btn-danger shadow-sm">
            <i class="fas fa-minus-circle fa-sm text-white-50"></i> Quick Expense
        </button>
    </div>

    <!-- Stats Row -->
    <div class="row">
        <!-- Categories -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Categories</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $category_count }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Products -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Products</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $product_count }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Orders -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $order_count }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Today Sales -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Today Sales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{ number_format($today_sales) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Expense Modal -->
    <div class="modal fade" id="quickExpenseModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold">Record Quick Expense</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form action="{{ route('expenses.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold">Amount (Rs.)</label>
                            <input type="number" name="amount" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold">Deduct From</label>
                            <select name="financial_account_id" class="form-control" required>
                                <option value="">-- Select Account --</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}" {{ $acc->id == ($staffAccId ?? null) ? 'selected' : '' }}>
                                        {{ $acc->name }} (Rs. {{ number_format($acc->current_balance) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-0">
                            <label class="small font-weight-bold">Description</label>
                            <textarea name="description" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger shadow">Save Expense</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
