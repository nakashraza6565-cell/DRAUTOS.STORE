@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Account Payables Report</h1>
        <button class="btn btn-sm btn-primary shadow-sm" onclick="window.print()"><i class="fas fa-print fa-sm text-white-50"></i> Print Report</button>
    </div>

    <!-- Summary -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Outstanding Payables</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{number_format($totalPayable, 2)}}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-up fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">Pending Payments to Suppliers</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="payablesTable">
                            <thead class="bg-light text-dark">
                                <tr>
                                    <th>Supplier</th>
                                    <th>Company / Contact</th>
                                    <th>Earliest Due Date</th>
                                    <th>Total Pending Balance</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bySupplier as $supplierBalance)
                                <tr>
                                    <td>{{$supplierBalance->party->name ?? 'N/A'}}</td>
                                    <td>{{$supplierBalance->party->company_name ?? $supplierBalance->party->phone ?? '-'}}</td>
                                    <td class="{{\Carbon\Carbon::parse($supplierBalance->earliest_due_date)->isPast() ? 'text-danger font-weight-bold' : ''}}">
                                        {{$supplierBalance->earliest_due_date ? \Carbon\Carbon::parse($supplierBalance->earliest_due_date)->format('d M Y') : '-'}}
                                    </td>
                                    <td class="font-weight-bold text-danger">Rs. {{number_format($supplierBalance->total, 2)}}</td>
                                    <td>
                                        @if($supplierBalance->party)
                                            <a href="{{route('admin.supplier-ledger.show', $supplierBalance->party_id)}}" class="btn btn-sm btn-info shadow-sm" title="View Ledger">
                                                <i class="fas fa-book mr-1"></i> Ledger
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card shadow mb-4 text-center">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">{{ $chartTitle ?? 'Supplier Liability Split' }}</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2" style="height: 300px;">
                        <canvas id="payablePieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<script>
    // Generate a diverse color palette
    function generateColors(count) {
        var colors = ['#e74a3b', '#f6c23e', '#4e73df', '#1cc88a', '#36b9cc', '#858796', '#fd7e14', '#20c997', '#6f42c1', '#e83e8c', '#17a2b8', '#28a745', '#ffc107', '#dc3545'];
        var result = [];
        for (var i = 0; i < count; i++) {
            result.push(colors[i % colors.length]);
        }
        return result;
    }

    var chartLabels = {!! json_encode($chartLabels ?? []) !!};
    var chartData = {!! json_encode($chartData ?? []) !!};

    var ctx = document.getElementById("payablePieChart");
    var myPieChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: chartLabels,
            datasets: [{
                data: chartData,
                backgroundColor: generateColors(chartData.length),
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            legend: { 
                position: 'bottom',
                labels: {
                    boxWidth: 12,
                    fontSize: 11
                }
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        var label = data.labels[tooltipItem.index] || '';
                        var value = data.datasets[0].data[tooltipItem.index];
                        return label + ': Rs. ' + Number(value).toLocaleString(undefined, {minimumFractionDigits: 2});
                    }
                }
            }
        },
    });
</script>
@endpush
