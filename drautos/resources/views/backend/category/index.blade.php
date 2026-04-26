@extends('backend.layouts.master')

@section('main-content')
 <!-- Category Header -->
 <div class="container-fluid">
    @include('backend.layouts.notification')
    
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Catalog Manager <span class="text-gray-500 font-weight-normal small ml-2">Drag & Drop Organizer</span></h1>
        <div>
            <a href="{{route('category.print')}}" class="btn btn-sm btn-outline-danger shadow-sm rounded-pill px-4 mr-2">
                <i class="fas fa-file-pdf mr-1"></i> Print Catalog
            </a>
            <a href="{{route('category.create')}}" class="btn btn-sm btn-primary shadow-sm rounded-pill px-4">
                <i class="fas fa-plus fa-sm text-white-50 mr-2"></i> Add New Category
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Catalog Grid -->
        <div class="col-lg-12" id="catalog-area">
            <div class="row">
        @if(count($categories)>0)
            @foreach($categories as $category)
            <div class="col-xl-4 col-lg-6 mb-4">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 20px; overflow: hidden; transition: all 0.3s ease;">
                    <!-- Category Image & Status -->
                    <div class="position-relative">
                        @if($category->photo)
                            <img src="{{$category->photo}}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="{{$category->title}}">
                        @else
                            <img src="{{asset('backend/img/thumbnail-default.jpg')}}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="default">
                        @endif
                        <div class="position-absolute" style="top: 15px; right: 15px;">
                            @if($category->status=='active')
                                <span class="badge badge-success px-3 py-2 shadow-sm rounded-pill">Active</span>
                            @else
                                <span class="badge badge-warning px-3 py-2 shadow-sm rounded-pill">Inactive</span>
                            @endif
                        </div>
                        
                        <!-- Quick Actions Overlay -->
                        <div class="category-actions position-absolute w-100 h-100 d-flex align-items-center justify-content-center" style="top: 0; left: 0; background: rgba(30, 41, 59, 0.4); opacity: 0; transition: opacity 0.3s ease;">
                             <a href="{{route('category.edit',$category->id)}}" class="btn btn-light btn-circle mx-2 shadow-sm" title="Edit Category"><i class="fas fa-edit text-primary"></i></a>
                             <a href="{{route('category.products', $category->id)}}" class="btn btn-light btn-circle mx-2 shadow-sm" title="Manage Products"><i class="fas fa-boxes text-info"></i></a>
                             <form method="POST" action="{{route('category.destroy',[$category->id])}}" class="d-inline">
                                @csrf
                                @method('delete')
                                <button class="btn btn-light btn-circle mx-2 shadow-sm dltBtn" data-id="{{$category->id}}" title="Delete"><i class="fas fa-trash text-danger"></i></button>
                             </form>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="font-weight-bold text-gray-900 mb-0">{{$category->title}}</h4>
                            <span class="badge badge-primary badge-counter px-2 py-1" style="font-size: 0.7rem;">ID: #{{$category->id}}</span>
                        </div>

                        <!-- Product Count Stats -->
                        <div class="mb-4">
                            <a href="{{route('category.products', $category->id)}}" class="btn btn-light btn-block text-left py-3 px-4 d-flex justify-content-between align-items-center" style="border-radius: 12px; border: 1px solid rgba(0,0,0,0.05);">
                                <div>
                                    <i class="fas fa-box-open text-primary mr-2 opacity-50"></i>
                                    <span class="text-gray-600 font-weight-bold small">CATALOGED PRODUCTS</span>
                                </div>
                                <span class="h5 mb-0 font-weight-bold text-primary">{{ $category->products->count() }}</span>
                            </a>
                        </div>

                        <!-- Subcategories Section -->
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="text-xs font-weight-bold text-gray-400 text-uppercase mb-0" style="letter-spacing: 0.1em;">Subcategories ({{$category->child_cat->count()}})</h6>
                            <a href="{{route('category.create')}}?parent_id={{$category->id}}" class="btn btn-link btn-sm text-primary p-0 font-weight-bold" style="font-size: 0.7rem;">+ ADD SUB</a>
                        </div>

                        <div class="subcategory-scroll scrollbar-custom" style="max-height: 200px; overflow-y: auto; padding-right: 5px;">
                            @if($category->child_cat->count() > 0)
                                @foreach($category->child_cat as $child)
                                <div class="subcat-item d-flex align-items-center justify-content-between p-3 mb-2 rounded-lg transition-all" style="background: #f8fafc; border: 1px solid transparent;">
                                    <div class="d-flex align-items-center overflow-hidden">
                                        <div class="mr-3" style="width: 4px; height: 20px; background: #e2e8f0; border-radius: 2px;"></div>
                                        <span class="text-gray-700 font-weight-bold small text-truncate">{{$child->title}}</span>
                                    </div>
                                    <div class="d-flex ml-2">
                                        <a href="{{route('category.products', $child->id)}}" class="btn btn-white btn-sm shadow-sm rounded-circle mr-1" style="width: 28px; height: 28px; padding: 0; line-height: 28px; background: white;" title="Manage Products">
                                            <i class="fas fa-boxes text-info" style="font-size: 0.7rem;"></i>
                                        </a>
                                        <a href="{{route('category.edit',$child->id)}}" class="btn btn-white btn-sm shadow-sm rounded-circle mr-1" style="width: 28px; height: 28px; padding: 0; line-height: 28px; background: white;" title="Edit">
                                            <i class="fas fa-edit text-primary" style="font-size: 0.7rem;"></i>
                                        </a>
                                        <form method="POST" action="{{route('category.destroy',[$child->id])}}" class="d-inline">
                                            @csrf
                                            @method('delete')
                                            <button class="btn btn-white btn-sm shadow-sm rounded-circle dltBtn" data-id="{{$child->id}}" style="width: 28px; height: 28px; padding: 0; line-height: 28px; background: white;" title="Delete">
                                                <i class="fas fa-trash text-danger" style="font-size: 0.7rem;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="text-center py-4 bg-gray-50 rounded-lg border-dashed" style="border: 2px dashed #e2e8f0;">
                                    <i class="fas fa-folder-open text-gray-300 mb-2" style="font-size: 1.5rem;"></i>
                                    <p class="text-gray-400 small mb-0">No subcategories yet</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="card shadow-sm border-0 text-center p-5" style="border-radius: 20px;">
                    <i class="fas fa-sitemap text-gray-200 mb-4" style="font-size: 5rem;"></i>
                    <h3 class="font-weight-bold text-gray-800">Your Catalog is Empty</h3>
                    <p class="text-gray-500 mb-4">Start by creating your first parent category to organize your products.</p>
                    <div class="d-flex justify-content-center">
                        <a href="{{route('category.create')}}" class="btn btn-primary rounded-pill px-5 py-3 shadow">
                            <i class="fas fa-plus mr-2"></i> Create First Category
                        </a>
                    </div>
                </div>
            </div>
        @endif
        </div> {{-- End Inner Row --}}
        
        <div class="mt-2 mb-5">
            <div class="mt-4">
                {{ $categories->links() }}
            </div>
        </div>
    </div> {{-- End Catalog Area --}}
 </div>

