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
            <a href="#" onclick="shareLedgerPdf(event, '{{route('admin.supplier-ledger.print', $supplier->id)}}', 'Supplier_Ledger_{{str_replace(' ', '_', $supplier->name)}}.png', 'image', this)" class="btn btn-success btn-sm shadow-sm">
                <i class="fas fa-share-alt fa-sm text-white-50"></i> Share Ledger
            </a>
            <button class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#addTransactionModal">
                <i class="fas fa-plus fa-sm text-white-50"></i> Add Transaction
            </button>
            <a href="{{route('admin.supplier-ledger.index')}}" class="btn btn-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back
            </a>
        </div>
    </div>

    @include('backend.layouts.notification')

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
                <table class="table table-bordered table-sm ledger-table-to-cards" width="100%" cellspacing="0">
                    <thead class="bg-gray-100">
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th class="text-right">
                                Debit (+) <small>(Purchases)</small><br>
                                <span class="text-danger font-weight-bold" style="font-size: 0.85rem;">Rs. {{ number_format($ledger->where('type', 'debit')->sum('amount'), 2) }}</span>
                            </th>
                            <th class="text-right">
                                Credit (-) <small>(Payments)</small><br>
                                <span class="text-success font-weight-bold" style="font-size: 0.85rem;">Rs. {{ number_format($ledger->where('type', 'credit')->sum('amount'), 2) }}</span>
                            </th>
                            <th class="text-right">Balance</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ledger as $item)
                            <tr>
                                <td data-title="Date" data-balance="Rs. {{number_format($item->balance, 2)}}">
                                    <span class="d-none d-md-inline">{{$item->transaction_date->format('d/m/y')}}</span>
                                    <span class="d-inline d-md-none">{{$item->transaction_date->format('d/m')}}</span>
                                </td>
                                <td data-title="Description">
                                    <div class="font-weight-bold text-primary text-uppercase" style="font-size: 0.85rem;">
                                        @if($item->category == 'purchase')
                                            Purchase
                                        @elseif($item->category == 'return')
                                            Return
                                        @else
                                            {{ $item->financialAccount->name ?? 'Cash' }}
                                        @endif
                                    </div>
                                    <div class="small text-dark">{{ $item->description }}</div>
                                    @if($item->payment_method)
                                        <div class="small text-muted mt-1">
                                            <i class="fas fa-credit-card mr-1"></i>
                                            <strong>{{strtoupper($item->payment_method)}}:</strong> 
                                            @if(($item->payment_method == 'cheque' || $item->payment_method == 'customer_cheque') && isset($item->payment_details['cheque_no']))
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
                                <td data-title="Category"><span class="badge badge-light text-uppercase">{{$item->category}}</span></td>
                                <td data-title="Debit (+)" class="text-right text-danger">{{$item->type == 'debit' ? 'Rs. '.number_format($item->amount, 0) : ''}}</td>
                                <td data-title="Credit (-)" class="text-right text-success">{{$item->type == 'credit' ? 'Rs. '.number_format($item->amount, 0) : ''}}</td>
                                <td data-title="Balance" class="text-right font-weight-bold">
                                    <span class="mob-amount d-none {{ $item->type == 'debit' ? 'text-danger' : 'text-success' }}" style="font-size: 0.75rem; margin-right: 3px;">
                                        {{ $item->type == 'debit' ? '+' : '-' }}{{ number_format($item->amount, 0) }}
                                    </span>
                                    <span class="d-none d-md-inline">Rs. </span>{{number_format($item->balance, 0)}}
                                </td>
                                <td data-title="Action" class="text-center">
                                    <div class="d-flex justify-content-end" style="gap: 5px;">
                                          @if($item->category == 'purchase' && $item->reference_id)
                                              @if(str_starts_with($item->description, 'Purchased (Invoice: RMP'))
                                                  @php
                                                      preg_match('/Invoice: (RMP-[^)]+)/', $item->description, $matches);
                                                      $rmp = isset($matches[1]) ? \App\Models\RawMaterialPurchase::where('invoice_number', $matches[1])->first() : null;
                                                  @endphp
                                                  @if($rmp)
                                                      <a href="{{route('manufacturing.production-factors.invoice.show', $rmp->id)}}" target="_blank" class="btn btn-info btn-sm rounded-circle" style="height:32px; width:32px; display: flex; align-items: center; justify-content: center;" title="View Raw Material Purchase">
                                                          <i class="fas fa-eye" style="font-size: 12px;"></i>
                                                      </a>
                                                  @endif
                                              @elseif(str_starts_with($item->description, 'Incoming Goods Record #'))
                                                  <a href="{{route('inventory-incoming.show', $item->reference_id)}}" target="_blank" class="btn btn-info btn-sm rounded-circle" style="height:32px; width:32px; display: flex; align-items: center; justify-content: center;" title="View Incoming Goods">
                                                      <i class="fas fa-eye" style="font-size: 12px;"></i>
                                                  </a>
                                              @endif
                                          @endif
                                        @if(in_array($item->category, ['payment', 'return', 'manual', 'purchase']))
                                            <a href="{{route('admin.supplier-ledger.transaction-voucher', $item->id)}}" target="_blank" class="btn btn-warning btn-sm rounded-circle" style="height:32px; width:32px; display: flex; align-items: center; justify-content: center;" title="Print Receipt">
                                                <i class="fas fa-receipt" style="font-size: 12px;"></i>
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
                                        <form method="POST" action="{{ route('admin.supplier-ledger.destroy', $item->id) }}" style="display:inline">
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

