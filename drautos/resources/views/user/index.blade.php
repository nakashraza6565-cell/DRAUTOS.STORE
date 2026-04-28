@extends('user.layouts.master')

@section('main-content')
<div class="container-fluid pb-5">
    @include('user.layouts.notification')
    
    <!-- Personalized Welcome Section -->
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h4 class="font-weight-800 mb-1" style="color:var(--primary);">Hello, {{Auth::user()->name}}!</h4>
            <p class="text-muted small mb-0">Welcome back to your dashboard.</p>
        </div>
        <div class="d-md-none">
             <a href="{{route('user.setting')}}" class="text-decoration-none">
                <img src="{{Auth::user()->photo ? Auth::user()->photo : asset('backend/img/avatar.png')}}" 
                     class="rounded-circle border shadow-sm" style="width:45px; height:45px; object-fit:cover;">
             </a>
        </div>
    </div>

    <!-- Outstanding Balance Window -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card border-0" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius: 20px; overflow: hidden;">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center mb-4 cursor-pointer" onclick="window.location.href='{{ route('user.ledger') }}'">
                        <div class="stat-icon bg-white text-primary mr-3">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div>
                            <div class="stat-label text-white-50 uppercase small font-weight-700" style="letter-spacing: 0.5px;">Outstanding Balance</div>
                            <div class="stat-value text-white h3 font-weight-900 mb-0">Rs. {{number_format($stats['total_pending'], 2)}}</div>
                        </div>
                        <i class="fas fa-chevron-right ml-auto text-white-50 small"></i>
                    </div>

                    <!-- Mini Ledger Preview -->
                    <div class="ledger-preview mt-3 pt-3 border-top border-secondary">
                        <h6 class="extra-small text-white-50 font-weight-700 text-uppercase mb-2" style="letter-spacing: 1px;">Recent Transactions</h6>
                        <div class="transaction-list custom-scrollbar" style="max-height: 200px; overflow-y: auto;">
                            @forelse($recent_ledger as $item)
                                <div class="d-flex justify-content-between align-items-center mb-2 p-2 rounded" style="background: rgba(255,255,255,0.05);">
                                    <div style="flex: 1; overflow: hidden;">
                                        <div class="extra-small font-weight-600 text-white truncate-text">{{ $item->description }}</div>
                                        <div class="extra-small text-white-50">{{ date('d M', strtotime($item->transaction_date)) }}</div>
                                    </div>
                                    <div class="text-right ml-2">
                                        <div class="extra-small font-weight-800 {{ $item->type == 'debit' ? 'text-danger' : 'text-success' }}">
                                            {{ $item->type == 'debit' ? '-' : '+' }} {{ number_format($item->amount, 0) }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-2 text-white-50 extra-small">No recent activity</div>
                            @endforelse
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('user.ledger.pdf') }}" class="btn btn-primary btn-sm rounded-pill px-3 font-weight-700" style="font-size: 10px; background: #3b82f6; border: none; box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);">
                                <i class="fas fa-file-pdf mr-1"></i> DOWNLOAD PDF STATEMENT
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Section -->
    <div class="mb-4">
        <h6 class="font-weight-700 text-uppercase small text-muted mb-3">Quick Actions</h6>
        <div class="row no-gutters" style="gap:12px;">
            <div class="col">
                <a href="{{route('user.online-order')}}" class="card h-100 text-center text-decoration-none py-3 border-0 shadow-sm">
                    <i class="fas fa-plus-circle text-primary mb-2 fa-lg"></i>
                    <span class="small font-weight-700 text-primary">New Order</span>
                </a>
            </div>
            <div class="col">
                <a href="{{route('user.order.index')}}" class="card h-100 text-center text-decoration-none py-3 border-0 shadow-sm">
                    <i class="fas fa-list-ul text-warning mb-2 fa-lg"></i>
                    <span class="small font-weight-700 text-warning">History</span>
                </a>
            </div>
            <div class="col">
                <a href="{{route('user.setting')}}" class="card h-100 text-center text-decoration-none py-3 border-0 shadow-sm">
                    <i class="fas fa-cog text-info mb-2 fa-lg"></i>
                    <span class="small font-weight-700 text-info">Settings</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Orders Status Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-body stat-card d-flex align-items-center py-3">
                    <div class="stat-icon bg-light text-warning mr-3" style="width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <div>
                        <div class="stat-label small text-muted font-weight-600">In-Process Orders</div>
                        <div class="stat-value font-weight-800" style="font-size: 1.1rem;">{{$stats['pending_orders']}} <small class="font-weight-normal text-muted">/ {{$stats['total_orders']}} total</small></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ledger Balance Trend Graph -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
                <div class="card-header bg-white border-0 py-4 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-800 text-primary small uppercase" style="letter-spacing: 1px;">Balance History Trend</h6>
                    <a href="{{ route('user.ledger') }}" class="btn btn-light btn-sm rounded-pill px-3 font-weight-700 extra-small">Full Ledger <i class="fas fa-chevron-right ml-1"></i></a>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 300px; position: relative;">
                        <canvas id="ledgerTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
    .font-weight-800 { font-weight: 800; }
    .font-weight-700 { font-weight: 700; }
    .text-primary { color: var(--primary) !important; }
    .text-accent { color: var(--accent) !important; }
    
    /* Animations */
    .mobile-order-card {
        transition: transform 0.2s ease;
    }
    .mobile-order-card:active {
        transform: scale(0.98);
    }
    .truncate-text {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
    }
    .hover-lift { transition: transform 0.2s, box-shadow 0.2s; }
    .hover-lift:active { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.2) !important; }
    
    /* Scrollbar for mini list */
    .transaction-list::-webkit-scrollbar { width: 3px; }
    .transaction-list::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
</style>
@endpush

@push('scripts')
<script>
    // Set new default font family and font color to mimic Bootstrap's default styling
    Chart.defaults.global.defaultFontFamily = 'Outfit', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#858796';

    function number_format(number, decimals, dec_point, thousands_sep) {
        // *     example: number_format(1234.56, 2, ',', ' ');
        // *     return: '1 234,56'
        number = (number + '').replace(',', '').replace(' ', '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function(n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    }

    var ctx = document.getElementById("ledgerTrendChart");
    if(ctx) {
        var myLineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($graphLabels) !!},
                datasets: [{
                    label: "Balance",
                    lineTension: 0.3,
                    backgroundColor: "rgba(59, 130, 246, 0.05)",
                    borderColor: "rgba(59, 130, 246, 1)",
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(59, 130, 246, 1)",
                    pointBorderColor: "rgba(59, 130, 246, 1)",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "rgba(59, 130, 246, 1)",
                    pointHoverBorderColor: "rgba(59, 130, 246, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: {!! json_encode($balanceHistory) !!},
                }],
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: { left: 10, right: 25, top: 25, bottom: 0 }
                },
                scales: {
                    xAxes: [{
                        time: { unit: 'date' },
                        gridLines: { display: false, drawBorder: false },
                        ticks: { maxTicksLimit: 7, fontSize: 10 }
                    }],
                    yAxes: [{
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            fontSize: 10,
                            callback: function(value, index, values) {
                                return 'Rs.' + number_format(value);
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
                legend: { display: false },
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
                            return datasetLabel + ': Rs.' + number_format(tooltipItem.yLabel);
                        }
                    }
                }
            }
        });
    }
</script>
@endpush

