@extends('backend.layouts.master')

@section('title', 'Die List')

@section('main-content')
<div class="card shadow mb-4 border-0">
    <div class="card-header py-3 d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-tools mr-2"></i>Die List</h6>
        <div class="d-flex align-items-center w-100 w-sm-auto mt-2 mt-sm-0 gap-2">
            <div class="input-group input-group-sm mr-2" style="max-width: 300px;">
                <div class="input-group-prepend">
                    <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-primary"></i></span>
                </div>
                <input type="text" id="dieSearch" class="form-control border-left-0" placeholder="Search by name, rack, product...">
            </div>
            <a href="{{route('die-management.create')}}" class="btn btn-primary btn-sm text-nowrap"><i class="fas fa-plus"></i> Add Die</a>
        </div>
    </div>
    
    <div class="card-body">
        @include('backend.layouts.notification')
        
        <div class="row" id="dieCardGrid">
            @if(count($dies) > 0)
                @foreach($dies as $die)
                    <div class="col-xl-4 col-md-6 mb-4 die-card-item" 
                         data-name="{{ strtolower($die->name) }}"
                         data-rack="{{ strtolower($die->rack_number ?? '') }}"
                         data-product="{{ strtolower($die->product->title ?? '') }}"
                         data-custody="{{ strtolower($die->custody_of ?? '') }}">
                        <div class="card shadow-sm h-100 border-bottom-primary die-profile-card hover-shadow transition">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col-auto mr-3">
                                        <a href="{{ route('die-management.show', $die->id) }}">
                                            @if($die->photo)
                                                <img src="{{$die->photo}}" class="img-fluid rounded shadow-xs" style="width: 85px; height: 85px; object-fit: cover; border: 2px solid #eaecf4;" alt="{{$die->name}}">
                                            @else
                                                <div class="bg-gray-200 rounded d-flex align-items-center justify-content-center" style="width: 85px; height: 85px; border: 2px solid #eaecf4;">
                                                    <i class="fas fa-tools fa-2x text-gray-400"></i>
                                                </div>
                                            @endif
                                        </a>
                                    </div>
                                    <div class="col">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-0">Rack: {{$die->rack_number ?? 'N/A'}}</div>
                                        <a href="{{ route('die-management.show', $die->id) }}" class="h5 mb-0 font-weight-bold text-gray-800 text-decoration-none hover-primary">{{$die->name}}</a>
                                        
                                        @if($die->product)
                                            <div class="text-xs text-gray-600 mt-1 font-weight-bold">
                                                <i class="fas fa-box-open mr-1"></i>{{ $die->product->title }}
                                            </div>
                                        @endif
                                        
                                        <div class="mt-2 d-flex flex-wrap gap-1">
                                            @php
                                                $qualityClass = 'badge-secondary';
                                                if($die->quality_status == 'good') $qualityClass = 'badge-success';
                                                if($die->quality_status == 'maintenance_required') $qualityClass = 'badge-warning';
                                                if($die->quality_status == 'damaged') $qualityClass = 'badge-danger';
                                            @endphp
                                            <span class="badge {{ $qualityClass }}">{{ str_replace('_', ' ', strtoupper($die->quality_status ?? 'unknown')) }}</span>
                                            @if($die->status=='active')
                                                <span class="badge badge-pill badge-primary">ACTIVE</span>
                                            @else
                                                <span class="badge badge-pill badge-light border">INACTIVE</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-3">
                                <div class="row small">
                                    <div class="col-6">
                                        <p class="mb-1 text-truncate"><strong>Maker:</strong> {{$die->maker ?? 'N/A'}}</p>
                                        <p class="mb-0"><strong>Produced:</strong> <span class="font-weight-bold text-success">{{ number_format($die->goods_produced) }}</span></p>
                                    </div>
                                    <div class="col-6 text-right">
                                        <p class="mb-1 text-truncate"><strong>Custody:</strong> {{$die->custody_of ?? 'N/A'}}</p>
                                        <p class="mb-0 text-truncate"><strong>Phone:</strong> {{$die->custody_phone ?? 'N/A'}}</p>
                                    </div>
                                </div>
                                <div class="mt-3 text-right">
                                    <a href="{{ route('die-management.show', $die->id) }}" class="btn btn-info btn-sm rounded-circle shadow-sm mr-1" style="width: 35px; height: 35px;" data-toggle="tooltip" title="View Profile"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('die-management.edit', $die->id) }}" class="btn btn-primary btn-sm rounded-circle shadow-sm mr-1" style="width: 35px; height: 35px;" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></a>
                                    <form method="POST" action="{{ route('die-management.destroy', [$die->id]) }}" class="d-inline">
                                        @csrf 
                                        @method('delete')
                                        <button class="btn btn-danger btn-sm rounded-circle shadow-sm dltBtn" data-id={{$die->id}} style="width: 35px; height: 35px;" data-toggle="tooltip" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-12" id="noDiesFound">
                    <h6 class="text-center py-5">No Dies found!!! Please add some.</h6>
                </div>
            @endif
            
            <div class="col-12 text-center py-5 d-none" id="noResultsFound">
                <i class="fas fa-tools fa-3x mb-3 text-gray-300"></i>
                <h6 class="text-muted font-weight-bold">No dies match your search query</h6>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
<style>
    .transition {
        transition: all 0.2s ease-in-out;
    }
    .hover-shadow:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    .hover-primary:hover {
        color: #4e73df !important;
    }
    .gap-2 { gap: 8px; }
    .gap-1 { gap: 4px; }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script>
    $(document).ready(function(){
        // CSRF Token Setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Real-time Search Box
        $('#dieSearch').on('input', function() {
            const query = $(this).val().toLowerCase().trim();
            let matches = 0;

            if (query === '') {
                $('.die-card-item').removeClass('d-none');
                $('#noResultsFound').addClass('d-none');
                return;
            }

            $('.die-card-item').each(function() {
                const name = $(this).attr('data-name');
                const rack = $(this).attr('data-rack');
                const product = $(this).attr('data-product');
                const custody = $(this).attr('data-custody');

                if (name.includes(query) || rack.includes(query) || product.includes(query) || custody.includes(query)) {
                    $(this).removeClass('d-none');
                    matches++;
                } else {
                    $(this).addClass('d-none');
                }
            });

            if (matches === 0) {
                $('#noResultsFound').removeClass('d-none');
            } else {
                $('#noResultsFound').addClass('d-none');
            }
        });

        // Delete SweetAlert
        $('.dltBtn').click(function(e){
            var form = $(this).closest('form');
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
                }
            });
        });
    });
</script>
@endpush
