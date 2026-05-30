@extends('frontend.layouts.master')
@section('title','Danyal Autos Co. || Premium B2B Auto Parts')

@section('main-content')
<!-- B2B Commercial Styles -->
<style>
    /* B2B Theme Core Elements */
    body, .section {
        background-color: #ffffff !important;
        color: var(--text-main);
    }
    .bg-soft { background-color: var(--bg-soft) !important; }
    
    /* Hero Section */
    .b2b-hero {
        position: relative;
        padding: 160px 0 120px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
        overflow: hidden;
        border-bottom: 4px solid var(--accent);
    }
    .b2b-hero::before {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background-image: url('https://www.transparenttextures.com/patterns/cubes.png');
        opacity: 0.1;
        z-index: 0;
    }
    .hero-content {
        position: relative;
        z-index: 1;
        text-align: left;
    }
    .hero-badge {
        display: inline-block;
        padding: 6px 16px;
        background: rgba(250, 204, 21, 0.1);
        border: 1px solid var(--accent);
        color: var(--accent);
        border-radius: 4px;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        font-size: 13px;
        margin-bottom: 24px;
    }
    .hero-title {
        font-size: 4rem;
        font-weight: 800;
        line-height: 1.1;
        color: #ffffff;
        margin-bottom: 20px;
        letter-spacing: -1px;
    }
    .hero-title .text-accent {
        color: var(--accent);
    }
    .hero-subtitle {
        font-size: 1.2rem;
        color: #cbd5e1;
        max-width: 600px;
        margin: 0 0 40px;
        line-height: 1.6;
    }
    
    /* Buttons */
    .btn-b2b {
        background: var(--accent);
        color: #000;
        border: 2px solid var(--accent);
        padding: 14px 32px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-radius: 4px;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-b2b:hover {
        background: transparent;
        color: var(--accent) !important;
        text-decoration: none;
    }
    .btn-b2b-outline {
        background: transparent;
        color: #fff;
        border: 2px solid #fff;
        padding: 14px 32px;
        font-weight: 800;
        text-transform: uppercase;
        border-radius: 4px;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-b2b-outline:hover {
        background: #fff;
        color: var(--primary) !important;
        text-decoration: none;
    }

    /* Section Titles */
    .b2b-section-title {
        text-align: center;
        margin-bottom: 50px;
    }
    .b2b-section-title h2 {
        font-size: 2.5rem;
        color: var(--primary);
        margin-bottom: 15px;
        position: relative;
        display: inline-block;
        font-weight: 800;
    }
    .b2b-section-title h2::after {
        content: "";
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 4px;
        background: var(--accent);
    }
    .b2b-section-title p {
        color: var(--text-muted);
        font-size: 1.1rem;
    }

    /* Features Grid */
    .b2b-feature-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        padding: 40px 30px;
        border-radius: 6px;
        text-align: center;
        transition: all 0.3s ease;
        height: 100%;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
    }
    .b2b-feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(15, 23, 42, 0.08);
        border-color: var(--primary);
    }
    .b2b-feature-icon {
        font-size: 45px;
        color: var(--primary);
        margin-bottom: 20px;
    }
    .b2b-feature-title {
        color: var(--primary);
        font-size: 1.25rem;
        margin-bottom: 15px;
        font-weight: 800;
    }
    .b2b-feature-desc {
        color: var(--text-muted);
        font-size: 0.95rem;
        line-height: 1.6;
    }

    /* "Slide to View" Trending Slider */
    .b2b-slider-wrapper {
        position: relative;
        width: 100%;
        padding: 0 40px;
    }
    .b2b-slider-container {
        display: flex;
        gap: 20px;
        overflow-x: auto;
        scroll-behavior: smooth;
        padding: 20px 0 40px;
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
    .b2b-slider-container::-webkit-scrollbar { display: none; }
    
    .b2b-slider-btn {
        position: absolute;
        top: 45%;
        transform: translateY(-50%);
        width: 45px; height: 45px;
        background: #fff;
        border: 2px solid var(--primary);
        color: var(--primary);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        z-index: 10;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .b2b-slider-btn:hover {
        background: var(--primary);
        color: #fff;
    }
    .b2b-slider-prev { left: -15px; }
    .b2b-slider-next { right: -15px; }

    /* Product Slide Card */
    .b2b-slide-card {
        flex: 0 0 280px;
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        overflow: hidden;
        transition: all 0.3s ease;
        position: relative;
    }
    .b2b-slide-card:hover {
        border-color: var(--primary);
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
        transform: translateY(-4px);
    }
    .b2b-slide-img-wrap {
        height: 220px;
        background: #fff;
        display: flex; align-items: center; justify-content: center;
        overflow: hidden;
        position: relative;
        cursor: pointer;
        border-bottom: 1px solid var(--bg-soft);
    }
    .b2b-slide-img-wrap img {
        width: 90%; height: 90%;
        object-fit: contain;
        transition: transform 0.4s ease;
    }
    .b2b-slide-card:hover .b2b-slide-img-wrap img {
        transform: scale(1.05);
    }
    /* Enlarge Photo Overlay Button */
    .b2b-enlarge-btn {
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%) scale(0.5);
        width: 50px; height: 50px;
        background: var(--primary);
        border: 2px solid #fff;
        color: #fff;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px;
        opacity: 0;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }
    .b2b-slide-img-wrap:hover .b2b-enlarge-btn {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }

    .b2b-slide-content {
        padding: 20px;
    }
    .b2b-slide-cat {
        font-size: 11px;
        color: var(--text-muted);
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 1px;
        display: block; margin-bottom: 8px;
    }
    .b2b-slide-title {
        color: var(--primary);
        font-size: 1.15rem;
        font-weight: 700;
        margin-bottom: 15px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .b2b-slide-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid var(--border-color);
        padding-top: 15px;
    }
    .b2b-price {
        color: var(--primary);
        font-weight: 800;
        font-size: 1.2rem;
    }
    .b2b-add-btn {
        background: var(--primary);
        border: 1px solid var(--primary);
        color: #fff;
        border-radius: 4px;
        padding: 6px 12px;
        font-size: 12px;
        text-transform: uppercase;
        font-weight: 700;
        transition: all 0.2s;
    }
    .b2b-add-btn:hover {
        background: var(--accent);
        border-color: var(--accent);
        color: #000;
    }
    
    .b2b-login-badge {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 8px;
        background: var(--bg-soft);
        border: 1px dashed var(--text-muted);
        color: var(--text-main);
        font-weight: 600;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-radius: 4px;
        text-decoration: none;
        transition: all 0.2s;
    }
    .b2b-login-badge:hover {
        background: var(--primary);
        color: #fff !important;
        border-color: var(--primary);
        text-decoration: none;
    }

    /* Lightbox Overlay */
    #b2b-lightbox {
        position: fixed; inset: 0;
        background: rgba(15, 23, 42, 0.95);
        z-index: 99999;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        opacity: 0; pointer-events: none;
        transition: opacity 0.3s ease;
        backdrop-filter: blur(5px);
    }
    #b2b-lightbox.active {
        opacity: 1; pointer-events: auto;
    }
    .lightbox-close {
        position: absolute; top: 30px; right: 40px;
        background: transparent; border: 2px solid #fff;
        color: #fff; width: 45px; height: 45px; border-radius: 50%;
        font-size: 20px; display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.2s;
    }
    .lightbox-close:hover { background: var(--accent); color: #000; border-color: var(--accent); }
    #lightbox-img {
        max-width: 90vw; max-height: 80vh;
        border-radius: 8px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        border: 4px solid #fff;
        background: #fff;
        object-fit: contain;
    }
    #lightbox-caption {
        color: #fff; font-size: 1.3rem; font-weight: 800; margin-top: 20px;
        text-transform: uppercase; letter-spacing: 1px;
    }
