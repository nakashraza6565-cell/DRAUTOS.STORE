@extends('backend.layouts.master')

@section('main-content')
<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
      <h6 class="m-0 font-weight-bold text-primary">Raw Material Purchase Invoices</h6>
      <a href="{{route('manufacturing.production-factors.purchase.create')}}" class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="bottom" title="Log New Purchase"><i class="fas fa-plus"></i> New Purchase</a>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        @if(count($invoices)>0)
        <table class="table table-bordered table-hover" id="invoice-dataTable" width="100%" cellspacing="0">
          <thead class="bg-primary text-white">
            <tr>
              <th>S.N.</th>
              <th>Invoice Number</th>
              <th>Supplier</th>
              <th>Purchase Date</th>
              <th>Total Amount</th>
              <th>Notes</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($invoices as $invoice)
              <tr>
                  <td>{{$loop->iteration}}</td>
                  <td class="font-weight-bold">{{$invoice->invoice_number}}</td>
                  <td>{{$invoice->supplier->name ?? 'N/A'}}</td>
                  <td>{{date('M d, Y', strtotime($invoice->purchase_date))}}</td>
                  <td class="font-weight-bold text-success">Rs. {{number_format($invoice->total_amount, 2)}}</td>
                  <td>{{ Str::limit($invoice->notes, 30) }}</td>
                  <td>
                      <a href="{{route('production-factors.invoice.show', $invoice->id)}}" class="btn btn-info btn-sm float-left mr-1" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="View Details" data-placement="bottom"><i class="fas fa-eye"></i></a>
                  </td>
              </tr>
            @endforeach
          </tbody>
        </table>
        <span style="float:right">{{$invoices->links()}}</span>
        @else
          <h6 class="text-center">No purchase invoices found! Please log a purchase.</h6>
        @endif
      </div>
    </div>
</div>
@endsection

@push('styles')
  <link href="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
  <style>
      div.dataTables_wrapper div.dataTables_paginate{
          display: none;
      }
  </style>
@endpush

@push('scripts')
  <!-- Page level plugins -->
  <script src="{{asset('backend/vendor/datatables/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>

  <!-- Page level custom scripts -->
  <script>
      $('#invoice-dataTable').DataTable({
            "columnDefs":[
                {
                    "orderable":false,
                    "targets":[6]
                }
            ]
        });
  </script>
@endpush
