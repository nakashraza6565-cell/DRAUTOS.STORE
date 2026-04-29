@extends('user.layouts.master')
@section('title','New Return/Claim || ' . (Settings::first()->title ?? 'Auto Store'))
@section('main-content')
<div class="container-fluid px-2 py-3">
    <!-- Modern Header -->
    <div class="d-flex align-items-center justify-content-between mb-3 px-1">
        <div>
            <h5 class="font-weight-bold text-gray-800 mb-0">New Request</h5>
            <p class="text-muted small mb-0">Order #{{$order->order_number}}</p>
        </div>
        <a href="{{route('user.order.index')}}" class="btn btn-light btn-sm rounded-circle shadow-sm">
            <i class="fas fa-times"></i>
        </a>
    </div>

    <form id="return-form">
        @csrf
        <input type="hidden" name="order_id" value="{{$order->id}}">
        
        <!-- Selection Card -->
        <div class="card border-0 shadow-sm rounded-20 mb-3">
            <div class="card-body p-3">
                <div class="form-group mb-3">
                    <label class="font-weight-bold small text-uppercase text-muted">Request Type</label>
                    <div class="btn-group btn-group-toggle d-flex w-100" data-toggle="buttons">
                        <label class="btn btn-outline-primary flex-fill active py-2">
                            <input type="radio" name="type" value="return" checked> Return
                        </label>
                        <label class="btn btn-outline-primary flex-fill py-2">
                            <input type="radio" name="type" value="claim"> Claim
                        </label>
                    </div>
                </div>
                <div class="form-group mb-0">
                    <label class="font-weight-bold small text-uppercase text-muted">Reason for Request</label>
                    <input type="text" name="reason" class="form-control rounded-12 bg-light border-0" placeholder="e.g. Damaged, Wrong size..." required>
                </div>
            </div>
        </div>

        <h6 class="font-weight-bold text-gray-800 mb-3 px-1">Select Items</h6>

        <!-- Items Container -->
        <div id="items-container">
            @foreach($order->cart_info as $index => $item)
                @php 
                    $isBundle = (bool)$item->bundle_id;
                    $title = $isBundle ? ($item->bundle->name ?? 'Bundle') : ($item->product->title ?? 'Product');
                    $sku = $isBundle ? ($item->bundle->sku ?? 'N/A') : ($item->product->sku ?? 'N/A');
                    $idAttr = $isBundle ? 'data-bid="'.$item->bundle_id.'"' : 'data-pid="'.$item->product_id.'"';
                    $itemId = $isBundle ? 'bundle-'.$item->bundle_id : 'product-'.$item->product_id;
                @endphp
                
                <div class="card border-0 shadow-sm rounded-20 mb-3 item-card" id="card-{{$itemId}}">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-start">
                            <div class="custom-control custom-checkbox mr-3 mt-1">
                                <input type="checkbox" class="custom-control-input item-checkbox" id="check-{{$itemId}}" 
                                       {{$isBundle ? 'data-bid="'.$item->bundle_id.'"' : 'data-pid="'.$item->product_id.'"'}}>
                                <label class="custom-control-label" for="check-{{$itemId}}"></label>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="font-weight-bold text-gray-800 text-truncate" style="max-width: 180px;">{{$title}}</div>
                                        <div class="text-muted extra-small">SKU: {{$sku}} | Price: Rs. {{number_format($item->price, 0)}}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-primary font-weight-bold small">Max: {{$item->quantity}}</div>
                                    </div>
                                </div>

                                <!-- Qty & Notes (Hidden by default) -->
                                <div class="qty-notes-wrapper mt-3 d-none">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <span class="small font-weight-bold text-muted">Return Quantity</span>
                                        <div class="input-group input-group-sm" style="width: 110px;">
                                            <div class="input-group-prepend">
                                                <button class="btn btn-light border qty-btn minus" type="button" data-target="qty-{{$itemId}}"><i class="fas fa-minus small"></i></button>
                                            </div>
                                            <input type="number" id="qty-{{$itemId}}" class="form-control text-center border-0 bg-light return-qty" 
                                                   value="1" min="1" max="{{$item->quantity}}" readonly 
                                                   {{$isBundle ? 'data-bid="'.$item->bundle_id.'"' : 'data-pid="'.$item->product_id.'"'}}>
                                            <div class="input-group-append">
                                                <button class="btn btn-light border qty-btn plus" type="button" data-target="qty-{{$itemId}}"><i class="fas fa-plus small"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="text" class="form-control form-control-sm rounded-8 bg-light border-0 item-notes" 
                                           placeholder="Specific issue with this item..." 
                                           {{$isBundle ? 'data-bid="'.$item->bundle_id.'"' : 'data-pid="'.$item->product_id.'"'}}>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Sticky Footer Space -->
        <div style="height: 80px;"></div>

        <!-- Sticky Action Bar -->
        <div class="fixed-bottom bg-white border-top p-3 shadow-lg d-flex align-items-center justify-content-between" style="z-index: 1030; border-radius: 20px 20px 0 0;">
            <div class="small">
                <span id="selected-count" class="font-weight-bold text-primary">0</span> Items Selected
            </div>
            <button type="button" class="btn btn-primary rounded-pill px-4 font-weight-bold shadow" id="submit-request">
                Confirm Return <i class="fas fa-chevron-right ml-1"></i>
            </button>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    .rounded-20 { border-radius: 20px !important; }
    .rounded-12 { border-radius: 12px !important; }
    .rounded-8 { border-radius: 8px !important; }
    .extra-small { font-size: 0.65rem; }
    .item-card { transition: all 0.2s ease; border: 2px solid transparent; }
    .item-card.selected { border-color: var(--primary); background: #f8fbff; }
    .qty-btn { width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; }
    .custom-control-label::before, .custom-control-label::after { width: 1.25rem; height: 1.25rem; }
    .btn-group .btn { border-radius: 12px !important; margin: 0 4px; }
    .mobile-nav { display: none !important; } /* Hide bottom nav on this page to avoid overlap */
</style>
@endpush

@push('scripts')
<script>
    // Handle Item Selection
    $('.item-checkbox').on('change', function() {
        let card = $(this).closest('.item-card');
        let wrapper = card.find('.qty-notes-wrapper');
        let isChecked = $(this).prop('checked');
        
        if(isChecked) {
            card.addClass('selected shadow-lg');
            wrapper.removeClass('d-none').addClass('animated fadeIn');
        } else {
            card.removeClass('selected shadow-lg');
            wrapper.addClass('d-none');
        }
        updateCount();
    });

    function updateCount() {
        let count = $('.item-checkbox:checked').length;
        $('#selected-count').text(count);
    }

    // Qty +/- Buttons
    $('.qty-btn').on('click', function() {
        let targetId = $(this).data('target');
        let input = $('#' + targetId);
        let currentVal = parseInt(input.val());
        let max = parseInt(input.attr('max'));
        
        if($(this).hasClass('plus')) {
            if(currentVal < max) input.val(currentVal + 1);
        } else {
            if(currentVal > 1) input.val(currentVal - 1);
        }
    });

    // Form Submission
    $('#submit-request').on('click', function() {
        let selectedItems = [];
        $('.item-checkbox:checked').each(function() {
            let pid = $(this).data('pid');
            let bid = $(this).data('bid');
            
            let query = pid ? `[data-pid="${pid}"]` : `[data-bid="${bid}"]`;
            
            selectedItems.push({
                product_id: pid || null,
                bundle_id: bid || null,
                quantity: $('.return-qty' + query).val(),
                notes: $('.item-notes' + query).val()
            });
        });

        if(selectedItems.length == 0) {
            return Swal.fire({
                title: 'No Items Selected',
                text: 'Please select at least one item to return.',
                icon: 'warning',
                confirmButtonColor: '#4e73df'
            });
        }

        if(!$('input[name="reason"]').val()) {
             return Swal.fire('Error', 'Please provide a reason for the return', 'warning');
        }

        let btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Processing...');

        $.ajax({
            url: "{{route('user.returns.store')}}",
            type: "POST",
            data: {
                _token: "{{csrf_token()}}",
                order_id: $("input[name='order_id']").val(),
                type: $("input[name='type']:checked").val(),
                reason: $("input[name='reason']").val(),
                items: selectedItems
            },
            success: function(res) {
                if(res.status == 'success') {
                    Swal.fire({
                        title: 'Submitted!',
                        text: res.message,
                        icon: 'success',
                        confirmButtonColor: '#1cc88a'
                    }).then(() => {
                        window.location.href = "{{route('user.returns.index')}}";
                    });
                } else {
                    Swal.fire('Error', res.message, 'error');
                    btn.prop('disabled', false).text('Confirm Return');
                }
            },
            error: function(err) {
                Swal.fire('Error', 'Something went wrong!', 'error');
                btn.prop('disabled', false).text('Confirm Return');
            }
        });
    });
</script>
@endpush
