@extends('backend.layouts.master')

@section('title','Order Detail')

@section('main-content')
@include('backend.layouts.notification')
<div class="card">
    <h5 class="card-header">Order 
        <div class="float-right mobile-stack">
            <a href="{{route('order.print',$order->id)}}?type=standard" class="btn btn-sm btn-info shadow-sm"><i class="fas fa-print fa-sm text-white-50 mr-1"></i> Standard Print</a>
            <a href="{{route('order.print',$order->id)}}?type=thermal" class="btn btn-sm btn-warning shadow-sm"><i class="fas fa-receipt fa-sm text-white-50 mr-1"></i> Thermal Print</a>
            <a href="{{route('order.pdf',$order->id)}}" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-download fa-sm text-white-50 mr-1"></i> Generate PDF</a>
            <a href="{{route('order.whatsapp',$order->id)}}" class="btn btn-sm btn-success shadow-sm"><i class="fab fa-whatsapp fa-sm text-white-50 mr-1"></i> Send WhatsApp</a>
        </div>
    </h5>
  <div class="card-body p-2 p-md-4">
<div class="row">
    {{-- Short Pane: Order Information --}}
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100 border-0" style="border-radius: 16px; overflow: hidden;">
            <div class="card-header py-3 bg-white border-bottom">
                <h6 class="m-0 font-weight-bold text-primary text-uppercase small" style="letter-spacing: 1px;"><i class="fas fa-info-circle mr-1"></i> Order Information</h6>
            </div>
            <div class="card-body">
                <div class="row mb-3 align-items-center">
                    <div class="col-5 text-muted small font-weight-bold">Order Number:</div>
                    <div class="col-7 font-weight-bold text-dark h6 mb-0">{{$order->order_number}}</div>
                </div>
                <div class="row mb-3 align-items-center">
                    <div class="col-5 text-muted small font-weight-bold">Order Date:</div>
                    <div class="col-7 small">{{$order->created_at->format('d M Y, h:i A')}}</div>
                </div>
                <div class="row mb-3 align-items-center">
                    <div class="col-5 text-muted small font-weight-bold">Status:</div>
                    <div class="col-7">
                        @if($order->status=='new') <span class="badge badge-pill badge-primary px-3">New</span>
                        @elseif($order->status=='process') <span class="badge badge-pill badge-warning px-3">Processing</span>
                        @elseif($order->status=='delivered') <span class="badge badge-pill badge-success px-3">Delivered</span>
                        @else <span class="badge badge-pill badge-danger px-3">{{$order->status}}</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-3 align-items-center">
                    <div class="col-5 text-muted small font-weight-bold">Payment Method:</div>
                    <div class="col-7 small">@if($order->payment_method=='cod') Cash on Delivery @else {{$order->payment_method}} @endif</div>
                </div>
                <div class="row align-items-center">
                    <div class="col-5 text-muted small font-weight-bold">Payment Status:</div>
                    <div class="col-7"><span class="badge badge-pill {{ $order->payment_status == 'paid' ? 'badge-success' : 'badge-warning' }} px-3">{{ucfirst($order->payment_status)}}</span></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Short Pane: Shipping Information --}}
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100 border-0" style="border-radius: 16px; overflow: hidden;">
            <div class="card-header py-3 bg-white border-bottom">
                <h6 class="m-0 font-weight-bold text-primary text-uppercase small" style="letter-spacing: 1px;"><i class="fas fa-truck mr-1"></i> Shipping Information</h6>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-4 text-muted small font-weight-bold">Customer:</div>
                    <div class="col-8 font-weight-bold text-dark">{{$order->first_name}} {{$order->last_name}}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-4 text-muted small font-weight-bold">Phone:</div>
                    <div class="col-8 small font-weight-bold text-primary"><a href="tel:{{$order->phone}}">{{$order->phone}}</a></div>
                </div>
                <div class="row mb-3">
                    <div class="col-4 text-muted small font-weight-bold">Email:</div>
                    <div class="col-8 small text-truncate">{{$order->email}}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-4 text-muted small font-weight-bold">Address:</div>
                    <div class="col-8 small">{{$order->address1}} {{$order->address2 ? ', '.$order->address2 : ''}}, {{$order->country}}</div>
                </div>
                @if($order->courier_company)
                <div class="row">
                    <div class="col-4 text-muted small font-weight-bold">Courier:</div>
                    <div class="col-8 small">{{$order->courier_company}} ({{$order->courier_number}})</div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Detail Information: Order Items --}}
