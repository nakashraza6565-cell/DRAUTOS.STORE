@extends('backend.layouts.master')
@section('title','Orders & Billing - Select Customer')
@section('main-content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
            <h6 class="m-0 font-weight-bold text-primary">Orders & Billing: Select Customer</h6>
            <div class="d-flex" style="gap: 10px;">
                <a href="{{route('order.index')}}?show_all=1" class="btn btn-outline-primary btn-sm shadow-sm">
                    <i class="fas fa-list fa-sm mr-1"></i> View All Orders
                </a>
                <a href="{{route('admin.pos')}}" class="btn btn-success btn-sm shadow-sm">
                    <i class="fas fa-plus fa-sm mr-1"></i> New Local Sale (POS)
                </a>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" class="mb-4">
                <div class="input-group shadow-sm">
                    <input type="text" name="search" class="form-control border-0 bg-light" placeholder="Search customer by name, phone or email..." value="{{request()->search}}">
                    <div class="input-group-append">
                        <button class="btn btn-primary px-4" type="submit">
                            <i class="fas fa-search mr-2"></i> Search
                        </button>
                        @if(request()->search)
                            <a href="{{route('order.index')}}" class="btn btn-secondary px-3">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
            <div class="table-responsive">
                @if(count($customersWithOrders) > 0)
                <table class="table table-hover order-table-to-cards overview-table-to-cards" id="customer-orders-table" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>Customer Name</th>
                            <th>Total Orders</th>
                            <th>Total Sales Value</th>
                            <th>Last Order</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customersWithOrders as $customer)
                            <tr>
                                <td data-title="Customer">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-circle bg-primary text-white mr-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                            <i class="fas fa-user text-xs"></i>
                                        </div>
                                        <div>
                                            <div class="font-weight-bold text-primary">{{ $customer->name }}</div>
                                            <div class="small text-muted">{{ $customer->phone ?: 'No Phone' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td data-title="Orders">
                                    <span class="badge badge-info px-3 py-2">{{ $customer->orders_count }} Orders</span>
                                </td>
                                <td data-title="Total Sales" class="font-weight-bold text-dark">
                                    Rs. {{ number_format($customer->total_sales, 0) }}
                                </td>

                                <td data-title="Last Order">
                                    @if($customer->last_order)
                                        <div class="small text-dark font-weight-bold">{{ $customer->last_order->created_at->format('d M Y') }}</div>
                                        <div class="small text-muted">#{{ $customer->last_order->order_number }}</div>
                                    @else
                                        <span class="text-muted small">N/A</span>
                                    @endif
                                </td>
                                <td data-title="Action">
                                    <a href="{{ route('order.index', ['user_id' => $customer->id]) }}" class="btn btn-primary btn-sm rounded-pill px-4">
                                        View Orders <i class="fas fa-chevron-right ml-1" style="font-size: 0.7rem;"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-users-slash fa-4x text-gray-300 mb-3"></i>
                    <h6 class="text-muted">No customers with orders found.</h6>
                    <p class="text-muted small">Once customers place orders via POS or Website, they will appear here.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table-hover tbody tr:hover {
        background-color: rgba(78, 115, 223, 0.05);
        transition: background-color 0.2s ease;
    }
    .icon-circle {
        flex-shrink: 0;
    }
    .badge-info {
        background-color: #e0f2fe;
        color: #0369a1;
        border: none;
        font-weight: 600;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    $('#customer-orders-table').DataTable({
        "order": [],
        "pageLength": 25
    });
});
</script>
@endpush
