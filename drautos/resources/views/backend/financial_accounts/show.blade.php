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
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white border-bottom d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Account Transaction History</h6>
            <a href="{{route('financial-accounts.index')}}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-chevron-left mr-1"></i> Back to Accounts
            </a>
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
@endsection
