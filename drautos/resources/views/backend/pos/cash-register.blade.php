@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid">
    @include('backend.layouts.notification')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Cash Register & Daily Reconciliation</h6>
        </div>
        <div class="card-body">
            @if($activeRegister)
                <!-- Active Session View -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="border rounded p-3 bg-light border-left-success" style="border-left: 5px solid #1cc88a !important;">
                            <small class="text-uppercase font-weight-bold text-muted">Register Status</small>
                            <h4 class="text-success mt-1">OPEN</h4>
                            <small>Account: <strong>{{$activeRegister->financialAccount->name ?? 'Default Cash'}}</strong></small><br>
                            <small>By: {{$activeRegister->user->name ?? 'Admin'}} at {{$activeRegister->opened_at->format('d M, h:i A')}}</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 bg-light">
                            <small class="text-uppercase font-weight-bold text-muted">Opening Balance</small>
                            <h4 class="mt-1">Rs. {{number_format($activeRegister->opening_amount, 2)}}</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 bg-white border-left-primary" style="border-left: 5px solid #4e73df !important;">
                            <small class="text-uppercase font-weight-bold text-primary">Expected Cash in Drawer</small>
                            <h4 class="mt-1 font-weight-bold">Rs. {{number_format($summary['expected_cash'], 2)}}</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                         <!-- Action to Close -->
                         <button class="btn btn-danger btn-block h-100" data-toggle="modal" data-target="#closeRegisterModal">
                            <i class="fas fa-power-off mb-2"></i><br>CLOSE REGISTER
                         </button>
                    </div>
                </div>

                <!-- Detailed Breakdown -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-white py-3"><h6 class="m-0 font-weight-bold text-success"><i class="fas fa-plus-circle mr-2"></i>Cash In (Received)</h6></div>
                            <div class="card-body py-4 text-center">
                                 <h2 class="text-success font-weight-bold">Rs. {{number_format($summary['total_in'], 2)}}</h2>
                                 <p class="text-muted small">Total amount received in this account since opening</p>
                                 <div class="border-top pt-3 mt-3 text-left">
                                     <div class="d-flex justify-content-between mb-1">
                                         <span>Total Sales & Collections:</span>
                                         <span class="font-weight-bold">Rs. {{number_format($summary['breakdown']['sales'], 2)}}</span>
                                     </div>
                                 </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-white py-3"><h6 class="m-0 font-weight-bold text-danger"><i class="fas fa-minus-circle mr-2"></i>Cash Out (Paid)</h6></div>
                            <div class="card-body py-4 text-center">
                                 <h2 class="text-danger font-weight-bold">Rs. {{number_format($summary['total_out'], 2)}}</h2>
                                 <p class="text-muted small">Total amount paid out from this account</p>
                                 <div class="border-top pt-3 mt-3 text-left">
                                     <div class="d-flex justify-content-between mb-1">
                                         <span>Daily Expenses:</span>
                                         <span class="font-weight-bold">Rs. {{number_format($summary['breakdown']['expenses'], 2)}}</span>
                                     </div>
                                     <div class="d-flex justify-content-between mb-1">
                                         <span>Supplier Payments:</span>
                                         <span class="font-weight-bold">Rs. {{number_format($summary['breakdown']['supplier_payments'], 2)}}</span>
                                     </div>
                                     <div class="d-flex justify-content-between mb-1">
                                         <span>Other Purchases:</span>
                                         <span class="font-weight-bold">Rs. {{number_format($summary['breakdown']['others'], 2)}}</span>
                                     </div>
                                 </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Open New Register -->
                <div class="text-center py-5 border rounded bg-white shadow-sm mb-4">
                    <i class="fas fa-cash-register fa-4x text-gray-300 mb-3"></i>
                    <h4 class="mb-3">Open a Cash Register</h4>
                    <form action="{{route('cash-register.open')}}" method="POST" class="d-inline-block text-left" style="max-width: 400px;">
                        @csrf
                        <div class="form-group">
                            <label>Select Admin / Cash Account</label>
                            <select name="financial_account_id" class="form-control" required>
                                @foreach($cashAccounts as $acc)
                                    <option value="{{$acc->id}}">{{$acc->name}} (Current: Rs. {{number_format($acc->current_balance, 0)}})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Opening Amount (Leave empty to use Current Balance)</label>
                            <input type="number" name="opening_amount" class="form-control" placeholder="Optional" min="0">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block shadow-sm">
                            <i class="fas fa-check mr-2"></i> OPEN REGISTER SESSION
                        </button>
                    </form>
                </div>
            @endif

            <!-- Unified Account Balances (Always Visible) -->
            <h5 class="mb-3 font-weight-bold text-gray-800 mt-4">All Accounts & Balances</h5>
            <div class="row mb-4">
                @foreach($financialAccounts as $acc)
                <div class="col-md-3 mb-3">
                    <div class="card border-left-{{$acc->type == 'cash' ? 'primary' : 'info'}} shadow-sm h-100 py-2">
                        <div class="card-body py-1">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-{{$acc->type == 'cash' ? 'primary' : 'info'}} text-uppercase mb-1">{{$acc->name}}</div>
                                    <div class="h6 mb-0 font-weight-bold text-gray-800">Rs. {{number_format($acc->current_balance, 2)}}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-{{$acc->type == 'bank' ? 'university' : ($acc->type == 'wallet' ? 'mobile-alt' : 'money-bill-wave')}} fa-sm text-gray-300"></i>
                                </div>
                            </div>
                            <div class="mt-2 text-right">
                                <a href="{{route('financial-accounts.show', $acc->id)}}" class="text-xs text-primary font-weight-bold">View History</a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <hr class="my-5">
            <h5 class="mb-3 font-weight-bold text-gray-800">Register History</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Staff</th>
                            <th>Account</th>
                            <th>Opened At</th>
                            <th>Closed At</th>
                            <th>Opening</th>
                            <th>Closing</th>
                            <th>Difference</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($history as $h)
                        <tr>
                            <td>#{{$h->id}}</td>
                            <td>{{$h->user->name ?? 'User'}}</td>
                            <td><small>{{$h->financialAccount->name ?? '-'}}</small></td>
                            <td>{{$h->opened_at->format('d M, h:i A')}}</td>
                            <td>{{$h->closed_at ? $h->closed_at->format('d M, h:i A') : 'STILL OPEN'}}</td>
                            <td>Rs. {{number_format($h->opening_amount, 2)}}</td>
                            <td>{{ $h->closing_amount ? 'Rs. '.number_format($h->closing_amount, 2) : '-'}}</td>
                            <td>
                                @if($h->closing_amount)
                                    @php $diff = $h->closing_amount - $h->opening_amount; @endphp
                                    <span class="{{$diff >= 0 ? 'text-success' : 'text-danger'}}">
                                        {{$diff >= 0 ? '+' : ''}} Rs. {{number_format($diff, 2)}}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td><span class="badge {{$h->status == 'open' ? 'badge-success' : 'badge-secondary'}}">{{strtoupper($h->status)}}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@if($activeRegister)
<!-- Close Register Modal -->
<div class="modal fade" id="closeRegisterModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Close Register Session</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{route('cash-register.close', $activeRegister->id)}}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to close the current register session?</p>
                    <div class="form-group">
                        <label>Closing Note / Remarks</label>
                        <textarea name="note" class="form-control" rows="3" placeholder="Enter any discrepancies or notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Confirm Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
