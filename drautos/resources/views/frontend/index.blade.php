@extends('frontend.layouts.master')
@section('title','Danyal Autos Co. || Elite Commercial Auto Parts')

@section('main-content')
<!-- Cyber Dark Styles -->
<style>
    /* Dark Theme Core Elements */
    body, .section {
        background-color: var(--primary) !important;
        color: var(--text-main);
    }
    .bg-soft { background-color: var(--primary-light) !important; }
    
    /* Hero Section */
    .cyber-hero {
        position: relative;
        padding: 140px 0 100px;
        background: radial-gradient(circle at center, var(--primary-light) 0%, var(--primary) 100%);
        overflow: hidden;
        border-bottom: 1px solid rgba(0, 240, 255, 0.1);
    }
    .cyber-hero::before {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background-image: 
            linear-gradient(rgba(0, 240, 255, 0.05) 1px, transparent 1px),
            linear-gradient(90deg, rgba(0, 240, 255, 0.05) 1px, transparent 1px);
        background-size: 30px 30px;
        opacity: 0.5;
        z-index: 0;
    }
    .hero-content {
        position: relative;
        z-index: 1;
        text-align: center;
    }
    .hero-badge {
        display: inline-block;
        padding: 6px 16px;
        background: rgba(0, 240, 255, 0.1);
        border: 1px solid var(--accent);
        color: var(--accent);
        border-radius: 50px;
        font-weight: 600;
        letter-spacing: 2px;
        text-transform: uppercase;
        font-size: 12px;
        margin-bottom: 24px;
        box-shadow: 0 0 15px rgba(0, 240, 255, 0.2);
    }
    .hero-title {
        font-size: 4rem;
        font-weight: 800;
        line-height: 1.1;
        color: #fff;
        margin-bottom: 20px;
    }
    .hero-title .text-gradient {
        background: linear-gradient(90deg, var(--accent), var(--accent-alt));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        text-shadow: 0 0 30px rgba(0, 240, 255, 0.3);
    }
    .hero-subtitle {
        font-size: 1.2rem;
        color: var(--text-muted);
        max-width: 600px;
        margin: 0 auto 40px;
        line-height: 1.6;
    }
    
    /* Buttons */
    .btn-cyber {
        background: transparent;
        color: var(--accent);
        border: 2px solid var(--accent);
        padding: 12px 30px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-radius: 4px;
        transition: all 0.3s ease;
        box-shadow: inset 0 0 0 0 var(--accent);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-cyber:hover {
        box-shadow: inset 0 0 0 40px var(--accent), 0 0 20px rgba(0, 240, 255, 0.4);
        color: #000 !important;
        text-decoration: none;
    }
    .btn-cyber-alt {
        background: rgba(255, 106, 0, 0.1);
        color: var(--accent-alt);
        border: 2px solid var(--accent-alt);
        padding: 12px 30px;
        font-weight: 700;
        text-transform: uppercase;
        border-radius: 4px;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-cyber-alt:hover {
        background: var(--accent-alt);
        color: #fff !important;
        box-shadow: 0 0 20px rgba(255, 106, 0, 0.4);
        text-decoration: none;
    }

    /* Section Titles */
    .cyber-section-title {
        text-align: center;
        margin-bottom: 50px;
    }
    .cyber-section-title h2 {
        font-size: 2.5rem;
        color: #fff;
        margin-bottom: 15px;
        position: relative;
        display: inline-block;
    }
    .cyber-section-title h2::after {
        content: "";
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 3px;
        background: var(--accent);
        box-shadow: 0 0 10px var(--accent);
    }
    .cyber-section-title p {
        color: var(--text-muted);
        font-size: 1.1rem;
    }

    /* Features Grid */
    .cyber-feature-card {
        background: var(--primary-light);
        border: 1px solid rgba(255,255,255,0.05);
        padding: 40px 30px;
        border-radius: 8px;
        text-align: center;
        transition: all 0.4s ease;
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    .cyber-feature-card::before {
        content: "";
        position: absolute;
        top: 0; left: 0; width: 2px; height: 0;
        background: var(--accent);
        transition: height 0.4s ease;
    }
    .cyber-feature-card:hover::before { height: 100%; }
    .cyber-feature-card:hover {
        transform: translateY(-10px);
        background: rgba(22, 32, 50, 0.8);
        box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        border-color: rgba(0, 240, 255, 0.2);
    }
    .cyber-feature-icon {
        font-size: 40px;
        color: var(--accent);
        margin-bottom: 20px;
        text-shadow: 0 0 15px rgba(0, 240, 255, 0.4);
    }
    .cyber-feature-title {
        color: #fff;
        font-size: 1.2rem;
        margin-bottom: 15px;
    }
    .cyber-feature-desc {
        color: var(--text-muted);
        font-size: 0.95rem;
        line-height: 1.6;
    }

    /* "Slide to View" Trending Slider */
    .cyber-slider-wrapper {
        position: relative;
        width: 100%;
        padding: 0 40px;
    }
    .cyber-slider-container {
        display: flex;
        gap: 20px;
        overflow-x: auto;
        scroll-behavior: smooth;
        padding: 20px 0 40px;
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
    .cyber-slider-container::-webkit-scrollbar { display: none; }
    
    .cyber-slider-btn {
        position: absolute;
        top: 45%;
        transform: translateY(-50%);
        width: 40px; height: 40px;
        background: var(--primary-light);
        border: 1px solid var(--accent);
        color: var(--accent);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        z-index: 10;
        transition: all 0.3s ease;
        box-shadow: 0 0 10px rgba(0, 240, 255, 0.2);
    }
    .cyber-slider-btn:hover {
        background: var(--accent);
        color: #000;
        box-shadow: 0 0 20px rgba(0, 240, 255, 0.5);
    }
    .cyber-slider-prev { left: -10px; }
    .cyber-slider-next { right: -10px; }

    /* Product Slide Card */
    .cyber-slide-card {
        flex: 0 0 280px;
        background: var(--primary-light);
        border: 1px solid rgba(255,255,255,0.05);
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.3s ease;
        position: relative;
    }
    .cyber-slide-card:hover {
        border-color: var(--accent-alt);
        box-shadow: 0 10px 30px rgba(255, 106, 0, 0.15);
        transform: translateY(-5px);
    }
    .cyber-slide-img-wrap {
        height: 200px;
        background: #fff;
        display: flex; align-items: center; justify-content: center;
        overflow: hidden;
        position: relative;
        cursor: pointer;
    }
    .cyber-slide-img-wrap img {
        width: 100%; height: 100%;
        object-fit: contain;
        transition: transform 0.4s ease;
    }
    .cyber-slide-card:hover .cyber-slide-img-wrap img {
        transform: scale(1.1);
    }
    /* Enlarge Photo Overlay Button */
    .cyber-enlarge-btn {
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%) scale(0.5);
        width: 50px; height: 50px;
        background: rgba(0,0,0,0.7);
        border: 1px solid var(--accent);
        color: var(--accent);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px;
        opacity: 0;
        transition: all 0.3s ease;
    }
    .cyber-slide-img-wrap:hover .cyber-enlarge-btn {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }

    .cyber-slide-content {
        padding: 20px;
    }
    .cyber-slide-cat {
        font-size: 10px;
        color: var(--accent-alt);
        text-transform: uppercase;
        letter-spacing: 1px;
        display: block; margin-bottom: 5px;
    }
    .cyber-slide-title {
        color: #fff;
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 15px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .cyber-slide-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid rgba(255,255,255,0.05);
        padding-top: 15px;
    }
    .cyber-price {
        color: var(--accent);
        font-weight: 700;
        font-size: 1.1rem;
    }
    .cyber-add-btn {
        background: transparent;
        border: 1px solid var(--accent);
        color: var(--accent);
        border-radius: 4px;
        padding: 6px 12px;
        font-size: 12px;
        text-transform: uppercase;
        font-weight: 700;
        transition: all 0.2s;
    }
    .cyber-add-btn:hover {
        background: var(--accent);
        color: #000;
    }
    
    .cyber-login-badge {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 8px;
        background: rgba(255, 106, 0, 0.1);
        border: 1px dashed var(--accent-alt);
        color: var(--accent-alt);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-radius: 4px;
        text-decoration: none;
        transition: all 0.2s;
    }
    .cyber-login-badge:hover {
        background: var(--accent-alt);
        color: #fff !important;
        text-decoration: none;
    }

    /* Lightbox Overlay */
    #cyber-lightbox {
        position: fixed; inset: 0;
        background: rgba(10, 15, 24, 0.95);
        z-index: 99999;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        opacity: 0; pointer-events: none;
        transition: opacity 0.3s ease;
        backdrop-filter: blur(10px);
    }
    #cyber-lightbox.active {
        opacity: 1; pointer-events: auto;
    }
    .lightbox-close {
        position: absolute; top: 30px; right: 40px;
        background: transparent; border: 1px solid var(--accent);
        color: var(--accent); width: 40px; height: 40px; border-radius: 50%;
        font-size: 20px; display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.2s;
    }
    .lightbox-close:hover { background: var(--accent); color: #000; }
    #lightbox-img {
        max-width: 90vw; max-height: 80vh;
        border-radius: 8px;
        box-shadow: 0 0 50px rgba(0, 240, 255, 0.2);
        border: 1px solid var(--border-color);
        background: #fff;
        object-fit: contain;
    }
    #lightbox-caption {
        color: #fff; font-size: 1.2rem; font-weight: 700; margin-top: 20px;
        text-transform: uppercase; letter-spacing: 1px; text-shadow: 0 0 10px rgba(0,240,255,0.5);
    }
