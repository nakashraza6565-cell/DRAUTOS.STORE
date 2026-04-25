@extends('backend.layouts.master')

@section('main-content')
 <div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
      <h6 class="m-0 font-weight-bold text-primary">Supplier List</h6>
      <div class="d-flex align-items-center">
          <form method="GET" action="{{route('suppliers.index')}}" class="mr-3">
              <div class="input-group">
                  <input type="text" name="search" class="form-control bg-light border-0 small" placeholder="Search suppliers..." value="{{request('search')}}" style="width: 250px; border-radius: 8px 0 0 8px;">
                  <div class="input-group-append">
                      <button class="btn btn-primary" type="submit" style="border-radius: 0 8px 8px 0;">
                          <i class="fas fa-search fa-sm"></i>
                      </button>
                  </div>
              </div>
          </form>
          <a href="{{route('suppliers.create')}}" class="btn btn-sm shadow-sm" style="background: #f6c23e; color: #fff; font-weight: bold; padding: 8px 20px; border-radius: 8px;"><i class="fas fa-plus"></i> Add Supplier</a>
      </div>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        @if(count($suppliers)>0)
        <div class="row">
            @foreach($suppliers as $supplier)
            <div class="col-xl-3 col-md-4 mb-4">
                <div class="card border-left-primary shadow h-100 py-2 supplier-card">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="d-inline-block position-relative avatar-circle">
                                <img src="{{asset('backend/img/avatar.png')}}" 
                                     class="img-fluid rounded-circle shadow-sm border" 
                                     style="width: 80px; height: 80px; object-fit: cover; background: #f8f9fc;" 
                                     alt="Supplier Avatar">
                                <span class="position-absolute" style="bottom: 5px; right: 5px;">
                                    <i class="fas fa-check-circle text-success bg-white rounded-circle"></i>
                                </span>
                            </div>
                        </div>
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2 text-center">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    {{$supplier->company_name ?? 'Supplier'}}
                                </div>
                                <div class="h5 mb-2 font-weight-bold text-gray-800">
                                    <a href="{{route('suppliers.show', $supplier->id)}}" class="text-dark" style="text-decoration:none">
                                        {{$supplier->name}}
                                    </a>
                                </div>
                                <div class="mb-3 text-muted small">
                                    <div class="mb-1"><i class="fas fa-phone fa-sm mr-1"></i> {{$supplier->phone}}</div>
                                    <div class="text-truncate"><i class="fas fa-envelope fa-sm mr-1"></i> {{$supplier->email ?? 'N/A'}}</div>
                                </div>
                                
                                <div class="d-flex justify-content-center align-items-center mb-3">
                                    @php
                                        $avgRating = (($supplier->loyalty_rating ?? 0) + ($supplier->goodwill_rating ?? 0) + ($supplier->payment_rating ?? 0) + ($supplier->behaviour_rating ?? 0)) / 4;
                                        if($avgRating == 0) $avgRating = 3.0; // Default fallback if no ratings yet
                                    @endphp
                                    <div class="mr-2 px-2 py-1 rounded bg-light border shadow-sm">
                                        <i class="fas fa-star text-warning mr-1"></i>
                                        <span class="font-weight-bold small">{{number_format($avgRating, 1)}}</span>
                                    </div>
                                    <span class="badge badge-pill badge-{{($supplier->status=='active') ? 'success' : 'warning'}}" style="font-size: 10px;">{{strtoupper($supplier->status)}}</span>
                                </div>

                                <div class="mt-3 text-center">
                                    <a href="javascript:void(0)" class="btn btn-success btn-circle btn-sm wa-btn" 
                                       data-id="{{$supplier->id}}" 
                                       data-name="{{$supplier->name}}" 
                                       data-phone="{{$supplier->phone}}"
                                       title="WhatsApp Order">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="btn btn-warning btn-circle btn-sm btn-rating" 
                                       data-id="{{$supplier->id}}" 
                                       data-loyalty="{{$supplier->loyalty_rating ?? 0}}" 
                                       data-goodwill="{{$supplier->goodwill_rating ?? 0}}" 
                                       data-payment="{{$supplier->payment_rating ?? 0}}" 
                                       data-behaviour="{{$supplier->behaviour_rating ?? 0}}" 
                                       title="Rate Supplier">
                                        <i class="fas fa-star"></i>
                                    </a>
                                    <a href="{{route('admin.supplier-ledger.show', $supplier->id)}}" class="btn btn-info btn-circle btn-sm" title="View Ledger">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </a>
                                    <a href="{{route('suppliers.edit',$supplier->id)}}" class="btn btn-primary btn-circle btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{route('suppliers.destroy',[$supplier->id])}}" class="d-inline">
                                      @csrf 
                                      @method('delete')
                                      <button class="btn btn-danger btn-circle btn-sm dltBtn" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
          <h6 class="text-center">No Suppliers found!!!</h6>
        @endif
      </div>
    </div>
