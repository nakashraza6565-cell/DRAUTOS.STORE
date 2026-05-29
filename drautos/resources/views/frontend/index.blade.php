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
    <div class="tech-hotspot" id="hotspot-engine" data-cat-id="{{ $engineId }}" data-cat-title="{{ $engineTitle }}">
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
    <div class="tech-hotspot" id="hotspot-clutch" data-cat-id="{{ $clutchId }}" data-cat-title="{{ $clutchTitle }}">
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
    <div class="tech-hotspot" id="hotspot-brakes" data-cat-id="{{ $brakeId }}" data-cat-title="{{ $brakeTitle }}">
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
    <div class="tech-hotspot" id="hotspot-suspension" data-cat-id="{{ $suspensionId }}" data-cat-title="{{ $suspensionTitle }}">
        <div class="hotspot-ring">
            <div class="hotspot-dot"></div>
            <div class="hotspot-pulse"></div>
        </div>
        <div class="hotspot-tooltip">
            <span class="tooltip-title">{{ $suspensionTitle }}</span>
            <span class="tooltip-desc">Leaf Springs & Shocks</span>
        </div>
    </div>
    
    <!-- HUD Info -->
    <div class="hud-controls-info">
        <i class="fa fa-info-circle mr-1"></i> Click on glowing rings to explore dynamic categorized inventory parts.
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

    <!-- Interactive High-Fidelity Showroom Script -->
    <script>
        $(document).ready(function() {
            const drawer = document.getElementById('parts-side-drawer');
            const drawerClose = document.getElementById('drawer-close-btn');
            const drawerTitle = document.getElementById('drawer-category-name');
            const drawerContainer = document.getElementById('drawer-parts-container');
            let activeHotspot = null;

            // 1. Hotspot click event handlers
            $('.tech-hotspot').on('click', function(e) {
                e.stopPropagation();
                
                // Add active glowing class to clicked hotspot
                if (activeHotspot) {
                    $(activeHotspot).find('.hotspot-ring').css('border-color', '#f97316');
                    $(activeHotspot).find('.hotspot-ring').css('box-shadow', '0 0 15px rgba(249, 115, 22, 0.6)');
                }
                
                activeHotspot = this;
                $(this).find('.hotspot-ring').css('border-color', '#22c55e');
                $(this).find('.hotspot-ring').css('box-shadow', '0 0 25px rgba(34, 197, 94, 0.9)');

                const catId = $(this).data('cat-id');
                const catTitle = $(this).data('cat-title');
                
                // Populate and Slide Drawer
                drawerTitle.innerText = catTitle;
                drawer.classList.add('open');

                // Render glassmorphism loader
                drawerContainer.innerHTML = `
                    <div class="text-center py-5">
                        <div class="spinner-border text-warning" role="status" style="width: 3rem; height: 3rem;">
                            <span class="sr-only">Scanning Database...</span>
                        </div>
                        <p class="text-muted mt-3" style="font-family: monospace; letter-spacing: 0.1em;">SCANNING COMPONENT DATABASE...</p>
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
                                        <div class="card bg-dark border-secondary overflow-hidden rounded" style="background: rgba(30, 41, 59, 0.4) !important; border: 1px solid rgba(255, 255, 255, 0.1) !important; transition: all 0.3s ease;">
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

            // Close Drawer & Reset Active Hotspot Highlight
            function resetShowroom() {
                drawer.classList.remove('open');
                if (activeHotspot) {
                    $(activeHotspot).find('.hotspot-ring').css('border-color', '#f97316');
                    $(activeHotspot).find('.hotspot-ring').css('box-shadow', '0 0 15px rgba(249, 115, 22, 0.6)');
                    activeHotspot = null;
                }
            }

            drawerClose.addEventListener('click', resetShowroom);
            
            // Close if clicked on grid/backdrop but not on active hotspots/drawer
            $(document).on('click', function(e) {
                if (drawer.classList.contains('open') && !$(e.target).closest('#parts-side-drawer').length && !$(e.target).closest('.tech-hotspot').length) {
                    resetShowroom();
                }
            });

            // 2. Real-Time Diagnostics HUD Jiggle (dynamic dashboard feeling)
            setInterval(function() {
                document.getElementById('hud-engine').innerText = (98.0 + Math.random() * 0.8).toFixed(1) + '%';
                document.getElementById('hud-clutch').innerText = (1.00 + (Math.random() - 0.5) * 0.02).toFixed(2);
                document.getElementById('hud-brakes').innerText = (95.0 + Math.random() * 3.5).toFixed(1) + '%';
                document.getElementById('hud-suspension').innerText = (12.0 + Math.random() * 0.8).toFixed(1) + ' kN';
            }, 2000);
        });
    </script>
@endpush
