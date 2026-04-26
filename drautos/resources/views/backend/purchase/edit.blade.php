@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3" style="background: #f8fafc;">
            <h6 class="m-0 font-weight-bold text-primary">Edit Purchase Order: {{$purchaseOrder->po_number}}</h6>
        </div>
        <div class="card-body">
            <form method="post" action="{{route('purchase-orders.update', $purchaseOrder->id)}}">
                @csrf 
                @method('PATCH')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card bg-light border-0 mb-4" style="border-radius: 12px;">
                            <div class="card-body">
                                <h6 class="font-weight-bold text-gray-800 mb-3">Order Details</h6>
                                <p class="mb-1"><strong>Supplier:</strong> {{$purchaseOrder->supplier->name ?? 'N/A'}}</p>
                                <p class="mb-1"><strong>Order Date:</strong> {{$purchaseOrder->order_date}}</p>
                                <p class="mb-0 text-primary h5 mt-2"><strong>Total Amount:</strong> Rs. {{number_format($purchaseOrder->total_amount, 2)}}</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="status" class="col-form-label font-weight-bold">Order Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="pending" {{$purchaseOrder->status == 'pending' ? 'selected' : ''}}>Pending</option>
                                <option value="ordered" {{$purchaseOrder->status == 'ordered' ? 'selected' : ''}}>Ordered</option>
                                <option value="received" {{$purchaseOrder->status == 'received' ? 'selected' : ''}}>Received</option>
                                <option value="cancelled" {{$purchaseOrder->status == 'cancelled' ? 'selected' : ''}}>Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 border-left">
                        <h6 class="font-weight-bold text-info mb-3"><i class="fas fa-wallet mr-2"></i>Payment Management</h6>
                        
                        <div class="form-group">
                            <label for="paid_amount" class="col-form-label font-weight-bold">Initial Paid Amount (PKR)</label>
                            <input id="paid_amount" type="number" step="0.01" name="paid_amount" value="{{$purchaseOrder->paid_amount}}" class="form-control" oninput="calculatePending()">
                        </div>

                        <div class="form-group">
                            <label class="col-form-label font-weight-bold text-secondary">Remaining Balance</label>
                            <input id="pending_display" type="text" class="form-control bg-white" readonly value="{{ number_format($purchaseOrder->total_amount - $purchaseOrder->paid_amount, 2) }}">
                        </div>

                        <div class="form-group">
                            <label for="due_date" class="col-form-label font-weight-bold">Balance Due Date</label>
                            @php
                                $reminder = \App\Models\PaymentReminder::where('reference_number', $purchaseOrder->po_number)->first();
                            @endphp
                            <input id="due_date" type="date" name="due_date" value="{{ $reminder ? $reminder->due_date->format('Y-m-d') : '' }}" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="form-group text-right mt-4 pt-3 border-top">
                    <a href="{{route('purchase-orders.index')}}" class="btn btn-light rounded-pill px-4 border mr-2">Cancel</a>
                    <button class="btn btn-primary rounded-pill px-5 shadow-sm" type="submit">Update Order & Payments</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function calculatePending() {
        let total = {{ $purchaseOrder->total_amount }};
        let paid = parseFloat(document.getElementById('paid_amount').value) || 0;
        let pending = total - paid;
        document.getElementById('pending_display').value = pending.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
</script>
@endpush