</div>

<!-- WhatsApp Modal -->
<div class="modal fade" id="whatsappModal" tabindex="-1" role="dialog" aria-labelledby="whatsappModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="whatsappModalLabel">
                    <i class="fab fa-whatsapp mr-2"></i>Send Stock Inquiry / Order
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="wa-form">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="supplier_id" id="wa_supplier_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Supplier Name</label>
                                <input type="text" id="wa_supplier_name" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">WhatsApp Number <span class="text-danger">*</span></label>
                                <input type="text" name="phone" id="wa_phone" class="form-control" placeholder="e.g. 923001234567" required>
                                <small class="text-muted">Include country code (e.g. 92 for Pakistan)</small>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body">
                            <label class="font-weight-bold"><i class="fas fa-search mr-2"></i>Add Products to Inquiry</label>
                            <select id="product_search" class="form-control"></select>
                            
                            <div class="table-responsive mt-3">
                                <table class="table table-sm bg-white rounded shadow-sm" id="wa_products_table">
                                    <thead class="bg-gray-200">
                                        <tr>
                                            <th>Product</th>
                                            <th width="120">Quantity</th>
                                            <th width="50"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="wa_product_list">
                                        <!-- Products will be added here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Additional Notes / Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Any special instructions or reference details..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success rounded-pill px-5 shadow-sm" id="send-wa-btn">
                        <i class="fas fa-paper-plane mr-2"></i>Send via WhatsApp
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Rating Modal -->
<div class="modal fade" id="ratingModal" tabindex="-1" role="dialog" aria-labelledby="ratingModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ratingModalLabel">Rate Supplier</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="ratingForm">
            <input type="hidden" id="rating_supplier_id" name="supplier_id">
            
            <div class="form-group text-center">
                <label>Loyalty Rating</label>
                <div class="rating-stars" data-type="loyalty">
                    @for($i=1; $i<=5; $i++)
                        <i class="far fa-star fa-2x star-icon" data-value="{{$i}}" data-type="loyalty" style="cursor: pointer; color: #f6c23e;"></i>
                    @endfor
                </div>
                <input type="hidden" id="rating_loyalty" name="loyalty_rating" value="0">
            </div>

            <div class="form-group text-center">
                <label>Goodwill Rating</label>
                <div class="rating-stars" data-type="goodwill">
                    @for($i=1; $i<=5; $i++)
                        <i class="far fa-star fa-2x star-icon" data-value="{{$i}}" data-type="goodwill" style="cursor: pointer; color: #f6c23e;"></i>
                    @endfor
                </div>
                <input type="hidden" id="rating_goodwill" name="goodwill_rating" value="0">
            </div>

            <div class="form-group text-center">
                <label>Payment Rating</label>
                <div class="rating-stars" data-type="payment">
                    @for($i=1; $i<=5; $i++)
                        <i class="far fa-star fa-2x star-icon" data-value="{{$i}}" data-type="payment" style="cursor: pointer; color: #f6c23e;"></i>
                    @endfor
                </div>
                <input type="hidden" id="rating_payment" name="payment_rating" value="0">
            </div>

            <div class="form-group text-center">
                <label>Behaviour Rating</label>
                <div class="rating-stars" data-type="behaviour">
                    @for($i=1; $i<=5; $i++)
                        <i class="far fa-star fa-2x star-icon" data-value="{{$i}}" data-type="behaviour" style="cursor: pointer; color: #f6c23e;"></i>
                    @endfor
                </div>
                <input type="hidden" id="rating_behaviour" name="behaviour_rating" value="0">
            </div>

        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="saveRatingBtn">Save Rating</button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
