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
    @if($order)
    <table class="table table-striped table-hover">
      <thead>
        <tr>
            <th>S.N.</th>
            <th>Order No.</th>
            <th>Name</th>
            <th>Email</th>
            <th>Quantity</th>
            <th>Charge</th>
            <th>Total Amount</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <tr>
            <td>{{$order->id}}</td>
            <td>{{$order->order_number}}</td>
            <td>{{$order->first_name}} {{$order->last_name}}</td>
            <td>{{$order->email}}</td>
            <td>{{$order->quantity}}</td>
            <td>Rs. {{ $order->shipping->price ?? '0.00' }}</td>
            <td>Rs. {{number_format($order->total_amount,2)}}</td>
            <td>
                @if($order->status=='new')
                  <span class="badge badge-primary">{{$order->status}}</span>
                @elseif($order->status=='process')
                  <span class="badge badge-warning">{{$order->status}}</span>
                @elseif($order->status=='delivered')
                  <span class="badge badge-success">{{$order->status}}</span>
                @else
                  <span class="badge badge-danger">{{$order->status}}</span>
                @endif
            </td>
            <td>
                <a href="{{route('order.edit',$order->id)}}" class="btn btn-primary btn-sm float-left mr-1" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                <form method="POST" action="{{route('order.destroy',[$order->id])}}">
                  @csrf
                  @method('delete')
                      <button class="btn btn-danger btn-sm dltBtn" data-id={{$order->id}} style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fas fa-trash-alt"></i></button>
                </form>
            </td>

        </tr>
      </tbody>
    </table>

    <section class="confirmation_part section_padding">
      <div class="order_boxes">
        <div class="row">
          <div class="col-lg-6 col-lx-4">
            <div class="order-info">
              <h4 class="text-center pb-4">ORDER INFORMATION</h4>
              <table class="table">
                    <tr class="">
                        <td>Order Number</td>
                        <td> : {{$order->order_number}}</td>
                    </tr>
                    <tr>
                        <td>Order Date</td>
                        <td> : {{$order->created_at->format('D d M, Y')}} at {{$order->created_at->format('g : i a')}} </td>
                    </tr>
                    <tr>
                        <td>Quantity</td>
                        <td> : {{$order->quantity}}</td>
                    </tr>
                    <tr>
                        <td>Order Status</td>
                        <td> : {{$order->status}}</td>
                    </tr>
                    <tr>
                        <td>Order Type</td>
                        <td> : {{strtoupper($order->order_type ?? 'online')}}</td>
                    </tr>
                    <tr>
                        <td>Shipping Charge</td>
                        <td> : Rs. {{$order->shipping->price ?? 0}}</td>
                    </tr>
                    <tr>
                      <td>Coupon</td>
                      <td> : Rs. {{number_format($order->coupon,2)}}</td>
                    </tr>
                    <tr>
                        <td>Total Amount</td>
                        <td> : Rs. {{number_format($order->total_amount,2)}}</td>
                    </tr>
                    <tr>
                        <td>Payment Method</td>
                        <td> : @if($order->payment_method=='cod') Cash on Delivery @else Paypal @endif</td>
                    </tr>
                    <tr>
                        <td>Payment Status</td>
                        <td> : {{$order->payment_status}}</td>
                    </tr>
              </table>
            </div>
          </div>

          <div class="col-lg-6 col-lx-4">
            <div class="shipping-info">
              <h4 class="text-center pb-4">SHIPPING INFORMATION</h4>
              <table class="table">
                    <tr class="">
                        <td>Full Name</td>
                        <td> : {{$order->first_name}} {{$order->last_name}}</td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td> : {{$order->email}}</td>
                    </tr>
                    <tr>
                        <td>Phone No.</td>
                        <td> : {{$order->phone}}</td>
                    </tr>
                    <tr>
                        <td>Address</td>
                        <td> : {{$order->address1}}, {{$order->address2}}</td>
                    </tr>
                    <tr>
                        <td>Country</td>
                        <td> : {{$order->country}}</td>
                    </tr>
                    <tr>
                        <td>Post Code</td>
                        <td> : {{$order->post_code}}</td>
                    </tr>
                    @if($order->courier_company)
                    <tr>
                        <td>Courier Company</td>
                        <td> : {{$order->courier_company}}</td>
                    </tr>
                    @endif
                    @if($order->courier_number)
                    <tr>
                        <td>Courier Number</td>
                        <td> : {{$order->courier_number}}</td>
                    </tr>
                    @endif
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
    @endif

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
