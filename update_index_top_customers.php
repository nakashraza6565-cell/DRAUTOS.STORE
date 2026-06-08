<?php
$file = __DIR__ . '/drautos/resources/views/backend/index.blade.php';
$content = file_get_contents($file);

// Replace Row 3 HTML
$row3_start = '    <!-- Row 3: Sales Analytics & Trending -->';
$row3_end = '<!-- Quick Attendance Modal -->';

$start_pos = strpos($content, $row3_start);
$end_pos = strpos($content, $row3_end);

if ($start_pos !== false && $end_pos !== false) {
    $new_html = <<<'EOD'
    <!-- Row 3: Top Customers (Revenue & Orders) -->
    <div class="row">
        <!-- Top Customers by Revenue (Bar Chart) -->
        <div class="col-xl-6 mb-4">
            <div class="premium-panel shadow-sm">
                <div class="panel-header d-flex justify-content-between align-items-center bg-light-soft">
                    <h5 class="m-0 font-weight-bolder text-gray-800">
                        <div class="icon-box bg-success-light mr-3"><i class="fas fa-crown text-success"></i></div>
                        Top 5 Customers (Revenue)
                        <span class="small text-muted ml-2">
                            (@if(request('start_date')) {{ \Carbon\Carbon::parse(request('start_date'))->format('M d') }} - {{ \Carbon\Carbon::parse(request('end_date'))->format('M d') }} @else Last 7 Days @endif)
                        </span>
                    </h5>
                </div>
                <div class="panel-body p-4">
                    <div class="chart-area" style="height: 350px;">
                        <canvas id="topRevenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Customers by Order Count (Doughnut/Bar Chart) -->
        <div class="col-xl-6 mb-4">
            <div class="premium-panel shadow-sm h-100">
                <div class="panel-header bg-light-soft">
                    <h5 class="m-0 font-weight-bolder text-gray-800">
                        <div class="icon-box bg-primary-light mr-3"><i class="fas fa-shopping-bag text-primary"></i></div>
                        Top 5 Customers (Order Volume)
                        <span class="small text-muted ml-2">
                            (@if(request('start_date')) {{ \Carbon\Carbon::parse(request('start_date'))->format('M d') }} - {{ \Carbon\Carbon::parse(request('end_date'))->format('M d') }} @else Last 7 Days @endif)
                        </span>
                    </h5>
                </div>
                <div class="panel-body p-4">
                    <div class="chart-area" style="height: 350px;">
                        <canvas id="topOrdersChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

EOD;
    $content = substr_replace($content, $new_html, $start_pos, $end_pos - $start_pos);
}

// Replace Sales Trends JS
$js_start = '// Sales Line/Bar Chart (Hybrid)';
$js_end = '// Cash Flow Bar Chart';

$start_pos_js = strpos($content, $js_start);
$end_pos_js = strpos($content, $js_end);

if ($start_pos_js !== false && $end_pos_js !== false) {
    $new_js = <<<'EOD'
        // Top 5 Customers by Revenue (Horizontal Bar Chart)
        var ctxRev = document.getElementById("topRevenueChart").getContext('2d');
        new Chart(ctxRev, {
            type: 'horizontalBar',
            data: {
                labels: {!! $topRevNamesJson !!},
                datasets: [{
                    label: "Total Spent",
                    backgroundColor: "#10b981",
                    hoverBackgroundColor: "#059669",
                    borderColor: "#10b981",
                    data: {!! $topRevAmountsJson !!},
                    barPercentage: 0.5
                }]
            },
            options: {
                maintainAspectRatio: false,
                layout: { padding: { left: 10, right: 25, top: 25, bottom: 0 } },
                scales: {
                    xAxes: [{
                        ticks: {
                            callback: function(value) { return 'Rs ' + Number(value).toLocaleString(); }
                        },
                        gridLines: { display: true, drawBorder: false, borderDash: [5, 5] }
                    }],
                    yAxes: [{
                        gridLines: { display: false, drawBorder: false }
                    }],
                },
                legend: { display: false },
                tooltips: {
                    backgroundColor: "#1e293b",
                    bodyFontColor: "#fff",
                    titleMarginBottom: 10,
                    titleFontColor: '#e2e8f0',
                    titleFontSize: 13,
                    borderColor: 'rgba(255,255,255,0.1)',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    callbacks: {
                        label: function(tooltipItem, chart) {
                            return 'Spent: Rs. ' + Number(tooltipItem.xLabel).toLocaleString();
                        }
                    }
                }
            }
        });

        // Top 5 Customers by Order Count (Doughnut Chart)
        var ctxOrd = document.getElementById("topOrdersChart").getContext('2d');
        new Chart(ctxOrd, {
            type: 'doughnut',
            data: {
                labels: {!! $topOrdNamesJson !!},
                datasets: [{
                    data: {!! $topOrdCountsJson !!},
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }]
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                },
                legend: {
                    display: true,
                    position: 'bottom'
                },
                cutoutPercentage: 70,
            }
        });

EOD;
    $content = substr_replace($content, $new_js, $start_pos_js, $end_pos_js - $start_pos_js);
}

file_put_contents($file, $content);
echo "Successfully updated index.blade.php.\n";
