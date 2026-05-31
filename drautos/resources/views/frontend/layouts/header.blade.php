<header class="header shop b2b-header-exact">
    <!-- Topbar (White) -->
    <div class="middle-inner py-3" style="background: #ffffff;">
        <div class="container">
            <div class="row align-items-center justify-content-between flex-nowrap">
                <!-- Logo -->
                <div class="col-auto">
                    <div class="logo m-0">
                        <a href="{{route('home')}}" class="d-flex align-items-center" style="text-decoration: none;">
                            <!-- Intertwined DR Graphic (Revue Font Native SVG Paths) -->
                            <div style="width: clamp(75px, 8vw, 100px); height: clamp(65px, 7vw, 85px); margin-right: 10px; flex-shrink: 0; display: flex; align-items: center; transform: translateY(-3px);">
                                <svg width="100%" height="100%" viewBox="0 0 75 65" xmlns="http://www.w3.org/2000/svg" style="display: block;">
                                  <path d="M 22.74 58.73 L 13.17 58.73 L 13.17 25.29 L 24.94 25.29 L 24.94 48.61 C 28.98 48.47 32.29 46.74 34.87 43.44 C 37.46 40.14 38.75 36.49 38.75 32.5 C 38.75 29.89 38.25 28.01 37.26 26.83 C 36.27 25.66 35.19 24.87 34.02 24.47 C 33.25 24.21 32.51 24.06 31.79 24.03 C 31.08 23.99 30.5 23.97 30.06 23.97 L 10.97 23.92 L 13.72 13.8 L 32.81 13.8 C 39.52 13.83 44.19 15.65 46.83 19.24 C 49.47 22.84 50.79 26.91 50.79 31.45 C 50.79 39.92 47.99 46.59 42.38 51.45 C 36.77 56.3 30.22 58.73 22.74 58.73 " fill="#083259" />
                                  <path d="M 36.48 20.54 L 56.18 20.54 C 60.18 20.58 63.08 21.43 64.9 23.11 C 66.72 24.78 67.86 26.64 68.33 28.68 C 68.39 29.08 68.45 29.5 68.5 29.94 C 68.55 30.37 68.58 30.79 68.58 31.19 C 68.58 34.16 67.71 36.67 65.98 38.73 C 64.24 40.79 62.19 42.57 59.83 44.07 C 61.39 47.2 63.18 50.21 65.2 53.11 C 67.22 56 69.38 58.76 71.68 61.39 L 62.73 67.59 C 59.43 63.86 56.49 59.84 53.93 55.53 C 51.36 51.22 49.06 46.79 47.03 42.23 C 47.53 41.83 48.14 41.38 48.88 40.88 C 49.61 40.38 50.36 39.85 51.13 39.28 C 52.46 38.28 53.68 37.19 54.8 36.01 C 55.92 34.83 56.48 33.62 56.48 32.39 C 56.48 31.43 56.13 30.78 55.45 30.44 C 54.77 30.11 54.03 29.93 53.23 29.89 C 52.96 29.86 52.7 29.84 52.45 29.84 C 52.2 29.84 51.96 29.84 51.73 29.84 C 51.66 29.84 51.59 29.84 51.53 29.84 C 51.46 29.84 51.39 29.84 51.33 29.84 L 33.98 29.79 L 36.48 20.54 Z M 46.68 31.04 L 46.68 61.39 L 35.98 61.39 L 35.98 31.04 L 46.68 31.04 Z " fill="#a3b1c6" stroke="#ffffff" stroke-width="2.5" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <!-- Logo Text -->
                            <div class="d-flex flex-column justify-content-center" style="white-space: nowrap; transform: translateY(-1px);">
                                <h3 style="color: #083259; font-family: 'Montserrat', 'Inter', sans-serif; font-size: 21px; font-weight: 900; letter-spacing: 0px; margin: 0; line-height: 1;">DANYAL AUTOS</h3>
                                <span style="color: #64748b; font-family: 'Montserrat', 'Inter', sans-serif; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 3px;">PREMIUM TRUCK PARTS B2B</span>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Mobile Login + Cart + Nav Toggle -->
                <div class="col-auto d-md-none d-flex justify-content-end align-items-center" style="gap: 12px;">
                    <!-- Mobile Login / Account Button -->
                    @auth
                        <a href="{{ Auth::user()->role == 'admin' ? route('admin') : route('user') }}" class="d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; border-radius: 50%; background: var(--primary); color: #fff; font-size: 16px; text-decoration: none; flex-shrink: 0;" title="My Account">
                            <i class="ti-user"></i>
                        </a>
                    @else
                        <a href="{{route('login')}}" class="d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; border-radius: 50%; background: var(--primary); color: #fff; font-size: 16px; text-decoration: none; flex-shrink: 0;" title="Login">
                            <i class="ti-user"></i>
                        </a>
                    @endauth
                    <!-- Mobile Cart -->
                    <a href="#" id="mobileCartToggle" onclick="openCartPane(); return false;" class="text-dark d-flex align-items-center" style="font-size: 22px; position: relative;">
                        <i class="ti-shopping-cart"></i>
                        <span class="total-count cart-count-badge" style="position: absolute; top: -8px; right: -12px; background: var(--accent); color: #000; width: 18px; height: 18px; border-radius: 50%; font-size: 10px; font-weight: bold; display: flex; align-items: center; justify-content: center;">{{Helper::cartCount()}}</span>
                    </a>
                    <div class="mobile-nav"></div>
                </div>
                
                <!-- Right Navigation -->
                <div class="col-lg-8 col-md-8 col-12 d-none d-md-block">
                    <ul class="list-main d-flex align-items-center justify-content-end mb-0" style="gap: 25px;">
                        <li class="dropdown" style="position: relative;">
                            <a href="#" class="font-weight-bold text-dark text-uppercase dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="font-size: 13px; letter-spacing: 0.5px;">Truck Models <i class="ti-angle-down ml-1" style="font-size: 10px;"></i></a>
                            <div class="dropdown-menu" style="position: absolute; left: 0; top: 100%; border-radius: 4px; border: 1px solid #eee; padding: 10px 0; min-width: 200px; box-shadow: 0px 5px 15px rgba(0,0,0,0.05); z-index: 9999;">
                                @php
                                    $truck_models = \App\Models\Product::whereNotNull('model')->where('model', '!=', '')->distinct()->orderBy('model')->pluck('model');
                                @endphp
                                @foreach($truck_models as $model)
                                    <a href="{{route('product-grids')}}?model={{urlencode($model)}}" class="dropdown-item" style="padding: 8px 20px; color: #333; font-size: 13px; font-weight: 500; display: block; transition: all 0.3s;" onmouseover="this.style.color='var(--primary)'; this.style.backgroundColor='#f8f9fa';" onmouseout="this.style.color='#333'; this.style.backgroundColor='transparent';">{{$model}}</a>
                                @endforeach
                            </div>
                        </li>
                        <li><a href="#" class="font-weight-bold text-dark text-uppercase" style="font-size: 13px; letter-spacing: 0.5px;">Brands</a></li>
                        <li><a href="#" class="font-weight-bold text-dark text-uppercase" style="font-size: 13px; letter-spacing: 0.5px;">Bulk Orders</a></li>
                        
                        @auth
                            <li class="dropdown">
                                <a href="{{ Auth::user()->role == 'admin' ? route('admin') : route('user') }}" class="font-weight-bold text-dark text-uppercase d-flex align-items-center" style="font-size: 13px; letter-spacing: 0.5px;">
                                    My Account <i class="ti-user ml-1" style="font-size: 16px;"></i>
                                </a>
                            </li>
                        @else
                            <li>
                                <a href="{{route('login')}}" class="font-weight-bold text-dark text-uppercase d-flex align-items-center" style="font-size: 13px; letter-spacing: 0.5px;">
                                    Login / Register <i class="ti-user ml-1" style="font-size: 16px;"></i>
                                </a>
                            </li>
                        @endauth
                        
                        <!-- Cart -->
                        <li class="shopping ml-2" style="position: relative;">
                            <a href="#" onclick="openCartPane(); return false;" class="text-dark d-flex align-items-center" style="font-size: 22px;" title="View Cart">
                                <i class="ti-shopping-cart"></i> 
                                <span class="total-count cart-count-badge" style="position: absolute; top: -8px; right: -12px; background: var(--accent); color: #000; width: 18px; height: 18px; border-radius: 50%; font-size: 10px; font-weight: bold; display: flex; align-items: center; justify-content: center;">{{Helper::cartCount()}}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bottom Bar (Navy) -->
    <div class="header-inner py-2" style="background: var(--primary); display: block !important;">
        <div class="container">
            <div class="row align-items-center">
                <!-- Main Menu Links -->
                <div class="col-lg-5 col-md-5 d-none d-md-block">
                    <nav class="navbar navbar-expand-lg p-0">
                        <div class="navbar-collapse">	
                            <ul class="nav main-menu menu navbar-nav d-flex align-items-center" style="gap: 30px; margin: 0;">
                                <li class="{{Request::path()=='home' ? 'active' : ''}}">
                                    <a href="{{route('home')}}" class="font-weight-bold text-uppercase" style="color: var(--accent); font-size: 13px; letter-spacing: 0.5px;">Home</a>
                                </li>
                                <li class="dropdown">
                                    <a href="#" class="font-weight-bold text-uppercase text-white d-flex align-items-center" style="font-size: 13px; letter-spacing: 0.5px;">
                                        Shop By Category <i class="ti-angle-down ml-1" style="font-size: 10px;"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
                
                <!-- Search Bar -->
                <div class="col-lg-7 col-md-7 col-12">
                    <div class="w-100" style="position: relative;">
                        <form method="POST" action="{{route('product.search')}}" class="d-flex w-100" style="height: 40px; border-radius: 4px; overflow: hidden; margin: 0;">
                            @csrf
                            <input name="search" placeholder="Search by Part No., OEM, VIN..." autocomplete="off" type="text" class="px-3" style="flex-grow: 1; border: none; outline: none; font-size: 14px; color: var(--text-main);" required>
                            <button type="submit" style="width: 50px; border: none; background: var(--accent); color: #000; font-size: 18px; cursor: pointer; transition: background 0.2s;">
                                <i class="ti-search"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<style>
    /* Exact Mockup Header Overrides */
    .header.b2b-header-exact .nav.main-menu li.active a {
        color: var(--accent) !important;
        position: relative;
    }
    .header.b2b-header-exact .nav.main-menu li.active a::after {
        content: "";
        position: absolute;
        bottom: -15px;
        left: 0;
        width: 100%;
        height: 3px;
        background: var(--accent);
    }
    .header.b2b-header-exact .nav.main-menu li:hover a {
        color: var(--accent) !important;
    }
    .header.b2b-header-exact .list-main li a:hover {
        color: var(--primary) !important;
    }
</style>
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
