@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            @include('backend.layouts.notification')
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white border-bottom">
            <h6 class="m-0 font-weight-bold text-primary">Financial Accounts (Bank/Wallets)</h6>
            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addAccountModal">
                <i class="fas fa-plus mr-1"></i> New Account
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="accounts-table">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th>Account Name</th>
                            <th>Type</th>
                            <th>Account Number</th>
                            <th>Current Balance</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accounts as $account)
                        <tr>
                            <td class="font-weight-bold">{{$account->name}}</td>
                            <td>{{ucfirst($account->type ?? '')}}</td>
                            <td>{{$account->account_number ?: 'N/A'}}</td>
                            <td class="font-weight-bold text-{{($account->current_balance ?? 0) >= 0 ? 'success' : 'danger'}}">
                                Rs. {{number_format($account->current_balance ?? 0, 2)}}
                            </td>
                            <td>
                                <span class="badge badge-{{$account->status == 'active' ? 'success' : 'danger'}}">
                                    {{ucfirst($account->status ?? '')}}
                                </span>
                            </td>
                            <td>
                                <a href="{{route('financial-accounts.show', $account->id)}}" class="btn btn-info btn-sm rounded-circle">
                                    <i class="fas fa-eye fa-sm"></i>
                                </a>
                                <button class="btn btn-primary btn-sm rounded-circle" data-toggle="modal" data-target="#editModal{{$account->id}}">
                                    <i class="fas fa-edit fa-sm"></i>
                                </button>
                                <form method="POST" action="{{ route('financial-accounts.destroy', $account->id) }}" style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm rounded-circle dltBtn" title="Delete Account" onclick="return confirm('Are you sure you want to delete this account? All associated transaction history will be lost.')">
                                        <i class="fas fa-trash fa-sm"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModal{{$account->id}}" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <form action="{{route('financial-accounts.update', $account->id)}}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Account</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Account Name</label>
                                                <input type="text" name="name" class="form-control" value="{{$account->name}}" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Type</label>
                                                <select name="type" class="form-control">
                                                    <option value="bank" {{$account->type == 'bank' ? 'selected' : ''}}>Bank</option>
                                                    <option value="wallet" {{$account->type == 'wallet' ? 'selected' : ''}}>Mobile Wallet (JazzCash/EasyPaisa)</option>
                                                    <option value="cash" {{$account->type == 'cash' ? 'selected' : ''}}>Physical Cash</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Account Number</label>
                                                <input type="text" name="account_number" class="form-control" value="{{$account->account_number}}">
                                            </div>
                                            <div class="form-group">
                                                <label>Opening Balance</label>
                                                <input type="number" name="opening_balance" class="form-control" value="{{$account->opening_balance}}" step="0.01">
                                            </div>
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select name="status" class="form-control">
                                                    <option value="active" {{$account->status == 'active' ? 'selected' : ''}}>Active</option>
                                                    <option value="inactive" {{$account->status == 'inactive' ? 'selected' : ''}}>Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Update Account</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addAccountModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{route('financial-accounts.store')}}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add New Financial Account</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Account Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. HBL Main, JazzCash Shop" required>
                    </div>
                    <div class="form-group">
                        <label>Type</label>
                        <select name="type" class="form-control">
                            <option value="bank">Bank</option>
                            <option value="wallet">Mobile Wallet (JazzCash/EasyPaisa)</option>
                            <option value="cash">Physical Cash</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Account Number</label>
                        <input type="text" name="account_number" class="form-control" placeholder="Optional">
                    </div>
                    <div class="form-group">
                        <label>Opening Balance</label>
                        <input type="number" name="opening_balance" class="form-control" placeholder="0.00" step="0.01">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