</style>

<!-- B2B Exact Hero Section -->
<section class="b2b-hero-exact" style="position: relative; min-height: 600px; padding: 100px 0; display: flex; align-items: center; background: url('https://images.unsplash.com/photo-1596541578135-c322b7dcaf96?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover no-repeat;">
    <div style="position: absolute; inset: 0; background: linear-gradient(90deg, rgba(8,50,89,0.95) 0%, rgba(8,50,89,0.6) 50%, rgba(8,50,89,0.2) 100%); z-index: 1;"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <div class="row">
            <div class="col-lg-8 col-md-10">
                <div class="hero-content" style="text-align: left;">
                    <h1 class="hero-title" style="font-size: clamp(2.5rem, 5vw + 1rem, 4.5rem); font-weight: 900; color: #fff; line-height: 1.1; margin-bottom: 25px; text-transform: uppercase;">
                        PREMIUM COMMERCIAL <br>TRUCK PARTS <span style="color: var(--accent);">|</span> FOR THE <br>B2B INDUSTRY
                    </h1>
                    <p class="hero-subtitle" style="font-size: clamp(1rem, 2vw + 0.5rem, 1.25rem); color: #e2e8f0; margin-bottom: 40px; font-weight: 500; max-width: 600px;">
                        Your Trusted Partner for Heavy-Duty Reliability. <br class="d-none d-md-block">Quality Parts. Delivered Fast.
                    </p>
                    
                    <a href="{{route('product-grids')}}" class="btn font-weight-bold" style="background: var(--accent); color: #000; padding: 15px 35px; font-size: 15px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.5px; border: none; display: inline-block;">
                        SHOP ALL PARTS <i class="fa fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- AJAX Live Search Results Section -->
