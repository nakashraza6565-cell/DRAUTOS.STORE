@extends('backend.layouts.master')
@section('title','Customer Ledgers')
@section('main-content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Customer Ledgers</h6>
        </div>
        <div class="card-body">
            <form method="GET" class="mb-4" id="ledger-filter-form">
                <div class="row">
                    <div class="col-md-8">
                        <div class="input-group">
                            <input type="text" name="search" id="customer-search" class="form-control" placeholder="Search by name, phone or email..." value="{{request()->search}}">
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

            <div class="table-responsive">
                <table class="table table-bordered responsive-table-to-cards" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>City</th>
                            <th>Customer Type</th>
                            <th>Current Balance</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            <tr>
                                <td data-title="Customer Name">{{$customer->name}}</td>
                                <td data-title="Phone">{{$customer->phone}}</td>
                                <td data-title="City">{{$customer->city ?? 'N/A'}}</td>
                                <td data-title="Type"><span class="badge badge-info text-capitalize">{{$customer->customer_type ?? 'Retail'}}</span></td>
                                <td data-title="Balance" class="{{$customer->current_balance > 0 ? 'text-danger' : 'text-success'}} font-weight-bold">
                                    Rs. {{number_format($customer->current_balance, 2)}}
                                </td>
                                <td data-title="Actions">
                                    <a href="{{route('admin.customer-ledger.show', $customer->id)}}" class="btn btn-primary btn-sm btn-block btn-md-inline">
                                        <i class="fas fa-eye"></i> View Ledger
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No customers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{$customers->links()}}
        </div>
    </div>
</div>
@push('scripts')
<script>
    $(document).ready(function() {
        var searchTimer;
        $('#customer-search').on('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() {
                $('#ledger-filter-form').submit();
            }, 500); // Submit after 500ms of typing
        });
    });
</script>
@endpush
@endsection
