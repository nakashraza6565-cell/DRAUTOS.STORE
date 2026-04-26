@extends('backend.layouts.master')

@section('main-content')
 <div class="card shadow mb-4">
     <div class="row">
         <div class="col-md-12">
            @include('backend.layouts.notification')
         </div>
     </div>

    {{-- Pending Requests Alert Banner --}}
    @if(isset($pendingCount) && $pendingCount > 0)
    <div class="alert alert-warning mx-3 mt-3 mb-0 d-flex align-items-center justify-content-between" style="border-radius:10px; border-left: 5px solid #ffc107;">
        <div>
            <i class="fas fa-clock mr-2 text-warning"></i>
            <strong>{{ $pendingCount }} Pending Registration Request(s)</strong> 
            — New users are awaiting admin approval.
        </div>
        <a href="{{ route('users.pending') }}" class="btn btn-sm btn-warning font-weight-bold">
            <i class="fas fa-user-clock mr-1"></i> View Pending
        </a>
    </div>
    @endif

    <div class="card-header py-3 mt-2">
      <h6 class="m-0 font-weight-bold text-primary float-left">
          Users List
          @if(isset($filterStatus) && $filterStatus == 'pending')
              <span class="badge badge-warning ml-2">Pending Requests</span>
          @endif
      </h6>
      <div class="float-right d-flex align-items-center" style="gap: 12px;">
          {{-- Search Bar --}}
          <form action="{{request()->url()}}" method="GET" class="d-flex align-items-center position-relative" style="width: 280px;">
              {{-- Preserve other filters --}}
              @if(request('status')) <input type="hidden" name="status" value="{{request('status')}}"> @endif
              @if(request('city')) <input type="hidden" name="city" value="{{request('city')}}"> @endif
              
              <div class="position-relative w-100">
                  <input type="text" name="search" class="form-control" 
                         placeholder="Search name, email, phone..." 
                         value="{{request('search')}}"
                         style="padding-left: 35px; border-radius: 50px; height: 38px; border: 1px solid rgba(0,0,0,0.1); font-size: 0.85rem;">
                  <i class="fas fa-search position-absolute text-muted" style="left: 12px; top: 50%; transform: translateY(-50%); font-size: 0.8rem;"></i>
                  @if(request('search'))
                    <a href="{{request()->url()}}?status={{request('status')}}&city={{request('city')}}" class="position-absolute text-danger" style="right: 12px; top: 50%; transform: translateY(-50%);">
                        <i class="fas fa-times-circle"></i>
                    </a>
                  @endif
              </div>
          </form>

          {{-- Status Filter --}}
          <form action="{{request()->url()}}" method="GET" class="d-inline-block" style="width: 150px;">
              @if(request('search')) <input type="hidden" name="search" value="{{request('search')}}"> @endif
              @if(request('city')) <input type="hidden" name="city" value="{{request('city')}}"> @endif
              <select name="status" class="form-control form-control-sm" onchange="this.form.submit()" style="border-radius: 50px; height: 38px;">
                  <option value="">-- All Status --</option>
                  <option value="active" {{request('status') == 'active' ? 'selected' : ''}}>Active</option>
                  <option value="pending" {{request('status') == 'pending' ? 'selected' : ''}}>Pending</option>
                  <option value="inactive" {{request('status') == 'inactive' ? 'selected' : ''}}>Inactive</option>
              </select>
          </form>

          <form action="{{request()->url()}}" method="GET" class="d-inline-block" style="width: 160px;">
              @if(request('search')) <input type="hidden" name="search" value="{{request('search')}}"> @endif
              @if(request('status')) <input type="hidden" name="status" value="{{request('status')}}"> @endif
              <select name="city" class="form-control form-control-sm" onchange="this.form.submit()" style="border-radius: 50px; height: 38px;">
                  <option value="">-- Filter City --</option>
                  @foreach($cities as $city)
                    <option value="{{$city}}" {{request('city') == $city ? 'selected' : ''}}>{{$city}}</option>
                  @endforeach
              </select>
          </form>
          
          <a href="{{route('users.create')}}" class="btn btn-primary btn-sm px-4 shadow-sm" style="height: 38px; display: flex; align-items: center;"><i class="fas fa-plus mr-2"></i> Add User</a>
      </div>
    </div>
    <div class="card-body">
      <div class="table-responsive" id="user-list-container">
        @if(count($users)>0)
        <div class="row">
            @foreach($users as $user)
            <div class="col-xl-3 col-md-4 mb-4">
                <div class="card border-left-primary shadow h-100 py-2 user-card {{ $user->status == 'pending' ? 'pending-card' : '' }}">
                    @if($user->status == 'pending')
                    <div class="pending-ribbon">
                        <span>PENDING</span>
                    </div>
                    @endif
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="d-inline-block position-relative avatar-circle">
                                @if($user->photo)
                                    <img src="{{$user->photo}}" 
                                         class="img-fluid rounded-circle shadow-sm border" 
                                         style="width: 80px; height: 80px; object-fit: cover; background: #f8f9fc;" 
                                         alt="User Avatar">
                                @else
                                    <img src="{{asset('backend/img/avatar.png')}}" 
                                         class="img-fluid rounded-circle shadow-sm border" 
                                         style="width: 80px; height: 80px; object-fit: cover; background: #f8f9fc;" 
                                         alt="User Avatar">
                                @endif
                                <span class="position-absolute" style="bottom: 5px; right: 5px;">
                                    @if($user->status == 'pending')
                                        <i class="fas fa-clock text-warning bg-white rounded-circle"></i>
                                    @else
                                        <i class="fas fa-check-circle text-success bg-white rounded-circle"></i>
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2 text-center">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    {{$user->role}}
                                </div>
                                <div class="h5 mb-2 font-weight-bold text-gray-800">
                                    {{$user->name}}
                                </div>
                                <div class="mb-3 text-muted small">
                                    <div class="text-truncate"><i class="fas fa-envelope fa-sm mr-1"></i> {{$user->email}}</div>
                                    @if($user->phone)
                                        <div class="mt-1"><i class="fas fa-phone fa-sm mr-1"></i> {{$user->phone}}</div>
                                    @endif
                                    @if($user->created_at)
                                        <div class="mt-1"><i class="fas fa-calendar fa-sm mr-1"></i> {{$user->created_at->diffForHumans()}}</div>
                                    @endif
                                </div>
                                
                                <div class="d-flex justify-content-center align-items-center mb-3">
                                    @php
                                        $pnts = ($user->loyalty_rating ?? 0) + 
                                               ($user->goodwill_rating ?? 0) + 
                                               ($user->payment_rating ?? 0) + 
                                               ($user->behaviour_rating ?? 0);
                                    @endphp
                                    <div class="mr-2 px-2 py-1 rounded bg-light border shadow-sm">
                                        <i class="fas fa-star text-warning mr-1"></i>
                                        <span class="font-weight-bold small">{{$pnts}}/20</span>
                                    </div>
                                    <span class="badge badge-pill badge-{{($user->status=='active') ? 'success' : (($user->status == 'pending') ? 'warning' : 'secondary')}}" style="font-size: 10px;">{{strtoupper($user->status)}}</span>
                                </div>

                                <div class="mt-3 text-center">
                                    @if($user->status == 'pending')
                                        {{-- Approve Button --}}
                                        <form method="POST" action="{{ route('users.approve', $user->id) }}" class="d-inline" id="approveForm_{{ $user->id }}">
                                            @csrf
                                            <button type="button" class="btn btn-success btn-sm approveBtn px-3 py-1 mb-1" 
                                                    data-id="{{ $user->id }}" 
                                                    data-name="{{ $user->name }}"
                                                    style="border-radius:20px; font-weight:bold;">
                                                <i class="fas fa-check mr-1"></i> Approve
                                            </button>
                                        </form>
                                    @else
                                        <a href="javascript:void(0)" class="btn btn-warning btn-circle btn-sm btn-rating" 
                                           data-id="{{$user->id}}" 
                                           data-loyalty="{{$user->loyalty_rating ?? 0}}" 
                                           data-goodwill="{{$user->goodwill_rating ?? 0}}" 
                                           data-payment="{{$user->payment_rating ?? 0}}" 
                                           data-behaviour="{{$user->behaviour_rating ?? 0}}" 
                                           title="Rate User">
                                            <i class="fas fa-star"></i>
                                        </a>
                                    @endif
                                    <a href="{{route('sales-orders.create')}}?user_id={{$user->id}}" class="btn btn-success btn-circle btn-sm shadow-sm" title="Create Sale Order">
                                        <i class="fas fa-cart-plus"></i>
                                    </a>
                                    <a href="{{route('users.edit',$user->id)}}" class="btn btn-primary btn-circle btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{route('users.destroy',[$user->id])}}" class="d-inline">
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
        <div class="row">
            <div class="col-12 d-flex justify-content-end">
                {{$users->links()}}
            </div>
        </div>
        @else
          <h6 class="text-center">No Users found!!!</h6>
        @endif
      </div>
    </div>
