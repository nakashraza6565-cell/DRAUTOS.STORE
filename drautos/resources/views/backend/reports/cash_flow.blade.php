@extends('backend.layouts.master')
@section('title', 'Danyal Autos || CASH FLOW REPORT')
@section('main-content')
<div class="container-fluid" style="padding: 1.5rem;">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-900 font-weight-bold">Cash Flow Report</h1>
            <p class="text-muted mb-0 small">Track all inflows and outflows across your wallets and accounts.</p>
        </div>
        <div class="d-flex align-items-center mt-3 mt-sm-0">
            <a href="{{ route('reports.cash-flow.pdf', ['start_date' => request('start_date'), 'end_date' => request('end_date'), 'group_by' => request('group_by')]) }}" class="btn btn-sm btn-danger shadow-sm mr-2 px-3 py-2" style="border-radius: 8px; font-weight: 700;">
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
            <form method="GET" action="{{ route('reports.cash-flow') }}" id="filterForm">
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
        <!-- Total Money In -->
        <div class="col-xl-3 col-md-6 mb-4 mb-xl-0">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 16px; border-left: 5px solid #10b981 !important;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-xs font-weight-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">Total Money In</span>
                        <div style="background: rgba(16,185,129,0.1); width: 36px; height: 36px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center;">
                            <i class="fas fa-arrow-down text-success"></i>
                        </div>
                    </div>
                    <h3 class="font-weight-bolder text-gray-900 mb-1">Rs. {{ number_format($totalMoneyIn) }}</h3>
                    <p class="text-muted small mb-0">Total cash inflows</p>
                </div>
            </div>
        </div>

        <!-- Total Money Out -->
        <div class="col-xl-3 col-md-6 mb-4 mb-xl-0">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 16px; border-left: 5px solid #ef4444 !important;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-xs font-weight-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">Total Money Out</span>
                        <div style="background: rgba(239,68,68,0.1); width: 36px; height: 36px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center;">
                            <i class="fas fa-arrow-up text-danger"></i>
                        </div>
                    </div>
                    <h3 class="font-weight-bolder text-gray-900 mb-1">Rs. {{ number_format($totalMoneyOut) }}</h3>
                    <p class="text-muted small mb-0">Total expenses & payouts</p>
                </div>
            </div>
        </div>

        <!-- Net Cash Flow -->
        <div class="col-xl-3 col-md-6 mb-4 mb-md-0">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 16px; border-left: 5px solid {{ ($totalMoneyIn - $totalMoneyOut) >= 0 ? '#3b82f6' : '#f59e0b' }} !important;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-xs font-weight-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">Net Cash Flow</span>
                        <div style="background: rgba(59,130,246,0.1); width: 36px; height: 36px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center;">
                            <i class="fas fa-scale-balanced text-primary"></i>
                        </div>
                    </div>
                    <h3 class="font-weight-bolder mb-1 {{ ($totalMoneyIn - $totalMoneyOut) >= 0 ? 'text-success' : 'text-danger' }}">
                        Rs. {{ number_format($totalMoneyIn - $totalMoneyOut) }}
                    </h3>
                    <p class="text-muted small mb-0">Net period balance difference</p>
                </div>
            </div>
        </div>

        <!-- All-Time Wallet Standing -->
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 16px; border-left: 5px solid #083259 !important; background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-xs font-weight-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">Available Wallets</span>
                        <div style="background: rgba(8,50,89,0.1); width: 36px; height: 36px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center;">
                            <i class="fas fa-wallet" style="color: #083259;"></i>
                        </div>
                    </div>
                    <h3 class="font-weight-bolder text-gray-900 mb-1">Rs. {{ number_format($totalWalletBalance) }}</h3>
                    <p class="text-muted small mb-0">Total across active accounts</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Visualization -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px;">
        <div class="card-header bg-white border-0 py-4 d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-gray-900"><i class="fas fa-chart-line mr-2 text-primary"></i> Cash Flow Trend</h5>
            <span class="badge badge-light text-primary font-weight-bold px-3 py-2" style="border-radius: 20px;">Grouped by: {{ ucfirst($groupBy) }}</span>
        </div>
        <div class="card-body">
            <div class="chart-area" style="height: 380px; position: relative;">
                <canvas id="cashFlowReportChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Tabular Breakdown -->
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
                            <th>Money In (Inflow)</th>
                            <th>Money Out (Outflow)</th>
                            <th>Net Flow</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(array_reverse($reportData) as $data)
                        <tr>
                            <td data-title="Period" class="font-weight-bold text-gray-800">{{ $data['label'] }}</td>
                            <td data-title="Money In" class="text-success font-weight-bold">Rs. {{ number_format($data['money_in']) }}</td>
                            <td data-title="Money Out" class="text-danger font-weight-bold">Rs. {{ number_format($data['money_out']) }}</td>
                            <td data-title="Net Flow" class="font-weight-bolder {{ $data['net_flow'] >= 0 ? 'text-primary' : 'text-warning' }}">
                                Rs. {{ number_format($data['net_flow']) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Detailed Ledger Log -->
    <div class="card shadow-sm border-0" style="border-radius: 16px;">
        <div class="card-header bg-white border-0 py-4">
            <h5 class="m-0 font-weight-bold text-gray-900"><i class="fas fa-list-ul mr-2 text-primary"></i> Detailed Transaction Ledger</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-items-center mb-0 responsive-table-to-cards" style="width: 100%;">
                    <thead class="thead-light">
                        <tr>
                            <th>Date</th>
                            <th>Account</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $txn)
                        <tr>
                            <td data-title="Date">{{ Carbon\Carbon::parse($txn->transaction_date)->format('M d, Y') }}</td>
                            <td data-title="Account" class="font-weight-bold">{{ $txn->financialAccount->name ?? 'N/A' }}</td>
                            <td data-title="Description" class="text-muted" style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $txn->description ?: 'No details' }}
                            </td>
                            <td data-title="Type">
                                <span class="badge {{ $txn->type == 'in' ? 'bg-success-light text-success' : 'bg-danger-light text-danger' }} px-2 py-1" style="border-radius: 6px; font-weight: 700; font-size: 0.75rem;">
                                    {{ strtoupper($txn->type == 'in' ? 'Inflow' : 'Outflow') }}
                                </span>
                            </td>
                            <td data-title="Amount" class="font-weight-bold text-right {{ $txn->type == 'in' ? 'text-success' : 'text-danger' }}">
                                {{ $txn->type == 'in' ? '+' : '-' }} Rs. {{ number_format($txn->amount) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-receipt fa-2x mb-3 text-gray-300"></i>
                                <p class="mb-0">No transactions recorded in this period.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($transactions->hasPages())
            <div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
                {{ $transactions->appends(request()->input())->links() }}
            </div>
            @endif
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
    .text-warning { color: #f59e0b !important; }
    
    @media print {
        .sidebar, .navbar, .btn, #filterForm, .card-footer { display: none !important; }
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
        var moneyIn = chartData.map(function(item) { return item.money_in; });
        var moneyOut = chartData.map(function(item) { return item.money_out; });

        var ctx = document.getElementById("cashFlowReportChart").getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: "Money In (Inflow)",
                        backgroundColor: "#10b981",
                        hoverBackgroundColor: "#059669",
                        borderColor: "#10b981",
                        data: moneyIn,
                        barPercentage: 0.5,
                        categoryPercentage: 0.6
                    },
                    {
                        label: "Money Out (Outflow)",
                        backgroundColor: "#ef4444",
                        hoverBackgroundColor: "#dc2626",
                        borderColor: "#ef4444",
                        data: moneyOut,
                        barPercentage: 0.5,
                        categoryPercentage: 0.6
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
