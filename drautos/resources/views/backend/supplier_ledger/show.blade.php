@extends('backend.layouts.master')
@section('title','Supplier Ledger - ' . $supplier->name)
@section('main-content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Supplier Ledger: {{$supplier->name}}</h1>
        <div>
            <a href="{{route('admin.supplier-ledger.pdf', $supplier->id)}}" class="btn btn-info btn-sm shadow-sm">
                <i class="fas fa-file-pdf fa-sm text-white-50"></i> PDF
            </a>
            <a href="{{route('admin.supplier-ledger.thermal', $supplier->id)}}" target="_blank" class="btn btn-warning btn-sm shadow-sm">
                <i class="fas fa-print fa-sm text-white-50"></i> Thermal
            </a>
            <form action="{{route('admin.supplier-ledger.whatsapp', $supplier->id)}}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success btn-sm shadow-sm">
                    <i class="fab fa-whatsapp fa-sm text-white-50"></i> WhatsApp
                </button>
            </form>
            <button class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#addTransactionModal">
                <i class="fas fa-plus fa-sm text-white-50"></i> Add Transaction
            </button>
            <a href="{{route('admin.supplier-ledger.index')}}" class="btn btn-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back
            </a>
        </div>
    </div>

    <!-- Stats Row & Graph -->
    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary text-uppercase small">Payment & Debt Trend</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 220px;">
                        <canvas id="supplierPerformanceChart"></canvas>
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
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Supplier Info</div>
                                    <div class="h6 mb-0 font-weight-bold text-gray-800">{{$supplier->company_name}}<br>{{$supplier->phone}}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-truck fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 mb-4">
                    <div class="card border-left-{{$supplier->current_balance > 0 ? 'danger' : 'success'}} shadow py-2 h-100">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-{{$supplier->current_balance > 0 ? 'danger' : 'success'}} text-uppercase mb-1">Payable Balance</div>
                                    <div class="h4 mb-0 font-weight-bold text-gray-800">Rs. {{number_format($supplier->current_balance, 2)}}</div>
                                    <div class="text-xs mt-1 {{ $supplier->current_balance > 0 ? 'text-danger' : ($supplier->current_balance < 0 ? 'text-success' : 'text-muted') }}">
                                        @if($supplier->current_balance > 0)
                                            You owe this supplier
                                        @elseif($supplier->current_balance < 0)
                                            You have advance credit
                                        @else
                                            Balance is clear
                                        @endif
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i>
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
                <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                    <thead class="bg-gray-100">
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th class="text-right">Debit (+) <small>(Purchases)</small></th>
                            <th class="text-right">Credit (-) <small>(Payments)</small></th>
                            <th class="text-right">Balance</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ledger as $item)
                            <tr>
                                <td>{{$item->transaction_date->format('Y-m-d')}}</td>
                                <td>
                                    <div class="font-weight-bold">{{$item->description}}</div>
                                    @if($item->payment_method)
                                        <div class="small text-muted mt-1">
                                            <i class="fas fa-credit-card mr-1"></i>
                                            <strong>{{strtoupper($item->payment_method)}}:</strong> 
                                            @if($item->payment_method == 'cheque' && isset($item->payment_details['cheque_no']))
                                                No. {{$item->payment_details['cheque_no']}} ({{$item->payment_details['bank_name'] ?? 'No Bank'}})
                                            @elseif($item->payment_method == 'bank' && isset($item->payment_details['account_no']))
                                                Acc: {{$item->payment_details['account_no']}} (Ref: {{$item->payment_details['ref_no'] ?? '-'}})
                                            @elseif($item->payment_method == 'wallet' && isset($item->payment_details['wallet_details']))
                                                {{$item->payment_details['wallet_details']}}
                                            @else
                                                Confirmed
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td><span class="badge badge-light text-uppercase">{{$item->category}}</span></td>
                                <td class="text-right text-danger">{{$item->type == 'debit' ? 'Rs. '.number_format($item->amount, 2) : ''}}</td>
                                <td class="text-right text-success">{{$item->type == 'credit' ? 'Rs. '.number_format($item->amount, 2) : ''}}</td>
                                <td class="text-right font-weight-bold">Rs. {{number_format($item->balance, 2)}}</td>
                                <td class="text-center">
                                    @if($item->category == 'purchase' && $item->reference_id)
                                        <a href="{{route('purchase-orders.show', $item->reference_id)}}" target="_blank" class="btn btn-info btn-sm rounded-circle" style="height:25px; width:25px; padding:0" title="View Purchase Order">
                                            <i class="fas fa-file-invoice" style="font-size: 10px;"></i>
                                        </a>
                                    @endif
                                    <button class="btn btn-primary btn-sm rounded-circle editBtn" 
                                            style="height:25px; width:25px; padding:0" 
                                            title="Edit Transaction"
                                            data-id="{{$item->id}}"
                                            data-date="{{$item->transaction_date->format('Y-m-d')}}"
                                            data-type="{{$item->type}}"
                                            data-category="{{$item->category}}"
                                            data-amount="{{$item->amount}}"
                                            data-description="{{$item->description}}">
                                        <i class="fas fa-edit" style="font-size: 10px;"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.supplier-ledger.destroy', $item->id) }}" style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm rounded-circle dltBtn" style="height:25px; width:25px; padding:0" title="Delete & Reverse Balance">
                                            <i class="fas fa-trash-alt" style="font-size: 10px;"></i>
                                        </button>
                                    </form>
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

