@extends('backend.layouts.master')
@section('title','Customer Ledger - ' . $user->name)
@section('main-content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Ledger: {{$user->name}}</h1>
        <div>
            <a href="{{route('admin.customer-ledger.pdf', $user->id)}}" class="btn btn-info btn-sm shadow-sm">
                <i class="fas fa-file-pdf fa-sm text-white-50"></i> PDF
            </a>
            <a href="{{route('admin.customer-ledger.thermal', $user->id)}}" target="_blank" class="btn btn-warning btn-sm shadow-sm">
                <i class="fas fa-print fa-sm text-white-50"></i> Thermal
            </a>
            <form action="{{route('admin.customer-ledger.whatsapp', $user->id)}}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success btn-sm shadow-sm">
                    <i class="fab fa-whatsapp fa-sm text-white-50"></i> WhatsApp
                </button>
            </form>
            <a href="{{route('sales-orders.create')}}?user_id={{$user->id}}" class="btn btn-success btn-sm shadow-sm">
                <i class="fas fa-cart-plus fa-sm text-white-50"></i> Create Order
            </a>
            <button class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#addTransactionModal">
                <i class="fas fa-plus fa-sm text-white-50"></i> Add Transaction
            </button>
            <a href="{{route('admin.customer-ledger.index')}}" class="btn btn-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back
            </a>
        </div>
    </div>

    <!-- Stats Row & Graph -->
    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary text-uppercase small">Account Performance (Balance Trend)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 220px;">
                        <canvas id="customerPerformanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-5">
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card border-left-info shadow py-2 h-100">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Customer Info</div>
                                    <div class="h6 mb-0 font-weight-bold text-gray-800">{{$user->phone ?: 'No Phone'}}<br>{{$user->email}}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-user-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 mb-4">
                    <div class="card border-left-{{$user->current_balance > 0 ? 'danger' : 'success'}} shadow py-2 h-100">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-{{$user->current_balance > 0 ? 'danger' : 'success'}} text-uppercase mb-1">Outstanding Balance</div>
                                    <div class="h4 mb-0 font-weight-bold text-gray-800">Rs. {{number_format($user->current_balance, 2)}}</div>
                                    <div class="text-xs mt-1 {{ $user->current_balance > 0 ? 'text-danger' : ($user->current_balance < 0 ? 'text-success' : 'text-muted') }}">
                                        @if($user->current_balance > 0)
                                            Customer owes you
                                        @elseif($user->current_balance < 0)
                                            Customer has credit
                                        @else
                                            Balance is clear
                                        @endif
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-wallet fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- History -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-light d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Transaction History</h6>
            <form class="form-inline" method="GET">
                <input type="date" name="date_from" class="form-control form-control-sm mr-2" value="{{request()->date_from}}">
                <input type="date" name="date_to" class="form-control form-control-sm mr-2" value="{{request()->date_to}}">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm responsive-table-to-cards" width="100%" cellspacing="0">
                    <thead class="bg-gray-100">
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th class="text-right">Debit (+)</th>
                            <th class="text-right">Credit (-)</th>
                            <th class="text-right">Balance</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ledger as $item)
                            <tr>
                                <td data-title="Date">{{$item->transaction_date->format('Y-m-d')}}</td>
                                <td data-title="Description">{{$item->description}}</td>
                                <td data-title="Category"><span class="badge badge-light">{{$item->category}}</span></td>
                                <td data-title="Debit (+)" class="text-right text-danger">{{$item->type == 'debit' ? 'Rs. '.number_format($item->amount, 2) : ''}}</td>
                                <td data-title="Credit (-)" class="text-right text-success">{{$item->type == 'credit' ? 'Rs. '.number_format($item->amount, 2) : ''}}</td>
                                <td data-title="Balance" class="text-right font-weight-bold">Rs. {{number_format($item->balance, 2)}}</td>
                                <td data-title="Action" class="text-center">
                                    <div class="d-flex justify-content-end" style="gap: 5px;">
                                        @if($item->category == 'order' && $item->reference_id)
                                            <a href="{{route('order.pdf', $item->reference_id)}}" target="_blank" class="btn btn-warning btn-sm rounded-circle" style="height:32px; width:32px; display: flex; align-items: center; justify-content: center;" title="View Order PDF">
                                                <i class="fas fa-file-pdf" style="font-size: 12px;"></i>
                                            </a>
                                        @endif
                                        <button class="btn btn-primary btn-sm rounded-circle editBtn" 
                                                style="height:32px; width:32px; display: flex; align-items: center; justify-content: center;" 
                                                title="Edit Transaction"
                                                data-id="{{$item->id}}"
                                                data-date="{{$item->transaction_date->format('Y-m-d')}}"
                                                data-type="{{$item->type}}"
                                                data-category="{{$item->category}}"
                                                data-amount="{{$item->amount}}"
                                                data-description="{{$item->description}}">
                                            <i class="fas fa-edit" style="font-size: 12px;"></i>
                                        </button>
                                        <form method="POST" action="{{ route('admin.customer-ledger.destroy', $item->id) }}" style="display:inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm rounded-circle dltBtn" style="height:32px; width:32px; display: flex; align-items: center; justify-content: center;" title="Delete & Reverse Balance">
                                                <i class="fas fa-trash-alt" style="font-size: 12px;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{$ledger->appends(request()->input())->links()}}
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addTransactionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transactionModalTitle">Manual Transaction</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="transactionForm" action="{{route('admin.customer-ledger.store')}}" method="POST">
                @csrf
                <div id="methodField"></div>
                <input type="hidden" name="user_id" value="{{$user->id}}">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="transaction_date" id="t_date" class="form-control" value="{{date('Y-m-d')}}" required>
                    </div>
                    <div class="form-group">
                        <label>Transaction Type</label>
                        <select name="type" id="t_type" class="form-control" required>
                            <option value="debit">Debit (Incr. Owed Amount)</option>
                            <option value="credit">Credit (Payment/Refund)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category" id="t_category" class="form-control" required>
                            <option value="manual">Manual Adjustment</option>
                            <option value="payment">Payment Received</option>
                            <option value="return">Product Return</option>
                            <option value="order">Order Correction</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Amount (Rs.)</label>
                        <input type="number" name="amount" id="t_amount" class="form-control" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" id="t_description" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">Save Transaction</button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById("customerPerformanceChart");
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

    $('.editBtn').click(function() {
        var id = $(this).data('id');
        var date = $(this).data('date');
        var type = $(this).data('type');
        var category = $(this).data('category');
        var amount = $(this).data('amount');
        var description = $(this).data('description');

        $('#transactionModalTitle').text('Edit Customer Transaction');
        $('#transactionForm').attr('action', '/admin/customer-ledger/' + id);
        $('#methodField').html('@method("PUT")');
        $('#t_date').val(date);
        $('#t_type').val(type);
        $('#t_category').val(category);
        $('#t_amount').val(amount);
        $('#t_description').val(description);
        $('#saveBtn').text('Update Transaction');
        $('#addTransactionModal').modal('show');
    });

    // Reset modal when opened for NEW transaction
    $('[data-target="#addTransactionModal"]').click(function() {
        if (!$(this).hasClass('editBtn')) {
            $('#transactionModalTitle').text('Manual Transaction');
            $('#transactionForm').attr('action', '{{route("admin.customer-ledger.store")}}');
            $('#methodField').html('');
            $('#t_date').val('{{date("Y-m-d")}}');
            $('#t_type').val('debit');
            $('#t_category').val('manual');
            $('#t_amount').val('');
            $('#t_description').val('');
            $('#saveBtn').text('Save Transaction');
        }
    });

    $(document).on('click', '.dltBtn', function(e) {
        var form = $(this).closest('form');
        e.preventDefault();
        Swal.fire({
            title: "Are you sure?",
            text: "Once deleted, the transaction logic will be reversed and the balance will be recalculated!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
</script>
@endpush
@endsection