</style>

<!-- Cyber Hero Section -->
<section class="cyber-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-10 mx-auto text-center">
                <div class="hero-content">
                    <span class="hero-badge">Danyal Autos Co. | Elite Tier</span>
                    <h1 class="hero-title">NEXT-GEN <span class="text-gradient">COMMERCIAL PARTS</span></h1>
                    <p class="hero-subtitle">High-performance heavy-duty truck components, precision engineering, and elite logistics tailored for maximum fleet efficiency.</p>
                    
                    <div class="d-flex justify-content-center flex-wrap" style="gap: 20px;">
                        <a href="{{route('product-grids')}}" class="btn-cyber">
                            <i class="fa fa-cogs"></i> Access Catalog
                        </a>
                        @guest
                        <a href="{{route('login')}}" class="btn-cyber-alt">
                            <i class="fa fa-lock"></i> Secure Login for Pricing
                        </a>
                        @else
                        <a href="{{route('user')}}" class="btn-cyber-alt">
                            <i class="fa fa-terminal"></i> Command Center
                        </a>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Cyber Features Section -->
<section class="section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="cyber-feature-card">
                    <div class="cyber-feature-icon"><i class="fa fa-microchip"></i></div>
                    <h3 class="cyber-feature-title">OEM Precision</h3>
                    <p class="cyber-feature-desc">Every part is verified against rigorous factory blueprints to ensure flawless integration and extreme durability under heavy load.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="cyber-feature-card">
                    <div class="cyber-feature-icon"><i class="fa fa-rocket"></i></div>
                    <h3 class="cyber-feature-title">Hyper-Logistics</h3>
                    <p class="cyber-feature-desc">Our distribution network is optimized for zero-downtime fulfillment. Get critical components deployed to your location instantly.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="cyber-feature-card">
                    <div class="cyber-feature-icon"><i class="fa fa-shield"></i></div>
                    <h3 class="cyber-feature-title">Ironclad Warranty</h3>
                    <p class="cyber-feature-desc">Backed by an elite B2B warranty protocol. We stand behind our hardware so you can keep your fleet operational 24/7.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- "Slide to View" Trending Products -->
