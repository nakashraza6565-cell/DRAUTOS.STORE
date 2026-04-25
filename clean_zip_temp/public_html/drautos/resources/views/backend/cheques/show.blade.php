@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Cheque Details: #{{ $cheque->cheque_number }}</h6>
            <a href="{{ route('cheques.index') }}" class="btn btn-primary btn-sm"><i class="fas fa-arrow-left"></i> Back to List</a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td class="font-weight-bold" style="width: 30%">Cheque Number:</td>
                            <td>{{ $cheque->cheque_number }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Amount:</td>
                            <td class="h5 text-primary font-weight-bold">PKR {{ number_format($cheque->amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Type:</td>
                            <td>
                                <span class="badge badge-{{ $cheque->type == 'received' ? 'success' : 'danger' }}">
                                    {{ strtoupper($cheque->type) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Status:</td>
                            <td>
                                @if($cheque->status == 'pending')
                                    <span class="badge badge-warning">PENDING</span>
                                @elseif($cheque->status == 'cleared')
                                    <span class="badge badge-success">CLEARED</span>
                                @elseif($cheque->status == 'bounced')
                                    <span class="badge badge-danger">BOUNCED</span>
                                @else
                                    <span class="badge badge-secondary">CANCELLED</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td class="font-weight-bold" style="width: 30%">Party:</td>
                            <td>{{ $cheque->party->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Cheque Date:</td>
                            <td>{{ $cheque->cheque_date->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Clearing Date:</td>
                            <td class="text-info font-weight-bold">{{ $cheque->clearing_date->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Bank Name:</td>
                            <td>{{ $cheque->bank_name ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <hr>
            
            <div class="row mt-3">
                <div class="col-md-12">
                    <h6>Notes / Remarks:</h6>
                    <div class="p-3 bg-light rounded">
                        {{ $cheque->notes ?: 'No notes available' }}
                    </div>
                </div>
            </div>

            <div class="mt-4 text-right">
                @if($cheque->status == 'pending')
                    <form method="POST" action="{{ route('cheques.mark-cleared', $cheque->id) }}" class="d-inline">
                        @csrf
                        <button class="btn btn-success"><i class="fas fa-check"></i> Mark Cleared</button>
                    </form>
                    <form method="POST" action="{{ route('cheques.mark-bounced', $cheque->id) }}" class="d-inline ml-2">
                        @csrf
                        <button class="btn btn-danger"><i class="fas fa-times-circle"></i> Mark Bounced</button>
                    </form>
                @endif
                <a href="{{ route('cheques.edit', $cheque->id) }}" class="btn btn-info ml-2"><i class="fas fa-edit"></i> Edit</a>
            </div>
        </div>
    </div>
</div>
@endsection