<section id="ajax-search-results-section" style="display: none; opacity: 0; max-height: 0; overflow: hidden; background: #f8fafc; border-bottom: 1px solid var(--border-color); transition: all 0.5s ease-in-out;">
    <div class="container py-4">
        <div class="d-flex align-items-center justify-content-between mb-4" style="border-bottom: 2px solid var(--primary); padding-bottom: 12px;">
            <div>
                <h5 style="color: var(--primary); font-weight: 800; margin: 0;"><i class="fa fa-search mr-2"></i>Search Results</h5>
                <small class="text-muted">Showing results for: <strong id="ajax-results-query"></strong> &mdash; <span id="ajax-results-count"></span></small>
            </div>
            <button onclick="clearSearch()" style="background: none; border: 1px solid #ccc; border-radius: 4px; padding: 4px 14px; font-size: 12px; color: var(--text-muted); cursor: pointer;"><i class="fa fa-times mr-1"></i>Clear</button>
        </div>
        <div class="row" id="ajax-results-grid">
            <!-- Products injected here by AJAX -->
        </div>
    </div>
</section>

<!-- Shop By Vehicle Type Section -->
<section class="section py-4" style="background: #ffffff; border-bottom: 1px solid var(--border-color);">
    <div class="container">
        <div class="text-center mb-4">
            <h2 style="color: var(--primary); font-weight: 900; font-size: clamp(1.5rem, 3vw, 1.8rem);">Shop by Vehicle Type</h2>
        </div>
        <!-- Placeholders for the categories (matching the dark block below the hero in the image) -->
        <div class="d-flex justify-content-center align-items-center flex-wrap" style="gap: 5px;">
            <div style="background: #0d1e30; color: #fff; padding: 15px 30px; font-weight: 600; border-radius: 4px; text-align: center; flex: 1; min-width: 150px;">Problems</div>
            <div style="background: var(--primary-light); color: #fff; padding: 15px 30px; font-weight: 600; border-radius: 4px; text-align: center; flex: 1; min-width: 150px;">Output</div>
            <div style="background: var(--primary-light); color: #fff; padding: 15px 30px; font-weight: 600; border-radius: 4px; text-align: center; flex: 1; min-width: 150px;">Terminal</div>
            <div style="background: var(--primary-light); color: #fff; padding: 15px 30px; font-weight: 600; border-radius: 4px; text-align: center; flex: 1; min-width: 150px;">Ports</div>
        </div>
    </div>
</section>