<section class="section bg-soft py-5" style="border-top: 1px solid rgba(255,255,255,0.05); border-bottom: 1px solid rgba(255,255,255,0.05);">
    <div class="container">
        <div class="cyber-section-title">
            <h2>TOP ACQUISITIONS</h2>
            <p>Slide to view our highest-demand hardware currently in stock.</p>
        </div>
        
        <div class="cyber-slider-wrapper">
            <button class="cyber-slider-btn cyber-slider-prev" onclick="scrollSlider(-300)"><i class="fa fa-chevron-left"></i></button>
            <button class="cyber-slider-btn cyber-slider-next" onclick="scrollSlider(300)"><i class="fa fa-chevron-right"></i></button>
            
            <div class="cyber-slider-container" id="trending-slider">
                @if(isset($product_lists) && count($product_lists) > 0)
                    @foreach($product_lists->take(10) as $product)
                    @php
                        $photos = explode(',',$product->photo);
                        $mainPhoto = $photos[0];
                    @endphp
                    <div class="cyber-slide-card">
                        <!-- Clickable Image Wrap -->
                        <div class="cyber-slide-img-wrap" onclick="openCyberLightbox('{{$mainPhoto}}', '{{addslashes($product->title)}}')">
                            <img src="{{$mainPhoto}}" alt="{{$product->title}}">
                            <div class="cyber-enlarge-btn"><i class="fa fa-search-plus"></i></div>
                        </div>
                        
                        <div class="cyber-slide-content">
                            <span class="cyber-slide-cat">{{$product->cat_info['title'] ?? 'Hardware'}}</span>
                            <a href="{{route('product-detail',$product->slug)}}" class="text-decoration-none">
                                <div class="cyber-slide-title" title="{{$product->title}}">{{$product->title}}</div>
                            </a>
                            
                            <div class="cyber-slide-footer">
                                @auth
                                    @php
                                        $after_discount = ($product->price - ($product->price*$product->discount)/100);
                                    @endphp
                                    <span class="cyber-price">Rs. {{number_format($after_discount,2)}}</span>
                                    <a href="{{route('add-to-cart',$product->slug)}}" class="cyber-add-btn"><i class="fa fa-cart-plus"></i></a>
                                @else
                                    <a href="{{route('login')}}" class="cyber-login-badge">
                                        <i class="fa fa-lock mr-2"></i> Auth Required
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="w-100 text-center py-5 text-muted">
                        <i class="fa fa-database fa-2x mb-3 opacity-50"></i>
                        <p>No telemetry available for top products.</p>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="{{route('product-grids')}}" class="btn-cyber" style="font-size: 12px; padding: 10px 20px;">
                <i class="fa fa-list"></i> VIEW COMPLETE INVENTORY
            </a>
        </div>
    </div>
