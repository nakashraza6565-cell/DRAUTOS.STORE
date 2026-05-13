@extends('backend.layouts.master')
@section('title','Danyal Autos || PREMIUM DASHBOARD')
@section('main-content')

<div class="container-fluid premium-bg" style="min-height: 100vh; padding: 1rem;">
    @include('backend.layouts.notification')
    
    <!-- Header Section -->
    <div class="row mb-4 align-items-center">
        <div class="col-lg-6 mb-3 mb-lg-0 text-center text-lg-left">
            <h1 class="font-weight-bolder text-gray-900 mb-1" style="font-size: 2.2rem;">
                Hello, {{ auth()->user()->name ?? 'Admin' }}! 👋
            </h1>
            <p class="text-muted mb-0">Here is what's happening today.</p>
        </div>
        <div class="col-lg-6 text-center text-lg-right">
             <button data-toggle="modal" data-target="#quickExpenseModal" class="btn btn-danger rounded-pill px-4 shadow">
                <i class="fas fa-minus-circle mr-2"></i> Quick Expense
             </button>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Today Sales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{ number_format($today_sales ?? 0) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Quick Expense Modal -->
<div class="modal fade" id="quickExpenseModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-minus-circle mr-2"></i> Record Quick Expense</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('expense.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="small font-weight-bold">Amount (Rs.) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control form-control-lg border-0 bg-light" placeholder="0.00" required>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="small font-weight-bold">Deduct From <span class="text-danger">*</span></label>
                        <select name="financial_account_id" class="form-control border-0 bg-light" required>
                            <option value="">-- Select Account --</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}" {{ $acc->id == ($staffAccId ?? null) ? 'selected' : '' }}>
                                    {{ $acc->name }} (Bal: Rs. {{ number_format($acc->current_balance) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-0">
                        <label class="small font-weight-bold">Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control border-0 bg-light" rows="3" placeholder="What was this expense for?" required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 shadow">SAVE EXPENSE</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
