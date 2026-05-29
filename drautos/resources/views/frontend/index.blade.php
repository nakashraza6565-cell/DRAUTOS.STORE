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
        <div class="hud-metric">
            <span class="hud-label">Engine Core:</span>
            <span class="hud-value" id="hud-engine">98.4%</span>
        </div>
        <div class="hud-metric">
            <span class="hud-label">Clutch Ratio:</span>
            <span class="hud-value" id="hud-clutch">1.00</span>
        </div>
        <div class="hud-metric">
            <span class="hud-label">Brakes Pad:</span>
            <span class="hud-value" id="hud-brakes">96.8%</span>
        </div>
        <div class="hud-metric">
            <span class="hud-label">Suspension Load:</span>
            <span class="hud-value" id="hud-suspension">12.5 kN</span>
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
            
            // 1. Scene Setup
            const scene = new THREE.Scene();
            scene.fog = new THREE.FogExp2(0x020617, 0.015);

            // 2. Camera Setup
            const camera = new THREE.PerspectiveCamera(45, container.clientWidth / container.clientHeight, 0.1, 1000);
            camera.position.set(-8.5, 4.5, 9.5);

            // 3. Renderer Setup
            const renderer = new THREE.WebGLRenderer({ canvas: canvas, antialias: true, alpha: false });
            renderer.setSize(container.clientWidth, container.clientHeight);
            renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
            renderer.setClearColor(0x020617, 1);
            renderer.shadowMap.enabled = true;
            renderer.shadowMap.type = THREE.PCFSoftShadowMap;

            // 4. Orbit Controls
            const controls = new THREE.OrbitControls(camera, renderer.domElement);
            controls.enableDamping = true;
            controls.dampingFactor = 0.05;
            controls.maxPolarAngle = Math.PI / 2 - 0.05; // Keep above floor
            controls.minDistance = 3;
            controls.maxDistance = 22;
            controls.target.set(0, 0.5, 0);

            // 5. Lighting Setup
            const ambientLight = new THREE.AmbientLight(0x1e293b, 1.4);
            scene.add(ambientLight);

            const mainLight = new THREE.DirectionalLight(0xffffff, 2.5);
            mainLight.position.set(6, 18, 6);
            mainLight.castShadow = true;
            mainLight.shadow.mapSize.width = 1024;
            mainLight.shadow.mapSize.height = 1024;
            scene.add(mainLight);

            // Tech highlight spotlights (Cyan & Orange Glow)
            const orangeTechLight = new THREE.PointLight(0xf97316, 4, 18);
            orangeTechLight.position.set(-3.2, 1.5, 1.2);
            scene.add(orangeTechLight);

            const blueTechLight = new THREE.PointLight(0x3b82f6, 3, 20);
            blueTechLight.position.set(1.5, 1.2, -1.8);
            scene.add(blueTechLight);

            // Ground floor plane shadow
            const floorGeo = new THREE.PlaneGeometry(120, 120);
            const floorMat = new THREE.ShadowMaterial({ opacity: 0.45 });
            const floor = new THREE.Mesh(floorGeo, floorMat);
            floor.rotation.x = -Math.PI / 2;
            floor.position.y = -1.2;
            floor.receiveShadow = true;
            scene.add(floor);

            // Dynamic sci-fi blueprint grid helper
            const gridHelper = new THREE.GridHelper(60, 60, 0x4f46e5, 0x1e293b);
            gridHelper.position.y = -1.19;
            gridHelper.material.opacity = 0.4;
            gridHelper.material.transparent = true;
            scene.add(gridHelper);

            // 6. BUILD FUTURISTIC HIGH-DETAILED 3D SEMI-TRUCK
            const chassisGroup = new THREE.Group();

            // Glowing Wireframe Overlay Generator
            function addGlowingWireframe(parentMesh, colorHex, opacity = 0.45) {
                const wireMat = new THREE.MeshBasicMaterial({
                    color: colorHex,
                    wireframe: true,
                    transparent: true,
                    opacity: opacity,
                    blending: THREE.AdditiveBlending
                });
                const wireMesh = new THREE.Mesh(parentMesh.geometry, wireMat);
                parentMesh.add(wireMesh);
            }

            // Material Presets
            const darkSlateMat = new THREE.MeshStandardMaterial({ color: 0x0f172a, metalness: 0.85, roughness: 0.15 });
            const chromeMat = new THREE.MeshStandardMaterial({ color: 0xf1f5f9, metalness: 0.98, roughness: 0.02 });
            const glowingOrangeMat = new THREE.MeshStandardMaterial({ color: 0x020617, emissive: 0xf97316, emissiveIntensity: 2.5, roughness: 0.3 });
            const glowingCyanMat = new THREE.MeshStandardMaterial({ color: 0x020617, emissive: 0x06b6d4, emissiveIntensity: 2.2, roughness: 0.3 });
            const techWindshieldMat = new THREE.MeshPhysicalMaterial({ color: 0x3b82f6, transparent: true, opacity: 0.4, roughness: 0.05, transmission: 0.85, thickness: 0.4 });
            const rubberMat = new THREE.MeshStandardMaterial({ color: 0x090d16, metalness: 0.1, roughness: 0.85 });
            const brightChromeMat = new THREE.MeshStandardMaterial({ color: 0xffffff, metalness: 0.95, roughness: 0.05 });

            // A. MAIN CHASSIS FRAME BEAMS
            const railGeo = new THREE.BoxGeometry(9.6, 0.28, 0.15);
            const leftRail = new THREE.Mesh(railGeo, darkSlateMat);
            leftRail.position.set(0.2, 0.4, 0.7);
            leftRail.castShadow = true;
            addGlowingWireframe(leftRail, 0x3b82f6, 0.5);
            chassisGroup.add(leftRail);

            const rightRail = leftRail.clone();
            rightRail.position.z = -0.7;
            chassisGroup.add(rightRail);

            // Cross bracing reinforcements
            for (let i = -4.2; i <= 4.2; i += 1.8) {
                const braceGeo = new THREE.BoxGeometry(0.15, 0.2, 1.25);
                const brace = new THREE.Mesh(braceGeo, darkSlateMat);
                brace.position.set(i, 0.4, 0);
                brace.castShadow = true;
                addGlowingWireframe(brace, 0x3b82f6, 0.4);
                chassisGroup.add(brace);
            }

            // Heavy Front Bumper
            const bumperGeo = new THREE.BoxGeometry(0.3, 0.4, 2.5);
            const bumper = new THREE.Mesh(bumperGeo, chromeMat);
            bumper.position.set(-4.7, 0.25, 0);
            bumper.castShadow = true;
            addGlowingWireframe(bumper, 0x3b82f6, 0.6);
            chassisGroup.add(bumper);

            // B. ULTRA-DETAILED SEMI-TRUCK CABIN ASSEMBLY
            const cabinGroup = new THREE.Group();

            // Lower Cab Box
            const cabBaseGeo = new THREE.BoxGeometry(2.3, 1.3, 2.0);
            const cabBase = new THREE.Mesh(cabBaseGeo, darkSlateMat);
            cabBase.position.set(-2.2, 1.15, 0);
            cabBase.castShadow = true;
            addGlowingWireframe(cabBase, 0x3b82f6, 0.4);
            cabinGroup.add(cabBase);

            // Upper Sleeper Cab
            const cabSleeperGeo = new THREE.BoxGeometry(2.1, 1.2, 1.95);
            const cabSleeper = new THREE.Mesh(cabSleeperGeo, darkSlateMat);
            cabSleeper.position.set(-2.1, 2.4, 0);
            cabSleeper.castShadow = true;
            addGlowingWireframe(cabSleeper, 0x3b82f6, 0.4);
            cabinGroup.add(cabSleeper);

            // Curved Engine Bonnet (Hood nose)
            const bonnetGeo = new THREE.BoxGeometry(1.9, 1.0, 1.95);
            const bonnet = new THREE.Mesh(bonnetGeo, darkSlateMat);
            bonnet.position.set(-3.85, 0.9, 0);
            bonnet.castShadow = true;
            addGlowingWireframe(bonnet, 0x3b82f6, 0.45);
            cabinGroup.add(bonnet);

            // Front Radiator Grille
            const grilleGeo = new THREE.BoxGeometry(0.1, 0.8, 1.5);
            const grille = new THREE.Mesh(grilleGeo, glowingCyanMat);
            grille.position.set(-4.81, 0.9, 0);
            addGlowingWireframe(grille, 0x06b6d4, 0.8);
            cabinGroup.add(grille);

            // Windshield Window (Translucent Tech Glass)
            const windscreenGeo = new THREE.BoxGeometry(0.1, 0.8, 1.8);
            const windscreen = new THREE.Mesh(windscreenGeo, techWindshieldMat);
            windscreen.rotation.z = -0.22; // Raked back
            windscreen.position.set(-3.0, 1.9, 0);
            addGlowingWireframe(windscreen, 0x3b82f6, 0.6);
            cabinGroup.add(windscreen);

            // Side glass panels
            const sideGlassGeo = new THREE.BoxGeometry(0.9, 0.6, 0.1);
            const leftGlass = new THREE.Mesh(sideGlassGeo, techWindshieldMat);
            leftGlass.position.set(-2.2, 1.4, 1.01);
            cabinGroup.add(leftGlass);

            const rightGlass = leftGlass.clone();
            rightGlass.position.z = -1.01;
            cabinGroup.add(rightGlass);

            // Twin vertical Chrome Exhaust Stacks
            const stackGeo = new THREE.CylinderGeometry(0.08, 0.08, 2.5, 12);
            const stackLeft = new THREE.Mesh(stackGeo, chromeMat);
            stackLeft.position.set(-1.0, 2.2, 0.9);
            stackLeft.castShadow = true;
            addGlowingWireframe(stackLeft, 0x3b82f6, 0.5);
            cabinGroup.add(stackLeft);

            const stackRight = stackLeft.clone();
            stackRight.position.z = -0.9;
            cabinGroup.add(stackRight);

            // Side Mirrors
            const mirrorGeo = new THREE.BoxGeometry(0.1, 0.5, 0.25);
            const leftMirror = new THREE.Mesh(mirrorGeo, chromeMat);
            leftMirror.position.set(-2.9, 1.5, 1.15);
            addGlowingWireframe(leftMirror, 0x3b82f6, 0.5);
            cabinGroup.add(leftMirror);

            const rightMirror = leftMirror.clone();
            rightMirror.position.z = -1.15;
            cabinGroup.add(rightMirror);

            // Headlights (glowing cones)
            const lightConeGeo = new THREE.CylinderGeometry(0.15, 0.15, 0.1, 12);
            const headlightL = new THREE.Mesh(lightConeGeo, glowingOrangeMat);
            headlightL.rotation.z = Math.PI / 2;
            headlightL.position.set(-4.72, 0.65, 0.9);
            cabinGroup.add(headlightL);

            const headlightR = headlightL.clone();
            headlightR.position.z = -0.9;
            cabinGroup.add(headlightR);

            chassisGroup.add(cabinGroup);

            // C. CYBERNETIC DUAL SIDE FUEL TANKS
            const tankGeo = new THREE.CylinderGeometry(0.42, 0.42, 2.2, 20);
            const leftTank = new THREE.Mesh(tankGeo, chromeMat);
            leftTank.rotation.z = Math.PI / 2;
            leftTank.position.set(-0.2, 0.35, 1.05);
            leftTank.castShadow = true;
            addGlowingWireframe(leftTank, 0x3b82f6, 0.45);
            chassisGroup.add(leftTank);

            const rightTank = leftTank.clone();
            rightTank.position.z = -1.05;
            chassisGroup.add(rightTank);

            // D. DETAILED DIGITAL POWER UNIT (Engine & Gearbox)
            const engineGeo = new THREE.BoxGeometry(1.7, 0.95, 0.95);
            const engine = new THREE.Mesh(engineGeo, glowingOrangeMat);
            engine.position.set(-3.5, 0.65, 0);
            engine.castShadow = true;
            addGlowingWireframe(engine, 0xf97316, 0.75);
            chassisGroup.add(engine);

            const transmissionGeo = new THREE.CylinderGeometry(0.35, 0.25, 1.1, 16);
            const transmission = new THREE.Mesh(transmissionGeo, brightChromeMat);
            transmission.rotation.z = Math.PI / 2;
            transmission.position.set(-2.0, 0.4, 0);
            transmission.castShadow = true;
            addGlowingWireframe(transmission, 0x06b6d4, 0.65);
            chassisGroup.add(transmission);

            // Driveshaft
            const shaftGeo = new THREE.CylinderGeometry(0.08, 0.08, 3.8, 8);
            const driveshaft = new THREE.Mesh(shaftGeo, brightChromeMat);
            driveshaft.rotation.z = Math.PI / 2;
            driveshaft.position.set(0.9, 0.25, 0);
            addGlowingWireframe(driveshaft, 0x3b82f6, 0.4);
            chassisGroup.add(driveshaft);

            // E. REAR TANDEM AXLES AND DIFFERENTIALS
            const axleGeo = new THREE.CylinderGeometry(0.12, 0.12, 2.3, 12);
            
            // Steer front axle
            const steerAxle = new THREE.Mesh(axleGeo, darkSlateMat);
            steerAxle.rotation.x = Math.PI / 2;
            steerAxle.position.set(-3.5, -0.2, 0);
            chassisGroup.add(steerAxle);

            // Tandem axle 1
            const tandemAxle1 = new THREE.Mesh(axleGeo, darkSlateMat);
            tandemAxle1.rotation.x = Math.PI / 2;
            tandemAxle1.position.set(2.2, -0.2, 0);
            tandemAxle1.castShadow = true;
            addGlowingWireframe(tandemAxle1, 0x3b82f6, 0.5);
            chassisGroup.add(tandemAxle1);

            // Tandem axle 2
            const tandemAxle2 = new THREE.Mesh(axleGeo, darkSlateMat);
            tandemAxle2.rotation.x = Math.PI / 2;
            tandemAxle2.position.set(3.7, -0.2, 0);
            tandemAxle2.castShadow = true;
            addGlowingWireframe(tandemAxle2, 0x3b82f6, 0.5);
            chassisGroup.add(tandemAxle2);

            // Diffs
            const diffGeo = new THREE.SphereGeometry(0.33, 16, 16);
            const diff1 = new THREE.Mesh(diffGeo, darkSlateMat);
            diff1.position.set(2.2, -0.2, 0);
            addGlowingWireframe(diff1, 0x3b82f6, 0.6);
            chassisGroup.add(diff1);

            const diff2 = diff1.clone();
            diff2.position.set(3.7, -0.2, 0);
            chassisGroup.add(diff2);

            // Brakes Rotors (Rear disc sets)
            const rotorGeo = new THREE.CylinderGeometry(0.36, 0.36, 0.09, 16);
            const rotorFL = new THREE.Mesh(rotorGeo, brightChromeMat);
            rotorFL.rotation.x = Math.PI / 2;
            rotorFL.position.set(2.2, -0.2, 0.95);
            addGlowingWireframe(rotorFL, 0xf97316, 0.65);
            chassisGroup.add(rotorFL);

            const rotorFR = rotorFL.clone();
            rotorFR.position.z = -0.95;
            chassisGroup.add(rotorFR);

            // F. HIGH-DENSITY WHEELS ASSEMBLY (10 Cyber Wheels)
            const tireGeo = new THREE.CylinderGeometry(0.72, 0.72, 0.44, 24);
            const rimHubGeo = new THREE.CylinderGeometry(0.32, 0.32, 0.47, 12);
            
            function buildWheel(x, y, z) {
                const wGroup = new THREE.Group();
                
                const tire = new THREE.Mesh(tireGeo, rubberMat);
                tire.rotation.x = Math.PI / 2;
                tire.castShadow = true;
                addGlowingWireframe(tire, 0x3b82f6, 0.25);
                wGroup.add(tire);
                
                const rim = new THREE.Mesh(rimHubGeo, chromeMat);
                rim.rotation.x = Math.PI / 2;
                addGlowingWireframe(rim, 0xf97316, 0.55);
                wGroup.add(rim);
                
                wGroup.position.set(x, y, z);
                return wGroup;
            }

            // 1. Front Steer Tires
            chassisGroup.add(buildWheel(-3.5, -0.2, 1.15));
            chassisGroup.add(buildWheel(-3.5, -0.2, -1.15));

            // 2. Rear Axle 1 Dual Pairs
            chassisGroup.add(buildWheel(2.2, -0.2, 1.1));
            chassisGroup.add(buildWheel(2.2, -0.2, 1.58));
            chassisGroup.add(buildWheel(2.2, -0.2, -1.1));
            chassisGroup.add(buildWheel(2.2, -0.2, -1.58));

            // 3. Rear Axle 2 Dual Pairs
            chassisGroup.add(buildWheel(3.7, -0.2, 1.1));
            chassisGroup.add(buildWheel(3.7, -0.2, 1.58));
            chassisGroup.add(buildWheel(3.7, -0.2, -1.1));
            chassisGroup.add(buildWheel(3.7, -0.2, -1.58));

            // G. SHOCKS AND LEAF COIL SUSPENSIONS
            const suspensionGroup = new THREE.Group();
            for (let zSide of [0.65, -0.65]) {
                const springGeo = new THREE.CylinderGeometry(0.12, 0.12, 0.48, 12);
                const coil1 = new THREE.Mesh(springGeo, glowingOrangeMat);
                coil1.position.set(2.2, 0.1, zSide);
                addGlowingWireframe(coil1, 0xf97316, 0.7);
                suspensionGroup.add(coil1);

                const coil2 = new THREE.Mesh(springGeo, glowingOrangeMat);
                coil2.position.set(3.7, 0.1, zSide);
                addGlowingWireframe(coil2, 0xf97316, 0.7);
                suspensionGroup.add(coil2);

                const shockGeo = new THREE.CylinderGeometry(0.06, 0.06, 0.58, 8);
                const frontShock = new THREE.Mesh(shockGeo, brightChromeMat);
                frontShock.position.set(-3.5, 0.1, zSide);
                addGlowingWireframe(frontShock, 0x06b6d4, 0.6);
                suspensionGroup.add(frontShock);
            }
            chassisGroup.add(suspensionGroup);

            scene.add(chassisGroup);

            // 7. Hotspot Anchors in 3D Space (pinned precisely onto components)
            const hotspotCoords = {
                engine: new THREE.Vector3(-3.5, 0.65, 0),
                clutch: new THREE.Vector3(-2.0, 0.4, 0),
                brakes: new THREE.Vector3(2.2, -0.2, 1.62),
                suspension: new THREE.Vector3(3.0, 0.1, 0.65)
            };

            // Orbit Target Configurations (Cinematic zooming look targets)
            const zoomTargets = {
                home: { cam: new THREE.Vector3(-8.5, 4.5, 9.5), look: new THREE.Vector3(0, 0.5, 0) },
                engine: { cam: new THREE.Vector3(-5.6, 2.3, 3.4), look: new THREE.Vector3(-3.5, 0.65, 0) },
                clutch: { cam: new THREE.Vector3(-3.4, 1.6, 2.6), look: new THREE.Vector3(-2.0, 0.4, 0) },
                brakes: { cam: new THREE.Vector3(3.2, 0.2, 3.0), look: new THREE.Vector3(2.2, -0.2, 1.2) },
                suspension: { cam: new THREE.Vector3(4.1, 1.3, -2.8), look: new THREE.Vector3(3.0, 0.1, -0.65) }
            };

            // Project 3D vector coordinates onto 2D screen positions
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

                    // If behind camera view frustum
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

            // 8. Dynamic Drawer Integration
            const drawer = document.getElementById('parts-side-drawer');
            const drawerClose = document.getElementById('drawer-close-btn');
            const drawerTitle = document.getElementById('drawer-category-name');
            const drawerContainer = document.getElementById('drawer-parts-container');
            let isZoomed = false;

            // Hotspot clicked handler
            $('.tech-hotspot').on('click', function(e) {
                e.stopPropagation();
                const key = this.id.replace('hotspot-', '');
                const catId = $(this).data('cat-id');
                const catTitle = $(this).data('cat-title');
                
                isZoomed = true;
                
                // Cinematic orbit movement
                const target = zoomTargets[key] || zoomTargets.home;
                gsap.to(camera.position, { x: target.cam.x, y: target.cam.y, z: target.cam.z, duration: 1.6, ease: 'power3.inOut' });
                gsap.to(controls.target, { x: target.look.x, y: target.look.y, z: target.look.z, duration: 1.6, ease: 'power3.inOut', onUpdate: () => controls.update() });

                // Open dynamic drawer
                drawerTitle.innerText = catTitle;
                drawer.classList.add('open');

                // Loader
                drawerContainer.innerHTML = `
                    <div class="text-center py-5">
                        <div class="spinner-border text-warning" role="status" style="width: 3rem; height: 3rem;">
                            <span class="sr-only">Scanning Database...</span>
                        </div>
                        <p class="text-muted mt-3" style="font-family: monospace; letter-spacing:0.1em;">SCANNING COMPONENT DATABASE...</p>
                    </div>
                `;

                // AJAX Fetch Products under selected category
                $.ajax({
                    url: `/api/category/${catId}/products`,
                    type: 'GET',
                    success: function(res) {
                        if (res.status === 'success' && res.products.length > 0) {
                            let cardsHtml = '<div class="row">';
                            res.products.forEach(p => {
                                let badgeHtml = '';
                                if (p.stock <= 0) {
                                    badgeHtml = '<span class="badge badge-danger position-absolute m-2 px-2 py-1" style="z-index: 2; top: 0; left: 0; font-size:10px;">OUT OF STOCK</span>';
                                } else if (p.discount > 0) {
                                    badgeHtml = `<span class="badge badge-warning position-absolute m-2 px-2 py-1" style="z-index: 2; top: 0; left: 0; font-size:10px; background:#f59e0b; color:#111827; font-weight:800;">${p.discount}% OFF</span>`;
                                } else if (p.condition === 'new') {
                                    badgeHtml = '<span class="badge badge-success position-absolute m-2 px-2 py-1" style="z-index: 2; top: 0; left: 0; font-size:10px; background:#22c55e;">NEW</span>';
                                }

                                cardsHtml += `
                                    <div class="col-12 mb-4">
                                        <div class="card bg-dark border-secondary overflow-hidden rounded animate__animated animate__fadeInUp" style="background: rgba(30, 41, 59, 0.4) !important; border: 1px solid rgba(255, 255, 255, 0.1) !important;">
                                            <div class="position-relative" style="height: 160px; overflow: hidden; background: #0f172a;">
                                                ${badgeHtml}
                                                <img src="${p.photo}" alt="${p.title}" class="w-100 h-100" style="object-fit: cover; opacity: 0.85; transition: opacity 0.3s;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0.85">
                                            </div>
                                            <div class="card-body p-3">
                                                <h4 class="card-title text-truncate" style="font-size: 14px; font-weight: 700; margin-bottom: 8px;">
                                                    <a href="${p.detail_url}" class="text-white" style="transition: color 0.2s;" onmouseover="this.style.color='#f59e0b'" onmouseout="this.style.color='#fff'">${p.title}</a>
                                                </h4>
                                                <div class="d-flex justify-content-between align-items-center mt-2">
                                                    <div class="price-box">
                                                        ${p.price_html}
                                                    </div>
                                                    <div>
                                                        ${p.stock > 0 ? 
                                                            `<a href="${p.add_to_cart_url}" class="btn btn-sm px-3" style="background: #f59e0b !important; color: #111827 !important; border:none; font-size: 11px; font-weight: 800; border-radius: 6px;">ADD <i class="fa fa-shopping-cart ml-1"></i></a>` : 
                                                            `<button class="btn btn-sm btn-secondary px-3" disabled style="font-size:11px; border-radius:6px;">SOLD</button>`
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
                                    <p class="text-muted mt-3" style="font-family: monospace; letter-spacing: 0.05em;">NO LIVE PRODUCTS FOUND IN THIS COMPONENT</p>
                                </div>
                            `;
                        }
                    },
                    error: function() {
                        drawerContainer.innerHTML = `
                            <div class="text-center py-5">
                                <i class="fa fa-exclamation-triangle text-danger" style="font-size: 40px;"></i>
                                <p class="text-danger mt-3" style="font-family: monospace;">CONNECTION TO SCAN DATABASE INTERRUPTED</p>
                            </div>
                        `;
                    }
                });
            });

            // Close Drawer & Reset Camera target
            function resetShowroom() {
                isZoomed = false;
                drawer.classList.remove('open');
                
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

            // 9. Real-Time Diagnostics HUD Jiggle
            setInterval(function() {
                if(!isZoomed) {
                    document.getElementById('hud-engine').innerText = (98.0 + Math.random() * 0.8).toFixed(1) + '%';
                    document.getElementById('hud-clutch').innerText = (1.00 + (Math.random() - 0.5) * 0.02).toFixed(2);
                    document.getElementById('hud-brakes').innerText = (95.0 + Math.random() * 3.5).toFixed(1) + '%';
                    document.getElementById('hud-suspension').innerText = (12.2 + Math.random() * 0.6).toFixed(1) + ' kN';
                }
            }, 2500);

            // 10. Viewport Scaling
            window.addEventListener('resize', function() {
                camera.aspect = container.clientWidth / container.clientHeight;
                camera.updateProjectionMatrix();
                renderer.setSize(container.clientWidth, container.clientHeight);
            });

            // 11. Core Animation Frame Loop
            function animate() {
                requestAnimationFrame(animate);
                
                // Slow rotation on idle
                if (!isZoomed && !controls.state === -1) {
                    chassisGroup.rotation.y += 0.0016;
                }
                
                controls.update();
                renderer.render(scene, camera);
                updateHotspotOverlay();
            }

            // Start loop
            animate();
        });
    </script>
@endpush
@endpush