<!-- Add Modal -->
<div class="modal fade" id="addTransactionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transactionModalTitle">Manual Supplier Transaction</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="transactionForm" action="{{route('admin.supplier-ledger.store')}}" method="POST">
                @csrf
                <div id="methodField"></div>
                <input type="hidden" name="supplier_id" value="{{$supplier->id}}">
                <div class="modal-body" style="max-height: calc(100vh - 180px); overflow-y: auto;">
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
                                    <input type="radio" id="method_bank" name="payment_method" value="bank" class="custom-control-input">
                                    <label class="custom-control-label" for="method_bank">Bank Account</label>
                                </div>
                                <div class="custom-control custom-radio mr-3">
                                    <input type="radio" id="method_wallet" name="payment_method" value="wallet" class="custom-control-input">
                                    <label class="custom-control-label" for="method_wallet">Wallet</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="method_customer_cheque" name="payment_method" value="customer_cheque" class="custom-control-input">
                                    <label class="custom-control-label" for="method_customer_cheque">Customer Cheque</label>
                                </div>
                            </div>
                        </div>

                        <!-- Specific Fields -->
                        <div id="customer_cheque_fields" class="payment_detail_fields" style="display:none;">
                            <div class="form-group mb-0">
                                <label class="small">Selected Cheques</label>
                                <div id="selected_cheques_list" class="mb-2 p-2 bg-white border rounded" style="min-height: 40px; font-size: 0.8rem;">
                                    <span class="text-muted">No cheques selected</span>
                                </div>
                                <button type="button" class="btn btn-info btn-block btn-sm" data-toggle="modal" data-target="#selectChequesModal">
                                    <i class="fas fa-search-plus mr-1"></i> Browse & Select Cheques
                                </button>
                                <div id="cheque_hidden_inputs"></div>
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
                        <label>Financial Account (Optional)</label>
                        <div class="input-group">
                            <select name="financial_account_id" id="financial_account_id" class="form-control">
                                <option value="">-- Auto-detect Active Register --</option>
                                @foreach($accounts as $acc)
                                    <option value="{{$acc->id}}">{{$acc->name}} (Bal: Rs. {{number_format($acc->current_balance, 0)}})</option>
                                @endforeach
                            </select>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#quickAddAccountModal">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <small class="text-muted">Select an account or leave for <strong>Automatic Register</strong> linking.</small>
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