<div class="card shadow-sm mb-4 border-0" style="border-radius: 16px; overflow: hidden;">
    <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white border-bottom">
        <h6 class="m-0 font-weight-bold text-primary text-uppercase small" style="letter-spacing: 1px;"><i class="fas fa-list mr-1"></i> Detailed Order Items</h6>
        <div class="badge badge-info px-3 py-2 badge-pill">Total Items: {{$order->quantity}}</div>
    </div>
    <div class="card-body p-0 p-md-4">
        <div class="table-responsive">
            <table class="table table-bordered table-hover responsive-table-to-cards" id="order-detail-table" width="100%" cellspacing="0">
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
                        <td data-title="#">{{$i++}}</td>
                        <td data-title="Product">
                            <div class="d-flex flex-column align-items-end w-100">
                                <div class="font-weight-bold text-dark mb-1" style="line-height: 1.3;">
                                    @if($cart->product)
                                        {{$cart->product->title}}
                                    @elseif($cart->bundle)
                                        <span class="text-primary">[BUNDLE]</span> {{$cart->bundle->name}}
                                    @else
                                        Deleted Item
                                    @endif
                                </div>
                                <div class="text-muted small" style="font-size: 0.7rem; opacity: 0.8;">
                                    SKU: {{ $cart->product->sku ?? ($cart->bundle->sku ?? 'N/A') }}
                                </div>
                            </div>
                        </td>
                        <td data-title="Quantity" class="text-center font-weight-bold">{{$cart->quantity}}</td>
                        <td data-title="Unit Price" class="text-right">Rs. {{number_format($cart->price, 2)}}</td>
                        <td data-title="Total" class="text-right font-weight-bold text-dark">Rs. {{number_format($cart->amount, 2)}}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-light font-weight-bold">
                    @php
                        $gross_subtotal = 0;
                        $item_discounts = 0;
                        foreach($order->cart_info as $ci) {
                            $actual_price = $ci->product->price ?? $ci->price;
                            if($actual_price > $ci->price) {
                                $gross_subtotal += ($actual_price * $ci->quantity);
                                $item_discounts += ($actual_price - $ci->price) * $ci->quantity;
                            } else {
                                $gross_subtotal += ($ci->price * $ci->quantity);
                            }
                        }
                    @endphp
                    <tr>
                        <td colspan="4" class="text-right d-none d-md-table-cell">Sub Total (Gross):</td>
                        <td class="text-right d-md-none" style="background: #f8fafc; border-top: 2px solid #e2e8f0;">Sub Total (Gross):</td>
                        <td class="text-right">Rs. {{number_format($gross_subtotal, 2)}}</td>
                    </tr>
                    @if($item_discounts > 0)
                    <tr>
                        <td colspan="4" class="text-right text-info d-none d-md-table-cell">Item Discounts:</td>
                        <td class="text-right text-info d-md-none">Item Discounts:</td>
                        <td class="text-right text-info">- Rs. {{number_format($item_discounts, 2)}}</td>
                    </tr>
                    @endif
                    <tr>
                        <td colspan="4" class="text-right d-none d-md-table-cell">Shipping Charge:</td>
                        <td class="text-right d-md-none">Shipping Charge:</td>
                        <td class="text-right">Rs. {{number_format($order->shipping->price ?? 0, 2)}}</td>
                    </tr>
                    @if($order->coupon > 0)
                    <tr>
                        <td colspan="4" class="text-right d-none d-md-table-cell">Coupon Discount:</td>
                        <td class="text-right d-md-none">Coupon Discount:</td>
                        <td class="text-right text-danger">- Rs. {{number_format($order->coupon, 2)}}</td>
                    </tr>
                    @endif
                    <tr class="text-primary" style="font-size: 1.1rem; background: #fff9f0;">
                        <td colspan="4" class="text-right font-weight-bold d-none d-md-table-cell">Grand Total:</td>
                        <td class="text-right font-weight-bold d-md-none">Grand Total:</td>
                        <td class="text-right font-weight-bolder h5 mb-0">Rs. {{number_format($order->total_amount, 2)}}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <div class="mt-4 pt-3 border-top d-flex justify-content-end mobile-stack">
            <a href="{{route('order.edit',$order->id)}}" class="btn btn-primary px-4">
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
