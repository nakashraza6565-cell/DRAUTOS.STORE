@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Account Receivables Report</h1>
        <button class="btn btn-sm btn-primary shadow-sm" onclick="window.print()"><i class="fas fa-print fa-sm text-white-50"></i> Print Report</button>
    </div>

    <!-- Summary -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Outstanding Receivables</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{number_format($totalReceivable, 2)}}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Pending Payments from Customers</h6>
                    <form action="{{route('reports.receivables')}}" method="GET" class="form-inline">
                        <select name="city" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                            <option value="">All Cities</option>
                            @foreach($cities as $c)
                                <option value="{{$c}}" {{ (isset($city) && $city == $c) ? 'selected' : '' }}>{{$c}}</option>
                            @endforeach
                        </select>
                        @if(isset($city) && $city)
                            <a href="{{route('reports.receivables')}}" class="btn btn-sm btn-outline-secondary">Clear</a>
                        @endif
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="receivablesTable">
                            <thead class="bg-light text-dark">
                                <tr>
                                    <th>Customer</th>
                                    <th>Contact Info</th>
                                    <th>Earliest Due Date</th>
                                    <th>Total Pending Balance</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($byCustomer as $customer)
                                <tr>
                                    <td>{{$customer->name ?? 'N/A'}}</td>
                                    <td>{{$customer->phone ?? '-'}}</td>
                                    <td class="text-muted">
                                        <em>Based on ledger</em>
                                    </td>
                                    <td class="font-weight-bold text-success">Rs. {{number_format($customer->current_balance, 2)}}</td>
                                    <td>
                                        <a href="{{route('admin.customer-ledger.show', $customer->id)}}" class="btn btn-sm btn-info shadow-sm" title="View Ledger">
                                            <i class="fas fa-book mr-1"></i> Ledger
                                        </a>
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
                    <h6 class="m-0 font-weight-bold text-primary">Receivable Split by City</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2" style="height: 250px;">
                        <canvas id="cityPieChart"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="card shadow mb-4 text-center">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">Top 10 Customers (% Share)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2" style="height: 250px;">
                        <canvas id="customerPieChart"></canvas>
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
        var colors = ['#1cc88a', '#36b9cc', '#4e73df', '#f6c23e', '#e74a3b', '#858796', '#fd7e14', '#20c997', '#6f42c1', '#e83e8c', '#17a2b8', '#28a745', '#ffc107', '#dc3545'];
        var result = [];
        for (var i = 0; i < count; i++) {
            result.push(colors[i % colors.length]);
        }
        return result;
    }

    var cityLabels = {!! json_encode($cityChartLabels ?? []) !!};
    var cityData = {!! json_encode($cityChartData ?? []) !!};

    if(document.getElementById("cityPieChart")) {
        new Chart(document.getElementById("cityPieChart"), {
            type: 'doughnut',
            data: {
                labels: cityLabels,
                datasets: [{
                    data: cityData,
                    backgroundColor: generateColors(cityData.length),
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                legend: { position: 'bottom', labels: { boxWidth: 12, fontSize: 11 } },
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
    }

    var custLabels = {!! json_encode($customerChartLabels ?? []) !!};
    var custData = {!! json_encode($customerChartData ?? []) !!};

    if(document.getElementById("customerPieChart")) {
        new Chart(document.getElementById("customerPieChart"), {
            type: 'pie',
            data: {
                labels: custLabels,
                datasets: [{
                    data: custData,
                    backgroundColor: generateColors(custData.length),
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                legend: { position: 'bottom', labels: { boxWidth: 12, fontSize: 11 } },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            var label = data.labels[tooltipItem.index] || '';
                            var value = data.datasets[0].data[tooltipItem.index];
                            var total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                            var percentage = ((value / total) * 100).toFixed(1);
                            return label + ': ' + percentage + '% (Rs. ' + Number(value).toLocaleString(undefined, {minimumFractionDigits: 2}) + ')';
                        }
                    }
                }
            },
        });
    }
</script>
@endpush
