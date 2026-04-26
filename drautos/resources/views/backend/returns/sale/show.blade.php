@extends('backend.layouts.master')
@section('title','Return Invoice ' . $return->return_number)

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Return Invoice #{{$return->return_number}}</h6>
        <div>
            <button onclick="window.print()" class="btn btn-secondary btn-sm"><i class="fas fa-print"></i> Standard Print</button>
            <a href="{{route('returns.sale.print-thermal', $return->id)}}" target="thermal_frame" class="btn btn-primary btn-sm ml-2">
                <i class="fas fa-receipt"></i> Print Thermal
            </a>
            <iframe name="thermal_frame" id="thermal_frame" style="display:none;"></iframe>
        </div>
    </div>
    <div class="card-body" id="printableArea">
        <div class="row mb-4">
            <div class="col-sm-6">
                <h5 class="mb-3">From:</h5>
                <h3 class="text-dark mb-1">Danyal Auto Store</h3>
                <div>Islamabad</div>
                <div>Phone: +92 300 1234567</div>
                <div>Email: info@danyalautos.com</div>
            </div>
            <div class="col-sm-6 text-right">
                <h5 class="mb-3">To:</h5>
                <h3 class="text-dark mb-1">{{$return->customer->name ?? 'Walk-in Customer'}}</h3>
                <div>{{$return->customer->address ?? ''}}</div>
                <div>{{$return->customer->phone ?? ''}}</div>
                <div>{{$return->customer->email ?? ''}}</div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-sm-6">
                <div><strong>Return Number:</strong> {{$return->return_number}}</div>
                <div><strong>Original Order:</strong> {{$return->order->order_number ?? 'N/A'}}</div>
                <div><strong>Return Date:</strong> {{$return->return_date->format('d M, Y')}}</div>
            </div>
            <div class="col-sm-6 text-right">
                <div><strong>Status:</strong> <span class="badge badge-{{$return->status=='approved'?'success':($return->status=='pending'?'warning':'danger')}}">{{ucfirst($return->status)}}</span></div>
                @if($return->status == 'pending')
                    <form action="{{route('returns.sale.approve', $return->id)}}" method="POST" class="d-inline ml-2">
                        @csrf
                        <button class="btn btn-success btn-sm" onclick="return confirm('Approve return?')">Approve</button>
                    </form>
                @endif
                <div><strong>Refund Method:</strong> {{ucfirst(str_replace('_',' ',$return->refund_method))}}</div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item</th>
                        <th>Condition</th>
                        <th class="text-right">Unit Cost</th>
                        <th class="text-center">Qty</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($return->items as $index => $item)
                    <tr>
                        <td>{{$index + 1}}</td>
                        <td>{{$item->product->title ?? 'Item'}}</td>
                        <td>{{ucfirst($item->condition)}}</td>
                        <td class="text-right">Rs. {{number_format($item->unit_price, 2)}}</td>
                        <td class="text-center">{{$item->quantity}}</td>
                        <td class="text-right">Rs. {{number_format($item->total_price, 2)}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="row">
            <div class="col-lg-4 col-sm-5 ml-auto">
                <table class="table table-clear">
                    <tbody>
                        <tr>
                            <td class="left"><strong>Total Refund</strong></td>
                            <td class="text-right"><strong>Rs. {{number_format($return->total_return_amount, 2)}}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        @if($return->reason)
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-light">
                    <strong>Reason for Return:</strong> {{$return->reason}}
                </div>
            </div>
        </div>
        @endif

        <div class="row mt-5">
            <div class="col-12 text-center text-muted">
                <p>Thank you for your business.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #printableArea, #printableArea * {
            visibility: visible;
        }
        #printableArea {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .card-header, .btn {
            display: none !important;
        }
    }
</style>
@endpush
