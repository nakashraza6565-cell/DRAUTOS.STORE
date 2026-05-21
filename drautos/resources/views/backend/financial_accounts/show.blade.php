@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{$account->name}}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{number_format($account->current_balance, 2)}}</div>
                            <div class="small text-muted">{{ucfirst($account->type)}} | {{$account->account_number}}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($summary)
        <div class="col-md-8 mt-3 mt-md-0">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-3">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-2">
                                <i class="fas fa-chart-line mr-1"></i> Money In vs. Money Out on {{ \Carbon\Carbon::parse($summary['date'])->format('d M Y') }}
                            </div>
                            <div class="row">
                                <div class="col-sm-4 mb-2 mb-sm-0">
                                    <small class="text-muted d-block uppercase font-weight-bold" style="font-size: 9px; letter-spacing: 0.5px;">MONEY IN (CREDIT)</small>
                                    <span class="font-weight-bold text-success" style="font-size: 1.1rem;">
                                        Rs. {{ number_format($summary['total_in'], 2) }}
                                    </span>
                                </div>
                                <div class="col-sm-4 mb-2 mb-sm-0 border-left-sm pl-sm-3">
                                    <small class="text-muted d-block uppercase font-weight-bold" style="font-size: 9px; letter-spacing: 0.5px;">MONEY OUT (DEBIT)</small>
                                    <span class="font-weight-bold text-danger" style="font-size: 1.1rem;">
                                        Rs. {{ number_format($summary['total_out'], 2) }}
                                    </span>
                                </div>
                                <div class="col-sm-4 border-left-sm pl-sm-3">
                                    <small class="text-muted d-block uppercase font-weight-bold" style="font-size: 9px; letter-spacing: 0.5px;">NET FLOW</small>
                                    <span class="font-weight-bold {{ $summary['net_flow'] >= 0 ? 'text-success' : 'text-danger' }}" style="font-size: 1.1rem;">
                                        Rs. {{ number_format($summary['net_flow'], 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto d-none d-lg-block">
                            <i class="fas fa-balance-scale fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <h6 class="m-0 font-weight-bold text-primary mb-3 mb-md-0">Account Transaction History</h6>
            
            <div class="d-flex flex-wrap align-items-center">
                <form method="GET" action="{{ route('financial-accounts.show', $account->id) }}" class="form-inline mr-md-3 mb-2 mb-md-0">
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light border-0" style="border-radius: 15px 0 0 15px;"><i class="fas fa-calendar-alt text-primary"></i></span>
                        </div>
                        <input type="date" name="date" class="form-control form-control-sm border-0 bg-light" value="{{ request('date') }}" style="border-radius: 0 15px 15px 0; width: 140px;">
                        <div class="input-group-append ml-2">
                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">Filter</button>
                            @if(request('date'))
                                <a href="{{ route('financial-accounts.show', $account->id) }}" class="btn btn-light btn-sm rounded-pill px-3 ml-1 border shadow-sm">Clear</a>
                            @endif
                        </div>
                    </div>
                </form>
                
                <a href="{{route('financial-accounts.index')}}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="fas fa-chevron-left mr-1"></i> Back to Accounts
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="bg-light">
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Reference</th>
                            <th>Credit (In)</th>
                            <th>Debit (Out)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $t)
                        <tr>
                            <td>{{$t->transaction_date->format('d M Y')}}</td>
                            <td>{{$t->description}}</td>
                            <td>
                                <span class="badge badge-secondary">{{$t->reference_type}} #{{$t->reference_id}}</span>
                            </td>
                            <td class="text-success font-weight-bold">
                                {{$t->type == 'in' ? 'Rs. '.number_format($t->amount, 2) : '-'}}
                            </td>
                            <td class="text-danger font-weight-bold">
                                {{$t->type == 'out' ? 'Rs. '.number_format($t->amount, 2) : '-'}}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">No transactions found for this account.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{$transactions->links()}}
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media (min-width: 576px) {
        .border-left-sm {
            border-left: 1px solid #e3e6f0 !important;
        }
    }
</style>
@endpush
@endsection