<!-- "Slide to View" Trending Products -->
<section class="section py-5" style="border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">
    <div class="container">
        <div class="b2b-section-title">
            <h2>TOP SELLING COMPONENTS</h2>
            <p>Slide to browse our highest-demand hardware currently in stock.</p>
        </div>
        
        <div class="b2b-slider-wrapper">
            <button class="b2b-slider-btn b2b-slider-prev" onclick="scrollSlider(-300)"><i class="fa fa-chevron-left"></i></button>
            <button class="b2b-slider-btn b2b-slider-next" onclick="scrollSlider(300)"><i class="fa fa-chevron-right"></i></button>
            
            <div class="b2b-slider-container" id="trending-slider">
                @if(isset($product_lists) && count($product_lists) > 0)
                    @foreach($product_lists->take(10) as $product)
                    @php
                        $photos = explode(',',$product->photo);
                        $mainPhoto = $photos[0];
                    @endphp
                    <div class="b2b-slide-card">
                        <!-- Clickable Image Wrap -->
                        <div class="b2b-slide-img-wrap" onclick="openB2BLightbox('{{$mainPhoto}}', '{{addslashes($product->title)}}')">
                            <img src="{{$mainPhoto}}" alt="{{$product->title}}">
                            <div class="b2b-enlarge-btn"><i class="fa fa-search-plus"></i></div>
                        </div>
                        
                        <div class="b2b-slide-content">
                            <span class="b2b-slide-cat">Part No. {{ strtoupper(Str::random(8)) }} | {{$product->cat_info['title'] ?? 'Hardware'}}</span>
                            <a href="{{route('product-detail',$product->slug)}}" class="text-decoration-none">
                                <div class="b2b-slide-title" title="{{$product->title}}">{{$product->title}}</div>
                            </a>
                            
                            <div class="b2b-slide-footer">
                                @auth
                                    @php
                                        $after_discount = ($product->price - ($product->price*$product->discount)/100);
                                    @endphp
                                    <span class="b2b-price">Rs. {{number_format($after_discount,2)}}</span>
                                    <a href="{{route('add-to-cart',$product->slug)}}" class="b2b-add-btn"><i class="fa fa-cart-plus"></i> ADD</a>
                                @else
                                    <a href="{{route('login')}}" class="b2b-login-badge">
                                        <i class="fa fa-lock mr-2"></i> Login to View Price
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="w-100 text-center py-5 text-muted">
                        <i class="fa fa-box-open fa-3x mb-3"></i>
                        <p class="font-weight-bold">Catalog updating. No telemetry available.</p>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="{{route('product-grids')}}" class="btn-b2b-outline" style="color: var(--primary); border-color: var(--primary); padding: 10px 24px; font-size: 13px;">
                <i class="fa fa-list mr-2"></i> View Complete Inventory
            </a>
        </div>
    </div>
</section>

<!-- Wholesale Banner -->
<section class="section py-5" style="background: var(--primary);">
    <div class="container text-center py-4">
        <i class="fa fa-briefcase fa-4x mb-4 text-white" style="opacity: 0.8;"></i>
        <h2 class="text-white font-weight-bold mb-3" style="font-size: 2.2rem; letter-spacing: 1px;">ESTABLISH A CORPORATE ACCOUNT</h2>
        <p class="mb-4 mx-auto" style="color: #cbd5e1; max-width: 700px; font-size: 1.15rem;">
            Partner with us to unlock exclusive volume pricing, dedicated procurement agents, and synchronized supply chain logistics for your entire fleet.
        </p>
        @guest
            <a href="{{route('register')}}" class="btn-b2b px-5 py-3"><i class="fa fa-user-plus mr-2"></i> Register Wholesale Account</a>
        @else
            <a href="{{route('contact')}}" class="btn-b2b px-5 py-3"><i class="fa fa-envelope mr-2"></i> Contact Sales Agent</a>
        @endguest
    </div>
</section>

<!-- Lightbox Element -->
<div id="b2b-lightbox" onclick="closeB2BLightbox()">
    <button class="lightbox-close" onclick="closeB2BLightbox()"><i class="fa fa-times"></i></button>
    <img id="lightbox-img" src="" alt="Enlarged Hardware" onclick="event.stopPropagation()">
    <div id="lightbox-caption">Product Title</div>
</div>

@endsection

@push('scripts')
<script>
    // Slider horizontal scrolling
    function scrollSlider(amount) {
        const slider = document.getElementById('trending-slider');
        if (slider) {
            slider.scrollBy({ left: amount, behavior: 'smooth' });
        }
    }

    // Clear search and restore hero
    function clearSearch() {
        var searchInput = document.getElementById('mainSearchInput');
        if (searchInput) { searchInput.value = ''; searchInput.dispatchEvent(new Event('input')); }
    }

    // Lightbox logic
    function openB2BLightbox(imgSrc, title) {
        document.getElementById('lightbox-img').src = imgSrc;
        document.getElementById('lightbox-caption').innerText = title;
        document.getElementById('b2b-lightbox').classList.add('active');
        document.body.style.overflow = 'hidden'; // prevent background scroll
    }

    function closeB2BLightbox() {
        document.getElementById('b2b-lightbox').classList.remove('active');
        document.body.style.overflow = 'auto';
        setTimeout(() => {
            document.getElementById('lightbox-img').src = '';
        }, 300);
    }
</script>
@endpush
