@extends('backend.layouts.master')
@section('title','Edit Order ' . $order->order_number)

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Edit Order: {{$order->order_number}}</h6>
    </div>
    <div class="card-body">
        <form action="{{route('order.update',$order->id)}}" method="POST" id="editOrderForm">
            @csrf
            @method('PATCH')
            
            <!-- Customer Information -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <h5 class="font-weight-bold mb-3 text-primary"><i class="fas fa-user-edit mr-2"></i>Customer & Shipping Information</h5>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="first_name" value="{{$order->first_name}}" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="last_name" value="{{$order->last_name}}" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" value="{{$order->phone}}" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" value="{{$order->email}}" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Full Address</label>
                        <input type="text" name="address1" value="{{$order->address1}}" class="form-control" placeholder="Address line 1">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Courier Company</label>
                        <input type="text" name="courier_company" value="{{$order->courier_company}}" class="form-control" placeholder="e.g. Leopard, TCS, Daewoo">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Courier/Tracking Number</label>
                        <input type="text" name="courier_number" value="{{$order->courier_number}}" class="form-control" placeholder="Tracking ID">
                    </div>
                </div>
            </div>

            <hr>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="new" {{$order->status=='new'?'selected':''}}>New</option>
                            <option value="process" {{$order->status=='process'?'selected':''}}>Process</option>
                            <option value="delivered" {{$order->status=='delivered'?'selected':''}}>Delivered</option>
                            <option value="cancel" {{$order->status=='cancel'?'selected':''}}>Cancel</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                     <div class="form-group">
                        <label>Assigned Staff</label>
                        <select name="staff_id" class="form-control">
                            <option value="">--Select Staff--</option>
                            @foreach(\App\User::whereIn('role', ['admin', 'manager', 'staff'])->get() as $staff)
                                <option value="{{$staff->id}}" {{$order->staff_id==$staff->id?'selected':''}}>{{$staff->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                     <div class="form-group">
                        <label>Staff Commission</label>
                        <input type="number" name="staff_commission" value="{{$order->staff_commission}}" class="form-control">
                    </div>
                </div>
            </div>
            
            <hr>
            
            <!-- Payment Information -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <h5 class="font-weight-bold mb-3 text-info"><i class="fas fa-money-check-alt mr-2"></i>Payment Information</h5>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Amount Paid at Counter (Initial)</label>
                        <input type="number" step="0.01" name="amount_paid" value="{{ $paid_at_pos }}" class="form-control" id="form_amount_paid">
                        <small class="text-muted">Total order amount minus initial balance</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Current Outstanding Balance</label>
                        <input type="number" step="0.01" name="pending_amount" value="{{ $reminder ? ($reminder->amount - $reminder->paid_amount) : 0 }}" class="form-control" id="form_pending_amount">
                        <small class="text-muted">Actual amount still owed by customer</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Balance Due Date</label>
                        <input type="date" name="due_date" value="{{ optional(optional($reminder)->due_date)->format('Y-m-d') }}" class="form-control">
                    </div>
                </div>
            </div>

            <hr>
            
            <!-- Items Section -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="font-weight-bold">Order Items</h5>
                <div class="position-relative" style="width: 300px;">
                    <input type="text" id="product_search" class="form-control" placeholder="Search & Add Product...">
                    <div id="search_results" class="list-group position-absolute w-100" style="z-index: 999; display:none;"></div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="bg-light">
                        <tr>
                            <th>Product</th>
                            <th width="150">Unit Price (Disc.)</th>
                            <th width="120">Quantity</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="cart_body">
                        <!-- JS will populate this -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-right">Total:</th>
                            <th id="grand_total">Rs. 0.00</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <button type="submit" class="btn btn-primary btn-lg mt-3">Update Complete Order</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
@php
    $cartData = $order->cart->map(function($item) {
        return [
            'id' => $item->product_id,
            'title' => $item->product ? $item->product->title : 'Unknown Product',
            'price' => (float)($item->price ?? 0),
            'qty' => (int)($item->quantity ?? 1)
        ];
    });
@endphp
<script>
    // Initialize cart with existing items safely
    let cart = @json($cartData);

    $(document).ready(function() {
        renderCart();

        // Product Search
        $('#product_search').on('input', function() {
            let query = $(this).val();
            if(query.length < 2) {
                $('#search_results').hide();
                return;
            }
            
            $.ajax({
                url: "{{route('pos.search-products')}}",
                data: {query: query},
                success: function(res) {
                    let html = '';
                    res.forEach(p => {
                        html += `<a href="#" class="list-group-item list-group-item-action add-item" 
                                data-id="${p.id}" data-title="${p.title}" data-price="${p.price}">
                                ${p.title} (Rs. ${p.price})
                                </a>`;
                    });
                    $('#search_results').html(html).show();
                }
            });
        });

        // Add Item
        $(document).on('click', '.add-item', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            let title = $(this).data('title');
            let price = parseFloat($(this).data('price'));

            let existing = cart.find(i => i.id == id);
            if(existing) {
                existing.qty++;
            } else {
                cart.push({id: id, title: title, price: price, qty: 1});
            }
            
            renderCart();
            $('#product_search').val('');
            $('#search_results').hide();
        });

        $(document).on('click', function(e) {
            if(!$(e.target).closest('#product_search, #search_results').length) {
                $('#search_results').hide();
            }
        });
    });

    function renderCart() {
        let html = '';
        let total = 0;
        
        cart.forEach((item, index) => {
            let subtotal = item.price * item.qty;
            total += subtotal;
            
            html += `
                <tr>
                    <td>
                        ${item.title}
                        <input type="hidden" name="items[${index}][id]" value="${item.id}">
                    </td>
                    <td>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text px-2">Rs.</span>
                            </div>
                            <input type="number" name="items[${index}][price]" class="form-control" 
                                   value="${item.price}" step="0.01" min="0" style="min-width: 80px;"
                                   onchange="updateTxPrice(${index}, this.value)">
                        </div>
                    </td>
                    <td>
                        <input type="number" name="items[${index}][qty]" class="form-control form-control-sm" 
                               value="${item.qty}" min="1" style="width: 80px;" 
                               onchange="updateTxQty(${index}, this.value)">
                    </td>
                    <td class="align-middle">Rs. ${subtotal.toFixed(2)}</td>
                    <td class="align-middle">
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(${index})"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
        });
        
        $('#cart_body').html(html);
        $('#grand_total').text('Rs. ' + total.toFixed(2));

        // Auto-update pending amount calculation
        updatePendingCalculation(total);
    }

    window.updateTxPrice = function(index, val) {
        let price = parseFloat(val) || 0;
        cart[index].price = price;
        renderCart();
    }

    function updatePendingCalculation(total) {
        let amountPaid = parseFloat($('#form_amount_paid').val()) || 0;
        let pending = total - amountPaid;
        $('#form_pending_amount').val(pending > 0 ? pending.toFixed(2) : 0);
    }

    // Listener for manual paid amount change
    $('#form_amount_paid').on('input', function() {
        let total = 0;
        cart.forEach(item => total += (item.price * item.qty));
        updatePendingCalculation(total);
    });

    window.updateTxQty = function(index, val) {
        let qty = parseInt(val) || 1;
        cart[index].qty = qty;
        renderCart();
    }

    window.removeItem = function(index) {
        cart.splice(index, 1);
        renderCart();
    }
</script>
@endpush
