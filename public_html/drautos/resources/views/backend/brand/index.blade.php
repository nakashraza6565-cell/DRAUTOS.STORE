@extends('backend.layouts.master')

@section('main-content')
 <!-- Brand Stock Dashboard -->
 <div class="container-fluid">
    @include('backend.layouts.notification')
    
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Brand Stock Control <span class="text-gray-500 font-weight-normal small ml-2">Manage Company Inventory</span></h1>
        <div>
            <a href="{{route('brand.create')}}" class="btn btn-sm btn-primary shadow-sm rounded-pill px-4">
                <i class="fas fa-plus fa-sm text-white-50 mr-2"></i> Add New Brand
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Brand Grid -->
        <div class="col-lg-12" id="brand-area">
            <div class="row">
        @if(count($brands)>0)
            @foreach($brands as $brand)
            <div class="col-xl-4 col-lg-6 mb-4">
                <div class="card shadow-sm border-0 h-100 brand-card" style="border-radius: 20px; overflow: hidden; transition: all 0.3s ease;" data-id="{{$brand->id}}">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="font-weight-bold text-gray-900 mb-1">{{$brand->title}}</h5>
                                <span class="badge badge-light text-gray-500 border rounded-pill px-2" style="font-size: 0.65rem;">COMPANY ID: #{{$brand->id}}</span>
                            </div>
                            <div class="dropdown no-arrow">
                                <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                                    <a class="dropdown-item" href="{{route('brand.edit',$brand->id)}}"><i class="fas fa-edit fa-sm fa-fw mr-2 text-gray-400"></i>Edit Brand</a>
                                    <div class="dropdown-divider"></div>
                                    <form method="POST" action="{{route('brand.destroy',[$brand->id])}}">
                                        @csrf 
                                        @method('delete')
                                        <button class="dropdown-item text-danger dltBtn" data-id="{{$brand->id}}"><i class="fas fa-trash fa-sm fa-fw mr-2 text-danger"></i>Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Stock Summary Stats -->
                        @php 
                            $brandProducts = $brand->products;
                            $totalStock = $brandProducts->sum('stock');
                            $lowStockCount = $brandProducts->filter(function($p) { return $p->stock <= ($p->low_stock_threshold ?? 0); })->count();
                        @endphp
                        
                        <div class="row no-gutters align-items-center bg-light rounded-lg p-3 mb-4">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Items (Varieties)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $brandProducts->count() }} Items</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-layer-group fa-2x text-gray-200"></i>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-xs font-weight-bold text-gray-400 text-uppercase">Stock Health</span>
                            <span class="text-xs font-weight-bold text-gray-800">{{ $totalStock }} Units Total</span>
                        </div>
                        <div class="mb-3">
                            @if($lowStockCount > 0)
                                <button class="btn btn-block badge-danger-soft text-danger px-3 py-2 rounded-pill transition-all hover-grow border-0" type="button" data-toggle="collapse" data-target="#brandProducts-{{$brand->id}}" aria-expanded="false" style="font-size: 0.75rem;">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> {{ $lowStockCount }} Low Stock (Click to View)
                                </button>
                            @else
                                <button class="btn btn-block badge-success-soft text-success px-3 py-2 rounded-pill transition-all hover-grow border-0" type="button" data-toggle="collapse" data-target="#brandProducts-{{$brand->id}}" aria-expanded="false" style="font-size: 0.75rem;">
                                    <i class="fas fa-check-circle mr-1"></i> Healthy (Click to View)
                                </button>
                            @endif
                        </div>

                        <div class="mb-4">
                            <div class="text-xs text-gray-500 mb-1">Product Variety: <strong>{{ $brandProducts->count() }} Items</strong></div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $brandProducts->count() > 0 ? min(100, ($totalStock / ($brandProducts->count() * 50)) * 100) : 0 }}%"></div>
                            </div>
                        </div>

                        <!-- Collapsible Detailed Product List -->
                        <div class="collapse mt-3" id="brandProducts-{{$brand->id}}">
                            <div class="bg-white rounded-lg border p-2 scrollbar-custom" style="max-height: 300px; overflow-y: auto;">
                                @if($brandProducts->count() > 0)
                                    @foreach($brandProducts as $product)
                                    <div class="d-flex align-items-center mb-2 p-2 rounded {{ $product->stock <= ($product->low_stock_threshold ?? 0) ? 'bg-danger-soft border-danger' : 'bg-light' }} border shadow-xs" style="font-size: 0.75rem;">
                                        @php $photos = explode(',', $product->photo); @endphp
                                        <img src="{{ $photos[0] ?? asset('backend/img/thumbnail-default.jpg') }}" class="rounded shadow-sm mr-2" style="width: 30px; height: 30px; object-fit: cover;">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <div class="text-gray-800 font-weight-bold text-truncate">{{ $product->title }}</div>
                                            <div class="text-gray-500" style="font-size: 10px;">STOCK: <span class="{{ $product->stock <= ($product->low_stock_threshold ?? 0) ? 'text-danger font-weight-bold' : '' }}">{{ $product->stock }}</span></div>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-4 opacity-50">
                                        <p class="text-xs mb-0">No products assigned</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div class="col-12 text-center p-5">
                <h4 class="text-gray-500">No brands found. Start by creating one!</h4>
            </div>
        @endif
            </div>
            <div class="mt-4">
                {{ $brands->links() }}
            </div>
        </div>
    </div>
 </div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
