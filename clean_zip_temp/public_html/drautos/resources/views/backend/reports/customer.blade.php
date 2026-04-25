@extends('backend.layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Customer Sales & Pending Report</h6>
    </div>
    <div class="card-body">
        <form action="{{route('reports.customer')}}" method="GET" class="mb-4">
            <div class="form-row align-items-end">
                <div class="col-md-4 mb-3">
                    <label for="customer_id">Select Customer</label>
                    <select name="customer_id" id="customer_id" class="form-control select2" required>
                        <option value="">-- Select Customer --</option>
                        @foreach($customers as $customer)
                            <option value="{{$customer->id}}" {{$selectedCustomer && $selectedCustomer->id == $customer->id ? 'selected' : ''}}>
                                {{$customer->name}} ({{$customer->email ?? $customer->phone}})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="start_date">From Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{$startDate->format('Y-m-d')}}">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="end_date">To Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{$endDate->format('Y-m-d')}}">
                </div>
                <div class="col-md-2 mb-3">
                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search"></i> Generate</button>
                </div>
            </div>
        </form>

        @if($selectedCustomer)
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card shadow h-100 py-2">
                        <div class="card-body">
                            <h6 class="font-weight-bold text-primary mb-3">Customer Profile</h6>
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    @php
                                        $pnts = ($selectedCustomer->loyalty_rating ?? 0) + 
                                               ($selectedCustomer->goodwill_rating ?? 0) + 
                                               ($selectedCustomer->payment_rating ?? 0) + 
                                               ($selectedCustomer->behaviour_rating ?? 0);
                                    @endphp
                                    <div class="text-center p-3 rounded bg-light border">
                                        <div class="h4 font-weight-bold text-warning mb-0">{{$pnts}}</div>
                                        <div class="text-xs text-muted">Rating Pnts</div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="h6 font-weight-bold mb-0 text-gray-800">{{$selectedCustomer->name}}</div>
                                    <div class="text-xs text-muted">ID: #{{$selectedCustomer->id}} | {{$selectedCustomer->phone}}</div>
                                    <div class="mt-2">
                                        <div class="progress progress-sm mr-2">
                                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{($pnts/20)*100}}%" aria-valuenow="{{$pnts}}" aria-valuemin="0" aria-valuemax="20"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-right d-flex align-items-end justify-content-end">
                    <a href="{{request()->fullUrlWithQuery(['export' => 'csv'])}}" class="btn btn-success btn-sm mr-2"><i class="fas fa-file-csv"></i> Export CSV</a>
                    <button onclick="window.print()" class="btn btn-warning btn-sm"><i class="fas fa-print"></i> Print</button>
                </div>
            </div>

            <div class="row">
                <!-- Earnings (Monthly) Card Example -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Sales (Period)</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{number_format($stats['total_sales'], 2)}}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Earnings (Monthly) Card Example -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Paid (Period)</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{number_format($stats['total_paid'], 2)}}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Requests Card Example -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Current Pending Due</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{number_format($stats['total_pending'], 2)}}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Requests Card Example -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Orders Count</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{$stats['orders_count']}}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-box fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Table -->
            <div class="card mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Order History for {{$selectedCustomer->name}}</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Order #</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Total Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                <tr>
                                    <td>{{$order->created_at->format('d M Y')}}</td>
                                    <td>{{$order->order_number}}</td>
                                    <td>
                                        <span class="badge badge-{{$order->status=='delivered' ? 'success' : ($order->status=='cancelled' ? 'danger' : 'warning')}}">
                                            {{ucfirst($order->status)}}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{$order->payment_status=='paid' ? 'success' : 'danger'}}">
                                            {{ucfirst($order->payment_status)}}
                                        </span>
                                    </td>
                                    <td>Rs. {{number_format($order->total_amount, 2)}}</td>
                                    <td>
                                        <a href="{{route('order.show', $order->id)}}" class="btn btn-info btn-sm btn-circle" target="_blank" title="View Order">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-right">Total (Period):</th>
                                    <th>Rs. {{number_format($stats['total_sales'], 2)}}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <h4 class="text-gray-500">Please select a customer and date range to view the report.</h4>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
    <style>
        .select2-container .select2-selection--single {
            height: calc(1.5em + .75rem + 2px) !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: calc(1.5em + .75rem + 2px) !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: calc(1.5em + .75rem + 2px) !important;
        }
        @media print {
            .navbar-nav, .sticky-footer, .btn, form {
                display: none !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
            }
            .card-header {
                display: none;
            }
            body {
                background-color: white !important;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
                placeholder: 'Select a Customer',
                allowClear: true
            });
        });
    </script>
@endpush
