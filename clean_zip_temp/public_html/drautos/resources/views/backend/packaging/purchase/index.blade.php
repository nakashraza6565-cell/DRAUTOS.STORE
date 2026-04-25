@extends('backend.layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary float-left">Packaging Purchases & Invoices</h6>
        <div class="float-right">
            <a href="{{route('packaging.purchases.create')}}" class="btn btn-primary btn-sm shadow-sm"><i class="fas fa-plus"></i> Record New Purchase</a>
        </div>
    </div>
    <div class="card-body">
        <!-- Search -->
        <form method="GET" action="{{route('packaging.purchases.index')}}" class="mb-4">
            <div class="row">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Search by Invoice #..." value="{{request('search')}}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>Date</th>
                        <th>Invoice #</th>
                        <th>Material</th>
                        <th>Supplier</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Total Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                    <tr>
                        <td>{{$purchase->purchase_date}}</td>
                        <td>{{$purchase->invoice_no}}</td>
                        <td>{{$purchase->packagingItem->name ?? 'N/A'}}</td>
                        <td>{{$purchase->supplier->name ?? 'N/A'}}</td>
                        <td>{{number_format($purchase->quantity, 2)}}</td>
                        <td>Rs. {{number_format($purchase->price, 2)}}</td>
                        <td>Rs. {{number_format($purchase->total_price, 2)}}</td>
                        <td>
                            <a href="{{route('packaging.purchases.invoice', $purchase->id)}}" class="btn btn-info btn-sm float-left mr-1" title="Download Invoice">
                                <i class="fas fa-file-invoice"></i> PDF
                            </a>
                            <a href="{{route('packaging.purchases.edit', $purchase->id)}}" class="btn btn-primary btn-sm float-left mr-1" title="Edit"><i class="fas fa-edit"></i></a>
                            <form method="POST" action="{{route('packaging.purchases.destroy', [$purchase->id])}}" class="float-left">
                                @csrf
                                @method('delete')
                                <button class="btn btn-danger btn-sm dltBtn" title="Delete"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No purchases recorded.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="float-right mt-3">
                {{$purchases->links()}}
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .dltBtn { border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('.dltBtn').click(function(e) {
            var form = $(this).closest('form');
            var dataID = $(this).data('id');
            e.preventDefault();
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this data!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    form.submit();
                } else {
                    swal("Your data is safe!");
                }
            });
        })
    })
</script>
@endpush
