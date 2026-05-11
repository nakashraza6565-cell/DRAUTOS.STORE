@extends('backend.layouts.master')
@section('title','Supplier Ledgers')
@section('main-content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Supplier Ledgers</h6>
        </div>
        <div class="card-body">
            <form method="GET" class="mb-4" id="ledger-filter-form">
                <div class="row">
                    <div class="col-md-12">
                        <div class="input-group">
                            <input type="text" name="search" id="supplier-search" class="form-control" placeholder="Search by name, company or phone..." value="{{request()->search}}" autocomplete="off">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i> Search
                                </button>
                                @if(request()->search)
                                    <a href="{{route('admin.supplier-ledger.index')}}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Clear
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="table-responsive" id="ledger-list-container">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Supplier Name</th>
                            <th>Company</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Current Balance</th>
                            <th>Last Payment Made</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $supplier)
                            <tr>
                                <td>{{$supplier->name}}</td>
                                <td>{{$supplier->company_name ?? 'N/A'}}</td>
                                <td>{{$supplier->phone}}</td>
                                <td>
                                    <span class="badge badge-{{$supplier->status == 'active' ? 'success' : 'warning'}}">{{strtoupper($supplier->status)}}</span>
                                </td>
                                <td class="{{$supplier->current_balance > 0 ? 'text-danger' : 'text-success'}} font-weight-bold">
                                    Rs. {{number_format($supplier->current_balance, 2)}}
                                </td>
                                <td>
                                    @if($supplier->latestPayment)
                                        <div class="small">
                                            <span class="text-info font-weight-bold">Rs. {{number_format($supplier->latestPayment->amount, 2)}}</span><br>
                                            <span class="text-muted" style="font-size: 0.75rem;">{{$supplier->latestPayment->transaction_date->format('d M, Y')}}</span><br>
                                            <span class="text-muted italic" style="font-size: 0.7rem; font-style: italic;">{{Str::limit($supplier->latestPayment->description, 40)}}</span>
                                        </div>
                                    @else
                                        <span class="text-muted small">No payments</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{route('admin.supplier-ledger.show', $supplier->id)}}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i> View Ledger
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No suppliers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-end mt-3">
                    {{$suppliers->links()}}
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

        $('#ledger-filter-form').on('submit', function(e) {
            e.preventDefault();
            let search = $('#supplier-search').val();
            
            $('#ledger-list-container').css('opacity', '0.5');
            
            $.ajax({
                url: "{{route('admin.supplier-ledger.index')}}",
                type: "GET",
                data: {
                    search: search
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
