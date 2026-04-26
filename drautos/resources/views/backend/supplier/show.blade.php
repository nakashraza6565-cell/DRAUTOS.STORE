@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            @include('backend.layouts.notification')
        </div>
    </div>

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Supplier History: {{ $supplier->name }}</h1>
        <div>
            <a href="{{ route('suppliers.export', ['id' => $supplier->id, 'from' => $from, 'to' => $to]) }}" class="btn btn-success btn-sm shadow-sm">
                <i class="fas fa-download fa-sm text-white-50"></i> Download CSV
            </a>
            <a href="{{ route('suppliers.index') }}" class="btn btn-primary btn-sm shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $purchaseOrders->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Ordered Amount</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">PKR {{ number_format($purchaseOrders->sum('total_amount'), 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Returns</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">PKR {{ number_format($returns->sum('total_return_amount'), 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-undo fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Net Balance (Period)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">PKR {{ number_format($purchaseOrders->sum('total_amount') - $returns->sum('total_return_amount'), 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-balance-scale fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary">Filter History</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('suppliers.show', $supplier->id) }}" method="GET" class="form-horizontal">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label class="small font-weight-bold">From Date</label>
                        <input type="date" name="from" class="form-control" value="{{ $from }}">
                    </div>
                    <div class="col-md-4">
                        <label class="small font-weight-bold">To Date</label>
                        <input type="date" name="to" class="form-control" value="{{ $to }}">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary shadow-sm"><i class="fas fa-filter"></i> Apply Filter</button>
                        <a href="{{ route('suppliers.show', $supplier->id) }}" class="btn btn-secondary shadow-sm"><i class="fas fa-sync"></i> Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- History Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white border-bottom">
            <h6 class="m-0 font-weight-bold text-dark">Transaction Ledger</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="supplier-history-table">
                    <thead class="bg-light">
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Reference #</th>
                            <th>Description</th>
                            <th>Debit (Out)</th>
                            <th>Credit (In)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php 
                            $combined = collect();
                            
                            foreach($purchaseOrders as $po) {
                                $combined->push([
                                    'date' => $po->order_date,
                                    'type' => 'Purchase Order',
                                    'ref'  => $po->po_number,
                                    'desc' => "Bulk purchase order - " . $po->items->count() . " items",
                                    'debit' => $po->total_amount,
                                    'credit' => 0,
                                    'status' => $po->status,
                                    'color' => 'primary'
                                ]);
                            }

                            foreach($returns as $ret) {
                                $combined->push([
                                    'date' => $ret->return_date->format('Y-m-d'),
                                    'type' => 'Purchase Return',
                                    'ref'  => $ret->return_number,
                                    'desc' => $ret->reason ?: 'No reason provided',
                                    'debit' => 0,
                                    'credit' => $ret->total_return_amount,
                                    'status' => $ret->status,
                                    'color' => 'danger'
                                ]);
                            }

                            foreach($incoming as $inc) {
                                $combined->push([
                                    'date' => $inc->received_date->format('Y-m-d'),
                                    'type' => 'Incoming Goods',
                                    'ref'  => $inc->reference_number,
                                    'desc' => "Invoice: " . ($inc->invoice_number ?: 'N/A'),
                                    'debit' => $inc->items->sum('total_cost'),
                                    'credit' => 0,
                                    'status' => $inc->status,
                                    'color' => 'info'
                                ]);
                            }

                            $sorted = $combined->sortByDesc('date');
                        @endphp

                        @foreach($sorted as $item)
                        <tr>
                            <td>{{ $item['date'] }}</td>
                            <td>
                                <span class="badge badge-outline-{{ $item['color'] }} border border-{{ $item['color'] }} text-{{ $item['color'] }} px-2 py-1">
                                    {{ $item['type'] }}
                                </span>
                            </td>
                            <td><strong>{{ $item['ref'] }}</strong></td>
                            <td class="small">{{ $item['desc'] }}</td>
                            <td class="text-danger font-weight-bold">
                                {{ $item['debit'] > 0 ? "PKR " . number_format($item['debit'], 2) : '-' }}
                            </td>
                            <td class="text-success font-weight-bold">
                                {{ $item['credit'] > 0 ? "PKR " . number_format($item['credit'], 2) : '-' }}
                            </td>
                            <td>
                                @php 
                                    $statusColor = 'secondary';
                                    if(in_array($item['status'], ['completed', 'approved', 'verified'])) $statusColor = 'success';
                                    if(in_array($item['status'], ['pending'])) $statusColor = 'warning';
                                    if(in_array($item['status'], ['cancelled', 'rejected'])) $statusColor = 'danger';
                                @endphp
                                <span class="badge badge-{{ $statusColor }}">{{ ucfirst($item['status']) }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-light font-weight-bold">
                        <tr>
                            <td colspan="4" class="text-right">Period Totals:</td>
                            <td class="text-danger">PKR {{ number_format($sorted->sum('debit'), 2) }}</td>
                            <td class="text-success">PKR {{ number_format($sorted->sum('credit'), 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
<style>
    .badge-outline-primary { border: 1px solid #4e73df; color: #4e73df; }
    .badge-outline-danger { border: 1px solid #e74a3b; color: #e74a3b; }
    .badge-outline-info { border: 1px solid #36b9cc; color: #36b9cc; }
</style>
@endpush

@push('scripts')
<script src="{{asset('backend/vendor/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
<script>
    $(document).ready(function() {
        $('#supplier-history-table').DataTable({
            "order": [[ 0, "desc" ]]
        });
    });
</script>
@endpush