</section>

<!-- Wholesale Banner -->
<section class="section py-5" style="background: url('https://www.transparenttextures.com/patterns/carbon-fibre.png'), linear-gradient(135deg, var(--primary) 0%, #000 100%);">
    <div class="container text-center border p-5" style="border-color: rgba(0, 240, 255, 0.2) !important; border-radius: 8px; background: rgba(0,0,0,0.4); backdrop-filter: blur(5px);">
        <i class="fa fa-industry fa-4x mb-4 text-white" style="text-shadow: 0 0 20px rgba(255,255,255,0.5);"></i>
        <h2 class="text-white font-weight-bold mb-3" style="font-size: 2.5rem; letter-spacing: 2px;">ESTABLISH A B2B LINK</h2>
        <p class="mb-4 mx-auto" style="color: var(--text-muted); max-width: 600px; font-size: 1.1rem;">
            Initiate a wholesale account protocol to unlock volume pricing, dedicated procurement agents, and synchronized supply chain logistics.
        </p>
        @guest
            <a href="{{route('register')}}" class="btn-cyber-alt px-5 py-3"><i class="fa fa-user-plus"></i> Initialize Account</a>
        @else
            <a href="{{route('contact')}}" class="btn-cyber-alt px-5 py-3"><i class="fa fa-envelope"></i> Contact Liaison</a>
        @endguest
    </div>
</section>

<!-- Lightbox Element -->
<div id="cyber-lightbox" onclick="closeCyberLightbox()">
    <button class="lightbox-close" onclick="closeCyberLightbox()"><i class="fa fa-times"></i></button>
    <img id="lightbox-img" src="" alt="Enlarged Hardware" onclick="event.stopPropagation()">
    <div id="lightbox-caption">Product Title</div>
    <p style="color: var(--text-muted); margin-top: 10px; font-size: 12px; letter-spacing: 1px;">CLICK ANYWHERE TO CLOSE</p>
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

    // Lightbox logic
    function openCyberLightbox(imgSrc, title) {
        document.getElementById('lightbox-img').src = imgSrc;
        document.getElementById('lightbox-caption').innerText = title;
        document.getElementById('cyber-lightbox').classList.add('active');
        document.body.style.overflow = 'hidden'; // prevent background scroll
    }

    function closeCyberLightbox() {
        document.getElementById('cyber-lightbox').classList.remove('active');
        document.body.style.overflow = 'auto';
        setTimeout(() => {
            document.getElementById('lightbox-img').src = '';
        }, 300);
    }
</script>
@endpush
