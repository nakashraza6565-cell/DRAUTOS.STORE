@extends('backend.layouts.master')
@section('title', 'Danyal Autos || SALES & PURCHASES COMPARISON')
@section('main-content')
<div class="container-fluid" style="padding: 1.5rem;">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-900 font-weight-bold">Sales & Purchases Comparison</h1>
            <p class="text-muted mb-0 small">Compare customer orders vs incoming factory/supplier stock shipments.</p>
        </div>
        <div class="d-flex align-items-center mt-3 mt-sm-0">
            <a href="{{ route('reports.sales-purchases.pdf', ['start_date' => request('start_date'), 'end_date' => request('end_date'), 'group_by' => request('group_by')]) }}" class="btn btn-sm btn-danger shadow-sm mr-2 px-3 py-2" style="border-radius: 8px; font-weight: 700;">
                <i class="fas fa-file-pdf mr-1"></i> Export PDF
            </a>
            <button class="btn btn-sm btn-light border shadow-sm px-3 py-2" style="border-radius: 8px; font-weight: 700;" onclick="window.print()">
                <i class="fas fa-print mr-1"></i> Print
            </button>
        </div>
    </div>

    <!-- Filters Section (Glassmorphism Card) -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px; background: rgba(255,255,255,0.9); backdrop-filter: blur(10px);">
        <div class="card-body p-3 p-md-4">
            <form method="GET" action="{{ route('reports.sales-purchases') }}" id="filterForm">
                <div class="row align-items-end">
                    <div class="col-md-3 mb-3 mb-md-0">
                        <label class="small font-weight-bold text-muted text-uppercase mb-2 d-block">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}" style="border-radius: 10px;">
                    </div>
                    <div class="col-md-3 mb-3 mb-md-0">
                        <label class="small font-weight-bold text-muted text-uppercase mb-2 d-block">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}" style="border-radius: 10px;">
                    </div>
                    <div class="col-md-2 mb-3 mb-md-0">
                        <label class="small font-weight-bold text-muted text-uppercase mb-2 d-block">Group By</label>
                        <select name="group_by" class="form-control" style="border-radius: 10px; height: auto;">
                            <option value="auto" {{ $groupBy == 'auto' ? 'selected' : '' }}>Auto Group</option>
                            <option value="daily" {{ request('group_by') == 'daily' ? 'selected' : '' }}>Daily</option>
                            <option value="weekly" {{ request('group_by') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                            <option value="monthly" {{ request('group_by') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex flex-wrap gap-2 justify-content-md-end mt-2 mt-md-0">
                        <button type="submit" class="btn btn-primary px-4 py-2" style="border-radius: 10px; font-weight: 700;">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                        <div class="btn-group ml-2">
                            <button type="button" class="btn btn-outline-secondary px-3 py-2 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border-radius: 10px; font-weight: 600;">
                                Quick Dates
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" style="border-radius: 10px;">
                                <a class="dropdown-item" href="javascript:void(0)" onclick="setQuickDates('{{ date('Y-m-d') }}', '{{ date('Y-m-d') }}', 'daily')">Today</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="setQuickDates('{{ date('Y-m-d', strtotime('-30 days')) }}', '{{ date('Y-m-d') }}', 'daily')">Last 30 Days</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="setQuickDates('{{ date('Y-01-01') }}', '{{ date('Y-12-31') }}', 'monthly')">This Year</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="setQuickDates('{{ date('Y-m-d', strtotime('first day of this month')) }}', '{{ date('Y-m-d', strtotime('last day of this month')) }}', 'weekly')">This Month</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Metrics Grid -->
    <div class="row mb-4">
        <!-- Customer Sales -->
        <div class="col-xl-3 col-md-6 mb-4 mb-xl-0">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 16px; border-left: 5px solid #facc15 !important;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-xs font-weight-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">Customer Sales</span>
                        <div style="background: rgba(250,204,21,0.1); width: 36px; height: 36px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center;">
                            <i class="fas fa-shopping-bag text-warning"></i>
                        </div>
                    </div>
                    <h3 class="font-weight-bolder text-gray-900 mb-1">Rs. {{ number_format($totalSales) }}</h3>
                    <p class="text-muted small mb-0">Total revenue generated</p>
                </div>
            </div>
        </div>

        <!-- Incoming Goods Cost -->
        <div class="col-xl-3 col-md-6 mb-4 mb-xl-0">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 16px; border-left: 5px solid #a3b1c6 !important;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-xs font-weight-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">Incoming Goods Cost</span>
                        <div style="background: rgba(163,177,198,0.1); width: 36px; height: 36px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center;">
                            <i class="fas fa-truck" style="color: #a3b1c6;"></i>
                        </div>
                    </div>
                    <h3 class="font-weight-bolder text-gray-900 mb-1">Rs. {{ number_format($totalPurchases) }}</h3>
                    <p class="text-muted small mb-0">Total wholesale stock value received</p>
                </div>
            </div>
        </div>

        <!-- Difference / Net Margin -->
        <div class="col-xl-3 col-md-6 mb-4 mb-md-0">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 16px; border-left: 5px solid {{ ($totalSales - $totalPurchases) >= 0 ? '#10b981' : '#ef4444' }} !important;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-xs font-weight-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">Stock Flow Difference</span>
                        <div style="background: rgba(16,185,129,0.1); width: 36px; height: 36px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center;">
                            <i class="fas fa-right-left text-success"></i>
                        </div>
                    </div>
                    <h3 class="font-weight-bolder mb-1 {{ ($totalSales - $totalPurchases) >= 0 ? 'text-success' : 'text-danger' }}">
                        Rs. {{ number_format($totalSales - $totalPurchases) }}
                    </h3>
                    <p class="text-muted small mb-0">Sales vs Purchases difference</p>
                </div>
            </div>
        </div>

        <!-- Gross Margin Percentage -->
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 16px; border-left: 5px solid #083259 !important;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-xs font-weight-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">Sales to Purchase Ratio</span>
                        <div style="background: rgba(8,50,89,0.1); width: 36px; height: 36px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center;">
                            <i class="fas fa-percent" style="color: #083259;"></i>
                        </div>
                    </div>
                    <h3 class="font-weight-bolder text-gray-900 mb-1">
                        {{ $totalPurchases > 0 ? number_format(($totalSales / $totalPurchases) * 100, 1) : '100+' }}%
                    </h3>
                    <p class="text-muted small mb-0">Sales as percentage of purchases</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Visualization -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px;">
        <div class="card-header bg-white border-0 py-4 d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-gray-900"><i class="fas fa-chart-bar mr-2 text-primary"></i> Inflows vs Outflows Performance</h5>
            <span class="badge badge-light text-primary font-weight-bold px-3 py-2" style="border-radius: 20px;">Grouped by: {{ ucfirst($groupBy) }}</span>
        </div>
        <div class="card-body">
            <div class="chart-area" style="height: 380px; position: relative;">
                <canvas id="salesPurchasesReportChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Periodical Breakdown -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px;">
        <div class="card-header bg-white border-0 py-4">
            <h5 class="m-0 font-weight-bold text-gray-900"><i class="fas fa-table mr-2 text-primary"></i> Periodical Breakdown</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-items-center mb-0 text-center responsive-table-to-cards" style="width: 100%;">
                    <thead class="thead-light">
                        <tr>
                            <th>Interval Period</th>
                            <th>Incoming Goods Cost</th>
                            <th>Customer Sales Amount</th>
                            <th>Difference</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(array_reverse($reportData) as $data)
                        <tr>
                            <td data-title="Period" class="font-weight-bold text-gray-800">{{ $data['label'] }}</td>
                            <td data-title="Incoming Goods" class="text-muted font-weight-bold">Rs. {{ number_format($data['incoming_goods']) }}</td>
                            <td data-title="Customer Sales" class="text-warning font-weight-bold">Rs. {{ number_format($data['customer_sales']) }}</td>
                            <td data-title="Difference" class="font-weight-bolder {{ $data['difference'] >= 0 ? 'text-success' : 'text-danger' }}">
                                Rs. {{ number_format($data['difference']) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Goods Received Log -->
        <div class="col-xl-6 mb-4 mb-xl-0">
            <div class="card shadow-sm border-0" style="border-radius: 16px;">
                <div class="card-header bg-white border-0 py-4">
                    <h5 class="m-0 font-weight-bold text-gray-900"><i class="fas fa-receipt mr-2 text-primary"></i> Recent Incoming Goods</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-items-center mb-0 responsive-table-to-cards" style="width: 100%;">
                            <thead class="thead-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Supplier</th>
                                    <th class="text-right">Total Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($incomingGoods->take(15) as $goods)
                                <tr>
                                    <td data-title="Date">{{ Carbon\Carbon::parse($goods->received_date)->format('M d, Y') }}</td>
                                    <td data-title="Supplier" class="font-weight-bold">{{ $goods->supplier->name ?? 'N/A' }}</td>
                                    <td data-title="Total Cost" class="font-weight-bold text-right text-muted">
                                        Rs. {{ number_format($goods->items->sum('total_cost') + ($goods->shipping_cost ?? 0)) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted">
                                        <i class="fas fa-truck fa-2x mb-3 text-gray-300"></i>
                                        <p class="mb-0">No incoming shipments in this period.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Sales Log -->
        <div class="col-xl-6">
            <div class="card shadow-sm border-0" style="border-radius: 16px;">
                <div class="card-header bg-white border-0 py-4">
                    <h5 class="m-0 font-weight-bold text-gray-900"><i class="fas fa-shopping-bag mr-2 text-primary"></i> Recent Customer Sales</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-items-center mb-0 responsive-table-to-cards" style="width: 100%;">
                            <thead class="thead-light">
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th class="text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders->take(15) as $order)
                                <tr>
                                    <td data-title="Order #">{{ $order->order_number }}</td>
                                    <td data-title="Customer" class="font-weight-bold">{{ $order->user->name ?? $order->first_name }}</td>
                                    <td data-title="Status">
                                        <span class="badge badge-{{ $order->status == 'delivered' ? 'success' : 'warning' }} px-2 py-1" style="border-radius: 6px;">
                                            {{ strtoupper($order->status) }}
                                        </span>
                                    </td>
                                    <td data-title="Amount" class="font-weight-bold text-right text-warning">
                                        Rs. {{ number_format($order->total_amount) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="fas fa-shopping-cart fa-2x mb-3 text-gray-300"></i>
                                        <p class="mb-0">No sales orders in this period.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-success-light { background-color: rgba(16, 185, 129, 0.1); }
    .bg-danger-light { background-color: rgba(239, 68, 68, 0.1); }
    .text-success { color: #10b981 !important; }
    .text-danger { color: #ef4444 !important; }
    .text-primary { color: #083259 !important; }
    .text-warning { color: #facc15 !important; }
    .text-muted { color: #64748b !important; }
    
    @media print {
        .sidebar, .navbar, .btn, #filterForm { display: none !important; }
        #wrapper #content-wrapper { margin: 0 !important; width: 100% !important; }
        .container-fluid { padding: 0 !important; }
        .card { border: 1px solid rgba(0,0,0,0.1) !important; box-shadow: none !important; margin-bottom: 20px !important; page-break-inside: avoid; }
        .table-responsive { overflow: visible !important; }
        table { width: 100% !important; border-collapse: collapse !important; }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<script>
    function setQuickDates(start, end, groupBy) {
        document.querySelector('input[name="start_date"]').value = start;
        document.querySelector('input[name="end_date"]').value = end;
        document.querySelector('select[name="group_by"]').value = groupBy;
        document.getElementById('filterForm').submit();
    }

    document.addEventListener('DOMContentLoaded', function() {
        var chartData = {!! json_encode($reportData) !!};
        var labels = chartData.map(function(item) { return item.label; });
        var incoming = chartData.map(function(item) { return item.incoming_goods; });
        var sales = chartData.map(function(item) { return item.customer_sales; });

        var ctx = document.getElementById("salesPurchasesReportChart").getContext('2d');
        
        var gradientIncoming = ctx.createLinearGradient(0, 0, 0, 400);
        gradientIncoming.addColorStop(0, "rgba(163, 177, 198, 0.4)");
        gradientIncoming.addColorStop(1, "rgba(163, 177, 198, 0.05)");

        var gradientSales = ctx.createLinearGradient(0, 0, 0, 400);
        gradientSales.addColorStop(0, "rgba(250, 204, 21, 0.4)");
        gradientSales.addColorStop(1, "rgba(250, 204, 21, 0.05)");

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: "Incoming Goods Cost",
                        lineTension: 0.3,
                        backgroundColor: gradientIncoming,
                        borderColor: "#a3b1c6",
                        pointRadius: 4,
                        pointBackgroundColor: "#fff",
                        pointBorderColor: "#a3b1c6",
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: "#a3b1c6",
                        pointHoverBorderColor: "#fff",
                        pointBorderWidth: 2,
                        data: incoming,
                    },
                    {
                        label: "Customer Sales Amount",
                        lineTension: 0.3,
                        backgroundColor: gradientSales,
                        borderColor: "#facc15",
                        pointRadius: 4,
                        pointBackgroundColor: "#fff",
                        pointBorderColor: "#facc15",
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: "#facc15",
                        pointHoverBorderColor: "#fff",
                        pointBorderWidth: 2,
                        data: sales,
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    xAxes: [{ gridLines: { display: false, drawBorder: false } }],
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            callback: function(value) { return 'Rs ' + Number(value).toLocaleString(); }
                        },
                        gridLines: { color: "rgba(0, 0, 0, .05)", zeroLineColor: "transparent", drawBorder: false, borderDash: [5, 5] }
                    }],
                },
                legend: { display: true, position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 6, fontStyle: 'bold' } },
                tooltips: {
                    backgroundColor: "#1e293b",
                    bodyFontColor: "#fff",
                    titleMarginBottom: 10,
                    titleFontColor: '#e2e8f0',
                    borderColor: 'rgba(255,255,255,0.1)',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    callbacks: {
                        label: function(tooltipItem, chart) {
                            var label = chart.datasets[tooltipItem.datasetIndex].label || '';
                            return label + ': Rs. ' + Number(tooltipItem.yLabel).toLocaleString();
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
