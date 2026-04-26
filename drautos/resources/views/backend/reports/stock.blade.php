@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Inventory Status Report</h1>
        <div>
            <a href="{{ route('reports.stock.pdf') }}" class="btn btn-sm btn-danger shadow-sm mr-2">
                <i class="fas fa-file-pdf fa-sm text-white-50"></i> Download PDF
            </a>
            <button class="btn btn-sm btn-primary shadow-sm" onclick="window.print()">
                <i class="fas fa-print fa-sm text-white-50"></i> Print Report
            </button>
        </div>
    </div>

    <!-- Summary -->
    <div class="row">
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Stock Value (Purchase Cost)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{number_format($totalStockValue, 2)}}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Low Stock Items</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{$products->where('stock', '<', 5)->count()}}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
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
                    <h6 class="m-0 font-weight-bold text-primary">Detailed Stock Inventory</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                        <table class="table table-bordered table-sm table-hover" id="stockTable">
                            <thead style="position: sticky; top: 0; background: white; z-index: 10;">
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Category</th>
                                    <th>Quantity</th>
                                    <th>Value</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                <tr>
                                    <td>{{$product->title}}</td>
                                    <td>{{$product->sku}}</td>
                                    <td>{{$product->cat_info->title ?? 'N/A'}}</td>
                                    <td class="font-weight-bold {{$product->stock < 5 ? 'text-danger' : ''}}">{{$product->stock}}</td>
                                    <td>Rs. {{number_format($product->stock * ($product->purchase_price ?? 0), 2)}}</td>
                                    <td>
                                        @if($product->stock <= 0)
                                            <span class="badge badge-danger">Out of Stock</span>
                                        @elseif($product->stock < 5)
                                            <span class="badge badge-warning">Low Stock</span>
                                        @else
                                            <span class="badge badge-success">Available</span>
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
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">Top 10 Products by Stock</h6>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 300px;">
                        <canvas id="stockChart"></canvas>
                    </div>
                    <div class="mt-3" id="chartLegend" style="max-height: 250px; overflow-y: auto;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<script>
    $(document).ready(function() {
        // Generate distinct colors for each product
        function generateColors(count) {
            const colors = [
                '#3b82f6', // Blue
                '#10b981', // Green
                '#f59e0b', // Amber
                '#ef4444', // Red
                '#8b5cf6', // Purple
                '#ec4899', // Pink
                '#06b6d4', // Cyan
                '#f97316', // Orange
                '#6366f1', // Indigo
                '#14b8a6', // Teal
            ];
            return colors.slice(0, count);
        }

        var ctx = document.getElementById("stockChart");
        if (ctx) {
            ctx = ctx.getContext('2d');
            
            @php
                $labels = $topProducts->pluck('title')->toArray();
                $quantities = $topProducts->pluck('stock')->toArray();
            @endphp

            var productLabels = {!! json_encode($labels) !!};
            var productQuantities = {!! json_encode($quantities) !!};
            var chartColors = generateColors(productLabels.length);

            var myDoughnutChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: productLabels,
                    datasets: [{
                        data: productQuantities,
                        backgroundColor: chartColors,
                        hoverBackgroundColor: chartColors.map(color => color + 'CC'),
                        borderWidth: 2,
                        borderColor: '#fff'
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutoutPercentage: 65,
                    legend: {
                        display: false
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var label = data.labels[tooltipItem.index] || '';
                                var value = data.datasets[0].data[tooltipItem.index];
                                return label + ': ' + value + ' units';
                            }
                        }
                    }
                },
            });

            // Create custom legend with quantities
            var legendHtml = '<div class="row">';
            productLabels.forEach(function(label, index) {
                var qty = productQuantities[index];
                var color = chartColors[index];
                legendHtml += `
                    <div class="col-12 mb-2">
                        <div class="d-flex align-items-center">
                            <div style="width: 15px; height: 15px; background-color: ${color}; border-radius: 3px; margin-right: 8px; flex-shrink: 0;"></div>
                            <div class="flex-grow-1 small text-truncate">
                                <strong>${label}</strong>
                                <span class="text-muted float-right">${qty} units</span>
                            </div>
                        </div>
                    </div>
                `;
            });
            legendHtml += '</div>';
            document.getElementById('chartLegend').innerHTML = legendHtml;
        }
    });
</script>
@endpush
