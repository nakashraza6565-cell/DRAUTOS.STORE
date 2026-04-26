@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            @include('backend.layouts.notification')
        </div>
    </div>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Employee Payroll: {{ $employee->name }}</h1>
        <a href="{{ route('payroll.ledger', $employee->id) }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-book fa-sm text-white-50 mr-1"></i> View Ledger
        </a>
    </div>

    <!-- Summary Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Paid</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">PKR {{ number_format($summary['total_paid'] ?? 0, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Pending Advances</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">PKR {{ number_format($summary['pending_advances'] ?? 0, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hand-holding-usd fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Commissions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">PKR {{ number_format($summary['pending_commissions'] ?? 0, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Base Salary</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">PKR {{ number_format($employee->base_salary ?? 0, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Quick Actions & Forms -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-dark">
                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-plus mr-2"></i>Record New Payment</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('payroll.record-payment') }}" method="POST">
                        @csrf
                        <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                        
                        <div class="form-group">
                            <label>Payment Date</label>
                            <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="form-group">
                            <label>Payment Type</label>
                            <select name="payment_type" id="payment_type_select" class="form-control" required>
                                <option value="salary">Basic Salary</option>
                                <option value="bonus">Bonus</option>
                                <option value="commission">Commission</option>
                                <option value="overtime">Overtime Pay</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div id="commission_selector" class="mb-3 d-none">
                            <div class="alert alert-info py-2 small">
                                <i class="fas fa-info-circle mr-1"></i> Selecting 'Commission' will allow you to pick pending commissions below.
                            </div>
                            <select name="commission_ids[]" id="commission_ids" class="form-control select2" multiple="multiple" style="width: 100%;">
                                @foreach($employee->commissions->where('status', 'pending') as $comm)
                                    <option value="{{ $comm->id }}" data-amount="{{ $comm->commission_amount }}">
                                        Order #{{ $comm->order_id }} - PKR {{ number_format($comm->commission_amount, 2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Amount (PKR)</label>
                            <input type="number" step="0.01" name="amount" id="payment_amount" class="form-control" placeholder="0.00" required>
                        </div>

                        <div class="form-group">
                            <label>Month/Year Applicable</label>
                            <input type="month" name="month_year" class="form-control" value="{{ date('Y-m') }}">
                        </div>

                        <div class="form-group">
                            <label>Payment Method</label>
                            <select name="payment_method" class="form-control" required>
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cheque">Cheque</option>
                                <option value="jazzcash">JazzCash</option>
                                <option value="easypaisa">EasyPaisa</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Reference # / Notes</label>
                            <input type="text" name="reference_number" class="form-control" placeholder="e.g. Bank Ref or Check #">
                        </div>

                        <button type="submit" class="btn btn-success btn-block shadow-sm">
                            <i class="fas fa-save mr-1"></i> Record Payment
                        </button>
                    </form>
                </div>
            </div>

            <!-- Advance Management -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-secondary text-white">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-hand-holding-usd mr-2"></i>Issue Advance / Loan</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('payroll.record-advance') }}" method="POST">
                        @csrf
                        <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                        
                        <div class="form-group">
                            <label>Advance Date</label>
                            <input type="date" name="advance_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="form-group">
                            <label>Amount (PKR)</label>
                            <input type="number" step="0.01" name="amount" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Number of Installments</label>
                            <input type="number" name="installments" class="form-control" value="1" min="1" required>
                            <small class="text-muted">Deductions per month will be calculated automatically</small>
                        </div>

                        <div class="form-group">
                            <label>Reason / Note</label>
                            <textarea name="reason" class="form-control" rows="2" placeholder="Describe why advance is given..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block shadow-sm">
                            <i class="fas fa-paper-plane mr-1"></i> Grant Advance
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Details Table -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white border-bottom">
                    <h6 class="m-0 font-weight-bold text-primary">Active Advances & Installments</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead class="bg-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Total Amount</th>
                                    <th>Repaid</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employee->advances->where('status', '!=', 'fully_repaid') as $adv)
                                <tr>
                                    <td>{{ $adv->advance_date }}</td>
                                    <td>{{ number_format($adv->amount, 2) }}</td>
                                    <td class="text-success">{{ number_format($adv->repaid_amount, 2) }}</td>
                                    <td class="font-weight-bold text-danger">{{ number_format($adv->balance, 2) }}</td>
                                    <td>
                                        <span class="badge badge-pill badge-{{ $adv->status == 'active' ? 'primary' : 'warning' }}">
                                            {{ ucfirst(str_replace('_', ' ', $adv->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-outline-info btn-xs px-2" data-toggle="modal" data-target="#repayModal{{ $adv->id }}">
                                            Repay
                                        </button>
                                        
                                        <!-- Repayment Modal -->
                                        <div class="modal fade" id="repayModal{{ $adv->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-info text-white">
                                                        <h5 class="modal-title">Record Repayment</h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                                                    </div>
                                                    <form action="{{ route('payroll.record-repayment', $adv->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label>Amount to Repay</label>
                                                                <input type="number" step="0.01" name="amount" class="form-control" value="{{ $adv->installment_amount }}" max="{{ $adv->balance }}" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Repayment Date</label>
                                                                <input type="date" name="repayment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Method</label>
                                                                <select name="repayment_method" class="form-control">
                                                                    <option value="salary_deduction">Salary Deduction</option>
                                                                    <option value="cash">Cash Payment</option>
                                                                    <option value="other">Other</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-info">Save Repayment</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted small">No active advances found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Payments -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white border-bottom">
                    <h6 class="m-0 font-weight-bold text-dark">Recent Salary & Bonus Payments</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead class="bg-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employee->payments->sortByDesc('payment_date')->take(5) as $pay)
                                <tr>
                                    <td>{{ $pay->payment_date }}</td>
                                    <td>{{ ucfirst($pay->payment_type) }} ({{ $pay->month_year ?? 'N/A' }})</td>
                                    <td class="font-weight-bold">PKR {{ number_format($pay->amount, 2) }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $pay->payment_method)) }}</td>
                                    <td><span class="badge badge-success">Completed</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted small">No payment history found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right mt-3">
                        <a href="{{ route('payroll.ledger', $employee->id) }}" class="small font-weight-bold">View All Payments &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .btn-xs { padding: 0.1rem 0.4rem; font-size: 0.7rem; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2();

        $('#payment_type_select').change(function() {
            if ($(this).val() === 'commission') {
                $('#commission_selector').removeClass('d-none');
            } else {
                $('#commission_selector').addClass('d-none');
            }
        });

        $('#commission_ids').change(function() {
            let total = 0;
            $(this).find('option:selected').each(function() {
                total += parseFloat($(this).data('amount'));
            });
            $('#payment_amount').val(total.toFixed(2));
        });
    });
</script>
@endpush
