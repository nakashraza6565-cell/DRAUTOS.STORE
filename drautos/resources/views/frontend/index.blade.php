@extends('frontend.layouts.master')
@section('title','Danyal Autos Co. || Premium Auto Parts')
@section('main-content')

<!-- Premium Hero Section -->
<section class="premium-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 col-md-10 mx-auto text-center">
                <div class="hero-content">
                    <span class="hero-badge">Welcome to Danyal Autos Co.</span>
                    <h1 class="hero-title">Premium Commercial <br><span class="text-gradient">Auto Parts & Logistics</span></h1>
                    <p class="hero-subtitle mx-auto">Your trusted B2B partner for heavy-duty truck components, engine assemblies, and genuine parts. We deliver quality you can depend on.</p>
                    
                    <div class="hero-buttons">
                        <a href="{{route('product-grids')}}" class="btn-premium">
                            <i class="fa fa-shopping-bag"></i> Explore Catalog
                        </a>
                        @guest
                        <a href="{{route('login')}}" class="btn-glass ml-3">
                            <i class="fa fa-lock"></i> Login for Pricing
                        </a>
                        @else
                        <a href="{{route('user')}}" class="btn-glass ml-3">
                            <i class="fa fa-tachometer"></i> Client Dashboard
                        </a>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Advertiser / Features Section -->
<section class="features-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fa fa-check-circle"></i></div>
                    <h3 class="feature-title">100% Genuine Parts</h3>
                    <p class="feature-desc">We source directly from trusted manufacturers to ensure every component meets rigorous OEM quality standards for maximum durability.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fa fa-truck"></i></div>
                    <h3 class="feature-title">Nationwide Delivery</h3>
                    <p class="feature-desc">Fast, reliable logistics network ensuring your heavy-duty parts arrive precisely when and where you need them across the country.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fa fa-headphones"></i></div>
                    <h3 class="feature-title">Expert B2B Support</h3>
                    <p class="feature-desc">Our dedicated team of automotive specialists is available 24/7 to assist with bulk orders, part matching, and technical inquiries.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Categories -->
<section class="section bg-soft py-5">
    <div class="container">
        <div class="section-title">
            <h2>Explore by Category</h2>
            <p>Find exactly what your fleet needs. From robust engine blocks to precision braking systems.</p>
        </div>
        <div class="row">
            @php
                $category_lists = DB::table('categories')->where('status','active')->where('is_parent',1)->limit(6)->get();
            @endphp
            @if($category_lists)
                @foreach($category_lists as $cat)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <a href="{{route('product-cat',$cat->slug)}}" class="category-card">
                            @if($cat->photo)
                                <img src="{{$cat->photo}}" alt="{{$cat->title}}">
                            @else
                                <img src="https://via.placeholder.com/600x400?text={{urlencode($cat->title)}}" alt="{{$cat->title}}">
                            @endif
                            <div class="category-overlay">
                                <h3>{{$cat->title}}</h3>
                                <span class="category-link">View Products <i class="fa fa-arrow-right"></i></span>
                            </div>
                        </a>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</section>

<!-- Trending Products (Store Area) -->
<section class="section py-5 bg-white">
    <div class="container">
        <div class="section-title">
            <h2>Trending Products</h2>
            <p>Our most requested parts, restocked and ready for dispatch.</p>
        </div>
        
        <div class="row">
            @if(isset($product_lists) && count($product_lists) > 0)
                @foreach($product_lists->take(8) as $product)
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="premium-product-card">
                        <div class="product-img-wrap">
                            <a href="{{route('product-detail',$product->slug)}}">
                                @php
                                    $photo = explode(',',$product->photo);
                                @endphp
                                <img src="{{$photo[0]}}" alt="{{$product->title}}">
                            </a>
                        </div>
                        <div class="product-content">
                            <span class="product-category">{{$product->cat_info['title'] ?? 'Auto Part'}}</span>
                            <a href="{{route('product-detail',$product->slug)}}" class="text-decoration-none">
                                <h4 class="product-title">{{$product->title}}</h4>
                            </a>
                            
                            <div class="product-footer">
                                @auth
                                    <div class="product-price">
                                        @php
                                            $after_discount = ($product->price - ($product->price*$product->discount)/100);
                                        @endphp
                                        <span class="text-dark">Rs. {{number_format($after_discount,2)}}</span>
                                    </div>
                                    <a href="{{route('add-to-cart',$product->slug)}}" class="btn btn-sm btn-outline-warning text-dark font-weight-bold" style="border-color:#f59e0b;">
                                        <i class="fa fa-shopping-cart"></i> Add
                                    </a>
                                @else
                                    <a href="{{route('login')}}" class="login-for-price-badge w-100 justify-content-center">
                                        <i class="fa fa-lock"></i> Login to View Price
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="col-12 text-center py-5 text-muted">
                    <i class="fa fa-box-open fa-3x mb-3 opacity-50"></i>
                    <p>No products available right now.</p>
                </div>
            @endif
        </div>
        
        <div class="row mt-4">
            <div class="col-12 text-center">
                <a href="{{route('product-grids')}}" class="btn-premium px-5 py-3">
                    View Full Catalog <i class="fa fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Promotional Banner -->
<section class="section py-5" style="background: var(--primary);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7 mb-4 mb-lg-0 text-white">
                <h2 class="font-weight-bold mb-3" style="color:white; font-size: 2.5rem;">Need Parts in Bulk?</h2>
                <p class="mb-4" style="color:#cbd5e1; font-size: 1.1rem; max-width:500px;">Create a corporate account today to get access to exclusive B2B wholesale pricing, dedicated account managers, and priority logistics.</p>
                @guest
                    <a href="{{route('register')}}" class="btn-premium bg-white text-dark">Register Wholesale Account</a>
                @else
                    <a href="{{route('contact')}}" class="btn-premium bg-white text-dark">Contact Sales Agent</a>
                @endguest
            </div>
            <div class="col-lg-5 text-center text-lg-right">
                <i class="fa fa-briefcase text-white" style="font-size: 12rem; opacity: 0.1; transform: rotate(15deg);"></i>
            </div>
        </div>
    </div>
</section>

@endsection

@push('styles')
<style>
    /* Prevent image overflow on small screens */
    .premium-hero {
        padding: 100px 0;
    }
    .bg-soft { background-color: var(--bg-soft); }
</style>
@endpush
