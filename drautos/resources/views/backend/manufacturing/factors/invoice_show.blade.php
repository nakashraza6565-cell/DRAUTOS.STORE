@extends('backend.layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
      <h6 class="m-0 font-weight-bold text-primary">Invoice Details: {{$invoice->invoice_number}}</h6>
      <div>
          <a href="{{route('production-factors.invoices')}}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back to Invoices</a>
          <button onclick="window.print()" class="btn btn-primary btn-sm ml-2"><i class="fas fa-print"></i> Print Invoice</button>
      </div>
    </div>
    <div class="card-body" id="printableArea">
        <div class="row mb-4">
            <div class="col-sm-6">
                <h5 class="mb-3">From Supplier:</h5>
                <div>
                    <strong>{{$invoice->supplier->name ?? 'N/A'}}</strong>
                </div>
                @if($invoice->supplier)
                    <div>Phone: {{$invoice->supplier->phone}}</div>
                    @if($invoice->supplier->address)
                        <div>Address: {{$invoice->supplier->address}}</div>
                    @endif
                @endif
            </div>
            <div class="col-sm-6 text-right">
                <h5 class="mb-3">Invoice Details:</h5>
                <div><strong>Invoice #:</strong> {{$invoice->invoice_number}}</div>
                <div><strong>Date:</strong> {{date('M d, Y', strtotime($invoice->purchase_date))}}</div>
                @if($invoice->notes)
                    <div class="mt-2"><strong>Notes:</strong> {{$invoice->notes}}</div>
                @endif
            </div>
        </div>

        <div class="table-responsive-sm">
            <table class="table table-striped">
                <thead class="bg-primary text-white">
                    <tr>
                        <th class="center">#</th>
                        <th>Item (Raw Material)</th>
                        <th class="right">Unit Price</th>
                        <th class="center">Qty</th>
                        <th class="right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                    <tr>
                        <td class="center">{{$loop->iteration}}</td>
                        <td class="left">
                            <strong>{{$item->factor->name ?? 'Unknown Factor'}}</strong>
                            @if($item->factor && $item->factor->unit)
                                <small>({{$item->factor->unit}})</small>
                            @endif
                        </td>
                        <td class="right">Rs. {{number_format($item->unit_price, 4)}}</td>
                        <td class="center">{{$item->quantity}}</td>
                        <td class="right text-dark font-weight-bold">Rs. {{number_format($item->total, 2)}}</td>
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
                            <td class="left">
                                <strong>Grand Total</strong>
                            </td>
                            <td class="right text-success font-weight-bold" style="font-size: 1.2rem;">
                                Rs. {{number_format($invoice->total_amount, 2)}}
                            </td>
                        </tr>
                    </tbody>
                </table>
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
        .card { border: none !important; box-shadow: none !important; }
        .card-header { display: none !important; }
    }
</style>
@endpush