<!-- Select Cheques Modal -->
<div class="modal fade" id="selectChequesModal" tabindex="-1" role="dialog" style="z-index: 1000000;">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content border-info shadow-lg">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Select Customer Cheques to Transfer</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body" style="max-height: calc(100vh - 180px); overflow-y: auto;">
                <div class="table-responsive">
                    <table class="table table-hover table-sm" id="chequesSelectionTable">
                        <thead class="bg-light">
                            <tr>
                                <th width="30"><input type="checkbox" id="selectAllCheques"></th>
                                <th>Cheque #</th>
                                <th>Customer</th>
                                <th>Bank</th>
                                <th>Date</th>
                                <th class="text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="cheques_selection_body">
                            <tr><td colspan="6" class="text-center">Loading cheques...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <div class="mr-auto font-weight-bold">Total Selected: Rs. <span id="selectedChequesTotalDisplay">0.00</span></div>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-info" id="confirmChequeSelection">Confirm Selection</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ... (Chart logic stays the same)
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
            $('#cheque_hidden_inputs').empty();
            $('#selected_cheques_list').html('<span class="text-muted">No cheques selected</span>');
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
        if (selected === 'bank') $('#bank_fields').show();
        else if (selected === 'wallet') $('#wallet_fields').show();
        else if (selected === 'customer_cheque') {
            $('#customer_cheque_fields').show();
            loadCustomerCheques();
        }
    });

    let allCheques = [];
    function loadCustomerCheques() {
        if (allCheques.length > 0) return;
        
        $.ajax({
            url: "{{ route('cheques.pending-customer') }}",
            type: "GET",
            success: function(res) {
                allCheques = res;
                renderChequesTable();
            },
            error: function(xhr) {
                $('#cheques_selection_body').html('<tr><td colspan="6" class="text-center text-danger">Error loading cheques. Please refresh the page or contact admin.</td></tr>');
            }
        });
    }

    function renderChequesTable() {
        let html = '';
        if (allCheques.length === 0) {
            html = '<tr><td colspan="6" class="text-center">No pending customer cheques found.</td></tr>';
        } else {
            allCheques.forEach(ch => {
                let partyName = ch.party ? ch.party.name : 'Unknown';
                html += `<tr>
                    <td><input type="checkbox" class="cheque-checkbox" value="${ch.id}" data-amount="${ch.amount}" data-no="${ch.cheque_number}" data-customer="${partyName}"></td>
                    <td>${ch.cheque_number}</td>
                    <td>${partyName}</td>
                    <td>${ch.bank_name || '-'}</td>
                    <td>${ch.cheque_date}</td>
                    <td class="text-right font-weight-bold">Rs. ${ch.amount.toLocaleString()}</td>
                </tr>`;
            });
        }
        $('#cheques_selection_body').html(html);
    }

    $(document).on('change', '#selectAllCheques', function() {
        $('.cheque-checkbox').prop('checked', $(this).is(':checked')).trigger('change');
    });

    $(document).on('change', '.cheque-checkbox', function() {
        let total = 0;
        $('.cheque-checkbox:checked').each(function() {
            total += parseFloat($(this).data('amount'));
        });
        $('#selectedChequesTotalDisplay').text(total.toLocaleString(undefined, {minimumFractionDigits: 2}));
    });

    $('#confirmChequeSelection').click(function() {
        let selected = [];
        let total = 0;
        let descParts = [];
        let hiddenInputs = '';
        
        $('.cheque-checkbox:checked').each(function() {
            let id = $(this).val();
            let amount = parseFloat($(this).data('amount'));
            let no = $(this).data('no');
            let customer = $(this).data('customer');
            
            selected.push({id, amount, no, customer});
            total += amount;
            descParts.push("#" + no + " (" + customer + ")");
            hiddenInputs += `<input type="hidden" name="payment_details[cheque_ids][]" value="${id}">`;
        });

        if (selected.length > 0) {
            $('#selected_cheques_list').html(selected.map(s => `<span class="badge badge-info mr-1 mb-1">#${s.no} - Rs.${s.amount.toLocaleString()}</span>`).join(''));
            $('#cheque_hidden_inputs').html(hiddenInputs);
            $('#t_amount').val(total);
            $('#t_description').val("Payment via Transfer of Customer Cheques: " + descParts.join(', '));
            $('#selectChequesModal').modal('hide');
        } else {
            Swal.fire('Wait', 'Please select at least one cheque', 'info');
        }
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

    // Fix for scroll issue when using multiple modals
    $(document).on('hidden.bs.modal', '.modal', function () {
        if ($('.modal:visible').length) {
            $('body').addClass('modal-open');
        }
    });
</script>
@endpush
<!-- Quick Add Account Modal -->
<div class="modal fade" id="quickAddAccountModal" tabindex="-1" role="dialog" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quick Add Bank/Wallet</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" style="max-height: calc(100vh - 180px); overflow-y: auto;">
                <div class="form-group">
                    <label>Account Name</label>
                    <input type="text" id="quick_acc_name" class="form-control" placeholder="e.g. HBL Main, JazzCash Shop">
                </div>
                <div class="form-group">
                    <label>Type</label>
                    <select id="quick_acc_type" class="form-control">
                        <option value="bank">Bank</option>
                        <option value="wallet">Mobile Wallet</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Opening Balance</label>
                    <input type="number" id="quick_acc_balance" class="form-control" value="0">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveQuickAccount">Save Account</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $('#saveQuickAccount').click(function() {
        var name = $('#quick_acc_name').val();
        var type = $('#quick_acc_type').val();
        var balance = $('#quick_acc_balance').val();

        if(!name) { alert('Please enter account name'); return; }

        $(this).prop('disabled', true).text('Saving...');

        $.ajax({
            url: "{{route('financial-accounts.store')}}",
            type: "POST",
            data: {
                _token: "{{csrf_token()}}",
                name: name,
                type: type,
                opening_balance: balance
            },
            success: function(res) {
                location.reload();
            },
            error: function() {
                alert('Error saving account. Please check if columns exist.');
                $('#saveQuickAccount').prop('disabled', false).text('Save Account');
            }
        });
    });
    window.ledgerPdfPreloads = {};

    async function shareLedgerPdf(e, url, filename, type, btnElement) {
        e.preventDefault();
        const originalHtml = btnElement.innerHTML;
        
        // STEP 2: Share immediately if already generated/downloaded
        if (window.ledgerPdfPreloads[url]) {
            if (navigator.share) {
                try {
                    const mimeType = type === 'image' ? 'image/png' : 'application/pdf';
                    await navigator.share({
                        files: [new File([window.ledgerPdfPreloads[url]], filename, { type: mimeType })]
                    });
                    btnElement.innerHTML = originalHtml;
                } catch (err) {
                    if (err.name !== 'AbortError') {
                        alert("Native Share Failed: " + err.name + " - " + err.message);
                        btnElement.innerHTML = originalHtml;
                    }
                }
            } else {
                alert("navigator.share is not supported.");
            }
            return;
        }
        
        // STEP 1: Download or Generate
        btnElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btnElement.classList.add('disabled');
        
        try {
            if (type === 'pdf') {
                const response = await fetch(url);
                const blob = await response.blob();
                window.ledgerPdfPreloads[url] = blob;
            } else if (type === 'image') {
                // Fetch the HTML
                const response = await fetch(url);
                let htmlText = await response.text();
                
                // CRITICAL: Strip out the auto-print command so it doesn't open the print dialog!
                htmlText = htmlText.replace(/onload\s*=\s*['"]window\.print\(\)['"]/gi, '');
                htmlText = htmlText.replace(/window\.onload\s*=\s*function\(\)\s*\{\s*window\.print\(\);\s*\}/gi, '');
                
                const isA5 = url.includes('print');

                // Create a temporary hidden iframe
                const iframe = document.createElement('iframe');
                iframe.style.position = 'fixed';
                iframe.style.right = '-9999px';
                iframe.style.width = isA5 ? '560px' : '80mm';
                iframe.style.height = isA5 ? '2500px' : '1200px';
                document.body.appendChild(iframe);
                
                // Inject HTML into iframe
                const iframeDoc = iframe.contentWindow.document;
                iframeDoc.open();
                iframeDoc.write(htmlText);
                iframeDoc.close();
                
                // Wait a moment for iframe to render fonts
                await new Promise(r => setTimeout(r, 800));
                
                // Dynamically resize iframe to fit the entire content to prevent squishing
                iframe.style.height = (iframeDoc.documentElement.scrollHeight + 100) + 'px';
                
                // Ensure html2canvas is loaded in parent
                if (typeof html2canvas === 'undefined') {
                    await new Promise((resolve) => {
                        const script = document.createElement('script');
                        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
                        script.onload = resolve;
                        document.head.appendChild(script);
                    });
                }
                
                // Run html2canvas on the exact wrapper to crop correctly
                let targetId = 'receipt-content';
                if (url.includes('ledger') && url.includes('print')) {
                    targetId = 'ledger-wrapper';
                } else if (url.includes('order/print')) {
                    targetId = 'invoice-wrapper';
                }
                const wrapper = iframeDoc.getElementById(targetId) || iframeDoc.body;
                
                const canvas = await html2canvas(wrapper, {
                    scale: 2,
                    useCORS: true,
                    backgroundColor: '#ffffff'
                });
                
                const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/png'));
                window.ledgerPdfPreloads[url] = blob;
                
                // Clean up
                document.body.removeChild(iframe);
            }
            
            // Change button to prompt immediate click
            btnElement.classList.remove('disabled');
            btnElement.classList.remove('btn-success');
            btnElement.classList.add('btn-warning');
            btnElement.innerHTML = '<i class="fas fa-share-alt text-dark"></i> Tap!';
            
        } catch (error) {
            console.error('Error fetching/generating file:', error);
            btnElement.innerHTML = originalHtml;
            btnElement.classList.remove('disabled');
            alert("Failed to prepare file.");
        }
    }
</script>
@endpush
@endsection
