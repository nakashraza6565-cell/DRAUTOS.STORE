@extends('frontend.layouts.master')
@section('title','Danyal Autos Co. || HOME PAGE')
@section('main-content')
<!-- 3D WebGL Showroom Hero Section -->
<section id="chassis-3d-showroom">
    <!-- Sci-fi blueprint grid -->
    <div class="showroom-grid-overlay"></div>
    
    <!-- Tech HUD Left Panel -->
    <div class="hud-panel hud-left">
        <div class="hud-title">
            <span class="blink-dot"></span>Chassis Scan Active
        </div>
        <div class="hud-metric">
            <span class="hud-label">Vehicle Type:</span>
            <span class="hud-value">Heavy Truck</span>
        </div>
        <div class="hud-metric">
            <span class="hud-label">Drivetrain:</span>
            <span class="hud-value">6x4 Tandem</span>
        </div>
        <div class="hud-metric">
            <span class="hud-label">Chassis Status:</span>
            <span class="hud-value text-success" style="color: #22c55e !important; font-weight: 800;">OPTIMAL</span>
        </div>
        <div class="hud-metric">
            <span class="hud-label">Scan Precision:</span>
            <span class="hud-value">100%</span>
        </div>
    </div>
    
    <!-- Tech HUD Right Panel -->
    <div class="hud-panel hud-right">
        <div class="hud-title">
            <span class="blink-dot" style="background-color: #f97316;"></span>Telemetry Diagnostics
        </div>
        <div class="hud-metric flex-column align-items-stretch">
            <div class="d-flex justify-content-between w-100">
                <span class="hud-label">Engine Core:</span>
                <span class="hud-value" id="hud-engine">98.4%</span>
            </div>
            <canvas class="telemetry-sparkline" id="sparkline-engine"></canvas>
        </div>
        <div class="hud-metric flex-column align-items-stretch">
            <div class="d-flex justify-content-between w-100">
                <span class="hud-label">Clutch Ratio:</span>
                <span class="hud-value" id="hud-clutch">1.00</span>
            </div>
            <canvas class="telemetry-sparkline" id="sparkline-clutch"></canvas>
        </div>
        <div class="hud-metric flex-column align-items-stretch">
            <div class="d-flex justify-content-between w-100">
                <span class="hud-label">Brakes Pad:</span>
                <span class="hud-value" id="hud-brakes">96.8%</span>
            </div>
            <canvas class="telemetry-sparkline" id="sparkline-brakes"></canvas>
        </div>
        <div class="hud-metric flex-column align-items-stretch">
            <div class="d-flex justify-content-between w-100">
                <span class="hud-label">Suspension Load:</span>
                <span class="hud-value" id="hud-suspension">12.5 kN</span>
            </div>
            <canvas class="telemetry-sparkline" id="sparkline-suspension"></canvas>
        </div>
    </div>
    
    <!-- Interactive Projected Hotspots -->
    @php
        // Auto-match categories by searching titles or slugs
        $engineCat = $category_lists->first(function($c) { return Str::contains(Str::lower($c->slug), ['engine', 'automizer', 'filter']); });
        $clutchCat = $category_lists->first(function($c) { return Str::contains(Str::lower($c->slug), ['clutch', 'gear']); });
        $brakeCat = $category_lists->first(function($c) { return Str::contains(Str::lower($c->slug), ['brake', 'hub']); });
        $suspensionCat = $category_lists->first(function($c) { return Str::contains(Str::lower($c->slug), ['suspension', 'spring', 'axle']); });
        
        // Fallbacks if not matched
        $firstFourCats = $category_lists->take(4)->values();
        $engineId = $engineCat->id ?? ($firstFourCats[0]->id ?? 0);
        $clutchId = $clutchCat->id ?? ($firstFourCats[1]->id ?? 0);
        $brakeId = $brakeCat->id ?? ($firstFourCats[2]->id ?? 0);
        $suspensionId = $suspensionCat->id ?? ($firstFourCats[3]->id ?? 0);

        $engineTitle = $engineCat->title ?? ($firstFourCats[0]->title ?? 'Engine Block');
        $clutchTitle = $clutchCat->title ?? ($firstFourCats[1]->title ?? 'Clutch Parts');
        $brakeTitle = $brakeCat->title ?? ($firstFourCats[2]->title ?? 'Brake Systems');
        $suspensionTitle = $suspensionCat->title ?? ($firstFourCats[3]->title ?? 'Suspension & Axles');
    @endphp

    <!-- Hotspot 1: Engine -->
    <div class="tech-hotspot" id="hotspot-engine" data-cat-id="{{ $engineId }}" data-cat-title="{{ $engineTitle }}" style="display: none;">
        <div class="hotspot-ring">
            <div class="hotspot-dot"></div>
            <div class="hotspot-pulse"></div>
        </div>
        <div class="hotspot-tooltip">
            <span class="tooltip-title">{{ $engineTitle }}</span>
            <span class="tooltip-desc">Power Unit & Filters</span>
        </div>
    </div>
    
    <!-- Hotspot 2: Clutch / Gearbox -->
    <div class="tech-hotspot" id="hotspot-clutch" data-cat-id="{{ $clutchId }}" data-cat-title="{{ $clutchTitle }}" style="display: none;">
        <div class="hotspot-ring">
            <div class="hotspot-dot"></div>
            <div class="hotspot-pulse"></div>
        </div>
        <div class="hotspot-tooltip">
            <span class="tooltip-title">{{ $clutchTitle }}</span>
            <span class="tooltip-desc">Transmission & Gears</span>
        </div>
    </div>
    
    <!-- Hotspot 3: Brakes -->
    <div class="tech-hotspot" id="hotspot-brakes" data-cat-id="{{ $brakeId }}" data-cat-title="{{ $brakeTitle }}" style="display: none;">
        <div class="hotspot-ring">
            <div class="hotspot-dot"></div>
            <div class="hotspot-pulse"></div>
        </div>
        <div class="hotspot-tooltip">
            <span class="tooltip-title">{{ $brakeTitle }}</span>
            <span class="tooltip-desc">Rotors, Calipers & Hubs</span>
        </div>
    </div>
    
    <!-- Hotspot 4: Suspension -->
    <div class="tech-hotspot" id="hotspot-suspension" data-cat-id="{{ $suspensionId }}" data-cat-title="{{ $suspensionTitle }}" style="display: none;">
        <div class="hotspot-ring">
            <div class="hotspot-dot"></div>
            <div class="hotspot-pulse"></div>
        </div>
        <div class="hotspot-tooltip">
            <span class="tooltip-title">{{ $suspensionTitle }}</span>
            <span class="tooltip-desc">Leaf Springs & Shocks</span>
        </div>
    </div>

    <!-- Canvas -->
    <canvas id="chassis-canvas"></canvas>
    
    <!-- HUD Info -->
    <div class="hud-controls-info">
        <i class="fa fa-info-circle mr-1"></i> Left-Click + Drag to rotate. Mousewheel to zoom. Click glowing rings to explore parts.
    </div>
</section>

<!-- Glassmorphism Drawer Overlay -->
<div id="parts-side-drawer">
    <div class="drawer-header">
        <h3 class="drawer-title" style="color: #fff !important;"><i class="fa fa-cogs"></i> <span id="drawer-category-name">Parts</span></h3>
        <button class="drawer-close" id="drawer-close-btn">&times;</button>
    </div>
    <div class="drawer-content" id="drawer-parts-container">
        <!-- Dyn list will populate here -->
    </div>
</div>

<!-- Start Small Banner  -->
<section class="small-banner section">
    <div class="container-fluid">
        <div class="row">
            @php
            $category_lists=DB::table('categories')->where('status','active')->limit(3)->get();
            @endphp
            @if($category_lists)
                @foreach($category_lists as $cat)
                    @if($cat->is_parent==1)
                        <!-- Single Banner  -->
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="single-banner">
                                @if($cat->photo)
                                    <img src="{{$cat->photo}}" alt="{{$cat->photo}}">
                                @else
                                    <img src="https://via.placeholder.com/600x370" alt="#">
                                @endif
                                <div class="content">
                                    <h3>{{$cat->title}}</h3>
                                        <a href="{{route('product-cat',$cat->slug)}}">Discover Now</a>
                                </div>
                            </div>
                        </div>
                    @endif
                    <!-- /End Single Banner  -->
                @endforeach
            @endif
        </div>
    </div>
</section>
<!-- End Small Banner -->

