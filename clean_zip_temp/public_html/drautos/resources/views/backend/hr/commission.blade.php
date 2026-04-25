@extends('backend.layouts.master')
@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Sales Commissions</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Staff Member</th>
                        <th>Sales Volume</th>
                        <th>Commission Rate</th>
                        <th>Earned Commission</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                         <td colspan="5" class="text-center">No commission records generated yet for this period.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
