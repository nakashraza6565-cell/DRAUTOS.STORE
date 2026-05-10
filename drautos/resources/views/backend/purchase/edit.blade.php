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
                    <div class="col-md-12">
                        <div class="card bg-light border-0 mb-4" style="border-radius: 12px;">
                            <div class="card-body">
                                <h6 class="font-weight-bold text-gray-800 mb-3">Order Summary</h6>
                                <p class="mb-1"><strong>Supplier:</strong> {{$purchaseOrder->supplier->name ?? 'N/A'}}</p>
                                <p class="mb-1"><strong>Order Date:</strong> {{$purchaseOrder->order_date}}</p>
                                <p class="mb-0"><strong>Items:</strong> {{$purchaseOrder->items->count()}} Products Requested</p>
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

                        <div class="form-group">
                            <label for="notes" class="col-form-label font-weight-bold">Notes / Instructions</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3">{{$purchaseOrder->notes}}</textarea>
                        </div>
                    </div>
                </div>

                <div class="form-group text-right mt-4 pt-3 border-top">
                    <a href="{{route('purchase-orders.index')}}" class="btn btn-light rounded-pill px-4 border mr-2">Cancel</a>
                    <button class="btn btn-primary rounded-pill px-5 shadow-sm" type="submit">Update Order Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
