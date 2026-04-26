@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3" style="background: #f8fafc;">
            <h6 class="m-0 font-weight-bold text-primary">Edit Cheque #{{$cheque->cheque_number}}</h6>
        </div>
        <div class="card-body">
            <form method="post" action="{{route('cheques.update', $cheque->id)}}">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="type" class="col-form-label font-weight-bold">Cheque Category</label>
                            <input type="text" class="form-control" value="{{$cheque->type == 'received' ? 'Cheque Received (From Customer)' : 'Cheque Paid (To Supplier)'}}" disabled>
                            <input type="hidden" name="type" value="{{$cheque->type}}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="cheque_number" class="col-form-label font-weight-bold">Cheque Number <span class="text-danger">*</span></label>
                            <input id="cheque_number" type="text" name="cheque_number" placeholder="Enter Cheque Number" value="{{$cheque->cheque_number}}" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="amount" class="col-form-label font-weight-bold">Amount (PKR) <span class="text-danger">*</span></label>
                            <input id="amount" type="number" step="0.01" name="amount" placeholder="0.00" value="{{$cheque->amount}}" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="col-form-label font-weight-bold">Party (Customer/Supplier)</label>
                            <input type="text" class="form-control" value="{{$cheque->party->name ?? 'N/A'}}" disabled>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="cheque_date" class="col-form-label font-weight-bold">Cheque Date <span class="text-danger">*</span></label>
                            <input id="cheque_date" type="date" name="cheque_date" value="{{$cheque->cheque_date->format('Y-m-d')}}" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="clearing_date" class="col-form-label font-weight-bold">Clearing/DueDate <span class="text-danger">*</span></label>
                            <input id="clearing_date" type="date" name="clearing_date" value="{{$cheque->clearing_date->format('Y-m-d')}}" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="bank_name" class="col-form-label font-weight-bold">Bank Name</label>
                            <input id="bank_name" type="text" name="bank_name" placeholder="Bank Name" value="{{$cheque->bank_name}}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="bank_branch" class="col-form-label font-weight-bold">Bank Branch</label>
                            <input id="bank_branch" type="text" name="bank_branch" placeholder="Branch Name/Code" value="{{$cheque->bank_branch}}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                         <div class="form-group">
                            <label for="status" class="col-form-label font-weight-bold">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="pending" {{$cheque->status == 'pending' ? 'selected' : ''}}>Pending</option>
                                <option value="cleared" {{$cheque->status == 'cleared' ? 'selected' : ''}}>Cleared</option>
                                <option value="bounced" {{$cheque->status == 'bounced' ? 'selected' : ''}}>Bounced</option>
                                <option value="cancelled" {{$cheque->status == 'cancelled' ? 'selected' : ''}}>Cancelled</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="notes" class="col-form-label font-weight-bold">Additional Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Reference details, reason, etc...">{{$cheque->notes}}</textarea>
                </div>

                <div class="form-group mb-0 text-right">
                    <a href="{{route('cheques.index')}}" class="btn btn-light rounded-pill px-4 mr-2 border">Cancel</a>
                    <button class="btn btn-primary rounded-pill px-5 shadow-sm" type="submit">Update Cheque</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
