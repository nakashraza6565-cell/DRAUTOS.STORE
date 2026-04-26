@extends('backend.layouts.master')
@section('title', 'Product Sales Analysis')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Advanced Product Analysis</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('reports.product-analysis') }}" class="mb-4">
            <div class="row align-items-end">
                <div class="col-md-3">
                    <label>Select Product</label>
                    <select name="product_id" class="form-control select2" required>
                        <option value="">-- Choose Product --</option>
                        @foreach($products as $prod)
                            <option value="{{ $prod->id }}" {{ request('product_id') == $prod->id ? 'selected' : '' }}>
                                {{ $prod->title }} (SKU: {{ $prod->sku }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Start Date</label>
                    <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label>End Date</label>
                    <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search"></i> Analyze</button>
                </div>
                <div class="col-md-3">
                    @if($selectedProduct)
                        <a href="{{ route('reports.product-analysis.pdf', ['product_id' => request('product_id'), 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" 
                           class="btn btn-danger btn-block">
                            <i class="fas fa-file-pdf"></i> Download PDF
                        </a>
                    @endif
                </div>
            </div>
        </form>

        @if($selectedProduct || count($salesHistory) > 0)
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Sold</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['quantity_sold'] }} Units</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-box fa-2x text-gray-300"></i>
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
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Revenue</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{ number_format($stats['total_revenue'], 2) }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
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
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Cost (Est.)</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $selectedProduct ? 'Rs. '.number_format($stats['total_cost'], 2) : 'N/A' }}
                                    </div>
                                    <small class="text-xs text-muted">{{ $selectedProduct ? '' : '(Select product for cost)' }}</small>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-{{ $stats['gross_profit'] >= 0 ? 'success' : 'danger' }} shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-{{ $stats['gross_profit'] >= 0 ? 'success' : 'danger' }} text-uppercase mb-1">Gross Profit</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $selectedProduct ? 'Rs. '.number_format($stats['gross_profit'], 2) : 'N/A' }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="analysisTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Order #</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salesHistory as $sale)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($sale->created_at)->format('d M Y h:i A') }}</td>
                            <td>
                                <a href="{{ route('order.show', $sale->order_id) }}">
                                    {{ $sale->order_number }}
                                </a>
                            </td>
                            <td>{{ $sale->quantity }}</td>
                            <td>Rs. {{ number_format($sale->unit_price, 2) }}</td>
                            <td>Rs. {{ number_format($sale->amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info text-center">
                Please select a product to view analysis.
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single {
        height: 38px;
        border: 1px solid #d1d3e2;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2();
        $('#analysisTable').DataTable({
            "order": [[ 0, "desc" ]]
        });
    });
</script>
@endpush
