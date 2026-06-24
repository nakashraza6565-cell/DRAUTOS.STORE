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
                <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                    <label class="font-weight-bold">Select Product</label>
                    <select name="product_id" class="form-control select2" required>
                        <option value="">-- Choose Product --</option>
                        @foreach($products as $prod)
                            <option value="{{ $prod->id }}" {{ request('product_id') == $prod->id ? 'selected' : '' }}>
                                {{ $prod->title }} (SKU: {{ $prod->sku }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-sm-6 mb-2 mb-md-0">
                    <label class="font-weight-bold">Start Date</label>
                    <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="form-control">
                </div>
                <div class="col-md-2 col-sm-6 mb-2 mb-md-0">
                    <label class="font-weight-bold">End Date</label>
                    <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="form-control">
                </div>
                <div class="col-md-2 col-sm-6 mb-2 mb-md-0">
                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search"></i> Analyze</button>
                </div>
                <div class="col-md-3 col-sm-12">
                    @if($selectedProduct)
                        <a href="{{ route('reports.product-analysis.pdf', ['product_id' => request('product_id'), 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" 
                           class="btn btn-danger btn-block">
                            <i class="fas fa-file-pdf"></i> Download PDF Report
                        </a>
                    @endif
                </div>
            </div>
        </form>

        @if($selectedProduct && $selectedProduct->purchase_price == 0)
            <div class="alert alert-warning shadow-sm border-left-warning mb-4" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Cost is Missing:</strong> The purchase price of this product is not set in the inventory database. Gross profit margin is calculated using fallback average cost from incoming goods or $0. Set a purchase price to see exact margin details. 
                <a href="{{ route('product.edit', $selectedProduct->id) }}" class="alert-link font-weight-bold text-dark ml-1" target="_blank"><i class="fas fa-edit"></i> Edit Product Cost</a>
            </div>
        @endif

        @if($selectedProduct || count($salesHistory) > 0)
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Net Sold Volume</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['net_sold']) }} Units</div>
                                    <span class="badge badge-pill badge-danger mt-1">Return Rate: {{ number_format($stats['return_ratio'], 1) }}%</span>
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
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Net Revenue</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{ number_format($stats['net_revenue'], 2) }}</div>
                                    <small class="text-xs text-muted">Refunds: Rs. {{ number_format($stats['refunded_revenue'], 2) }}</small>
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
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Goods Received (Incoming)</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['purchased_qty']) }} Units</div>
                                    <small class="text-xs text-muted">Received Cost: Rs. {{ number_format($stats['total_purchased_cost'], 2) }}</small>
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
                                    <div class="text-xs font-weight-bold text-{{ $stats['gross_profit'] >= 0 ? 'success' : 'danger' }} text-uppercase mb-1">Gross Profit / Margin</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{ number_format($stats['gross_profit'], 2) }}</div>
                                    @if($stats['net_revenue'] > 0)
                                        <small class="text-xs text-muted">Margin: {{ number_format(($stats['gross_profit'] / $stats['net_revenue']) * 100, 1) }}%</small>
                                    @endif
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($selectedProduct && count($chartLabels) > 0)
                <div class="card shadow mb-4 border-0">
                    <div class="card-header py-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-area mr-1"></i> Sales vs Purchases Trend (Timeline Flow)</h6>
                    </div>
                    <div class="card-body">
                        <div style="height: 320px; position: relative;">
                            <canvas id="salesVelocityChart"></canvas>
                        </div>
                    </div>
                </div>
            @endif

            <h5 class="font-weight-bold text-dark mb-3 mt-4"><i class="fas fa-exchange-alt mr-2 text-primary"></i> Inventory Flow Ledger</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-hover shadow-sm" id="analysisTable" width="100%">
                    <thead class="bg-light text-dark font-weight-bold">
                        <tr>
                            <th>Date</th>
                            <th>Event Type</th>
                            <th>Reference #</th>
                            <th>Customer / Supplier</th>
                            <th class="text-center">Quantity Change</th>
                            <th class="text-right">Unit Price/Cost</th>
                            <th class="text-right">Total Flow Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salesHistory as $event)
                        @php
                            $rowClass = '';
                            $badgeClass = '';
                            $typeLabel = '';
                            $qtyPrefix = '';
                            $qtyColor = '';
                            
                            if ($event->type == 'sale') {
                                $rowClass = 'table-primary-light';
                                $badgeClass = 'badge-primary';
                                $typeLabel = 'Sale (Outgoing)';
                                $qtyPrefix = '-';
                                $qtyColor = 'text-danger font-weight-bold';
                            } elseif ($event->type == 'purchase') {
                                $rowClass = 'table-success-light';
                                $badgeClass = 'badge-success';
                                $typeLabel = 'Incoming Goods';
                                $qtyPrefix = '+';
                                $qtyColor = 'text-success font-weight-bold';
                            } elseif ($event->type == 'return') {
                                $rowClass = 'table-danger-light';
                                $badgeClass = 'badge-danger';
                                $typeLabel = 'Sale Return';
                                $qtyPrefix = '+';
                                $qtyColor = 'text-info font-weight-bold';
                            }
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td>{{ \Carbon\Carbon::parse($event->date)->format('d M Y h:i A') }}</td>
                            <td>
                                <span class="badge {{ $badgeClass }} px-2 py-1">{{ $typeLabel }}</span>
                            </td>
                            <td>
                                @if($event->ref_url)
                                    <a href="{{ $event->ref_url }}" class="font-weight-bold" target="_blank">{{ $event->ref }}</a>
                                @else
                                    {{ $event->ref }}
                                @endif
                            </td>
                            <td>
                                @if($event->party_url)
                                    <a href="{{ $event->party_url }}" class="font-weight-bold text-dark" target="_blank">
                                        <i class="fas fa-link mr-1 small text-muted"></i>{{ $event->party_name }}
                                    </a>
                                @else
                                    {{ $event->party_name }}
                                @endif
                            </td>
                            <td class="text-center {{ $qtyColor }}" style="font-size: 1.05rem;">
                                {{ $qtyPrefix }}{{ abs($event->qty) }}
                            </td>
                            <td class="text-right">Rs. {{ number_format($event->unit_price, 2) }}</td>
                            <td class="text-right font-weight-bold">Rs. {{ number_format($event->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info text-center py-4">
                <i class="fas fa-info-circle fa-2x mb-2 text-primary d-block"></i>
                Please select a product and click <strong>Analyze</strong> to load inventory analysis charts and ledger history.
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
    .table-primary-light { background-color: rgba(78, 115, 223, 0.03); }
    .table-success-light { background-color: rgba(28, 200, 138, 0.03); }
    .table-danger-light { background-color: rgba(231, 74, 59, 0.03); }
    .table-primary-light:hover { background-color: rgba(78, 115, 223, 0.07) !important; }
    .table-success-light:hover { background-color: rgba(28, 200, 138, 0.07) !important; }
    .table-danger-light:hover { background-color: rgba(231, 74, 59, 0.07) !important; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2();
        
        $('#analysisTable').DataTable({
            "order": [[ 0, "desc" ]],
            "pageLength": 25
        });

        @if($selectedProduct && count($chartLabels) > 0)
        // Setup Chart
        const ctx = document.getElementById('salesVelocityChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [
                    {
                        label: 'Units Sold (Sales)',
                        data: @json($chartSalesData),
                        borderColor: '#4e73df',
                        backgroundColor: 'rgba(78, 115, 223, 0.03)',
                        borderWidth: 3,
                        pointBackgroundColor: '#4e73df',
                        pointHoverRadius: 6,
                        tension: 0.35,
                        fill: true
                    },
                    {
                        label: 'Units Bought (Incoming Goods)',
                        data: @json($chartPurchasesData),
                        borderColor: '#1cc88a',
                        backgroundColor: 'rgba(28, 200, 138, 0.03)',
                        borderWidth: 3,
                        pointBackgroundColor: '#1cc88a',
                        pointHoverRadius: 6,
                        tension: 0.35,
                        fill: true
                    },
                    {
                        label: 'Units Returned (Sales Return)',
                        data: @json($chartReturnsData),
                        borderColor: '#e74c3c',
                        backgroundColor: 'rgba(231, 74, 59, 0.03)',
                        borderWidth: 2,
                        pointBackgroundColor: '#e74c3c',
                        pointHoverRadius: 5,
                        tension: 0.35,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#eaecf4',
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            boxWidth: 15,
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        padding: 12,
                        backgroundColor: 'rgba(30, 30, 45, 0.95)',
                        titleFont: {
                            size: 13,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 12
                        },
                        cornerRadius: 8
                    }
                }
            }
        });
        @endif
    });
</script>
@endpush
