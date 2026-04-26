@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Financial Ledger: {{ $employee->name }}</h1>
        <div class="btn-group">
            <button class="btn btn-outline-primary btn-sm" onclick="window.print()">
                <i class="fas fa-print fa-sm text-primary mr-1"></i> Print Statement
            </button>
            <a href="{{ route('payroll.show', $employee->id) }}" class="btn btn-primary btn-sm ml-2">
                <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> New Entry
            </a>
        </div>
    </div>

    <!-- Period Filter -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('payroll.ledger', $employee->id) }}" method="GET" class="form-inline">
                <label class="mr-2 small font-weight-bold">From:</label>
                <input type="date" name="from" class="form-control form-control-sm mr-3" value="{{ $from }}">
                <label class="mr-2 small font-weight-bold">To:</label>
                <input type="date" name="to" class="form-control form-control-sm mr-3" value="{{ $to }}">
                <button type="submit" class="btn btn-dark btn-sm px-4">Filter Records</button>
            </form>
        </div>
    </div>

    <!-- Combined Ledger Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary">Consolidated Statement ({{ $from }} to {{ $to }})</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0" id="ledger-table" style="font-size: 0.9rem;">
                    <thead class="bg-light">
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Reference</th>
                            <th class="text-danger">Debit (Advance)</th>
                            <th class="text-success">Credit (Payment)</th>
                            <th class="text-info">Commission</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php 
                            // Combine all records into one chronological array
                            $all_records = collect();
                            
                            foreach($payments as $p) {
                                $all_records->push([
                                    'date' => $p->payment_date,
                                    'desc' => ucfirst($p->payment_type) . ($p->month_year ? " for " . $p->month_year : ""),
                                    'ref' => $p->reference_number ?? 'CMS-'.$p->id,
                                    'debit' => 0,
                                    'credit' => $p->amount,
                                    'comm' => 0,
                                    'type' => 'payment'
                                ]);
                            }

                            foreach($advances as $a) {
                                $all_records->push([
                                    'date' => $a->advance_date,
                                    'desc' => "Advance Issued: " . ($a->reason ?: 'No reason given'),
                                    'ref' => 'ADV-'.$a->id,
                                    'debit' => $a->amount,
                                    'credit' => 0,
                                    'comm' => 0,
                                    'type' => 'advance'
                                ]);

                                foreach($a->repayments as $r) {
                                    $all_records->push([
                                        'date' => $r->repayment_date,
                                        'desc' => "Advance Repayment (" . ucfirst(str_replace('_', ' ', $r->repayment_method)) . ")",
                                        'ref' => 'REP-'.$r->id,
                                        'debit' => 0,
                                        'credit' => $r->amount,
                                        'comm' => 0,
                                        'type' => 'repayment'
                                    ]);
                                }
                            }

                            foreach($commissions as $c) {
                                $all_records->push([
                                    'date' => $c->commission_date,
                                    'desc' => "Sales Commission (Order #{$c->order_id})",
                                    'ref' => "Sale: PKR " . number_format($c->sale_amount, 2),
                                    'debit' => 0,
                                    'credit' => 0,
                                    'comm' => $c->commission_amount,
                                    'type' => 'commission'
                                ]);
                            }

                            $sorted_records = $all_records->sortBy('date');
                        @endphp

                        @foreach($sorted_records as $record)
                        <tr>
                            <td>{{ $record['date'] }}</td>
                            <td>{{ $record['desc'] }}</td>
                            <td><span class="text-muted small">{{ $record['ref'] }}</span></td>
                            <td class="text-danger">
                                {{ $record['debit'] > 0 ? "PKR " . number_format($record['debit'], 2) : '-' }}
                            </td>
                            <td class="text-success">
                                {{ $record['credit'] > 0 ? "PKR " . number_format($record['credit'], 2) : '-' }}
                            </td>
                            <td class="text-info">
                                {{ $record['comm'] > 0 ? "PKR " . number_format($record['comm'], 2) : '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-light font-weight-bold">
                        <tr>
                            <td colspan="3" class="text-right">Totals for Period:</td>
                            <td class="text-danger">PKR {{ number_format($sorted_records->sum('debit'), 2) }}</td>
                            <td class="text-success">PKR {{ number_format($sorted_records->sum('credit'), 2) }}</td>
                            <td class="text-info">PKR {{ number_format($sorted_records->sum('comm'), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="card-footer py-2 bg-light d-flex justify-content-between align-items-center">
            <small class="text-muted">Generated on {{ date('Y-m-d H:i') }}</small>
            <span class="font-weight-bold">Net Monthly Earnings: PKR {{ number_format($sorted_records->sum('credit') + $sorted_records->sum('comm') - $sorted_records->sum('debit'), 2) }}</span>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    @media print {
        .navbar-nav, .sidebar, .btn, .card-header, .card-footer, form {
            display: none !important;
        }
        .container-fluid {
            padding: 0;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
@endsection