<!-- Add Modal -->
<div class="modal fade" id="addTransactionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transactionModalTitle">Manual Supplier Transaction</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="transactionForm" action="{{route('admin.supplier-ledger.store')}}" method="POST">
                @csrf
                <div id="methodField"></div>
                <input type="hidden" name="supplier_id" value="{{$supplier->id}}">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="transaction_date" id="t_date" class="form-control" value="{{date('Y-m-d')}}" required>
                    </div>
                    <div class="form-group">
                        <label>Transaction Type</label>
                        <select name="type" id="t_type" class="form-control" required>
                            <option value="debit">Debit (Purchase/Incr. Owed)</option>
                            <option value="credit">Credit (Payment/Decr. Owed)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category" id="t_category" class="form-control" required>
                            <option value="manual">Manual Adjustment</option>
                            <option value="payment">Payment Made</option>
                            <option value="purchase">Inventory Purchase</option>
                            <option value="return">Purchase Return</option>
                        </select>
                    </div>

                    <!-- Dynamic Payment Method Section -->
                    <div id="payment_method_section" style="display:none;" class="p-3 mb-3 bg-light rounded border">
                        <h6 class="font-weight-bold text-primary small text-uppercase mb-3">Payment Details</h6>
                        <div class="form-group">
                            <label class="small font-weight-bold">Select Method</label>
                            <div class="d-flex flex-wrap">
                                <div class="custom-control custom-radio mr-3">
                                    <input type="radio" id="method_cash" name="payment_method" value="cash" class="custom-control-input" checked>
                                    <label class="custom-control-label" for="method_cash">Cash</label>
                                </div>
                                <div class="custom-control custom-radio mr-3">
                                    <input type="radio" id="method_cheque" name="payment_method" value="cheque" class="custom-control-input">
                                    <label class="custom-control-label" for="method_cheque">Cheque</label>
                                </div>
                                <div class="custom-control custom-radio mr-3">
                                    <input type="radio" id="method_bank" name="payment_method" value="bank" class="custom-control-input">
                                    <label class="custom-control-label" for="method_bank">Bank Account</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="method_wallet" name="payment_method" value="wallet" class="custom-control-input">
                                    <label class="custom-control-label" for="method_wallet">Wallet</label>
                                </div>
                            </div>
                        </div>

                        <!-- Specific Fields -->
                        <div id="cheque_fields" class="payment_detail_fields" style="display:none;">
                            <div class="form-group mb-2">
                                <label class="small">Cheque Number</label>
                                <input type="text" name="payment_details[cheque_no]" class="form-control form-control-sm" placeholder="Enter cheque number">
                            </div>
                            <div class="form-group mb-0">
                                <label class="small">Bank Name</label>
                                <input type="text" name="payment_details[bank_name]" class="form-control form-control-sm" placeholder="Enter bank name">
                            </div>
                        </div>

                        <div id="bank_fields" class="payment_detail_fields" style="display:none;">
                            <div class="form-group mb-2">
                                <label class="small">Account Number / IBAN</label>
                                <input type="text" name="payment_details[account_no]" class="form-control form-control-sm" placeholder="Enter account details">
                            </div>
                            <div class="form-group mb-0">
                                <label class="small">Transaction ID / Reference</label>
                                <input type="text" name="payment_details[ref_no]" class="form-control form-control-sm" placeholder="Enter reference number">
                            </div>
                        </div>

                        <div id="wallet_fields" class="payment_detail_fields" style="display:none;">
                            <div class="form-group mb-0">
                                <label class="small">Wallet Name / Number (e.g. EasyPaisa)</label>
                                <input type="text" name="payment_details[wallet_details]" class="form-control form-control-sm" placeholder="Enter wallet details">
                            </div>
                        </div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById("supplierPerformanceChart");
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
                        padding: { left: 10, right: 25, top: 25, bottom: 0 }
                    },
                    scales: {
                        xAxes: [{
                            gridLines: { display: false, drawBorder: false },
                            ticks: { maxTicksLimit: 7, fontSize: 10 }
                        }],
                        yAxes: [{
                            ticks: {
                                maxTicksLimit: 5,
                                padding: 10,
                                fontSize: 10,
                                callback: function(value) { return 'Rs. ' + value.toLocaleString(); }
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

        $('#transactionModalTitle').text('Edit Supplier Transaction');
        $('#transactionForm').attr('action', '/admin/supplier-ledger/' + id);
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
            $('#transactionModalTitle').text('Manual Supplier Transaction');
            $('#transactionForm').attr('action', '{{route("admin.supplier-ledger.store")}}');
            $('#methodField').html('');
            $('#t_date').val('{{date("Y-m-d")}}');
            $('#t_type').val('debit');
            $('#t_category').val('manual').trigger('change');
            $('#t_amount').val('');
            $('#t_description').val('');
            $('#saveBtn').text('Save Transaction');
        }
    });

    // Handle Category Change (show/hide payment section)
    $('#t_category').on('change', function() {
        if ($(this).val() === 'payment') {
            $('#payment_method_section').slideDown();
        } else {
            $('#payment_method_section').slideUp();
        }
    });

    // Handle Payment Method Selection
    $('input[name="payment_method"]').on('change', function() {
        $('.payment_detail_fields').hide();
        var selected = $(this).val();
        if (selected === 'cheque') $('#cheque_fields').show();
        else if (selected === 'bank') $('#bank_fields').show();
        else if (selected === 'wallet') $('#wallet_fields').show();
    });

    $(document).on('click', '.dltBtn', function(e) {
        var form = $(this).closest('form');
        e.preventDefault();
        Swal.fire({
            title: "Are you sure?",
            text: "Once deleted, the balance will be recalculated!",
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