<!-- Start Product Area -->
<div class="product-area section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section-title">
                        <h2>Trending Item</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="product-info">
                        <div class="nav-main">
                            <!-- Tab Nav -->
                            <ul class="nav nav-tabs filter-tope-group" id="myTab" role="tablist">
                                @php
                                    $categories=DB::table('categories')->where('status','active')->where('is_parent',1)->get();
                                    // dd($categories);
                                @endphp
                                @if($categories)
                                <button class="btn" style="background:black"data-filter="*">
                                    All Products
                                </button>
                                    @foreach($categories as $key=>$cat)

                                    <button class="btn" style="background:none;color:black;"data-filter=".{{$cat->id}}">
                                        {{$cat->title}}
                                    </button>
                                    @endforeach
                                @endif
                            </ul>
                            <!--/ End Tab Nav -->
                        </div>
                        <div class="tab-content isotope-grid" id="myTabContent">
                             <!-- Start Single Tab -->
                            @if($product_lists)
                                @foreach($product_lists as $key=>$product)
                                <div class="col-sm-6 col-md-4 col-lg-3 p-b-35 isotope-item {{$product->cat_id}}">
                                    <div class="single-product">
                                        <div class="product-img">
                                            <a href="{{route('product-detail',$product->slug)}}">
                                                @php
                                                    $photo=explode(',',$product->photo);
                                                // dd($photo);
                                                @endphp
                                                <img class="default-img" src="{{$photo[0]}}" alt="{{$photo[0]}}">
                                                <img class="hover-img" src="{{$photo[0]}}" alt="{{$photo[0]}}">
                                                @if($product->stock<=0)
                                                    <span class="out-of-stock">Sale out</span>
                                                @elseif($product->condition=='new')
                                                    <span class="new">New</span
                                                @elseif($product->condition=='hot')
                                                    <span class="hot">Hot</span>
                                                @else
                                                    <span class="price-dec">{{$product->discount}}% Off</span>
                                                @endif


                                            </a>
                                            <div class="button-head">
                                                <div class="product-action">
                                                    <a data-toggle="modal" data-target="#{{$product->id}}" title="Quick View" href="#"><i class=" ti-eye"></i><span>Quick Shop</span></a>
                                                    <a title="Wishlist" href="{{route('add-to-wishlist',$product->slug)}}" ><i class=" ti-heart "></i><span>Add to Wishlist</span></a>
                                                </div>
                                                <div class="product-action-2">
                                                    <a title="Add to cart" href="{{route('add-to-cart',$product->slug)}}">Add to cart</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="product-content">
                                            <h3><a href="{{route('product-detail',$product->slug)}}">{{$product->title}}</a></h3>
                                            @auth
                                            <div class="product-price">
                                                @php
                                                    $after_discount=($product->price-($product->price*$product->discount)/100);
                                                @endphp
                                                <span>${{number_format($after_discount,2)}}</span>
                                                <del style="padding-left:4%;">${{number_format($product->price,2)}}</del>
                                            </div>
                                            @else
                                            <div class="product-price">
                                                <span>Price: <a href="{{route('login')}}" style="color: #f59e0b;">Login to see</a></span>
                                            </div>
                                            @endauth
                                        </div>
                                    </div>
                                </div>
                                @endforeach

                             <!--/ End Single Tab -->
                            @endif

                        <!--/ End Single Tab -->

                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
<!-- End Product Area -->
{{-- @php
    $featured=DB::table('products')->where('is_featured',1)->where('status','active')->orderBy('id','DESC')->limit(1)->get();
@endphp --}}
<!-- Start Midium Banner  -->
<section class="midium-banner">
    <div class="container">
        <div class="row">
            @if($featured)
                @foreach($featured as $data)
                    <!-- Single Banner  -->
                    <div class="col-lg-6 col-md-6 col-12">
                        <div class="single-banner">
                            @php
                                $photo=explode(',',$data->photo);
                            @endphp
                            <img src="{{$photo[0]}}" alt="{{$photo[0]}}">
                            <div class="content">
                                <p>{{$data->cat_info->title ?? 'N/A'}}</p>
                                <h3>{{$data->title}} <br>Up to<span> {{$data->discount}}%</span></h3>
                                <a href="{{route('product-detail',$data->slug)}}">Shop Now</a>
                            </div>
                        </div>
                    </div>
                    <!-- /End Single Banner  -->
                @endforeach
            @endif
        </div>
    </div>
</section>
<!-- End Midium Banner -->

<!-- Start Most Popular -->
<div class="product-area most-popular section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section-title">
                    <h2>Hot Item</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="owl-carousel popular-slider">
                    @foreach($product_lists as $product)
                        @if($product->condition=='hot')
                            <!-- Start Single Product -->
                        <div class="single-product">
                            <div class="product-img">
                                <a href="{{route('product-detail',$product->slug)}}">
                                    @php
                                        $photo=explode(',',$product->photo);
                                    // dd($photo);
                                    @endphp
                                    <img class="default-img" src="{{$photo[0]}}" alt="{{$photo[0]}}">
                                    <img class="hover-img" src="{{$photo[0]}}" alt="{{$photo[0]}}">
                                    {{-- <span class="out-of-stock">Hot</span> --}}
                                </a>
                                <div class="button-head">
                                    <div class="product-action">
                                        <a data-toggle="modal" data-target="#{{$product->id}}" title="Quick View" href="#"><i class=" ti-eye"></i><span>Quick Shop</span></a>
                                        <a title="Wishlist" href="{{route('add-to-wishlist',$product->slug)}}" ><i class=" ti-heart "></i><span>Add to Wishlist</span></a>
                                    </div>
                                    <div class="product-action-2">
                                        <a href="{{route('add-to-cart',$product->slug)}}">Add to cart</a>
                                    </div>
                                </div>
                            </div>
                            <div class="product-content">
                                <h3><a href="{{route('product-detail',$product->slug)}}">{{$product->title}}</a></h3>
                                @auth
                                <div class="product-price">
                                    <span class="old">${{number_format($product->price,2)}}</span>
                                    @php
                                    $after_discount=($product->price-($product->price*$product->discount)/100)
                                    @endphp
                                    <span>${{number_format($after_discount,2)}}</span>
                                </div>
                                @else
                                <div class="product-price">
                                    <span>Price: <a href="{{route('login')}}" style="color: #f59e0b;">Login to see</a></span>
                                </div>
                                @endauth
                            </div>
                        </div>
                        <!-- End Single Product -->
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Most Popular Area -->

<!-- Start Shop Home List  -->
<section class="shop-home-list section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-12">
                <div class="row">
                    <div class="col-12">
                        <div class="shop-section-title">
                            <h1>Latest Items</h1>
                        </div>
                    </div>
                </div>
                <div class="row">
                    @php
                        $product_lists=DB::table('products')->where('status','active')->orderBy('id','DESC')->limit(6)->get();
                    @endphp
                    @foreach($product_lists as $product)
                        <div class="col-md-4">
                            <!-- Start Single List  -->
                            <div class="single-list">
                                <div class="row">
                                <div class="col-lg-6 col-md-6 col-12">
                                    <div class="list-image overlay">
                                        @php
                                            $photo=explode(',',$product->photo);
                                            // dd($photo);
                                        @endphp
                                        <img src="{{$photo[0]}}" alt="{{$photo[0]}}">
                                        <a href="{{route('add-to-cart',$product->slug)}}" class="buy"><i class="fa fa-shopping-bag"></i></a>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-12 no-padding">
                                    <div class="content">
                                        <h4 class="title"><a href="#">{{$product->title}}</a></h4>
                                        @auth
                                        <p class="price with-discount">${{number_format($product->discount,2)}}</p>
                                        @else
                                        <p class="price">Price: <a href="{{route('login')}}" style="color: #f59e0b;">Login</a></p>
                                        @endauth
                                    </div>
                                </div>
                                </div>
                            </div>
                            <!-- End Single List  -->
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
</section>
<!-- End Shop Home List  -->

<!-- Start Shop Blog  -->

<!-- Start Shop Services Area -->
<section class="shop-services section home py-5" style="background: #f8fafc;">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 col-12">
                <!-- Start Single Service -->
                <div class="single-service d-flex align-items-center p-3 rounded" style="background: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.03); transition: transform 0.3s ease;">
                    <i class="ti-rocket mr-3" style="font-size: 30px; color: #f59e0b;"></i>
                    <div>
                        <h4 class="mb-0" style="font-size: 16px; font-weight: 800;">Free Shipping</h4>
                        <p class="mb-0 small text-muted">Orders over $100</p>
                    </div>
                </div>
                <!-- End Single Service -->
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <!-- Start Single Service -->
                <div class="single-service d-flex align-items-center p-3 rounded" style="background: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.03); transition: transform 0.3s ease;">
                    <i class="ti-reload mr-3" style="font-size: 30px; color: #f59e0b;"></i>
                    <div>
                        <h4 class="mb-0" style="font-size: 16px; font-weight: 800;">Free Return</h4>
                        <p class="mb-0 small text-muted">Within 30 days returns</p>
                    </div>
                </div>
                <!-- End Single Service -->
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <!-- Start Single Service -->
                <div class="single-service d-flex align-items-center p-3 rounded" style="background: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.03); transition: transform 0.3s ease;">
                    <i class="ti-lock mr-3" style="font-size: 30px; color: #f59e0b;"></i>
                    <div>
                        <h4 class="mb-0" style="font-size: 16px; font-weight: 800;">Secure Payment</h4>
                        <p class="mb-0 small text-muted">100% secure payment</p>
                    </div>
                </div>
                <!-- End Single Service -->
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <!-- Start Single Service -->
                <div class="single-service d-flex align-items-center p-3 rounded" style="background: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.03); transition: transform 0.3s ease;">
                    <i class="ti-tag mr-3" style="font-size: 30px; color: #f59e0b;"></i>
                    <div>
                        <h4 class="mb-0" style="font-size: 16px; font-weight: 800;">Best Price</h4>
                        <p class="mb-0 small text-muted">Guaranteed price</p>
                    </div>
                </div>
                <!-- End Single Service -->
            </div>
        </div>
    </div>
