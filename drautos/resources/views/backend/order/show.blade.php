@extends('backend.layouts.master')

@section('title','Order Detail')

@section('main-content')
@include('backend.layouts.notification')
<div class="card">
    <h5 class="card-header">Order 
        <div class="float-right">
            <a href="{{route('order.print',$order->id)}}?type=standard" class="btn btn-sm btn-info shadow-sm"><i class="fas fa-print fa-sm text-white-50"></i> Standard Print</a>
            <a href="{{route('order.print',$order->id)}}?type=thermal" class="btn btn-sm btn-warning shadow-sm ml-2"><i class="fas fa-receipt fa-sm text-white-50"></i> Thermal Print</a>
            <a href="{{route('order.pdf',$order->id)}}" class="btn btn-sm btn-primary shadow-sm ml-2"><i class="fas fa-download fa-sm text-white-50"></i> Generate PDF</a>
            <a href="{{route('order.whatsapp',$order->id)}}" class="btn btn-sm btn-success shadow-sm ml-2"><i class="fab fa-whatsapp fa-sm text-white-50"></i> Send WhatsApp</a>
        </div>
    </h5>
  <div class="card-body">
<div class="row">
    {{-- Short Pane: Order Information --}}
    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 bg-light">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-info-circle mr-1"></i> Order Information</h6>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-5 text-muted small font-weight-bold">Order Number:</div>
                    <div class="col-sm-7 font-weight-bold text-dark">{{$order->order_number}}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-5 text-muted small font-weight-bold">Order Date:</div>
                    <div class="col-sm-7 small">{{$order->created_at->format('d M Y, h:i A')}}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-5 text-muted small font-weight-bold">Status:</div>
                    <div class="col-sm-7">
                        @if($order->status=='new') <span class="badge badge-primary">New</span>
                        @elseif($order->status=='process') <span class="badge badge-warning">Processing</span>
                        @elseif($order->status=='delivered') <span class="badge badge-success">Delivered</span>
                        @else <span class="badge badge-danger">{{$order->status}}</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-5 text-muted small font-weight-bold">Payment Method:</div>
                    <div class="col-sm-7 small">@if($order->payment_method=='cod') Cash on Delivery @else {{$order->payment_method}} @endif</div>
                </div>
                <div class="row">
                    <div class="col-sm-5 text-muted small font-weight-bold">Payment Status:</div>
                    <div class="col-sm-7"><span class="badge {{ $order->payment_status == 'paid' ? 'badge-success' : 'badge-warning' }}">{{ucfirst($order->payment_status)}}</span></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Short Pane: Shipping Information --}}
    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 bg-light">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-truck mr-1"></i> Shipping Information</h6>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-4 text-muted small font-weight-bold">Customer:</div>
                    <div class="col-sm-8 font-weight-bold">{{$order->first_name}} {{$order->last_name}}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-4 text-muted small font-weight-bold">Phone:</div>
                    <div class="col-sm-8 small font-weight-bold text-primary">{{$order->phone}}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-4 text-muted small font-weight-bold">Email:</div>
                    <div class="col-sm-8 small">{{$order->email}}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-4 text-muted small font-weight-bold">Address:</div>
                    <div class="col-sm-8 small">{{$order->address1}} {{$order->address2 ? ', '.$order->address2 : ''}}, {{$order->country}}</div>
                </div>
                @if($order->courier_company)
                <div class="row">
                    <div class="col-sm-4 text-muted small font-weight-bold">Courier:</div>
                    <div class="col-sm-8 small">{{$order->courier_company}} ({{$order->courier_number}})</div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Detail Information: Order Items --}}
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list mr-1"></i> Detailed Order Items</h6>
        <div class="badge badge-info px-3 py-2">Total Items: {{$order->quantity}}</div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="order-detail-table" width="100%" cellspacing="0">
                <thead class="bg-light">
                    <tr>
                        <th width="50">#</th>
                        <th>Product</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-right">Unit Price</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i = 1; @endphp
                    @foreach($order->cart_info as $cart)
                    <tr>
                        <td>{{$i++}}</td>
                        <td>
                            <div class="font-weight-bold">{{$cart->product->title ?? 'Deleted Product'}}</div>
                            <small class="text-muted">SKU: {{$cart->product->sku ?? 'N/A'}}</small>
                        </td>
                        <td class="text-center">{{$cart->quantity}}</td>
                        <td class="text-right">Rs. {{number_format($cart->price, 2)}}</td>
                        <td class="text-right font-weight-bold text-dark">Rs. {{number_format($cart->amount, 2)}}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-light font-weight-bold">
                    <tr>
                        <td colspan="4" class="text-right">Sub Total:</td>
                        <td class="text-right">Rs. {{number_format($order->sub_total, 2)}}</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-right">Shipping Charge:</td>
                        <td class="text-right">Rs. {{number_format($order->shipping->price ?? 0, 2)}}</td>
                    </tr>
                    @if($order->coupon > 0)
                    <tr>
                        <td colspan="4" class="text-right">Coupon Discount:</td>
                        <td class="text-right text-danger">- Rs. {{number_format($order->coupon, 2)}}</td>
                    </tr>
                    @endif
                    <tr class="text-primary" style="font-size: 1.1rem;">
                        <td colspan="4" class="text-right">Grand Total:</td>
                        <td class="text-right font-weight-bolder">Rs. {{number_format($order->total_amount, 2)}}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <div class="mt-4 pt-3 border-top d-flex justify-content-end">
            <a href="{{route('order.edit',$order->id)}}" class="btn btn-primary px-4 mr-2">
                <i class="fas fa-edit mr-1"></i> Edit Order
            </a>
            <form method="POST" action="{{route('order.destroy',[$order->id])}}" class="d-inline">
              @csrf
              @method('delete')
              <button class="btn btn-danger px-4 dltBtn" data-id={{$order->id}} title="Delete Order">
                  <i class="fas fa-trash-alt mr-1"></i> Delete
              </button>
            </form>
        </div>
    </div>
</div>

  </div>
</div>
@endsection

@push('styles')
<style>
    .order-info,.shipping-info{
        background:#fff;
        padding:25px;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        border: 1px solid #edf2f7;
    }
    .order-info h4,.shipping-info h4{
        font-weight: 800;
        color: #4b312c;
        text-transform: uppercase;
        font-size: 16px;
        border-bottom: 2px solid #4b312c;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }
    .table td {
        vertical-align: middle;
        font-size: 14px;
    }

</style>
@endpush
