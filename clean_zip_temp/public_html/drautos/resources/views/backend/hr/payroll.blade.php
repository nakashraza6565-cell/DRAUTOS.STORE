@extends('backend.layouts.master')
@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Employee Payroll (Run: {{date('F Y')}})</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Role</th>
                        <th>Base Salary</th>
                        <th>Bonus/Comm.</th>
                        <th>Deductions</th>
                        <th>Net Pay</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(\App\User::whereIn('role', ['admin','manager','staff'])->get() as $emp)
                    <tr>
                        <td>{{$emp->name}}</td>
                        <td><span class="badge badge-info">{{ ucfirst($emp->role) }}</span></td>
                        <td>-</td> <!-- Placeholder for salary column if not exists -->
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td><span class="badge badge-warning">Unpaid</span></td>
                        <td>
                            <button class="btn btn-sm btn-success">Pay Now</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