</section>
<!-- End Shop Services Area -->


<!-- Modal -->
@if($product_lists)
    @foreach($product_lists as $key=>$product)
        <div class="modal fade" id="{{$product->id}}" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span class="ti-close" aria-hidden="true"></span></button>
                        </div>
                        <div class="modal-body">
                            <div class="row no-gutters">
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                    <!-- Product Slider -->
                                        <div class="product-gallery">
                                            <div class="quickview-slider-active">
                                                @php
                                                    $photo=explode(',',$product->photo);
                                                // dd($photo);
                                                @endphp
                                                @foreach($photo as $data)
                                                    <div class="single-slider">
                                                        <img src="{{$data}}" alt="{{$data}}">
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    <!-- End Product slider -->
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                    <div class="quickview-content">
                                        <h2>{{$product->title}}</h2>
                                        <div class="quickview-ratting-review">
                                            <div class="quickview-ratting-wrap">
                                                <div class="quickview-ratting">
                                                    {{-- <i class="yellow fa fa-star"></i>
                                                    <i class="yellow fa fa-star"></i>
                                                    <i class="yellow fa fa-star"></i>
                                                    <i class="yellow fa fa-star"></i>
                                                    <i class="fa fa-star"></i> --}}
                                                    @php
                                                        $rate=DB::table('product_reviews')->where('product_id',$product->id)->avg('rate');
                                                        $rate_count=DB::table('product_reviews')->where('product_id',$product->id)->count();
                                                    @endphp
                                                    @for($i=1; $i<=5; $i++)
                                                        @if($rate>=$i)
                                                            <i class="yellow fa fa-star"></i>
                                                        @else
                                                        <i class="fa fa-star"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <a href="#"> ({{$rate_count}} customer review)</a>
                                            </div>
                                            <div class="quickview-stock">
                                                @if($product->stock >0)
                                                <span><i class="fa fa-check-circle-o"></i> {{$product->stock}} in stock</span>
                                                @else
                                                <span><i class="fa fa-times-circle-o text-danger"></i> {{$product->stock}} out stock</span>
                                                @endif
                                            </div>
                                        </div>
                                        @php
                                            $after_discount=($product->price-($product->price*$product->discount)/100);
                                        @endphp
                                        @auth
                                        <h3><small><del class="text-muted">${{number_format($product->price,2)}}</del></small>    ${{number_format($after_discount,2)}}  </h3>
                                        @else
                                        <h3>Price: <a href="{{route('login')}}" style="color: #f59e0b;">Login to see</a></h3>
                                        @endauth
                                        <div class="quickview-peragraph">
                                            <p>{!! html_entity_decode($product->summary) !!}</p>
                                        </div>
                                        @if($product->size)
                                            <div class="size">
                                                <div class="row">
                                                    <div class="col-lg-6 col-12">
                                                        <h5 class="title">Size</h5>
                                                        <select>
                                                            @php
                                                            $sizes=explode(',',$product->size);
                                                            // dd($sizes);
                                                            @endphp
                                                            @foreach($sizes as $size)
                                                                <option>{{$size}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    {{-- <div class="col-lg-6 col-12">
                                                        <h5 class="title">Color</h5>
                                                        <select>
                                                            <option selected="selected">orange</option>
                                                            <option>purple</option>
                                                            <option>black</option>
                                                            <option>pink</option>
                                                        </select>
                                                    </div> --}}
                                                </div>
                                            </div>
                                        @endif
                                        <form action="{{route('single-add-to-cart')}}" method="POST" class="mt-4">
                                            @csrf
                                            <div class="quantity">
                                                <!-- Input Order -->
                                                <div class="input-group">
                                                    <div class="button minus">
                                                        <button type="button" class="btn btn-primary btn-number" disabled="disabled" data-type="minus" data-field="quant[1]">
                                                            <i class="ti-minus"></i>
                                                        </button>
                                                    </div>
													<input type="hidden" name="slug" value="{{$product->slug}}">
                                                    <input type="text" name="quant[1]" class="input-number"  data-min="1" data-max="1000" value="1">
                                                    <div class="button plus">
                                                        <button type="button" class="btn btn-primary btn-number" data-type="plus" data-field="quant[1]">
                                                            <i class="ti-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <!--/ End Input Order -->
                                            </div>
                                            <div class="add-to-cart">
                                                <button type="submit" class="btn">Add to cart</button>
                                                <a href="{{route('add-to-wishlist',$product->slug)}}" class="btn min"><i class="ti-heart"></i></a>
                                            </div>
                                        </form>
                                        <div class="default-social">
                                        <!-- ShareThis BEGIN --><div class="sharethis-inline-share-buttons"></div><!-- ShareThis END -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    @endforeach
@endif
<!-- Modal end -->
@endsection

@push('styles')

    <style>
        /* Banner Sliding */
        #Gslider .carousel-inner {
        background: #000000;
        color:black;
        }

        #Gslider .carousel-inner{
        height: 550px;
        }
        #Gslider .carousel-inner img{
            width: 100% !important;
            opacity: .8;
        }

        #Gslider .carousel-inner .carousel-caption {
        bottom: 60%;
        }

        #Gslider .carousel-inner .carousel-caption h1 {
        font-size: 50px;
        font-weight: bold;
        line-height: 100%;
        color: #f59e0b;
        }

        #Gslider .carousel-inner .carousel-caption p {
        font-size: 18px;
        color: black;
        margin: 28px 0 28px 0;
        }

        #Gslider .carousel-indicators {
        bottom: 70px;
        }
    </style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script>
        /*==================================================================
        [ Isotope ]*/
        var $topeContainer = $('.isotope-grid');
        var $filter = $('.filter-tope-group');

        // filter items on button click
        $filter.each(function () {
            $filter.on('click', 'button', function () {
                var filterValue = $(this).attr('data-filter');
                $topeContainer.isotope({filter: filterValue});
            });

        });

        // init Isotope
        $(window).on('load', function () {
            var $grid = $topeContainer.each(function () {
                $(this).isotope({
                    itemSelector: '.isotope-item',
                    layoutMode: 'fitRows',
                    percentPosition: true,
                    animationEngine : 'best-available',
                    masonry: {
                        columnWidth: '.isotope-item'
                    }
                });
            });
        });

        var isotopeButton = $('.filter-tope-group button');

        $(isotopeButton).each(function(){
            $(this).on('click', function(){
                for(var i=0; i<isotopeButton.length; i++) {
                    $(isotopeButton[i]).removeClass('how-active1');
                }

                $(this).addClass('how-active1');
            });
        });
    </script>
    <script>
         function cancelFullScreen(el) {
            var requestMethod = el.cancelFullScreen||el.webkitCancelFullScreen||el.mozCancelFullScreen||el.exitFullscreen;
            if (requestMethod) { // cancel full screen.
                requestMethod.call(el);
            } else if (typeof window.ActiveXObject !== "undefined") { // Older IE.
                var wscript = new ActiveXObject("WScript.Shell");
                if (wscript !== null) {
                    wscript.SendKeys("{F11}");
                }
            }
        }

        function requestFullScreen(el) {
            // Supports most browsers and their versions.
            var requestMethod = el.requestFullScreen || el.webkitRequestFullScreen || el.mozRequestFullScreen || el.msRequestFullscreen;

            if (requestMethod) { // Native full screen.
                requestMethod.call(el);
            } else if (typeof window.ActiveXObject !== "undefined") { // Older IE.
                var wscript = new ActiveXObject("WScript.Shell");
                if (wscript !== null) {
                    wscript.SendKeys("{F11}");
                }
            }
            return false
        }
    </script>

    <!-- 3D WebGL Showroom Engine Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof THREE === 'undefined') {
                console.error('Three.js is not loaded.');
                return;
            }

            const container = document.getElementById('chassis-3d-showroom');
            const canvas = document.getElementById('chassis-canvas');
            
            // =================================================================
            // 1. SCENE SETUP & SCI-FI ENVIRONMENT
            // =================================================================
            const scene = new THREE.Scene();
            scene.fog = new THREE.FogExp2(0x020617, 0.02);

            // Camera Viewport
            const camera = new THREE.PerspectiveCamera(40, container.clientWidth / container.clientHeight, 0.1, 1000);
            camera.position.set(-9.5, 3.8, 10.5);

            // WebGL Renderer
            const renderer = new THREE.WebGLRenderer({ canvas: canvas, antialias: true, alpha: false });
            renderer.setSize(container.clientWidth, container.clientHeight);
            renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
            renderer.setClearColor(0x020617, 1);
            renderer.shadowMap.enabled = true;
            renderer.shadowMap.type = THREE.PCFSoftShadowMap;

            // Orbit Controls with damping
            const controls = new THREE.OrbitControls(camera, renderer.domElement);
            controls.enableDamping = true;
            controls.dampingFactor = 0.05;
            controls.maxPolarAngle = Math.PI / 2 - 0.04; // Floor lock
            controls.minDistance = 3.5;
            controls.maxDistance = 18;
            controls.target.set(0, 0.4, 0);

            // Ambient Light
            const ambientLight = new THREE.AmbientLight(0x0d1527, 2.0);
            scene.add(ambientLight);

            // Main Directional Light
            const mainLight = new THREE.DirectionalLight(0xffffff, 2.5);
            mainLight.position.set(8, 20, 8);
            mainLight.castShadow = true;
            mainLight.shadow.mapSize.width = 2048;
            mainLight.shadow.mapSize.height = 2048;
            scene.add(mainLight);

            // Volumetric projector lights (Cyan & Orange spot flares)
            const orangeSpot = new THREE.SpotLight(0xf97316, 5, 25, Math.PI / 6, 0.5, 1);
            orangeSpot.position.set(-4, 6, 4);
            orangeSpot.target.position.set(-3.5, 0.5, 0);
            scene.add(orangeSpot);
            scene.add(orangeSpot.target);

            const cyanSpot = new THREE.SpotLight(0x00f5ff, 4, 25, Math.PI / 5, 0.4, 1);
            cyanSpot.position.set(4, 5, -4);
            cyanSpot.target.position.set(2.5, -0.2, 0);
            scene.add(cyanSpot);
            scene.add(cyanSpot.target);

            // Shadow receiving floor plane
            const floorGeo = new THREE.PlaneGeometry(150, 150);
            const floorMat = new THREE.ShadowMaterial({ opacity: 0.5 });
            const floor = new THREE.Mesh(floorGeo, floorMat);
            floor.rotation.x = -Math.PI / 2;
            floor.position.y = -1.2;
            floor.receiveShadow = true;
            scene.add(floor);

            // Sci-fi Blueprint Hangar Grid Helper
            const gridHelper = new THREE.GridHelper(50, 50, 0x6366f1, 0x111b2d);
            gridHelper.position.y = -1.19;
            gridHelper.material.opacity = 0.35;
            gridHelper.material.transparent = true;
            scene.add(gridHelper);

            // Concentric sci-fi scanning rings under the chassis
            const ringGroup = new THREE.Group();
            ringGroup.position.y = -1.18;
            const ringColors = [0x00f5ff, 0xf97316, 0x06b6d4];
            for (let i = 0; i < 3; i++) {
                const ringGeo = new THREE.RingGeometry(2.5 + i * 1.8, 2.52 + i * 1.8, 64);
                const ringMat = new THREE.MeshBasicMaterial({
                    color: ringColors[i],
                    side: THREE.DoubleSide,
                    transparent: true,
                    opacity: 0.25 - i * 0.05
                });
                const rMesh = new THREE.Mesh(ringGeo, ringMat);
                rMesh.rotation.x = Math.PI / 2;
                ringGroup.add(rMesh);
            }
            scene.add(ringGroup);

            // =================================================================
            // 2. REAL-TIME DATA & SPARKLINE CONTROLLERS
            // =================================================================
            const sparklineData = {
                engine: Array(25).fill(98.4),
                clutch: Array(25).fill(1.00),
                brakes: Array(25).fill(96.8),
                suspension: Array(25).fill(12.5)
            };

            function drawSparkline(canvasId, data, colorHex) {
                const c = document.getElementById(canvasId);
                if (!c) return;
                const ctx = c.getContext('2d');
                const w = c.width = c.offsetWidth;
                const h = c.height = c.offsetHeight;

                ctx.clearRect(0, 0, w, h);
                ctx.beginPath();

                const step = w / (data.length - 1);
                const min = Math.min(...data) - 0.05;
                const max = Math.max(...data) + 0.05;
                const range = (max - min) || 1;

                for (let i = 0; i < data.length; i++) {
                    const x = i * step;
                    const y = h - ((data[i] - min) / range) * (h - 6) - 3;
                    if (i === 0) ctx.moveTo(x, y);
                    else ctx.lineTo(x, y);
                }
                ctx.strokeStyle = colorHex;
                ctx.lineWidth = 1.8;
                ctx.lineCap = 'round';
                ctx.lineJoin = 'round';
                ctx.stroke();

                // Semi-transparent gradient fill
                ctx.lineTo(w, h);
                ctx.lineTo(0, h);
                ctx.fillStyle = colorHex.replace(')', ', 0.07)').replace('rgb', 'rgba');
                ctx.fill();
            }

            // =================================================================
            // 3. DETAILED PROCEDURAL CYBERNETIC TRUCK ASSEMBLY
            // =================================================================
            const chassisGroup = new THREE.Group();

            // Glowing Cyber-Blueprint Edge Overlay Helper
            function addTechEdges(parentMesh, colorHex, opacity = 0.5) {
                const edgesGeo = new THREE.EdgesGeometry(parentMesh.geometry);
                const lineMat = new THREE.LineBasicMaterial({
                    color: colorHex,
                    transparent: true,
                    opacity: opacity,
                    blending: THREE.AdditiveBlending
                });
                const lineMesh = new THREE.LineSegments(edgesGeo, lineMat);
                parentMesh.add(lineMesh);
            }

            // Material Presets
            const carbonBodyMat = new THREE.MeshPhysicalMaterial({
                color: 0x0a101f,
                metalness: 0.95,
                roughness: 0.12,
                clearcoat: 1.0,
                clearcoatRoughness: 0.05,
                transmission: 0.15,
                thickness: 0.4
            });
            const reflectiveChromeMat = new THREE.MeshStandardMaterial({
                color: 0xf3f4f6,
                metalness: 1.0,
                roughness: 0.03
            });
            const glowingCyanMat = new THREE.MeshStandardMaterial({
                color: 0x020617,
                emissive: 0x00f5ff,
                emissiveIntensity: 2.5,
                roughness: 0.2
            });
            const glowingOrangeMat = new THREE.MeshStandardMaterial({
                color: 0x020617,
                emissive: 0xf97316,
                emissiveIntensity: 2.2,
                roughness: 0.2
            });
            const translucentWindshieldMat = new THREE.MeshPhysicalMaterial({
                color: 0x06b6d4,
                transparent: true,
                opacity: 0.4,
                roughness: 0.05,
                transmission: 0.9,
                thickness: 0.3
            });
            const cyberRubberMat = new THREE.MeshStandardMaterial({
                color: 0x090c15,
                roughness: 0.82,
                metalness: 0.08
            });

            // -----------------------------------------------------------------
            // A. CHASSIS RAILS & DOUBLE FRAME
            // -----------------------------------------------------------------
            const railGeo = new THREE.BoxGeometry(9.8, 0.32, 0.15);
            const leftRail = new THREE.Mesh(railGeo, carbonBodyMat);
            leftRail.position.set(0.1, 0.4, 0.72);
            leftRail.castShadow = true;
            addTechEdges(leftRail, 0x00f5ff, 0.45);
            chassisGroup.add(leftRail);

            const rightRail = leftRail.clone();
            rightRail.position.z = -0.72;
            chassisGroup.add(rightRail);

            // Extruded structural cross support beams
            for (let i = -4.4; i <= 4.4; i += 1.4) {
                const braceGeo = new THREE.BoxGeometry(0.18, 0.22, 1.28);
                const brace = new THREE.Mesh(braceGeo, carbonBodyMat);
                brace.position.set(i, 0.4, 0);
                brace.castShadow = true;
                addTechEdges(brace, 0x00f5ff, 0.35);
                chassisGroup.add(brace);
            }

            // Tech conduits underneath rails (Neon conduits)
            const pipeGeo = new THREE.CylinderGeometry(0.03, 0.03, 9.4, 8);
            const leftConduit = new THREE.Mesh(pipeGeo, glowingCyanMat);
            leftConduit.rotation.z = Math.PI / 2;
            leftConduit.position.set(0.1, 0.18, 0.55);
            chassisGroup.add(leftConduit);

            const rightConduit = leftConduit.clone();
            rightConduit.position.z = -0.55;
            rightConduit.material = glowingOrangeMat;
            chassisGroup.add(rightConduit);

            // Front protective heavy chrome bumper with mesh guards
            const bumperGroup = new THREE.Group();
            bumperGroup.position.set(-4.85, 0.25, 0);

            const bumperBase = new THREE.Mesh(new THREE.BoxGeometry(0.32, 0.42, 2.55), reflectiveChromeMat);
            bumperBase.castShadow = true;
            addTechEdges(bumperBase, 0x00f5ff, 0.6);
            bumperGroup.add(bumperBase);

            const guardBarGeo = new THREE.CylinderGeometry(0.04, 0.04, 2.3, 12);
            const lowerGuard = new THREE.Mesh(guardBarGeo, reflectiveChromeMat);
            lowerGuard.rotation.x = Math.PI / 2;
            lowerGuard.position.set(-0.15, 0.22, 0);
            bumperGroup.add(lowerGuard);

            const upperGuard = lowerGuard.clone();
            upperGuard.position.y = 0.45;
            bumperGroup.add(upperGuard);

            chassisGroup.add(bumperGroup);

            // -----------------------------------------------------------------
            // B. SLEEK AERODYNAMIC HIGH-TECH CABIN
            // -----------------------------------------------------------------
            const cabinGroup = new THREE.Group();

            // Lower Cab shell
            const cabLower = new THREE.Mesh(new THREE.BoxGeometry(2.35, 1.35, 2.05), carbonBodyMat);
            cabLower.position.set(-2.2, 1.15, 0);
            cabLower.castShadow = true;
            addTechEdges(cabLower, 0x00f5ff, 0.4);
            cabinGroup.add(cabLower);

            // Sleeper compartment top wedge
            const cabSleeper = new THREE.Mesh(new THREE.BoxGeometry(2.15, 1.25, 2.02), carbonBodyMat);
            cabSleeper.position.set(-2.1, 2.45, 0);
            cabSleeper.castShadow = true;
            addTechEdges(cabSleeper, 0x00f5ff, 0.4);
            cabinGroup.add(cabSleeper);

            // Aero Deflector Shield with glowing circuit pattern
            const cabAero = new THREE.Mesh(new THREE.BoxGeometry(1.65, 0.45, 1.85), carbonBodyMat);
            cabAero.position.set(-2.25, 3.12, 0);
            cabAero.rotation.z = -0.22;
            addTechEdges(cabAero, 0x00f5ff, 0.5);
            cabinGroup.add(cabAero);

            // Curved Bonnet (engine cover front nose)
            const cabBonnet = new THREE.Mesh(new THREE.BoxGeometry(1.95, 1.05, 2.02), carbonBodyMat);
            cabBonnet.position.set(-3.9, 0.9, 0);
            cabBonnet.castShadow = true;
            addTechEdges(cabBonnet, 0x00f5ff, 0.45);
            cabinGroup.add(cabBonnet);

            // Deep-set chrome radiator grille with glowing cyan honeycomb matrix
            const grilleFrame = new THREE.Mesh(new THREE.BoxGeometry(0.12, 0.85, 1.55), reflectiveChromeMat);
            grilleFrame.position.set(-4.88, 0.9, 0);
            grilleFrame.castShadow = true;
            cabinGroup.add(grilleFrame);

            const honeycombMesh = new THREE.Mesh(new THREE.BoxGeometry(0.02, 0.78, 1.45), glowingCyanMat);
            honeycombMesh.position.set(-4.95, 0.9, 0);
            cabinGroup.add(honeycombMesh);

            // Raked aerodynamic front windshield (Translucent Glass)
            const windscreen = new THREE.Mesh(new THREE.BoxGeometry(0.08, 0.85, 1.88), translucentWindshieldMat);
            windscreen.rotation.z = -0.24;
            windscreen.position.set(-3.05, 1.95, 0);
            addTechEdges(windscreen, 0x00f5ff, 0.5);
            cabinGroup.add(windscreen);

            // Cyber interior computer server nodes inside cabin (seen through glass)
            const serverBlock = new THREE.Mesh(new THREE.BoxGeometry(0.6, 0.8, 0.8), carbonBodyMat);
            serverBlock.position.set(-2.1, 1.8, 0);
            const serverGrid = new THREE.Mesh(new THREE.BoxGeometry(0.62, 0.7, 0.7), glowingOrangeMat);
            serverGrid.position.set(-2.1, 1.8, 0);
            cabinGroup.add(serverBlock);
            cabinGroup.add(serverGrid);

            // Translucent side door glass panels
            const doorGlassGeo = new THREE.BoxGeometry(0.92, 0.62, 0.08);
            const leftDoorGlass = new THREE.Mesh(doorGlassGeo, translucentWindshieldMat);
            leftDoorGlass.position.set(-2.18, 1.42, 1.03);
            cabinGroup.add(leftDoorGlass);

            const rightDoorGlass = leftDoorGlass.clone();
            rightDoorGlass.position.z = -1.03;
            cabinGroup.add(rightDoorGlass);

            // Side steps with dynamic LED underglow strips
            const stepGeo = new THREE.BoxGeometry(1.6, 0.08, 0.45);
            const leftStep = new THREE.Mesh(stepGeo, reflectiveChromeMat);
            leftStep.position.set(-2.1, 0.42, 1.25);
            addTechEdges(leftStep, 0x00f5ff, 0.4);
            cabinGroup.add(leftStep);

            const leftStepUnderglow = new THREE.Mesh(new THREE.BoxGeometry(1.5, 0.02, 0.05), glowingCyanMat);
            leftStepUnderglow.position.set(-2.1, 0.38, 1.48);
            cabinGroup.add(leftStepUnderglow);

            const rightStep = leftStep.clone();
            rightStep.position.z = -1.25;
            cabinGroup.add(rightStep);

            const rightStepUnderglow = leftStepUnderglow.clone();
            rightStepUnderglow.position.z = -1.48;
            rightStepUnderglow.material = glowingOrangeMat;
            cabinGroup.add(rightStepUnderglow);

            // Futuristic Aerodynamic Side Mirrors
            const mirrorBaseGeo = new THREE.BoxGeometry(0.12, 0.54, 0.28);
            const leftMirror = new THREE.Mesh(mirrorBaseGeo, carbonBodyMat);
            leftMirror.position.set(-2.92, 1.55, 1.18);
            addTechEdges(leftMirror, 0x00f5ff, 0.5);
            cabinGroup.add(leftMirror);

            const rightMirror = leftMirror.clone();
            rightMirror.position.z = -1.18;
            cabinGroup.add(rightMirror);

            // Volumetric high-intensity projector headlamps
            const headlampGeo = new THREE.CylinderGeometry(0.16, 0.16, 0.1, 12);
            const headlightLeft = new THREE.Mesh(headlampGeo, glowingCyanMat);
            headlightLeft.rotation.z = Math.PI / 2;
            headlightLeft.position.set(-4.88, 0.65, 0.95);
            cabinGroup.add(headlightLeft);

            const headlightRight = headlightLeft.clone();
            headlightRight.position.z = -0.95;
            headlightRight.material = glowingOrangeMat;
            cabinGroup.add(headlightRight);

            // Twin vertical Chrome Exhaust Stacks with structural support brackets
            const exhaustStackGeo = new THREE.CylinderGeometry(0.08, 0.08, 2.7, 16);
            
            const exhaustLeft = new THREE.Mesh(exhaustStackGeo, reflectiveChromeMat);
            exhaustLeft.position.set(-0.95, 2.3, 0.92);
            exhaustLeft.castShadow = true;
            addTechEdges(exhaustLeft, 0x00f5ff, 0.5);
            cabinGroup.add(exhaustLeft);

            const supportBracketL = new THREE.Mesh(new THREE.BoxGeometry(0.1, 0.06, 0.35), carbonBodyMat);
            supportBracketL.position.set(-0.95, 2.6, 0.74);
            cabinGroup.add(supportBracketL);

            const exhaustRight = exhaustLeft.clone();
            exhaustRight.position.z = -0.92;
            cabinGroup.add(exhaustRight);

            const supportBracketR = supportBracketL.clone();
            supportBracketR.position.z = -0.74;
            cabinGroup.add(supportBracketR);

            chassisGroup.add(cabinGroup);

            // -----------------------------------------------------------------
            // C. CYBERNETIC DUAL SIDE FUEL TANKS
            // -----------------------------------------------------------------
            const tankGeo = new THREE.CylinderGeometry(0.44, 0.44, 2.3, 24);
            const leftFuelTank = new THREE.Mesh(tankGeo, reflectiveChromeMat);
            leftFuelTank.rotation.z = Math.PI / 2;
            leftFuelTank.position.set(-0.1, 0.35, 1.1);
            leftFuelTank.castShadow = true;
            addTechEdges(leftFuelTank, 0x00f5ff, 0.4);
            chassisGroup.add(leftFuelTank);

            const rightFuelTank = leftFuelTank.clone();
            rightFuelTank.position.z = -1.1;
            chassisGroup.add(rightFuelTank);

            // Strapping metallic bands holding fuel tanks
            for (let offset of [-0.7, 0.7]) {
                const strapGeo = new THREE.CylinderGeometry(0.45, 0.45, 0.06, 24);
                const leftStrap = new THREE.Mesh(strapGeo, carbonBodyMat);
                leftStrap.rotation.z = Math.PI / 2;
                leftStrap.position.set(-0.1 + offset, 0.35, 1.1);
                addTechEdges(leftStrap, 0x00f5ff, 0.3);
                chassisGroup.add(leftStrap);

                const rightStrap = leftStrap.clone();
                rightStrap.position.z = -1.1;
                chassisGroup.add(rightStrap);
            }

            // -----------------------------------------------------------------
            // D. HIGH-DETAILED POWER CORE (Engine block & Turbocharger)
            // -----------------------------------------------------------------
            const powerCoreGroup = new THREE.Group();
            powerCoreGroup.position.set(-3.5, 0.65, 0);

            // Main block cylinder assembly
            const blockBase = new THREE.Mesh(new THREE.BoxGeometry(1.65, 0.98, 0.98), carbonBodyMat);
            blockBase.castShadow = true;
            addTechEdges(blockBase, 0xf97316, 0.65);
            powerCoreGroup.add(blockBase);

            // Pulsing circuit plates (emissive strips on engine shell)
            const circuitPlateL = new THREE.Mesh(new THREE.BoxGeometry(1.4, 0.05, 0.08), glowingOrangeMat);
            circuitPlateL.position.set(0, 0.38, 0.46);
            powerCoreGroup.add(circuitPlateL);

            const circuitPlateR = circuitPlateL.clone();
            circuitPlateR.position.z = -0.46;
            powerCoreGroup.add(circuitPlateR);

            // Coolant tubes (orange chrome curvy manifolds)
            const pipeGeo1 = new THREE.CylinderGeometry(0.06, 0.06, 1.1, 8);
            const tube1 = new THREE.Mesh(pipeGeo1, reflectiveChromeMat);
            tube1.rotation.x = Math.PI / 2;
            tube1.position.set(0.4, 0.42, 0);
            powerCoreGroup.add(tube1);

            // Detailed chrome metallic Turbocharger blower
            const turboGeo = new THREE.TorusGeometry(0.24, 0.08, 8, 16);
            const turboL = new THREE.Mesh(turboGeo, reflectiveChromeMat);
            turboL.position.set(-0.5, 0.2, 0.52);
            powerCoreGroup.add(turboL);

            const turboR = turboL.clone();
            turboR.position.z = -0.52;
            powerCoreGroup.add(turboR);

            // Core Transmission Gearbox
            const transmissionGeo = new THREE.CylinderGeometry(0.38, 0.28, 1.25, 16);
            const gearbox = new THREE.Mesh(transmissionGeo, reflectiveChromeMat);
            gearbox.rotation.z = Math.PI / 2;
            gearbox.position.set(1.4, -0.22, 0);
            gearbox.castShadow = true;
            addTechEdges(gearbox, 0x00f5ff, 0.6);
            powerCoreGroup.add(gearbox);

            chassisGroup.add(powerCoreGroup);

            // Polished carbon metallic driveshaft
            const driveshaft = new THREE.Mesh(new THREE.CylinderGeometry(0.08, 0.08, 3.8, 8), carbonBodyMat);
            driveshaft.rotation.z = Math.PI / 2;
            driveshaft.position.set(0.9, 0.25, 0);
            addTechEdges(driveshaft, 0x00f5ff, 0.4);
            chassisGroup.add(driveshaft);

            // -----------------------------------------------------------------
            // E. REAR TANDEM AXLES AND DIFFERENTIALS
            // -----------------------------------------------------------------
            const axleGeo = new THREE.CylinderGeometry(0.12, 0.12, 2.38, 12);
            
            // Steer front wheel axle
            const steeringAxle = new THREE.Mesh(axleGeo, carbonBodyMat);
            steeringAxle.rotation.x = Math.PI / 2;
            steeringAxle.position.set(-3.5, -0.2, 0);
            chassisGroup.add(steeringAxle);

            // Tandem axle 1
            const tandem1 = new THREE.Mesh(axleGeo, carbonBodyMat);
            tandem1.rotation.x = Math.PI / 2;
            tandem1.position.set(2.25, -0.2, 0);
            tandem1.castShadow = true;
            addTechEdges(tandem1, 0x00f5ff, 0.5);
            chassisGroup.add(tandem1);

            // Tandem axle 2
            const tandem2 = new THREE.Mesh(axleGeo, carbonBodyMat);
            tandem2.rotation.x = Math.PI / 2;
            tandem2.position.set(3.75, -0.2, 0);
            tandem2.castShadow = true;
            addTechEdges(tandem2, 0x00f5ff, 0.5);
            chassisGroup.add(tandem2);

            // High-detail spherical differentials
            const diffGeo = new THREE.SphereGeometry(0.35, 18, 18);
            const diff1 = new THREE.Mesh(diffGeo, carbonBodyMat);
            diff1.position.set(2.25, -0.2, 0);
            addTechEdges(diff1, 0x00f5ff, 0.55);
            chassisGroup.add(diff1);

            const diff2 = diff1.clone();
            diff2.position.set(3.75, -0.2, 0);
            chassisGroup.add(diff2);

            // Heavy Rear braking disc rotors & glowing orange brake calipers
            const discGeo = new THREE.CylinderGeometry(0.38, 0.38, 0.08, 16);
            const caliperGeo = new THREE.BoxGeometry(0.12, 0.24, 0.12);

            function addBrakeDiscAssembly(x, z) {
                const brakeGroup = new THREE.Group();
                brakeGroup.position.set(x, -0.2, z);

                const disc = new THREE.Mesh(discGeo, reflectiveChromeMat);
                disc.rotation.x = Math.PI / 2;
                brakeGroup.add(disc);

                const caliper = new THREE.Mesh(caliperGeo, glowingOrangeMat);
                caliper.position.set(0.18, 0.16, 0.02);
                brakeGroup.add(caliper);

                chassisGroup.add(brakeGroup);
            }

            // Steer front brake hubs
            addBrakeDiscAssembly(-3.5, 0.95);
            addBrakeDiscAssembly(-3.5, -0.95);

            // Rear tandem brake assemblies
            addBrakeDiscAssembly(2.25, 0.95);
            addBrakeDiscAssembly(2.25, -0.95);
            addBrakeDiscAssembly(3.75, 0.95);
            addBrakeDiscAssembly(3.75, -0.95);

            // -----------------------------------------------------------------
            // F. PREMIUM DETAIL WHEELS ASSEMBLY (10x Detailed Cyber Wheels)
            // -----------------------------------------------------------------
            const tireGeo = new THREE.CylinderGeometry(0.72, 0.72, 0.44, 28);
            const rimBaseGeo = new THREE.CylinderGeometry(0.36, 0.36, 0.46, 16);
            const spokeGeo = new THREE.BoxGeometry(0.04, 0.32, 0.12);
            const lockGeo = new THREE.CylinderGeometry(0.08, 0.08, 0.47, 8);

            function buildHighFidelityWheel(x, y, z, isFront) {
                const wGroup = new THREE.Group();
                wGroup.position.set(x, y, z);

                // Thick rubber tire with procedural treads
                const tire = new THREE.Mesh(tireGeo, cyberRubberMat);
                tire.rotation.x = Math.PI / 2;
                tire.castShadow = true;
                addTechEdges(tire, 0x00f5ff, 0.22);
                wGroup.add(tire);

                // Inner rim core
                const rim = new THREE.Mesh(rimBaseGeo, reflectiveChromeMat);
                rim.rotation.x = Math.PI / 2;
                addTechEdges(rim, 0xf97316, 0.5);
                wGroup.add(rim);

                // Chrome multi-spoke patterns
                for (let i = 0; i < 8; i++) {
                    const spoke = new THREE.Mesh(spokeGeo, reflectiveChromeMat);
                    spoke.rotation.z = (i * Math.PI) / 4;
                    spoke.position.y = 0;
                    spoke.position.z = isFront ? 0.05 : 0;
                    wGroup.add(spoke);
                }

                // Tech Center lock cap (Orange emissive core)
                const lock = new THREE.Mesh(lockGeo, isFront ? glowingCyanMat : glowingOrangeMat);
                lock.rotation.x = Math.PI / 2;
                wGroup.add(lock);

                // 8 Detailed Lug Nuts on rim face
                const lugGeo = new THREE.CylinderGeometry(0.016, 0.016, 0.48, 6);
                for (let i = 0; i < 8; i++) {
                    const angle = (i * Math.PI) / 4;
                    const lug = new THREE.Mesh(lugGeo, reflectiveChromeMat);
                    lug.rotation.x = Math.PI / 2;
                    lug.position.set(Math.cos(angle) * 0.22, Math.sin(angle) * 0.22, 0.01);
                    wGroup.add(lug);
                }

                return wGroup;
            }

            // 1. Steer Wheels
            chassisGroup.add(buildHighFidelityWheel(-3.5, -0.2, 1.15, true));
            chassisGroup.add(buildHighFidelityWheel(-3.5, -0.2, -1.15, true));

            // 2. Tandem Axle 1 Dual Wheels
            chassisGroup.add(buildHighFidelityWheel(2.25, -0.2, 1.1, false));
            chassisGroup.add(buildHighFidelityWheel(2.25, -0.2, 1.58, false));
            chassisGroup.add(buildHighFidelityWheel(2.25, -0.2, -1.1, false));
            chassisGroup.add(buildHighFidelityWheel(2.25, -0.2, -1.58, false));

            // 3. Tandem Axle 2 Dual Wheels
            chassisGroup.add(buildHighFidelityWheel(3.75, -0.2, 1.1, false));
            chassisGroup.add(buildHighFidelityWheel(3.75, -0.2, 1.58, false));
            chassisGroup.add(buildHighFidelityWheel(3.75, -0.2, -1.1, false));
            chassisGroup.add(buildHighFidelityWheel(3.75, -0.2, -1.58, false));

            // -----------------------------------------------------------------
            // G. SHOCKS AND LEAF SPRING SUSPENSIONS
            // -----------------------------------------------------------------
            const suspensionGroup = new THREE.Group();
            
            for (let zSide of [0.68, -0.68]) {
                const springGeo = new THREE.CylinderGeometry(0.12, 0.12, 0.52, 12);
                
                // Rear tandem double-leaf springs (glowing coils)
                const coil1 = new THREE.Mesh(springGeo, glowingOrangeMat);
                coil1.position.set(2.25, 0.12, zSide);
                addTechEdges(coil1, 0xf97316, 0.65);
                suspensionGroup.add(coil1);

                const coil2 = new THREE.Mesh(springGeo, glowingOrangeMat);
                coil2.position.set(3.75, 0.12, zSide);
                addTechEdges(coil2, 0xf97316, 0.65);
                suspensionGroup.add(coil2);

                // Steer front hydraulic vertical damper struts
                const strutGeo = new THREE.CylinderGeometry(0.05, 0.05, 0.6, 8);
                const frontStrut = new THREE.Mesh(strutGeo, reflectiveChromeMat);
                frontStrut.position.set(-3.5, 0.12, zSide);
                addTechEdges(frontStrut, 0x00f5ff, 0.6);
                suspensionGroup.add(frontStrut);
            }
            chassisGroup.add(suspensionGroup);

            // -----------------------------------------------------------------
            // H. STYLISH HIGH-FIDELITY CHASSIS DETAILS
            // -----------------------------------------------------------------
            // Steer wheel fender arch covers
            const frontArchGeo = new THREE.TorusGeometry(0.85, 0.09, 8, 24, Math.PI);
            const archL = new THREE.Mesh(frontArchGeo, carbonBodyMat);
            archL.position.set(-3.5, 0.12, 1.15);
            archL.rotation.x = Math.PI / 2;
            archL.rotation.z = Math.PI; // Flip arch
            addTechEdges(archL, 0x00f5ff, 0.45);
            chassisGroup.add(archL);

            const archR = archL.clone();
            archR.position.z = -1.15;
            chassisGroup.add(archR);

            // Rear tandem drive wheel mudguards/fenders
            const rearFenderGeo = new THREE.BoxGeometry(2.38, 0.06, 1.1);
            const fenderL = new THREE.Mesh(rearFenderGeo, carbonBodyMat);
            fenderL.position.set(3.0, 0.58, 1.34);
            addTechEdges(fenderL, 0x00f5ff, 0.4);
            chassisGroup.add(fenderL);

            const fenderR = fenderL.clone();
            fenderR.position.z = -1.34;
            chassisGroup.add(fenderR);

            // Trailer coupling plate (Fifth Wheel hitch)
            const hitchGeo = new THREE.CylinderGeometry(0.48, 0.48, 0.06, 20);
            const hitch = new THREE.Mesh(hitchGeo, carbonBodyMat);
            hitch.position.set(1.1, 0.46, 0);
            addTechEdges(hitch, 0x00f5ff, 0.55);
            chassisGroup.add(hitch);

            const hitchSlot = new THREE.Mesh(new THREE.BoxGeometry(0.42, 0.06, 0.16), carbonBodyMat);
            hitchSlot.position.set(1.3, 0.46, 0);
            addTechEdges(hitchSlot, 0x00f5ff, 0.5);
            chassisGroup.add(hitchSlot);

            scene.add(chassisGroup);

            // =================================================================
            // 4. ACTIVE HOLOGRAM LASER SCANNER & BLUEPRINT DUST
            // =================================================================
            // Holographic Laser Scan Plane
            const scanplaneGeo = new THREE.BoxGeometry(0.05, 2.7, 3.35);
            const scanplaneMat = new THREE.MeshBasicMaterial({
                color: 0x00f5ff,
                transparent: true,
                opacity: 0.22,
                blending: THREE.AdditiveBlending,
                side: THREE.DoubleSide
            });
            const scanplane = new THREE.Mesh(scanplaneGeo, scanplaneMat);
            scanplane.position.set(0, 1.05, 0);
            scene.add(scanplane);

            const scanplaneEdges = new THREE.EdgesGeometry(scanplaneGeo);
            const scanplaneLineMat = new THREE.LineBasicMaterial({
                color: 0x00ffff,
                transparent: true,
                opacity: 0.8,
                blending: THREE.AdditiveBlending
            });
            const scanplaneLines = new THREE.LineSegments(scanplaneEdges, scanplaneLineMat);
            scanplane.add(scanplaneLines);

            // Holographic blueprint space dust (Particle cloud)
            const blueprintGeo = new THREE.BufferGeometry();
            const particleCount = 180;
            const positions = new Float32Array(particleCount * 3);

            for (let i = 0; i < particleCount * 3; i += 3) {
                positions[i] = (Math.random() - 0.5) * 16;
                positions[i + 1] = Math.random() * 4 - 1.2;
                positions[i + 2] = (Math.random() - 0.5) * 16;
            }

            blueprintGeo.setAttribute('position', new THREE.BufferAttribute(positions, 3));
            const blueprintMat = new THREE.PointsMaterial({
                color: 0x00f5ff,
                size: 0.055,
                transparent: true,
                opacity: 0.45,
                blending: THREE.AdditiveBlending
            });
            const blueprintCloud = new THREE.Points(blueprintGeo, blueprintMat);
            scene.add(blueprintCloud);

            // =================================================================
            // 5. EXHAUST SCI-FI SMOKE PARTICLE SYSTEM
            // =================================================================
            const exhaustParticles = [];
            const maxSmokeCount = 60;
            const smokeGeo = new THREE.SphereGeometry(0.05, 5, 5);

            function spawnSmoke(x, y, z, colorHex) {
                if (exhaustParticles.length >= maxSmokeCount) {
                    const oldest = exhaustParticles.shift();
                    scene.remove(oldest);
                }
                const smokeMat = new THREE.MeshBasicMaterial({
                    color: colorHex,
                    transparent: true,
                    opacity: 0.75,
                    blending: THREE.AdditiveBlending
                });
                const smokeMesh = new THREE.Mesh(smokeGeo, smokeMat);
                smokeMesh.position.set(x, y, z);
                smokeMesh.scale.set(1.0, 1.0, 1.0);
                
                // Additive randomized velocities
                smokeMesh.userData = {
                    vx: (Math.random() - 0.5) * 0.015,
                    vy: 0.035 + Math.random() * 0.015,
                    vz: (Math.random() - 0.5) * 0.015,
                    life: 1.0,
                    decay: 0.018 + Math.random() * 0.01
                };
                
                scene.add(smokeMesh);
                exhaustParticles.push(smokeMesh);
            }

            // =================================================================
            // 6. HOTSPOTS 3D TO 2D COORDINATES MAPPING
            // =================================================================
            const hotspotCoords = {
                engine: new THREE.Vector3(-3.9, 0.9, 0),
                clutch: new THREE.Vector3(-2.1, 0.4, 0),
                brakes: new THREE.Vector3(2.25, -0.2, 1.45),
                suspension: new THREE.Vector3(3.0, 0.12, 0.68)
            };

            const zoomTargets = {
                home: { cam: new THREE.Vector3(-9.5, 3.8, 10.5), look: new THREE.Vector3(0, 0.4, 0) },
                engine: { cam: new THREE.Vector3(-6.2, 2.0, 3.2), look: new THREE.Vector3(-3.9, 0.9, 0) },
                clutch: { cam: new THREE.Vector3(-3.6, 1.3, 2.4), look: new THREE.Vector3(-2.1, 0.4, 0) },
                brakes: { cam: new THREE.Vector3(3.1, 0.1, 2.8), look: new THREE.Vector3(2.25, -0.2, 1.1) },
                suspension: { cam: new THREE.Vector3(4.2, 1.1, -2.6), look: new THREE.Vector3(3.0, 0.12, -0.68) }
            };

            const tempV = new THREE.Vector3();
            function updateHotspotOverlay() {
                const rect = canvas.getBoundingClientRect();
                const widthHalf = rect.width / 2;
                const heightHalf = rect.height / 2;

                for (let key in hotspotCoords) {
                    const el = document.getElementById('hotspot-' + key);
                    if (!el) continue;

                    tempV.copy(hotspotCoords[key]);
                    tempV.project(camera);

                    // Frustum check (clip if behind camera plane)
                    if (tempV.z > 1) {
                        el.style.display = 'none';
                        continue;
                    }

                    const x = (tempV.x * widthHalf) + widthHalf;
                    const y = -(tempV.y * heightHalf) + heightHalf;

                    el.style.left = `${x}px`;
                    el.style.top = `${y}px`;
                    el.style.display = 'flex';
                }
            }

            // =================================================================
            // 7. INTERACTIVE DRAWER & GSAP TRANSITIONS
            // =================================================================
            const drawer = document.getElementById('parts-side-drawer');
            const drawerClose = document.getElementById('drawer-close-btn');
            const drawerTitle = document.getElementById('drawer-category-name');
            const drawerContainer = document.getElementById('drawer-parts-container');
            let isZoomed = false;

            // Hotspot click action
            $('.tech-hotspot').on('click', function(e) {
                e.stopPropagation();
                const key = this.id.replace('hotspot-', '');
                const catId = $(this).data('cat-id');
                const catTitle = $(this).data('cat-title');
                
                isZoomed = true;
                
                // Swoop camera to zoom target
                const target = zoomTargets[key] || zoomTargets.home;
                gsap.to(camera.position, { x: target.cam.x, y: target.cam.y, z: target.cam.z, duration: 1.6, ease: 'power3.inOut' });
                gsap.to(controls.target, { x: target.look.x, y: target.look.y, z: target.look.z, duration: 1.6, ease: 'power3.inOut', onUpdate: () => controls.update() });

                // Highlight active hotspot visual indicators
                $('.tech-hotspot').find('.hotspot-ring').css({
                    'border-color': '#06b6d4',
                    'box-shadow': '0 0 15px rgba(6, 182, 212, 0.5)'
                });
                $(this).find('.hotspot-ring').css({
                    'border-color': '#22c55e',
                    'box-shadow': '0 0 25px rgba(34, 197, 94, 0.9)'
                });

                // Open Side Drawer
                drawerTitle.innerText = catTitle;
                drawer.classList.add('open');

                // Tech loader animation inside drawer
                drawerContainer.innerHTML = `
                    <div class="text-center py-5">
                        <div class="spinner-border text-warning" role="status" style="width: 3rem; height: 3rem;">
                            <span class="sr-only">Scanning Database...</span>
                        </div>
                        <p class="text-muted mt-3" style="font-family: monospace; letter-spacing:0.12em;">SCANNING COMPONENT DATABASE...</p>
                    </div>
                `;

                // AJAX database fetch category items
                $.ajax({
                    url: `/api/category/${catId}/products`,
                    type: 'GET',
                    success: function(res) {
                        if (res.status === 'success' && res.products.length > 0) {
                            let cardsHtml = '<div class="row">';
                            res.products.forEach(p => {
                                let badgeHtml = '';
                                if (p.stock <= 0) {
                                    badgeHtml = '<span class="badge badge-danger position-absolute m-2 px-2 py-1" style="z-index: 2; top: 0; left: 0; font-size:10px; font-weight:800; border-radius:4px;">OUT OF STOCK</span>';
                                } else if (p.discount > 0) {
                                    badgeHtml = `<span class="badge badge-warning position-absolute m-2 px-2 py-1" style="z-index: 2; top: 0; left: 0; font-size:10px; background:#f59e0b; color:#111827; font-weight:800; border-radius:4px;">${p.discount}% OFF</span>`;
                                } else if (p.condition === 'new') {
                                    badgeHtml = '<span class="badge badge-success position-absolute m-2 px-2 py-1" style="z-index: 2; top: 0; left: 0; font-size:10px; background:#22c55e; font-weight:800; border-radius:4px;">NEW</span>';
                                }

                                cardsHtml += `
                                    <div class="col-12 mb-4">
                                        <div class="card bg-dark border-secondary overflow-hidden rounded animate__animated animate__fadeInUp" style="background: rgba(15, 23, 42, 0.45) !important; border: 1px solid rgba(255, 255, 255, 0.08) !important;">
                                            <div class="position-relative" style="height: 160px; overflow: hidden; background: #0b111e;">
                                                ${badgeHtml}
                                                <img src="${p.photo}" alt="${p.title}" class="w-100 h-100" style="object-fit: cover; opacity: 0.85; transition: all 0.3s;" onmouseover="this.style.opacity=1; this.style.transform='scale(1.05)'" onmouseout="this.style.opacity=0.85; this.style.transform='scale(1)'">
                                            </div>
                                            <div class="card-body p-3">
                                                <h4 class="card-title text-truncate" style="font-size: 14px; font-weight: 700; margin-bottom: 8px;">
                                                    <a href="${p.detail_url}" class="text-white" style="transition: color 0.2s;" onmouseover="this.style.color='#f97316'" onmouseout="this.style.color='#fff'">${p.title}</a>
                                                </h4>
                                                <div class="d-flex justify-content-between align-items-center mt-2">
                                                    <div class="price-box" style="font-family: monospace; font-size: 13px;">
                                                        ${p.price_html}
                                                    </div>
                                                    <div>
                                                        ${p.stock > 0 ? 
                                                            `<a href="${p.add_to_cart_url}" class="btn btn-sm px-3" style="background: #f97316 !important; color: #fff !important; border:none; font-size: 11px; font-weight: 800; border-radius: 6px; box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);">ADD TO CART <i class="fa fa-shopping-cart ml-1"></i></a>` : 
                                                            `<button class="btn btn-sm btn-secondary px-3" disabled style="font-size:11px; border-radius:6px;">SOLD OUT</button>`
                                                        }
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });
                            cardsHtml += '</div>';
                            drawerContainer.innerHTML = cardsHtml;
                        } else {
                            drawerContainer.innerHTML = `
                                <div class="text-center py-5">
                                    <i class="fa fa-info-circle text-muted" style="font-size: 40px; color: #94a3b8 !important;"></i>
                                    <p class="text-muted mt-3" style="font-family: monospace; letter-spacing: 0.05em; font-size: 11px;">NO LIVE PRODUCTS FOUND IN THIS COMPONENT</p>
                                </div>
                            `;
                        }
                    },
                    error: function() {
                        drawerContainer.innerHTML = `
                            <div class="text-center py-5">
                                <i class="fa fa-exclamation-triangle text-danger" style="font-size: 40px;"></i>
                                <p class="text-danger mt-3" style="font-family: monospace; font-size: 11px;">CONNECTION TO COMPONENT DATABASE INTERRUPTED</p>
                            </div>
                        `;
                    }
                });
            });

            // Close Drawer & Reset Camera target
            function resetShowroom() {
                isZoomed = false;
                drawer.classList.remove('open');

                // Restore hotspot styling
                $('.tech-hotspot').find('.hotspot-ring').css({
                    'border-color': '#06b6d4',
                    'box-shadow': '0 0 15px rgba(6, 182, 212, 0.5)'
                });
                
                const home = zoomTargets.home;
                gsap.to(camera.position, { x: home.cam.x, y: home.cam.y, z: home.cam.z, duration: 1.4, ease: 'power3.inOut' });
                gsap.to(controls.target, { x: home.look.x, y: home.look.y, z: home.look.z, duration: 1.4, ease: 'power3.inOut', onUpdate: () => controls.update() });
            }

            drawerClose.addEventListener('click', resetShowroom);
            container.addEventListener('click', function(e) {
                if (drawer.classList.contains('open') && !e.target.closest('#parts-side-drawer') && !e.target.closest('.tech-hotspot')) {
                    resetShowroom();
                }
            });

            // =================================================================
            // 8. REAL-TIME TELEMETRY DATA GENERATOR
            // =================================================================
            let timeIndex = 0;
            setInterval(function() {
                timeIndex++;
                
                // Generate smooth noise telemetry data values
                const engineVal = 98.0 + Math.sin(timeIndex * 0.4) * 0.4 + Math.random() * 0.3;
                const clutchVal = 0.98 + Math.cos(timeIndex * 0.25) * 0.015;
                const brakesVal = 95.0 + Math.sin(timeIndex * 0.15) * 2.8 + Math.random() * 0.6;
                const suspensionVal = 12.0 + Math.cos(timeIndex * 0.5) * 0.4 + Math.random() * 0.2;

                document.getElementById('hud-engine').innerText = engineVal.toFixed(1) + '%';
                document.getElementById('hud-clutch').innerText = clutchVal.toFixed(2);
                document.getElementById('hud-brakes').innerText = brakesVal.toFixed(1) + '%';
                document.getElementById('hud-suspension').innerText = suspensionVal.toFixed(1) + ' kN';

                // Shift data pools
                sparklineData.engine.shift(); sparklineData.engine.push(engineVal);
                sparklineData.clutch.shift(); sparklineData.clutch.push(clutchVal);
                sparklineData.brakes.shift(); sparklineData.brakes.push(brakesVal);
                sparklineData.suspension.shift(); sparklineData.suspension.push(suspensionVal);

                // Spawn tech smoke particles from exhaust towers
                spawnSmoke(-0.95, 3.6, 0.92, 0x00f5ff);
                spawnSmoke(-0.95, 3.6, -0.92, 0xf97316);

            }, 600);

            // =================================================================
            // 9. RESPONSIVE SCALE ADJUSTER
            // =================================================================
            window.addEventListener('resize', function() {
                camera.aspect = container.clientWidth / container.clientHeight;
                camera.updateProjectionMatrix();
                renderer.setSize(container.clientWidth, container.clientHeight);
            });

            // =================================================================
            // 10. ADVANCED FRAME RENDER LOOP
            // =================================================================
            function animate() {
                requestAnimationFrame(animate);
                
                // Sweep active laser scanplane back and forth dynamically
                if (typeof scanplane !== 'undefined') {
                    const scanX = Math.sin(Date.now() * 0.0016) * 4.8;
                    scanplane.position.x = scanX;
                }

                // Slow rotation on idle
                if (!isZoomed && !controls.state === -1) {
                    chassisGroup.rotation.y += 0.0014;
                }

                // Animate background star particles cloud
                if (blueprintCloud) {
                    blueprintCloud.rotation.y += 0.0006;
                }

                // Animate exhaust smoke particles
                for (let i = exhaustParticles.length - 1; i >= 0; i--) {
                    const p = exhaustParticles[i];
                    p.position.x += p.userData.vx;
                    p.position.y += p.userData.vy;
                    p.position.z += p.userData.vz;
                    p.scale.addScalar(0.015);
                    
                    p.userData.life -= p.userData.decay;
                    p.material.opacity = p.userData.life;

                    if (p.userData.life <= 0) {
                        scene.remove(p);
                        exhaustParticles.splice(i, 1);
                    }
                }

                // Draw HUD sparkline panels
                drawSparkline('sparkline-engine', sparklineData.engine, 'rgb(6, 182, 212)');
                drawSparkline('sparkline-clutch', sparklineData.clutch, 'rgb(6, 182, 212)');
                drawSparkline('sparkline-brakes', sparklineData.brakes, 'rgb(249, 115, 22)');
                drawSparkline('sparkline-suspension', sparklineData.suspension, 'rgb(249, 115, 22)');

                controls.update();
                renderer.render(scene, camera);
                updateHotspotOverlay();
            }

            // Start loop
            animate();
        });
    </script>
@endpush
