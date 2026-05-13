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

<!-- Modal Placeholder -->
<div class="modal fade" id="quickExpenseModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header"><h5>Quick Expense</h5></div>
            <div class="modal-body">Modal content here...</div>
        </div>
    </div>
</div>

@endsection