<style>
    .brand-card.drop-zone-active {
        border: 2px dashed #4e73df !important;
        background-color: rgba(78, 115, 223, 0.05) !important;
        transform: scale(1.02);
    }
    
    .badge-danger-soft { background-color: rgba(231, 74, 59, 0.1); color: #e74a3b; }
    .badge-success-soft { background-color: rgba(28, 200, 138, 0.1); color: #1cc88a; }
    
    .draggable-product:hover {
        background: white !important;
        border-color: #4e73df !important;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    .scrollbar-custom::-webkit-scrollbar { width: 4px; }
    .scrollbar-custom::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .scrollbar-custom::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    
    .transition-all { transition: all 0.25s ease-in-out; }
    .shadow-xs { box-shadow: 0 .125rem .25rem rgba(0,0,0,.03)!important; }
    .hover-grow:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important; }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    $(document).ready(function(){
        // Search & Filter Logic
        $('#productSearch').on('keyup', function() {
            var val = $(this).val().toLowerCase();
            var filterAll = $('#btnAll').hasClass('active');
            $('.draggable-product').each(function() {
                var title = $(this).data('title');
                var isVisible = title.includes(val);
                if(!filterAll && $(this).hasClass('assigned-product')) isVisible = false;
                $(this).toggleClass('d-none', !isVisible);
            });
        });

        $('#btnUnassigned').click(function() {
            setTimeout(function() {
                $('.assigned-product').addClass('d-none');
                $('#productSearch').trigger('keyup');
            }, 50);
        });

        $('#btnAll').click(function() {
            setTimeout(function() {
                $('.assigned-product').removeClass('d-none');
                $('#productSearch').trigger('keyup');
            }, 50);
        });

        // Initialize Sortable for product pool
        new Sortable(document.getElementById('product-pool'), {
            group: { name: 'brand_products', pull: 'clone', put: false },
            sort: false,
            animation: 150
        });

        // Brand Cards as Drop Zones
        $('.brand-card').each(function() {
            var brandId = $(this).data('id');
            new Sortable(this.querySelector('.card-body'), {
                group: 'brand_products',
                onAdd: function (evt) {
                    var productId = evt.item.getAttribute('data-id');
                    evt.item.parentNode.removeChild(evt.item);
                    updateProductBrand(productId, brandId);
                },
                onDragOver: function(evt) { $(evt.to).closest('.brand-card').addClass('drop-zone-active'); },
                onDragLeave: function(evt) { $(evt.to).closest('.brand-card').removeClass('drop-zone-active'); }
            });
        });

        function updateProductBrand(productId, brandId) {
            swal({ title: "Updating Brand...", text: "Please wait.", buttons: false, closeOnClickOutside: false });
            
            $.ajax({
                url: "{{ route('brand.products.update', ':id') }}".replace(':id', brandId),
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    product_ids: [productId]
                },
                success: function(response) {
                    swal({ title: "Success!", text: "Brand updated.", icon: "success", timer: 1000, buttons: false })
                    .then(() => { location.reload(); });
                },
                error: function() {
                    swal("Error!", "Update failed.", "error");
                }
            });
        }

        $('.dltBtn').click(function(e){
            var form=$(this).closest('form');
            e.preventDefault();
            swal({
                title: "Are you sure?",
                text: "Deleting this brand will unassign its products!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) form.submit();
            });
        });
    });
</script>
@endpush
