@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3" style="background: #f8fafc;">
            <h6 class="m-0 font-weight-bold text-primary">Add New Cheque</h6>
        </div>
        <div class="card-body">
            <form method="post" action="{{route('cheques.store')}}">
                {{csrf_field()}}
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="type" class="col-form-label font-weight-bold">Cheque Category <span class="text-danger">*</span></label>
                            <select name="type" id="type" class="form-control" required>
                                <option value="received">Cheque Received (From Customer)</option>
                                <option value="paid">Cheque Paid (To Supplier)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="cheque_number" class="col-form-label font-weight-bold">Cheque Number <span class="text-danger">*</span></label>
                            <input id="cheque_number" type="text" name="cheque_number" placeholder="Enter Cheque Number" value="{{old('cheque_number')}}" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="amount" class="col-form-label font-weight-bold">Amount (PKR) <span class="text-danger">*</span></label>
                            <input id="amount" type="number" step="0.01" name="amount" placeholder="0.00" value="{{old('amount')}}" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group" id="customer_div">
                            <label for="customer_id" class="col-form-label font-weight-bold">Select Customer</label>
                            <select name="party_id_cust" id="party_id_cust" class="form-control selectpicker" data-live-search="true">
                                <option value="">-- Select Customer --</option>
                                @foreach($customers as $customer)
                                    <option value="{{$customer->id}}">{{$customer->name}} ({{$customer->phone}})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" id="supplier_div" style="display:none;">
                            <label for="supplier_id" class="col-form-label font-weight-bold">Select Supplier</label>
                            <select name="party_id_supp" id="party_id_supp" class="form-control selectpicker" data-live-search="true">
                                <option value="">-- Select Supplier --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{$supplier->id}}">{{$supplier->name}} ({{$supplier->company_name}})</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="party_type" id="party_type" value="App\User">
                        <input type="hidden" name="party_id" id="party_id">
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="cheque_date" class="col-form-label font-weight-bold">Cheque Date <span class="text-danger">*</span></label>
                            <input id="cheque_date" type="date" name="cheque_date" value="{{date('Y-m-d')}}" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="clearing_date" class="col-form-label font-weight-bold">Clearing/DueDate <span class="text-danger">*</span></label>
                            <input id="clearing_date" type="date" name="clearing_date" value="{{date('Y-m-d')}}" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="bank_name" class="col-form-label font-weight-bold">Bank Name</label>
                            <input id="bank_name" type="text" name="bank_name" placeholder="Bank Name" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="bank_branch" class="col-form-label font-weight-bold">Bank Branch</label>
                            <input id="bank_branch" type="text" name="bank_branch" placeholder="Branch Name/Code" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="notes" class="col-form-label font-weight-bold">Additional Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Reference details, reason, etc..."></textarea>
                </div>

                <div class="form-group mb-0 text-right">
                    <a href="{{route('cheques.index')}}" class="btn btn-light rounded-pill px-4 mr-2 border">Cancel</a>
                    <button class="btn btn-primary rounded-pill px-5 shadow-sm" type="submit">Save Cheque</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"></script>
<script>
    $(document).ready(function() {
        $('#type').change(function() {
            if($(this).val() == 'received') {
                $('#customer_div').show();
                $('#supplier_div').hide();
                $('#party_type').val('App\\User');
            } else {
                $('#customer_div').hide();
                $('#supplier_div').show();
                $('#party_type').val('App\\Models\\Supplier');
            }
        });

        $('form').submit(function() {
            if($('#type').val() == 'received') {
                $('#party_id').val($('#party_id_cust').val());
            } else {
                $('#party_id').val($('#party_id_supp').val());
            }
        });
    });
</script>
@endpush