</div>


<!-- Rating Modal -->
<div class="modal fade" id="ratingModal" tabindex="-1" role="dialog" aria-labelledby="ratingModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ratingModalLabel">Rate User</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="ratingForm">
            <input type="hidden" id="rating_user_id" name="user_id">
            
            <div class="form-group text-center">
                <label>Customer Loyalty Rating</label>
                <div class="rating-stars" data-type="loyalty">
                    @for($i=1; $i<=5; $i++)
                        <i class="far fa-star fa-2x star-icon" data-value="{{$i}}" data-type="loyalty" style="cursor: pointer; color: #f6c23e;"></i>
                    @endfor
                </div>
                <input type="hidden" id="rating_loyalty" name="loyalty_rating" value="0">
            </div>

            <div class="form-group text-center">
                <label>Customer Goodwill Rating</label>
                <div class="rating-stars" data-type="goodwill">
                    @for($i=1; $i<=5; $i++)
                        <i class="far fa-star fa-2x star-icon" data-value="{{$i}}" data-type="goodwill" style="cursor: pointer; color: #f6c23e;"></i>
                    @endfor
                </div>
                <input type="hidden" id="rating_goodwill" name="goodwill_rating" value="0">
            </div>

            <div class="form-group text-center">
                <label>Customer Payment Rating</label>
                <div class="rating-stars" data-type="payment">
                    @for($i=1; $i<=5; $i++)
                        <i class="far fa-star fa-2x star-icon" data-value="{{$i}}" data-type="payment" style="cursor: pointer; color: #f6c23e;"></i>
                    @endfor
                </div>
                <input type="hidden" id="rating_payment" name="payment_rating" value="0">
            </div>

            <div class="form-group text-center">
                <label>Customer Behaviour Rating</label>
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
  <link href="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
  <style>
      .user-card {
          transition: transform 0.2s, box-shadow 0.2s;
          border-radius: 12px;
      }
      .user-card:hover {
          transform: translateY(-5px);
          box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.15) !important;
      }
      .avatar-circle {
          position: relative;
      }
      .btn-circle {
          width: 35px;
          height: 35px;
          padding: 0;
          border-radius: 50%;
          display: inline-flex;
          align-items: center;
          justify-content: center;
      }
      /* Pending Card Styles */
      .pending-card {
          border-left: 4px solid #ffc107 !important;
          background: linear-gradient(135deg, #fff9e6 0%, #fff 100%);
          position: relative;
          overflow: hidden;
      }
      .pending-ribbon {
          position: absolute;
          top: 0;
          right: 0;
          width: 70px;
          height: 70px;
          overflow: hidden;
          z-index: 1;
      }
      .pending-ribbon span {
          position: absolute;
          top: 14px;
          right: -16px;
          background: #ffc107;
          color: #333;
          font-size: 9px;
          font-weight: bold;
          padding: 3px 20px;
          transform: rotate(45deg);
          letter-spacing: 0.5px;
      }
  </style>
@endpush

@push('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

  <script>
      $(document).ready(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
          $('.dltBtn').click(function(e){
            var form=$(this).closest('form');
              var dataID=$(this).data('id');
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

          // Approve Button
          $(document).on('click', '.approveBtn', function(){
              var userId   = $(this).data('id');
              var userName = $(this).data('name');
              var form     = $('#approveForm_' + userId);
              swal({
                  title: "Approve User?",
                  text: "This will approve '" + userName + "' and send them a WhatsApp notification.",
                  icon: "success",
                  buttons: ["Cancel", "Yes, Approve!"],
              }).then((willApprove) => {
                  if (willApprove) {
                      form.submit();
                  }
              });
          });
      })
  </script>
  <script>
      $(document).ready(function() {
          // Live Search Logic
          let searchTimer;
          const searchDelay = 500; // 500ms debounce

          $('input[name="search"]').on('keyup input', function() {
              clearTimeout(searchTimer);
              let searchInput = $(this);
              
              searchTimer = setTimeout(function() {
                  let search = searchInput.val();
                  let status = $('select[name="status"]').val();
                  let city = $('select[name="city"]').val();
                  
                  // Show loading state
                  $('#user-list-container').css('opacity', '0.5');
                  
                  $.ajax({
                      url: "{{request()->url()}}",
                      type: "GET",
                      data: {
                          search: search,
                          status: status,
                          city: city
                      },
                      success: function(response) {
                          // Extract the content of #user-list-container from the response
                          let newContent = $(response).find('#user-list-container').html();
                          $('#user-list-container').html(newContent).css('opacity', '1');
                          
                          // Re-initialize any dynamic elements if needed
                          // (e.g. tooltips, delete buttons logic)
                      },
                      error: function(err) {
                          console.log(err);
                          $('#user-list-container').css('opacity', '1');
                      }
                  });
              }, searchDelay);
          });

          $('.btn-rating').click(function() {
              let userId = $(this).data('id');
              let loyalty = $(this).data('loyalty');
              let goodwill = $(this).data('goodwill');
              let payment = $(this).data('payment');
              let behaviour = $(this).data('behaviour');

              $('#rating_user_id').val(userId);
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
              let userId = $('#rating_user_id').val();
              let loyalty = $('#rating_loyalty').val();
              let goodwill = $('#rating_goodwill').val();
              let payment = $('#rating_payment').val();
              let behaviour = $('#rating_behaviour').val();
              
              $.ajax({
                  url: "/admin/users/" + userId + "/rating",
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
                      swal("Success", response.message, "success")
                      .then(() => {
                          location.reload();
                      });
                  },
                  error: function(err) {
                      console.log(err);
                      swal("Error", "Something went wrong", "error");
                  }
              });
          });
      });
  </script>
@endpush
