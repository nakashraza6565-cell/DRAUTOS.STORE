@extends('user.layouts.master')
@section('title','New Return/Claim || ' . (Settings::first()->title ?? 'Auto Store'))
@section('main-content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Initiate Return or Claim</h1>
        <a href="{{route('user.order.index')}}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back to Orders</a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-light">
                    <h6 class="m-0 font-weight-bold text-dark">Order #{{$order->order_number}} Details</h6>
                </div>
                <div class="card-body">
                    <form id="return-form">
                        @csrf
                        <input type="hidden" name="order_id" value="{{$order->id}}">
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="font-weight-bold">Request Type</label>
                                <select name="type" class="form-control" required>
                                    <option value="return">Product Return (Exchange or Credit)</option>
                                    <option value="claim">Warranty Claim (Repair or Replacement)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="font-weight-bold">Reason</label>
                                <input type="text" name="reason" class="form-control" placeholder="e.g. Damaged at delivery, Size issue..." required>
                            </div>
                        </div>

                        <h6 class="font-weight-bold border-bottom pb-2 mb-3">Select Items to Return/Claim</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th width="30">Select</th>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th width="120">Qty in Order</th>
                                        <th width="120">Return Qty</th>
                                        <th>Issues/Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->cart_info as $item)
                                        @if($item->product)
                                            <tr>
                                                <td class="text-center">
                                                    <input type="checkbox" class="item-checkbox" data-pid="{{$item->product_id}}">
                                                </td>
                                                <td>
                                                    <strong>{{$item->product->title}}</strong><br>
                                                    <span class="small text-muted">SKU: {{$item->product->sku}}</span>
                                                </td>
                                                <td>Rs. {{number_format($item->price, 2)}}</td>
                                                <td>{{$item->quantity}}</td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm return-qty" 
                                                           data-pid="{{$item->product_id}}" 
                                                           min="1" max="{{$item->quantity}}" 
                                                           value="1" disabled>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm item-notes" 
                                                           data-pid="{{$item->product_id}}" 
                                                           placeholder="Notes..." disabled>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 text-right">
                            <button type="button" class="btn btn-success btn-lg px-5 shadow" id="submit-request">
                                <i class="fas fa-paper-plane mr-1"></i> Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow mb-4 border-left-info">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Return Policy</h6>
                </div>
                <div class="card-body small">
                    <ul class="pl-3">
                        <li class="mb-2">Returns usually take 3-5 business days to process.</li>
                        <li class="mb-2">Claims might require inspection of the physical item.</li>
                        <li class="mb-2">After approval, the amount will be credited to your Account Ledger.</li>
                        <li>Items must be in original packaging for standard returns.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $('.item-checkbox').on('change', function() {
        let pid = $(this).data('pid');
        let checked = $(this).prop('checked');
        $(`.return-qty[data-pid="${pid}"], .item-notes[data-pid="${pid}"]`).prop('disabled', !checked);
    });

    $('#submit-request').on('click', function() {
        let selectedItems = [];
        $('.item-checkbox:checked').each(function() {
            let pid = $(this).data('pid');
            selectedItems.push({
                product_id: pid,
                quantity: $(`.return-qty[data-pid="${pid}"]`).val(),
                notes: $(`.item-notes[data-pid="${pid}"]`).val()
            });
        });

        if(selectedItems.length == 0) {
            return Swal.fire('Error', 'Please select at least one item', 'warning');
        }

        let btn = $(this);
        btn.prop('disabled', true).text('Submitting...');

        $.ajax({
            url: "{{route('user.returns.store')}}",
            type: "POST",
            data: {
                _token: "{{csrf_token()}}",
                order_id: $("input[name='order_id']").val(),
                type: $("select[name='type']").val(),
                reason: $("input[name='reason']").val(),
                items: selectedItems
            },
            success: function(res) {
                if(res.status == 'success') {
                    Swal.fire('Success', res.message, 'success').then(() => {
                        window.location.href = "{{route('user.returns.index')}}";
                    });
                } else {
                    Swal.fire('Error', res.message, 'error');
                    btn.prop('disabled', false).text('Submit Request');
                }
            },
            error: function(err) {
                Swal.fire('Error', 'Something went wrong!', 'error');
                btn.prop('disabled', false).text('Submit Request');
            }
        });
    });
</script>
@endpush
