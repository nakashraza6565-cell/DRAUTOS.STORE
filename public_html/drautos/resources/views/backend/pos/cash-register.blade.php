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
                        <small>Opened by: {{$activeRegister->user->name ?? 'Admin'}} at {{$activeRegister->opened_at->format('d M, h:i A')}}</small>
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
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-2"><h6 class="m-0 font-weight-bold text-success">Cash Inflow (+)</h6></div>
                        <div class="card-body py-2">
                             <div class="d-flex justify-content-between mb-1"><span>POS Cash Sales:</span><span class="font-weight-bold">Rs. {{number_format($summary['pos_sales'], 2)}}</span></div>
                             <div class="d-flex justify-content-between"><span>Later Cash Collections:</span><span class="font-weight-bold">Rs. {{number_format($summary['collections'], 2)}}</span></div>
                             <hr class="my-2">
                             <div class="d-flex justify-content-between text-success"><span>Total Inflow:</span><span class="font-weight-bold">Rs. {{number_format($summary['total_in'], 2)}}</span></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-2"><h6 class="m-0 font-weight-bold text-danger">Cash Outflow (-)</h6></div>
                        <div class="card-body py-2">
                             <div class="d-flex justify-content-between mb-1"><span>Expenses Paid:</span><span class="font-weight-bold">Rs. {{number_format($summary['expenses'], 2)}}</span></div>
                             <div class="d-flex justify-content-between mb-1"><span>Purchase Paid:</span><span class="font-weight-bold">Rs. {{number_format($summary['purchase_payments'], 2)}}</span></div>
                             <div class="d-flex justify-content-between"><span>Packaging Paid:</span><span class="font-weight-bold">Rs. {{number_format($summary['packaging_payments'], 2)}}</span></div>
                             <hr class="my-2">
                             <div class="d-flex justify-content-between text-danger"><span>Total Outflow:</span><span class="font-weight-bold">Rs. {{number_format($summary['total_out'], 2)}}</span></div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <!-- Closed State - Open New -->
            <div class="text-center py-5 border rounded bg-white shadow-sm">
                <i class="fas fa-cash-register fa-4x text-gray-300 mb-3"></i>
                <h4 class="mb-3">Register is Closed</h4>
                <form action="{{route('cash-register.open')}}" method="POST" class="d-inline-block form-inline">
                    @csrf
                    <input type="number" name="opening_amount" class="form-control mr-2" placeholder="Opening Cash Amount" required min="0">
                    <button type="submit" class="btn btn-primary shadow-sm">
                        <i class="fas fa-check mr-2"></i> OPEN REGISTER FOR NEW SESSION
                    </button>
                </form>
            </div>
            @endif

            <hr class="my-5">
            <h5 class="mb-3 font-weight-bold text-gray-800">Register History</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Staff</th>
                            <th>Opened At</th>
                            <th>Closed At</th>
                            <th>Opening Amount</th>
                            <th>Closing Amount</th>
                            <th>Difference</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($history as $h)
                        <tr>
                            <td>#{{$h->id}}</td>
                            <td>{{$h->user->name ?? 'User'}}</td>
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
