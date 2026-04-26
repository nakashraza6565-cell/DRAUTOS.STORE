@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: #f8fafc;">
            <h6 class="m-0 font-weight-bold text-primary">Purchase Order Details: {{$purchaseOrder->po_number}}</h6>
            <div>
                <a href="{{route('purchase-orders.index')}}" class="btn btn-light btn-sm rounded-pill border px-3">
                    <i class="fas fa-arrow-left fa-sm mr-1"></i> Back to List
                </a>
                <button onclick="window.print();" class="btn btn-info btn-sm rounded-pill shadow-sm px-3 ml-2">
                    <i class="fas fa-print fa-sm mr-1"></i> Print PO
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-5">
                <div class="col-md-6">
                    <h6 class="text-uppercase text-gray-500 font-weight-bold small mb-3">Supplier Information</h6>
                    <div class="p-3 rounded-lg border bg-light">
                        <h5 class="text-gray-900 font-weight-bold mb-1">{{$purchaseOrder->supplier->name ?? 'N/A'}}</h5>
                        <p class="mb-1 text-gray-700"><strong>Company:</strong> {{$purchaseOrder->supplier->company_name ?? 'N/A'}}</p>
                        <p class="mb-1 text-gray-700"><strong>Phone:</strong> {{$purchaseOrder->supplier->phone ?? 'N/A'}}</p>
                        <p class="mb-0 text-gray-700"><strong>Email:</strong> {{$purchaseOrder->supplier->email ?? 'N/A'}}</p>
                    </div>
                </div>
                <div class="col-md-6 text-md-right">
                    <h6 class="text-uppercase text-gray-500 font-weight-bold small mb-3">Order Information</h6>
                    <div class="p-3">
                        <p class="mb-1"><strong>PO Number:</strong> <span class="bg-gray-200 px-2 py-1 rounded">#{{$purchaseOrder->po_number}}</span></p>
                        <p class="mb-1"><strong>Order Date:</strong> {{$purchaseOrder->order_date}}</p>
                        <p class="mb-1"><strong>Expected Delivery:</strong> {{$purchaseOrder->expected_delivery_date ?? 'N/A'}}</p>
                        <p class="mb-0"><strong>Status:</strong> 
                            @if($purchaseOrder->status=='pending')
                                <span class="badge badge-warning text-uppercase px-3 py-1">Pending</span>
                            @elseif($purchaseOrder->status=='ordered')
                                <span class="badge badge-info text-uppercase px-3 py-1">Ordered</span>
                            @elseif($purchaseOrder->status=='received')
                                <span class="badge badge-success text-uppercase px-3 py-1">Received</span>
                            @else
                                <span class="badge badge-danger text-uppercase px-3 py-1">Cancelled</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Product</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-right">Unit Price (Rs.)</th>
                            <th class="text-right">Subtotal (Rs.)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchaseOrder->items as $index => $item)
                            <tr>
                                <td>{{$index + 1}}</td>
                                <td>
                                    <div class="font-weight-bold text-gray-900">{{$item->product->title ?? 'N/A'}}</div>
                                    <small class="text-gray-500">SKU: {{$item->product->sku ?? 'N/A'}}</small>
                                </td>
                                <td class="text-center">{{$item->quantity}}</td>
                                <td class="text-right">{{number_format($item->unit_price, 2)}}</td>
                                <td class="text-right font-weight-bold">{{number_format($item->subtotal, 2)}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-right font-weight-bold text-gray-900" style="padding: 15px;">Grand Total:</td>
                            <td class="text-right" style="padding: 15px;">
                                <h5 class="m-0 font-weight-bold text-primary">Rs. {{number_format($purchaseOrder->total_amount, 2)}}</h5>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if($purchaseOrder->notes)
                <div class="mt-4 p-3 rounded border bg-light">
                    <h6 class="font-weight-bold text-gray-900 mb-2">Notes:</h6>
                    <p class="text-gray-700 mb-0">{{$purchaseOrder->notes}}</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    @media print {
        .navbar-nav, .topbar, .btn, footer {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        #wrapper #content-wrapper {
            background-color: white !important;
        }
    }
</style>
@endsection