<style>
    .wa-btn:hover { background-color: #128C7E !important; transform: scale(1.1); transition: all 0.2s; }
    #wa_products_table .form-control-sm { height: 30px; }
    .select2-container--bootstrap4 .select2-selection--single { height: 45px !important; }
    .supplier-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 15px !important;
        overflow: hidden;
    }
    .supplier-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,0.1) !important;
    }
    .avatar-circle {
        transition: transform 0.3s ease;
    }
    .supplier-card:hover .avatar-circle {
        transform: scale(1.05);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Toggle WhatsApp Modal
        $('.wa-btn').click(function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            const phone = $(this).data('phone');
            
            $('#wa_supplier_id').val(id);
            $('#wa_supplier_name').val(name);
            $('#wa_phone').val(phone);
            $('#wa_product_list').empty();
            $('#whatsappModal').modal('show');
        });

        // Product Selector via Select2
        $('#product_search').select2({
            theme: 'bootstrap4',
            placeholder: 'Search product by name or barcode...',
            allowClear: true,
            ajax: {
                url: "{{ route('pos.search-products') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return { query: params.term };
                },
                processResults: function(data) {
                    return {
                        results: data.map(function(item) {
                            return { id: item.id, text: item.title, barcode: item.barcode };
                        })
                    };
                },
                cache: true
            }
        }).on('select2:select', function(e) {
            const product = e.params.data;
            addProductToList(product.id, product.text);
            $(this).val(null).trigger('change');
        });

        function addProductToList(id, title) {
            if ($(`#prod_row_${id}`).length > 0) return; // Already added

            const html = `
                <tr id="prod_row_${id}">
                    <td class="align-middle">
                        <input type="hidden" name="product_ids[]" value="${id}">
                        <strong>${title}</strong>
                    </td>
                    <td>
                        <input type="number" name="quantities[]" class="form-control form-control-sm" value="1" min="1">
                    </td>
                    <td class="text-right">
                        <button type="button" class="btn btn-sm btn-outline-danger border-0 remove-prod" onclick="$(this).closest('tr').remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#wa_product_list').append(html);
        }

        // Form Submit
        $('#wa-form').submit(function(e) {
            e.preventDefault();
            
            if ($('#wa_product_list tr').length === 0) {
                Swal.fire('Error', 'Please add at least one product.', 'error');
                return;
            }

            const btn = $('#send-wa-btn');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Sending...');

            $.ajax({
                url: "{{ route('suppliers.whatsapp.send') }}",
                method: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Success', response.message, 'success');
                        $('#whatsappModal').modal('hide');
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-2"></i>Send via WhatsApp');
                }
            });
        });

        // Rating Logic
        $('.btn-rating').click(function() {
            let id = $(this).data('id');
            let loyalty = $(this).data('loyalty');
            let goodwill = $(this).data('goodwill');
            let payment = $(this).data('payment');
            let behaviour = $(this).data('behaviour');
              
            $('#rating_supplier_id').val(id);
            $('#rating_loyalty').val(loyalty);
            $('#rating_goodwill').val(goodwill);
            $('#rating_payment').val(payment);
            $('#rating_behaviour').val(behaviour);

            updateStars('loyalty', loyalty);
            updateStars('goodwill', goodwill);
            updateStars('payment', payment);
            updateStars('behaviour', behaviour);

            $('#ratingModal').modal('show');
        });

        $('.star-icon').hover(function() {
            let value = $(this).data('value');
            let type = $(this).data('type');
            updateStars(type, value);
        }, function() {
            let type = $(this).data('type');
            let rating = $('#rating_' + type).val();
            updateStars(type, rating);
        });

        $('.star-icon').click(function() {
            let value = $(this).data('value');
            let type = $(this).data('type');
            $('#rating_' + type).val(value);
            updateStars(type, value);
        });

        function updateStars(type, value) {
            $('.star-icon[data-type="' + type + '"]').each(function() {
                let iconVal = $(this).data('value');
                if (iconVal <= value) {
                    $(this).removeClass('far').addClass('fas');
                } else {
                    $(this).removeClass('fas').addClass('far');
                }
            });
        }

        $('#saveRatingBtn').click(function() {
            let id = $('#rating_supplier_id').val();
            let loyalty = $('#rating_loyalty').val();
            let goodwill = $('#rating_goodwill').val();
            let payment = $('#rating_payment').val();
            let behaviour = $('#rating_behaviour').val();
            
            $.ajax({
                url: "/admin/suppliers/" + id + "/rating",
                type: "POST",
                data: {
                    loyalty_rating: loyalty,
                    goodwill_rating: goodwill,
                    payment_rating: payment,
                    behaviour_rating: behaviour,
                    _token: "{{csrf_token()}}"
                },
                success: function(response) {
                    $('#ratingModal').modal('hide');
                    Swal.fire("Success", response.message, "success")
                    .then(() => {
                        location.reload();
                    });
                },
                error: function(err) {
                    console.log(err);
                    Swal.fire("Error", "Something went wrong", "error");
                }
            });
        });
    });
</script>
@endpush