@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
<style>
    /* Catalog Organizer Styling */
    #catalog-area .card {
        border: 2px dashed transparent;
        transition: all 0.3s ease;
    }
    
    #catalog-area .drop-zone-active {
        border-color: #4e73df !important;
        background-color: rgba(78, 115, 223, 0.05) !important;
        transform: scale(1.02);
    }

    .draggable-product:hover {
        background: white !important;
        border-color: #4e73df !important;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    .draggable-product:active {
        cursor: grabbing;
    }

    .sortable-ghost {
        opacity: 0.4;
        background: #4e73df !important;
    }

    .subcat-item.drop-zone-active {
        background: #eef2ff !important;
        border-color: #4e73df !important;
        transform: scale(1.05);
    }

    /* Card Hover Effects */
    .card:hover .category-actions {
        opacity: 1 !important;
    }
    
    /* Custom Scrollbar */
    .scrollbar-custom::-webkit-scrollbar {
        width: 4px;
    }
    .scrollbar-custom::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .scrollbar-custom::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .transition-all {
        transition: all 0.25s ease-in-out;
    }
    
    .btn-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    $(document).ready(function(){
        // Product Search logic
        $('#productSearch').on('keyup', function() {
            var val = $(this).val().toLowerCase();
            var filterAll = $('#btnAll').hasClass('active');
            
            $('.draggable-product').each(function() {
                var title = $(this).data('title');
                var isVisible = title.includes(val);
                
                if(!filterAll) {
                    if($(this).hasClass('assigned-product')) isVisible = false;
                }
                
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

        // Initialize Sortable for the product pool
        new Sortable(document.getElementById('product-pool'), {
            group: {
                name: 'products',
                pull: 'clone', 
                put: false
            },
            sort: false,
            animation: 150
        });

        // Subcategory Drop Zones
        $('.subcat-item').each(function() {
            var subcatId = $(this).find('.dltBtn').data('id');
            new Sortable(this, {
                group: 'products',
                onAdd: function (evt) {
                    var productId = evt.item.getAttribute('data-id');
                    evt.item.parentNode.removeChild(evt.item);
                    updateProductCategory(productId, subcatId);
                },
                onDragOver: function(evt) { $(evt.to).addClass('drop-zone-active'); },
                onDragLeave: function(evt) { $(evt.to).removeClass('drop-zone-active'); }
            });
        });

        // Parent Category Drop Zones (Card Body)
        $('.card-body').each(function() {
            // Only make it a parent drop zone if it's NOT a subcat list container
            if($(this).closest('.card').length > 0) {
                var parentCatId = $(this).closest('.card').find('.dltBtn').first().data('id');
                new Sortable(this, {
                    group: 'products',
                    onAdd: function (evt) {
                        var productId = evt.item.getAttribute('data-id');
                        evt.item.parentNode.removeChild(evt.item);
                        updateProductCategory(productId, parentCatId);
                    },
                    onDragOver: function(evt) { $(evt.to).addClass('drop-zone-active'); },
                    onDragLeave: function(evt) { $(evt.to).removeClass('drop-zone-active'); }
                });
            }
        });

        function updateProductCategory(productId, catId) {
            // Show loading toast
            const toast = swal({
                title: "Assigning...",
                text: "Please wait while we update the catalog.",
                buttons: false,
                closeOnClickOutside: false,
                closeOnEsc: false
            });

            $.ajax({
                url: "{{ route('category.products.update', ':id') }}".replace(':id', catId),
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    product_ids: [productId],
                    append: true // Custom flag to logic know we are adding, not replacing all
                },
                success: function(response) {
                    swal({
                        title: "Success!",
                        text: "Product successfully moved to category.",
                        icon: "success",
                        timer: 1500,
                        buttons: false
                    }).then(() => {
                        location.reload(); // Quickest way to update all counts and UI
                    });
                },
                error: function() {
                    swal("Error!", "Failed to update category. Please try again.", "error");
                }
            });
        }

        // Delete logic
        $('.dltBtn').click(function(e){
            var form=$(this).closest('form');
            e.preventDefault();
            swal({
                title: "Are you sure?",
                text: "Deleting this category may affect products associated with it!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
                className: "rounded-lg"
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
