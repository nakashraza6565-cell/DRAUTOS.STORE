<header class="header shop b2b-header">
    <!-- Topbar -->
    <div class="topbar py-2" style="background: var(--primary); border-bottom: 1px solid rgba(255,255,255,0.1);">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5 col-md-12 col-12">
                    <!-- Top Left -->
                    <div class="top-left">
                        <ul class="list-main d-flex align-items-center mb-0" style="gap: 15px; flex-wrap: wrap;">
                            <li class="text-white" style="font-size: 12px;"><i class="ti-headphone-alt mr-1" style="color: var(--accent);"></i> +923042000274</li>
                            <li class="text-white" style="font-size: 12px;"><i class="ti-email mr-1" style="color: var(--accent);"></i> support@danyalautos.com</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-7 col-md-12 col-12">
                    <!-- Top Right -->
                    <div class="right-content">
                        <ul class="list-main d-flex align-items-center justify-content-end mb-0" style="gap: 10px;">
                            <li class="d-none d-lg-block small"><i class="ti-location-pin mr-1" style="color: var(--accent);"></i> <a href="{{route('contact')}}" class="text-white">Store location</a></li>
                            @auth 
                                @if(Auth::user()->role == 'admin')
                                    <li><a href="{{route('admin')}}" class="btn btn-sm font-weight-bold px-3 py-1" style="border-radius: 4px; color: #000 !important; background: var(--accent) !important; border: none !important;"><i class="ti-user mr-1"></i> Dashboard</a></li>
                                @else 
                                    <li><a href="{{route('user')}}" class="btn btn-sm font-weight-bold px-3 py-1" style="border-radius: 4px; color: #000 !important; background: var(--accent) !important; border: none !important;"><i class="ti-user mr-1"></i> Dashboard</a></li>
                                @endif
                                <li><a href="{{route('user.logout')}}" class="btn btn-sm font-weight-bold px-3 py-1" style="border-radius: 4px; font-size: 11px; color: #fff !important; border: 1px solid rgba(255,255,255,0.3) !important; background: transparent;"><i class="ti-power-off mr-1"></i> Logout</a></li>
                            @else
                                <li><a href="{{route('login')}}" class="btn btn-sm font-weight-bold px-4 py-2" style="border-radius: 4px; color: #000 !important; background: var(--accent) !important; border: none !important; text-transform: uppercase; letter-spacing: 1px; font-size: 12px;"><i class="ti-lock mr-1"></i> Login</a></li>
                                <li class="d-none d-sm-block"><a href="{{route('register')}}" class="btn btn-sm font-weight-bold px-3 py-1" style="border-radius: 4px; color: #fff !important; border: 1px solid rgba(255,255,255,0.3) !important; background: transparent;">Register</a></li>
                            @endauth
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Middle Header -->
    <div class="middle-inner py-4" style="background: #ffffff; border-bottom: 1px solid var(--border-color);">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-3 col-md-3 col-12">
                    <div class="logo">
                        <a href="{{route('home')}}">
                            <h3 class="font-weight-bold" style="color: var(--primary); letter-spacing: -1px;">
                                <span class="px-2 rounded mr-1" style="background: var(--accent); color: #000;"><i class="fas fa-truck-moving"></i> Danyal Autos</span> Co.
                            </h3>
                        </a>
                    </div>
                    <!-- Search Form -->
                    <div class="search-top">
                        <div class="top-search"><a href="#0" style="color: var(--primary);"><i class="ti-search"></i></a></div>
                        <div class="search-top">
                            <form class="search-form" method="POST" action="{{route('product.search')}}">
                                @csrf
                                <input type="text" placeholder="Search part # or keyword..." name="search" style="border-radius: 4px; background: #fff; color: var(--text-main); border: 2px solid var(--primary);">
                                <button value="search" type="submit" style="border-radius: 0 4px 4px 0; background: var(--primary); color: #fff;"><i class="ti-search"></i></button>
                            </form>
                        </div>
                    </div>
                    <!--/ End Search Form -->
                    <div class="mobile-nav"></div>
                </div>
                <div class="col-lg-6 col-md-6 col-12">
                     <!-- Search Form -->
                     <form method="POST" action="{{route('product.search')}}">
                     @csrf
                     <div class="search-bar-top">
                        <div class="search-bar" style="border: 2px solid var(--primary); border-radius: 6px; overflow: hidden; background: #fff;">
                            <select name="cat_id" style="background: var(--bg-soft); border: none; border-right: 1px solid var(--border-color); font-weight: 600; color: var(--text-main);">
                                <option value="">Categories</option>
                                @php
                                    $categories = DB::table('categories')->where('status', 'active')->where('is_parent', 1)->get();
                                @endphp
                                @foreach($categories as $cat)
                                    <option value="{{$cat->id}}">{{$cat->title}}</option>
                                @endforeach
                            </select>
                            <input name="search" placeholder="Search OEM part numbers, tools, components..." type="search" style="background: transparent; color: var(--text-main);">
                            <button class="btnn" type="submit" style="background: var(--primary); color: #fff; height: 100%; border-radius: 0 4px 4px 0; transition: all 0.3s;"><i class="ti-search"></i></button>
                        </div>
                    </div>
                    </form>
                    <!--/ End Search Form -->
                </div>
                <div class="col-lg-3 col-md-3 col-12 d-flex justify-content-end">
                   <div class="right-bar d-flex align-items-center" style="gap: 20px;">
                         <!-- Search Form -->
                         <div class="sinlge-bar shopping">
                            <a href="{{route('wishlist')}}" class="single-icon" style="font-size: 20px; color: var(--primary);"><i class="fa fa-heart-o"></i> <span class="total-count" style="background: var(--accent); color: #000; top: -5px; right: -10px;">{{Helper::wishlistCount()}}</span></a>
                        </div>
                        <div class="sinlge-bar shopping">
                            <a href="{{route('cart')}}" class="single-icon" style="font-size: 20px; color: var(--primary);"><i class="ti-bag"></i> <span class="total-count" style="background: var(--primary); color: #fff; top: -5px; right: -10px;">{{Helper::cartCount()}}</span></a>
                             <!-- Shopping Item -->
                            @auth
                                <div class="shopping-item shadow-lg border-0" style="border-radius: 8px; overflow: hidden; padding: 0; background: #fff; border: 1px solid var(--border-color) !important;">
                                    <div class="dropdown-cart-header p-3" style="background: var(--bg-soft); border-bottom: 1px solid var(--border-color);">
                                        <span class="font-weight-bold" style="color: var(--primary);">{{count(Helper::getAllProductFromCart())}} Items</span>
                                        <a href="{{route('cart')}}" class="font-weight-bold" style="color: var(--primary);">View Cart</a>
                                    </div>
                                    <ul class="shopping-list p-3" style="max-height: 300px; overflow-y: auto;">
                                        @foreach(Helper::getAllProductFromCart() as $data)
                                                @php
                                                    $photo=explode(',',$data->product['photo']);
                                                @endphp
                                                <li class="mb-3 d-flex align-items-center">
                                                    <a href="{{route('cart-delete',$data->id)}}" class="remove mr-3" style="color: #ef4444;" title="Remove item"><i class="fa fa-remove"></i></a>
                                                    <a class="cart-img rounded overflow-hidden mr-3" href="#" style="width: 50px; height: 50px; flex-shrink: 0; border: 1px solid var(--border-color);"><img src="{{$photo[0]}}" alt="{{$photo[0]}}" style="object-fit: cover; width: 100%; height: 100%;"></a>
                                                    <div class="flex-grow-1">
                                                        <h4 class="mb-0" style="font-size: 13px;"><a href="{{route('product-detail',$data->product['slug'])}}" class="font-weight-bold" style="color: var(--text-main);">{{$data->product['title']}}</a></h4>
                                                        <p class="quantity small text-muted">{{$data->quantity}} x <span class="amount font-weight-bold" style="color: var(--primary);">${{number_format($data->price,2)}}</span></p>
                                                    </div>
                                                </li>
                                        @endforeach
                                    </ul>
                                    <div class="bottom p-3" style="background: var(--bg-soft); border-top: 1px solid var(--border-color);">
                                        <div class="total d-flex justify-content-between mb-3">
                                            <span class="text-muted font-weight-bold">Total</span>
                                            <span class="total-amount h5 mb-0 font-weight-bold" style="color: var(--primary);">${{number_format(Helper::totalCartPrice(),2)}}</span>
                                        </div>
                                        <div class="d-flex" style="gap: 10px;">
                                            <a href="{{route('checkout')}}" class="btn flex-grow-1 py-2 font-weight-bold" style="background: var(--primary); color: #fff; border-radius: 4px;">Checkout</a>
                                            <a href="{{route('cart.clear')}}" class="btn flex-grow-1 py-2 font-weight-bold" style="border: 1px solid var(--primary); color: var(--primary); background: transparent; border-radius: 4px;">Clear</a>
                                        </div>
                                    </div>
                                </div>
                            @endauth
                            <!--/ End Shopping Item -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Bottom Header -->
    <div class="header-inner" style="background: var(--primary); border-top: 1px solid rgba(255,255,255,0.05);">
        <div class="container">
            <div class="cat-nav-head">
                <div class="row">
                    <div class="col-lg-12 col-12">
                        <div class="menu-area">
                            <!-- Main Menu -->
                            <nav class="navbar navbar-expand-lg p-0">
                                <div class="navbar-collapse">	
                                    <div class="nav-inner">	
                                        <ul class="nav main-menu menu navbar-nav d-flex align-items-center" style="gap: 30px;">
                                            <li class="{{Request::path()=='home' ? 'active' : ''}}"><a href="{{route('home')}}" class="font-weight-bold py-3 text-uppercase" style="color: #fff; letter-spacing: 0.5px; transition: color 0.3s; font-size: 14px;">Home</a></li>
                                            <li class="{{Request::path()=='about-us' ? 'active' : ''}}"><a href="{{route('about-us')}}" class="font-weight-bold py-3 text-uppercase" style="color: #fff; letter-spacing: 0.5px; transition: color 0.3s; font-size: 14px;">About Us</a></li>
                                            <li class="{{Request::path()=='contact' ? 'active' : ''}}"><a href="{{route('contact')}}" class="font-weight-bold py-3 text-uppercase" style="color: #fff; letter-spacing: 0.5px; transition: color 0.3s; font-size: 14px;">Contact Us</a></li>
                                            <li class="{{Request::path()=='cart' ? 'active' : ''}}"><a href="{{route('cart')}}" class="font-weight-bold py-3 text-uppercase" style="color: #fff; letter-spacing: 0.5px; transition: color 0.3s; font-size: 14px;">Cart</a></li>
                                            <li class="{{Request::path()=='wishlist' ? 'active' : ''}}"><a href="{{route('wishlist')}}" class="font-weight-bold py-3 text-uppercase" style="color: #fff; letter-spacing: 0.5px; transition: color 0.3s; font-size: 14px;">Wishlist</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </nav>
                            <!--/ End Main Menu -->	
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<style>
    /* Header Active states & Hovers */
    .header.b2b-header .nav.main-menu li.active a {
        color: var(--accent) !important;
        border-bottom: 3px solid var(--accent);
    }
    .header.b2b-header .nav.main-menu li:hover a {
        color: var(--accent) !important;
    }
    
    /* Breadcrumbs B2B Overrides */
    .breadcrumbs {
        background: var(--bg-soft) !important;
        border-bottom: 1px solid var(--border-color);
        padding: 20px 0;
    }
    .breadcrumbs .bread-list li a {
        color: var(--text-muted) !important;
        font-weight: 600;
    }
    .breadcrumbs .bread-list li a:hover {
        color: var(--primary) !important;
    }
    .breadcrumbs .bread-list li.active a {
        color: var(--primary) !important;
    }
    .breadcrumbs .bread-list li i {
        color: var(--border-color);
    }
</style>
