@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Incoming Goods: Select Supplier</h6>
                    <a href="{{route('inventory-incoming.create')}}" class="btn btn-primary btn-sm shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> New Entry</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        @if(count($suppliersWithEntries) > 0)
                        <table class="table table-hover responsive-table-to-cards" id="supplier-table" width="100%" cellspacing="0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Supplier Name</th>
                                    <th>Entries Count</th>
                                    <th>Total Value Received</th>
                                    <th>Last Receipt Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($suppliersWithEntries as $supplier)
                                    <tr onclick="window.location='{{ route('inventory-incoming.index', ['supplier_id' => $supplier->id]) }}'" style="cursor: pointer;">
                                        <td data-title="Supplier">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-circle bg-primary text-white mr-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                                    <i class="fas fa-user-tie"></i>
                                                </div>
                                                <div>
                                                    <div class="font-weight-bold text-primary">{{ $supplier->name }}</div>
                                                    <div class="small text-muted">{{ $supplier->company_name ?? 'Individual' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td data-title="Entries">
                                            <span class="badge badge-info px-3 py-2">{{ $supplier->entries_count }} Records</span>
                                        </td>
                                        <td data-title="Total Value" class="font-weight-bold">
                                            PKR {{ number_format($supplier->total_spent, 0) }}
                                        </td>
                                        <td data-title="Last Date">
                                            @php $last = $supplier->latestIncomingGoods; @endphp
                                            {{ $last ? $last->received_date->format('d M Y') : 'N/A' }}
                                        </td>
                                        <td data-title="Action">
                                            <a href="{{ route('inventory-incoming.index', ['supplier_id' => $supplier->id]) }}" class="btn btn-primary btn-sm rounded-pill px-4">
                                                View Records <i class="fas fa-chevron-right ml-1"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <div class="text-center py-5">
                            <img src="{{asset('backend/img/empty.svg')}}" alt="No records" style="max-width: 200px; opacity: 0.5;">
                            <h6 class="mt-4 text-muted">No Incoming Goods Records found.</h6>
                            <p class="text-muted small">Create your first entry to start tracking inventory intake.</p>
                            <a href="{{route('inventory-incoming.create')}}" class="btn btn-primary btn-sm mt-3">Create New Entry</a>
                        </div>
                        @endif
                    </div>
                </div>
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
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    $('#supplier-table').DataTable({
        "order": [[ 1, "desc" ]], // Sort by entry count by default
        "pageLength": 25
    });
});
</script>
@endpush
