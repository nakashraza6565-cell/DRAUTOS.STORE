@extends('frontend.layouts.master')
@section('title','Danyal Autos Co. || HOME PAGE')
@section('main-content')
<!-- Holographic Truck Showroom -->
<section id="chassis-3d-showroom">
    <!-- Sci-fi blueprint grid -->
    <div class="showroom-grid-overlay"></div>

    <!-- Scan line sweep -->
    <div class="scan-sweep"></div>

    <!-- ===== LEFT PANEL: Part Cards ===== -->
    <div class="hud-panel hud-left">
        <div class="hud-title">
            <span class="blink-dot"></span>Parts Explorer
        </div>
        @php
            $engineCat2    = $category_lists->first(function($c) { return Str::contains(Str::lower($c->slug), ['engine','automizer','filter']); });
            $clutchCat2    = $category_lists->first(function($c) { return Str::contains(Str::lower($c->slug), ['clutch','gear']); });
            $brakeCat2     = $category_lists->first(function($c) { return Str::contains(Str::lower($c->slug), ['brake','hub']); });
            $suspCat2      = $category_lists->first(function($c) { return Str::contains(Str::lower($c->slug), ['suspension','spring','axle']); });
            $ff4           = $category_lists->take(4)->values();
            $parts = [
                ['icon'=>'fa-cogs',      'title'=> $suspCat2->title  ?? ($ff4[0]->title ?? 'Next-Gen Suspension Array'), 'desc'=>'Leaf springs & shocks',        'id'=> $suspCat2->id  ?? ($ff4[0]->id ?? 0),  'slug'=> $suspCat2->slug  ?? ($ff4[0]->slug ?? '#')],
                ['icon'=>'fa-bolt',      'title'=> $engineCat2->title ?? ($ff4[1]->title ?? 'Advanced Powerpack'),       'desc'=>'Power unit & filters',          'id'=> $engineCat2->id ?? ($ff4[1]->id ?? 0),  'slug'=> $engineCat2->slug ?? ($ff4[1]->slug ?? '#')],
                ['icon'=>'fa-th-large',  'title'=> $clutchCat2->title ?? ($ff4[2]->title ?? 'Intelligent Cabin'),        'desc'=>'Transmission & gears',          'id'=> $clutchCat2->id ?? ($ff4[2]->id ?? 0),  'slug'=> $clutchCat2->slug ?? ($ff4[2]->slug ?? '#')],
                ['icon'=>'fa-circle',    'title'=> $brakeCat2->title  ?? ($ff4[3]->title ?? 'Brake Systems'),            'desc'=>'Rotors, calipers & hubs',       'id'=> $brakeCat2->id  ?? ($ff4[3]->id ?? 0),  'slug'=> $brakeCat2->slug  ?? ($ff4[3]->slug ?? '#')],
            ];
        @endphp
        @foreach($parts as $part)
        <a href="{{ route('product-cat', $part['slug']) }}" class="part-card" id="part-{{ $loop->index }}">
            <div class="part-card-icon"><i class="fa {{ $part['icon'] }}"></i></div>
            <div class="part-card-body">
                <div class="part-card-title">{{ $part['title'] }}</div>
                <div class="part-card-desc">{{ $part['desc'] }}</div>
            </div>
            <div class="part-card-arrow"><i class="fa fa-chevron-right"></i></div>
        </a>
        @endforeach
    </div>

    <!-- ===== CENTER: Holographic Truck Image + Hotspots ===== -->
    <div class="showroom-truck-wrap">
        <img src="{{ asset('frontend/images/holographic_truck.png') }}" alt="Holographic Semi-Truck Chassis" class="showroom-truck-img" id="showroom-truck-img">
        <!-- Floating hotspot rings on key parts -->
        <div class="img-hotspot" id="hs-engine"   style="top:52%; left:28%;"><div class="hotspot-ring"><div class="hotspot-dot"></div><div class="hotspot-pulse"></div></div></div>
        <div class="img-hotspot" id="hs-cabin"    style="top:35%; left:50%;"><div class="hotspot-ring"><div class="hotspot-dot"></div><div class="hotspot-pulse"></div></div></div>
        <div class="img-hotspot" id="hs-wheel-f"  style="top:68%; left:38%;"><div class="hotspot-ring"><div class="hotspot-dot"></div><div class="hotspot-pulse"></div></div></div>
        <div class="img-hotspot" id="hs-wheel-r"  style="top:68%; left:72%;"><div class="hotspot-ring"><div class="hotspot-dot"></div><div class="hotspot-pulse"></div></div></div>
        <!-- Scan label overlay -->
        <div class="truck-scan-label">WEBGL 3D EXPERIENCE &nbsp;&#8212;&nbsp; ASTRA HD-7</div>
    </div>

    <!-- ===== RIGHT PANEL: Live Charts ===== -->
    <div class="hud-panel hud-right">
        <div class="hud-title">
            Performance Metrics<span class="blink-dot" style="background:#f97316;margin-left:8px;"></span>
        </div>

        <div class="chart-block">
            <div class="chart-header">
                <span class="chart-label">Battery State of Charge</span>
                <span class="chart-value" id="val-battery">85%</span>
            </div>
            <canvas class="hud-chart" id="chart-battery"></canvas>
        </div>

        <div class="chart-block">
            <div class="chart-header">
                <span class="chart-label">Dynometer Readings</span>
                <span class="chart-value" id="val-dyno">3.34</span>
            </div>
            <canvas class="hud-chart" id="chart-dyno"></canvas>
        </div>

        <div class="chart-block">
            <div class="chart-header">
                <span class="chart-label">Power Distribution</span>
                <span class="chart-value" id="val-power">6.0t/s</span>
            </div>
            <canvas class="hud-chart" id="chart-power"></canvas>
        </div>
    </div>

    <!-- HUD Info Bar -->
    <div class="hud-controls-info">
        <i class="fa fa-info-circle mr-1"></i> Hover hotspots &bull; Click parts to explore &bull; Danyal Autos Co.
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

    <!-- Holographic Showroom: lightweight chart engine (no Three.js needed) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            /* ============================================================
               CANVAS BAR CHARTS (right HUD panel)
            ============================================================ */
            function drawBarChart(canvasId, valuePercent, color1, color2) {
                const canvas = document.getElementById(canvasId);
                if (!canvas) return;
                const ctx = canvas.getContext('2d');
                const W = canvas.offsetWidth || 200;
                const H = canvas.offsetHeight || 48;
                canvas.width  = W;
                canvas.height = H;

                // Background
                ctx.fillStyle = 'rgba(0,0,0,0.25)';
                ctx.fillRect(0, 0, W, H);

                // Grid lines
                ctx.strokeStyle = 'rgba(255,255,255,0.04)';
                ctx.lineWidth = 1;
                for (let x = 0; x < W; x += W / 5) {
                    ctx.beginPath(); ctx.moveTo(x, 0); ctx.lineTo(x, H); ctx.stroke();
                }

                // Gradient filled bar
                const barW = (valuePercent / 100) * W;
                const grad = ctx.createLinearGradient(0, 0, barW, 0);
                grad.addColorStop(0, color1);
                grad.addColorStop(1, color2);
                ctx.fillStyle = grad;
                const barH = H * 0.38;
                const barY = (H - barH) / 2;
                ctx.beginPath();
                ctx.roundRect(0, barY, barW, barH, 3);
                ctx.fill();

                // Glow edge
                ctx.shadowColor = color2;
                ctx.shadowBlur = 10;
                ctx.fillStyle = color2;
                ctx.beginPath();
                ctx.roundRect(barW - 3, barY, 3, barH, 2);
                ctx.fill();
                ctx.shadowBlur = 0;

                // Y-axis tick labels
                ctx.font = '9px monospace';
                ctx.fillStyle = 'rgba(255,255,255,0.25)';
                const steps = [0, 50, 100, 150, 200];
                steps.forEach((v, i) => {
                    const tx = (i / (steps.length - 1)) * W;
                    ctx.fillText(v, tx + 2, H - 3);
                });
            }

            /* ============================================================
               SPARKLINE WAVE CHARTS (animated)
            ============================================================ */
            const sparkData = {
                battery: Array.from({length: 60}, (_, i) => 85 - (i * 0.2) + Math.random() * 4),
                dyno:    Array.from({length: 60}, () => 2.5 + Math.random() * 2),
                power:   Array.from({length: 60}, () => 4.5 + Math.random() * 3),
            };

            function drawWaveChart(canvasId, data, strokeColor) {
                const canvas = document.getElementById(canvasId);
                if (!canvas) return;
                const ctx = canvas.getContext('2d');
                const W = canvas.offsetWidth || 200;
                const H = canvas.offsetHeight || 48;
                canvas.width  = W;
                canvas.height = H;

                const min = Math.min(...data);
                const max = Math.max(...data);
                const range = (max - min) || 1;

                ctx.clearRect(0, 0, W, H);
                ctx.fillStyle = 'rgba(0,0,0,0.2)';
                ctx.fillRect(0, 0, W, H);

                // Filled area
                const areaGrad = ctx.createLinearGradient(0, 0, 0, H);
                areaGrad.addColorStop(0, strokeColor.replace(')', ',0.35)').replace('rgb', 'rgba'));
                areaGrad.addColorStop(1, 'rgba(0,0,0,0)');
                ctx.beginPath();
                data.forEach((v, i) => {
                    const x = (i / (data.length - 1)) * W;
                    const y = H - ((v - min) / range) * (H * 0.75) - H * 0.1;
                    i === 0 ? ctx.moveTo(x, y) : ctx.lineTo(x, y);
                });
                ctx.lineTo(W, H); ctx.lineTo(0, H);
                ctx.closePath();
                ctx.fillStyle = areaGrad;
                ctx.fill();

                // Line
                ctx.beginPath();
                ctx.strokeStyle = strokeColor;
                ctx.lineWidth = 1.5;
                ctx.lineJoin = 'round';
                data.forEach((v, i) => {
                    const x = (i / (data.length - 1)) * W;
                    const y = H - ((v - min) / range) * (H * 0.75) - H * 0.1;
                    i === 0 ? ctx.moveTo(x, y) : ctx.lineTo(x, y);
                });
                ctx.stroke();
            }

            /* ============================================================
               ANIMATION LOOP
            ============================================================ */
            let tick = 0;
            function animate() {
                tick++;

                // Update data rolling
                if (tick % 8 === 0) {
                    sparkData.battery.shift(); sparkData.battery.push(80 + Math.random() * 10);
                    sparkData.dyno.shift();    sparkData.dyno.push(2.5 + Math.random() * 2);
                    sparkData.power.shift();   sparkData.power.push(4.5 + Math.random() * 3);

                    // Update numeric values
                    const bv = sparkData.battery[sparkData.battery.length - 1];
                    const dv = sparkData.dyno[sparkData.dyno.length - 1];
                    const pv = sparkData.power[sparkData.power.length - 1];
                    const el = id => document.getElementById(id);
                    if (el('val-battery')) el('val-battery').textContent = bv.toFixed(0) + '%';
                    if (el('val-dyno'))    el('val-dyno').textContent    = dv.toFixed(2);
                    if (el('val-power'))   el('val-power').textContent   = pv.toFixed(1) + 't/s';

                    // Redraw bar charts
                    drawBarChart('chart-battery', bv,                    'rgb(6,182,212)',   'rgb(0,240,255)');
                    drawBarChart('chart-dyno',    (dv / 4.5) * 100,      'rgb(249,115,22)',  'rgb(255,160,50)');
                    drawBarChart('chart-power',   (pv / 7.5) * 100,      'rgb(6,182,212)',   'rgb(100,220,255)');
                }

                requestAnimationFrame(animate);
            }

            // Initial draw
            drawBarChart('chart-battery', 85,   'rgb(6,182,212)',  'rgb(0,240,255)');
            drawBarChart('chart-dyno',    74,   'rgb(249,115,22)', 'rgb(255,160,50)');
            drawBarChart('chart-power',   60,   'rgb(6,182,212)',  'rgb(100,220,255)');
            animate();

            /* ============================================================
               TRUCK IMAGE: floating hover animation
            ============================================================ */
            const truckImg = document.getElementById('showroom-truck-img');
            if (truckImg) {
                let floatDir = 1;
                let floatPos = 0;
                function floatTruck() {
                    floatPos += 0.02 * floatDir;
                    if (Math.abs(floatPos) > 1) floatDir *= -1;
                    truckImg.style.transform = 'translateY(' + floatPos.toFixed(2) + 'px) scale(1.01)';
                    requestAnimationFrame(floatTruck);
                }
                floatTruck();
            }

        });
    </script>

@endpush
