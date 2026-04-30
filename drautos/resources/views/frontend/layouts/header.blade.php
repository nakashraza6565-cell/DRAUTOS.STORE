<header class="header shop">
    <!-- Topbar -->
    <div class="topbar py-2" style="background: #1e293b; border-bottom: 1px solid rgba(255,255,255,0.1);">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5 col-md-12 col-12">
                    <!-- Top Left -->
                    <div class="top-left">
                        <ul class="list-main d-flex align-items-center mb-0" style="gap: 20px;">
                            <li class="text-white small"><i class="ti-headphone-alt mr-2 text-warning"></i> +923042000274</li>
                            <li class="text-white small"><i class="ti-email mr-2 text-warning"></i> support@danyalautos.com</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-7 col-md-12 col-12">
                    <!-- Top Right -->
                    <div class="right-content">
                        <ul class="list-main d-flex align-items-center justify-content-end mb-0" style="gap: 15px;">
                            <li class="d-none d-lg-block small"><i class="ti-location-pin text-warning mr-1"></i> <a href="{{route('contact')}}" class="text-white">Store location</a></li>
                            @auth 
                                @if(Auth::user()->role == 'admin')
                                    <li><a href="{{route('admin')}}" class="btn btn-sm btn-warning font-weight-bold px-3 py-1 text-dark" style="border-radius: 6px;"><i class="ti-user mr-1"></i> Dashboard</a></li>
                                @else 
                                    <li><a href="{{route('user')}}" class="btn btn-sm btn-warning font-weight-bold px-3 py-1 text-dark" style="border-radius: 6px;"><i class="ti-user mr-1"></i> Dashboard</a></li>
                                @endif
                                <li><a href="{{route('user.logout')}}" class="btn btn-sm btn-outline-light font-weight-bold px-3 py-1" style="border-radius: 6px; font-size: 11px;"><i class="ti-power-off mr-1"></i> Logout</a></li>
                            @else
                                <li><a href="{{route('login')}}" class="btn btn-sm btn-warning font-weight-bold px-3 py-1 text-dark" style="border-radius: 6px; box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3);"><i class="ti-power-off mr-1"></i> Login</a></li>
                                <li class="d-none d-sm-block"><a href="{{route('register')}}" class="btn btn-sm btn-outline-light font-weight-bold px-3 py-1" style="border-radius: 6px;">Register</a></li>
                            @endauth
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Middle Header -->
    <div class="middle-inner py-4" style="background: #fff;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-3 col-md-3 col-12">
                    <div class="logo">
                        <a href="{{route('home')}}">
                            <h3 class="font-weight-bold" style="color: #1e293b; letter-spacing: -1px;">
                                <span class="bg-warning text-dark px-2 rounded mr-1" style="box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);"><i class="fas fa-car-side"></i> DrAutos</span>Store
                            </h3>
                        </a>
                    </div>
                    <!-- Search Form -->
                    <div class="search-top">
                        <div class="top-search"><a href="#0" class="text-dark"><i class="ti-search"></i></a></div>
                        <div class="search-top">
                            <form class="search-form" method="POST" action="{{route('product.search')}}">
                                @csrf
                                <input type="text" placeholder="Search here..." name="search" style="border-radius: 100px;">
                                <button value="search" type="submit" style="border-radius: 0 100px 100px 0;"><i class="ti-search"></i></button>
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
                        <div class="search-bar" style="border: 2px solid #f1f5f9; border-radius: 12px; overflow: hidden; background: #f8fafc;">
                            <select name="cat_id" style="background: transparent; border: none; font-weight: 600; color: #475569;">
                                <option value="">Categories</option>
                                @php
                                    $categories = DB::table('categories')->where('status', 'active')->where('is_parent', 1)->get();
                                @endphp
                                @foreach($categories as $cat)
                                    <option value="{{$cat->id}}">{{$cat->title}}</option>
                                @endforeach
                            </select>
                            <input name="search" placeholder="Search auto parts, tools, accessories..." type="search" style="background: transparent;">
                            <button class="btnn" type="submit" style="background: #1e293b; color: #fff; height: 100%; border-radius: 0 10px 10px 0;"><i class="ti-search"></i></button>
                        </div>
                    </div>
                    </form>
                    <!--/ End Search Form -->
                </div>
                <div class="col-lg-3 col-md-3 col-12 d-flex justify-content-end">
                   <div class="right-bar d-flex align-items-center" style="gap: 20px;">
                         <!-- Search Form -->
                         <div class="sinlge-bar shopping">
                            <a href="{{route('wishlist')}}" class="single-icon text-dark" style="font-size: 20px;"><i class="fa fa-heart-o"></i> <span class="total-count bg-danger" style="top: -5px; right: -10px;">{{Helper::wishlistCount()}}</span></a>
                        </div>
                        <div class="sinlge-bar shopping">
                            <a href="{{route('cart')}}" class="single-icon text-dark" style="font-size: 20px;"><i class="ti-bag"></i> <span class="total-count bg-warning text-dark" style="top: -5px; right: -10px;">{{Helper::cartCount()}}</span></a>
                             <!-- Shopping Item -->
                            @auth
                                <div class="shopping-item shadow-lg border-0" style="border-radius: 16px; overflow: hidden; padding: 0;">
                                    <div class="dropdown-cart-header p-3" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                                        <span class="font-weight-bold text-dark">{{count(Helper::getAllProductFromCart())}} Items</span>
                                        <a href="{{route('cart')}}" class="text-primary font-weight-bold">View Cart</a>
                                    </div>
                                    <ul class="shopping-list p-3" style="max-height: 300px; overflow-y: auto;">
                                        @foreach(Helper::getAllProductFromCart() as $data)
                                                @php
                                                    $photo=explode(',',$data->product['photo']);
                                                @endphp
                                                <li class="mb-3 d-flex align-items-center">
                                                    <a href="{{route('cart-delete',$data->id)}}" class="remove mr-3 text-danger" title="Remove item"><i class="fa fa-remove"></i></a>
                                                    <a class="cart-img rounded overflow-hidden mr-3" href="#" style="width: 50px; height: 50px; flex-shrink: 0;"><img src="{{$photo[0]}}" alt="{{$photo[0]}}" style="object-fit: cover; width: 100%; height: 100%;"></a>
                                                    <div class="flex-grow-1">
                                                        <h4 class="mb-0" style="font-size: 13px;"><a href="{{route('product-detail',$data->product['slug'])}}" class="text-dark font-weight-bold">{{$data->product['title']}}</a></h4>
                                                        <p class="quantity small text-muted">{{$data->quantity}} x <span class="amount text-primary font-weight-bold">${{number_format($data->price,2)}}</span></p>
                                                    </div>
                                                </li>
                                        @endforeach
                                    </ul>
                                    <div class="bottom p-3" style="background: #fff; border-top: 1px solid #e2e8f0;">
                                        <div class="total d-flex justify-content-between mb-3">
                                            <span class="text-muted font-weight-bold">Total</span>
                                            <span class="total-amount text-dark h5 mb-0 font-weight-bold">${{number_format(Helper::totalCartPrice(),2)}}</span>
                                        </div>
                                        <div class="d-flex" style="gap: 10px;">
                                            <a href="{{route('checkout')}}" class="btn flex-grow-1 py-2 font-weight-bold" style="background: #1e293b; color: #fff; border-radius: 8px;">Checkout</a>
                                            <a href="{{route('cart.clear')}}" class="btn btn-outline-danger flex-grow-1 py-2 font-weight-bold" style="border-radius: 8px;">Clear</a>
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
    <div class="header-inner" style="background: #f8fafc; border-top: 1px solid #e2e8f0; border-bottom: 2px solid #e2e8f0;">
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
                                            <li class="{{Request::path()=='home' ? 'active' : ''}}"><a href="{{route('home')}}" class="font-weight-bold py-3" style="color: #475569; letter-spacing: 0.5px;">Home</a></li>
                                            <li class="{{Request::path()=='about-us' ? 'active' : ''}}"><a href="{{route('about-us')}}" class="font-weight-bold py-3" style="color: #475569; letter-spacing: 0.5px;">About Us</a></li>
                                            <li class="{{Request::path()=='contact' ? 'active' : ''}}"><a href="{{route('contact')}}" class="font-weight-bold py-3" style="color: #475569; letter-spacing: 0.5px;">Contact Us</a></li>
                                            <li class="{{Request::path()=='cart' ? 'active' : ''}}"><a href="{{route('cart')}}" class="font-weight-bold py-3" style="color: #475569; letter-spacing: 0.5px;">Cart</a></li>
                                            <li class="{{Request::path()=='wishlist' ? 'active' : ''}}"><a href="{{route('wishlist')}}" class="font-weight-bold py-3" style="color: #475569; letter-spacing: 0.5px;">Wishlist</a></li>
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
