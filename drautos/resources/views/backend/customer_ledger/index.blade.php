@extends('backend.layouts.master')
@section('title','Orders & Billing')
@section('main-content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Orders & Billing (Select Customer)</h6>
            <a href="{{route('order.index')}}" class="btn btn-outline-primary btn-sm shadow-sm">
                <i class="fas fa-list-check mr-1"></i> Global Order List
            </a>
        </div>
        <div class="card-body">
            <form method="GET" class="mb-4" id="ledger-filter-form">
                <div class="row">
                    <div class="col-md-8">
                        <div class="input-group">
                            <input type="text" name="search" id="customer-search" class="form-control" placeholder="Search by name, phone or email..." value="{{request()->search}}" autocomplete="off">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i> Search
                                </button>
                                @if(request()->search || request()->city)
                                    <a href="{{route('admin.customer-ledger.index')}}" class="btn btn-secondary">
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
                <table class="table table-bordered responsive-table-to-cards" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>City</th>
                            <th>Customer Type</th>
                            <th>Last Payment Received</th>
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
                                <td data-title="Last Payment">
                                    @if($customer->latestPayment)
                                        <div class="small">
                                            <span class="text-success font-weight-bold">Rs. {{number_format($customer->latestPayment->amount, 2)}}</span><br>
                                            <span class="text-muted" style="font-size: 0.75rem;">{{$customer->latestPayment->transaction_date->format('d M, Y')}}</span><br>
                                            <span class="text-muted italic" style="font-size: 0.7rem; font-style: italic;">{{Str::limit($customer->latestPayment->description, 40)}}</span>
                                        </div>
                                    @else
                                        <span class="text-muted small">No payments</span>
                                    @endif
                                </td>
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
                <div class="d-flex justify-content-end mt-3">
                    {{$customers->links()}}
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    $(document).ready(function() {
        let searchTimer;
        const searchDelay = 500; // 500ms debounce

        // Automatic search on typing has been disabled per user request.
        // Search now only triggers on explicit form submission.

        // Prevent default form submission to keep search AJAX-based
        $('#ledger-filter-form').on('submit', function(e) {
            e.preventDefault();
            let search = $('#customer-search').val();
            let city = $('select[name="city"]').val();
            
            $('#ledger-list-container').css('opacity', '0.5');
            
            $.ajax({
                url: "{{route('admin.customer-ledger.index')}}",
                type: "GET",
                data: {
                    search: search,
                    city: city
                },
                success: function(response) {
                    let newContent = $(response).find('#ledger-list-container').html();
                    $('#ledger-list-container').html(newContent).css('opacity', '1');
                },
                error: function(err) {
                    console.log(err);
                    $('#ledger-list-container').css('opacity', '1');
                }
            });
        });
    });
</script>
@endpush
@endsection
