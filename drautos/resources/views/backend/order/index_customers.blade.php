@extends('backend.layouts.master')
@section('title','Orders & Billing')
@section('main-content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white border-bottom-0">
            <h6 class="m-0 font-weight-bold text-primary">Orders & Billing (Select Customer)</h6>
            <div class="d-flex align-items-center" style="gap: 10px;">
                <a href="{{route('order.index')}}?show_all=1" class="btn btn-outline-primary btn-sm shadow-sm px-3">
                    <i class="fas fa-list-check mr-1"></i> Global Order List
                </a>
                <a href="{{route('users.create')}}" class="btn btn-primary btn-sm shadow-sm px-3">
                    <i class="fas fa-user-plus mr-1"></i> New Customer
                </a>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" class="mb-4" id="ledger-filter-form">
                <div class="row">
                    <div class="col-md-8">
                        <div class="input-group">
                            <input type="text" name="search" id="customer-search" class="form-control" placeholder="Search customer by name, phone or email..." value="{{request()->search}}" autocomplete="off">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i> Search
                                </button>
                                @if(request()->search || request()->city)
                                    <a href="{{route('order.index')}}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Clear
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select name="city" class="form-control" onchange="this.form.submit()">
                            <option value="">-- All Cities --</option>
                            @foreach($cities as $city)
                                <option value="{{$city}}" {{request()->city == $city ? 'selected' : ''}}>{{$city}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>

            <div class="table-responsive" id="ledger-list-container">
                <table class="table table-bordered table-hover order-table-to-cards customer-table-to-cards" width="100%" cellspacing="0">
                    <thead class="bg-gray-100">
                        <tr>
                            <th>Customer Details</th>
                            <th>Location</th>
                            <th>Account Type</th>
                            <th class="text-right">Current Balance</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            <tr style="cursor: pointer;" onclick="window.location='{{route('order.index')}}?user_id={{$customer->id}}'">
                                <td data-title="Customer">
                                    <div class="font-weight-bold text-dark">{{$customer->name}}</div>
                                    <div class="small text-muted"><i class="fas fa-phone-alt mr-1"></i> {{$customer->phone ?: 'No phone'}}</div>
                                </td>
                                <td data-title="Location">
                                    <div class="small">{{$customer->city ?: 'N/A'}}</div>
                                    <div class="text-xs text-muted text-truncate" style="max-width: 150px;">{{$customer->address ?: ''}}</div>
                                </td>
                                <td data-title="Type">
                                    <span class="badge badge-info-soft text-info text-capitalize px-2 py-1" style="background: #e0f2fe; color: #0369a1;">
                                        {{$customer->customer_type ?: 'Retail'}}
                                    </span>
                                </td>
                                <td data-title="Balance" class="text-right">
                                    <div class="{{$customer->current_balance > 0 ? 'text-danger' : ($customer->current_balance < 0 ? 'text-success' : 'text-muted')}} font-weight-bold">
                                        Rs. {{number_format($customer->current_balance, 2)}}
                                    </div>
                                    @if($customer->latestPayment)
                                        <div class="text-xs text-muted italic mt-1">
                                            Last pay: Rs. {{number_format($customer->latestPayment->amount, 0)}} ({{$customer->latestPayment->transaction_date->format('d M')}})
                                        </div>
                                    @endif
                                </td>
                                <td data-title="Actions" class="text-center">
                                    <a href="{{route('order.index')}}?user_id={{$customer->id}}" class="btn btn-primary btn-sm px-3 rounded-pill shadow-sm">
                                        <i class="fas fa-file-invoice-dollar mr-1"></i> View Billing
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-user-slash fa-3x mb-3 opacity-25"></i>
                                    <p>No customers found match your search criteria.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-end mt-3">
                    {{$customers->links()}}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .badge-info-soft {
        border-radius: 6px;
        font-weight: 600;
        letter-spacing: 0.3px;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(78, 115, 223, 0.03);
    }
</style>
@endsection
