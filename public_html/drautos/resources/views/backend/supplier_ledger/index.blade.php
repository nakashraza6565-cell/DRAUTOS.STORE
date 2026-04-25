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
                            <input type="text" name="search" id="supplier-search" class="form-control" placeholder="Search by name, company or phone..." value="{{request()->search}}">
                        </div>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Supplier Name</th>
                            <th>Company</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Current Balance</th>
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
            </div>
            {{$suppliers->links()}}
        </div>
    </div>
</div>
@push('scripts')
<script>
    $(document).ready(function() {
        var searchTimer;
        $('#supplier-search').on('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() {
                $('#ledger-filter-form').submit();
            }, 500);
        });
    });
</script>
@endpush
@endsection
