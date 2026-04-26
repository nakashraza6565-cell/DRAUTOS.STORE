@extends('user.layouts.master')
@section('title','Account Ledger || ' . (Settings::first()->title ?? 'Auto Store'))
@section('main-content')
<div class="container-fluid">
    @include('user.layouts.notification')

    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Account Ledger</h1>
            <p class="text-muted small m-0">Detailed transaction history and account performance tracking.</p>
        </div>
        <div class="col-md-6 text-right">
            <div class="d-inline-block bg-white shadow-sm p-3 rounded-lg border-left-{{$user->current_balance > 0 ? 'danger' : 'success'}}" style="min-width: 200px;">
                <div class="text-xs font-weight-bold text-uppercase text-secondary mb-1">Total Outstanding</div>
                <div class="h4 m-0 font-weight-bold {{$user->current_balance > 0 ? 'text-danger' : 'text-success'}}">
                    Rs. {{number_format($user->current_balance, 2)}}
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Graph -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 bg-white d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary text-uppercase small"><i class="fas fa-chart-line mr-2"></i>Account Performance (Balance Trend)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 250px;">
                        <canvas id="userPerformanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="card shadow mb-4">
        <div class="card-body p-3">
            <form class="form-inline" method="GET">
                <div class="form-group mr-3">
                    <label class="mr-2 small font-weight-bold font-italic text-uppercase text-secondary">From:</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{request()->date_from}}">
                </div>
                <div class="form-group mr-3">
                    <label class="mr-2 small font-weight-bold font-italic text-uppercase text-secondary">To:</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{request()->date_to}}">
                </div>
                <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm"><i class="fas fa-filter mr-1"></i> Filter</button>
                <a href="{{route('user.ledger')}}" class="btn btn-secondary btn-sm ml-2 shadow-sm"><i class="fas fa-sync mr-1"></i> Reset</a>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-light">
            <h6 class="m-0 font-weight-bold text-primary">Transaction History</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="ledger-dataTable" width="100%" cellspacing="0">
                    <thead class="bg-gray-200 text-dark small text-uppercase font-weight-bold">
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th class="text-right">Debit (Owed)</th>
                            <th class="text-right">Credit (Paid)</th>
                            <th class="text-right">Running Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ledger as $item)
                            <tr>
                                <td>{{$item->transaction_date->format('M d, Y')}}</td>
                                <td>
                                    {{$item->description}}
                                    @if($item->category == 'order' && $item->reference_id)
                                        <div class="small"><a href="{{route('user.order.show', $item->reference_id)}}" class="text-primary font-weight-bold">View Order</a></div>
                                    @endif
                                </td>
                                <td><span class="badge badge-light text-uppercase">{{$item->category}}</span></td>
                                <td class="text-right text-danger">
                                    {{$item->type == 'debit' ? 'Rs. '.number_format($item->amount, 2) : '-'}}
                                </td>
                                <td class="text-right text-success">
                                    {{$item->type == 'credit' ? 'Rs. '.number_format($item->amount, 2) : '-'}}
                                </td>
                                <td class="text-right font-weight-bold">
                                    Rs. {{number_format($item->balance, 2)}}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No ledger transactions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{$ledger->appends(request()->input())->links()}}
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script src="{{asset('backend/vendor/chart.js/Chart.min.js')}}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById("userPerformanceChart");
        if(ctx) {
            var myLineChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($graphLabels) !!},
                    datasets: [{
                        label: "Account Balance",
                        lineTension: 0.3,
                        backgroundColor: "rgba(78, 115, 223, 0.05)",
                        borderColor: "rgba(78, 115, 223, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointBorderColor: "rgba(78, 115, 223, 1)",
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data: {!! json_encode($balanceHistory) !!},
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            left: 10,
                            right: 25,
                            top: 25,
                            bottom: 0
                        }
                    },
                    scales: {
                        xAxes: [{
                            gridLines: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                maxTicksLimit: 7,
                                fontSize: 10
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                maxTicksLimit: 5,
                                padding: 10,
                                fontSize: 10,
                                callback: function(value, index, values) {
                                    return 'Rs. ' + value.toLocaleString();
                                }
                            },
                            gridLines: {
                                color: "rgb(234, 236, 244)",
                                zeroLineColor: "rgb(234, 236, 244)",
                                drawBorder: false,
                                borderDash: [2],
                                zeroLineBorderDash: [2]
                            }
                        }],
                    },
                    legend: {
                        display: false
                    },
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        titleMarginBottom: 10,
                        titleFontColor: '#6e707e',
                        titleFontSize: 14,
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        intersect: false,
                        mode: 'index',
                        caretPadding: 10,
                        callbacks: {
                            label: function(tooltipItem, chart) {
                                var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                                return datasetLabel + ': Rs. ' + tooltipItem.yLabel.toLocaleString();
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection
