<header class="header shop b2b-header-exact">
    <!-- Topbar (White) -->
    <div class="middle-inner py-3" style="background: #ffffff;">
        <div class="container">
            <div class="row align-items-center justify-content-between">
                <!-- Logo -->
                <div class="col-lg-4 col-md-4 col-8">
                    <div class="logo m-0">
                        <a href="{{route('home')}}" class="d-flex align-items-center" style="text-decoration: none;">
                            <!-- Intertwined DR Custom Graphic -->
                            <div style="width: 55px; height: 55px; margin-right: 15px; flex-shrink: 0;">
                                <img src="{{asset('frontend/images/dr_logo.png')}}" alt="DR Logo" style="width: 100%; height: 100%; object-fit: contain; mix-blend-mode: multiply;">
                            </div>
                            <!-- Logo Text -->
                            <div class="d-flex flex-column justify-content-center">
                                <h3 style="color: #031430; font-size: clamp(18px, 4vw, 26px); font-weight: 900; letter-spacing: -0.5px; margin: 0; line-height: 1;">DANYAL AUTOS</h3>
                                <span style="color: #64748b; font-size: clamp(9px, 2vw, 13px); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px;">PREMIUM TRUCK PARTS B2B</span>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Mobile Cart & Nav Toggle -->
                <div class="col-4 d-md-none d-flex justify-content-end align-items-center">
                    <a href="{{route('cart')}}" class="text-dark d-flex align-items-center mr-3" style="font-size: 22px; position: relative;">
                        <i class="ti-shopping-cart"></i> 
                        <span class="total-count" style="position: absolute; top: -8px; right: -12px; background: var(--accent); color: #000; width: 18px; height: 18px; border-radius: 50%; font-size: 10px; font-weight: bold; display: flex; align-items: center; justify-content: center;">{{Helper::cartCount()}}</span>
                    </a>
                    <div class="mobile-nav"></div>
                </div>
                
                <!-- Right Navigation -->
                <div class="col-lg-8 col-md-8 col-12 d-none d-md-block">
                    <ul class="list-main d-flex align-items-center justify-content-end mb-0" style="gap: 25px;">
                        <li><a href="#" class="font-weight-bold text-dark text-uppercase" style="font-size: 13px; letter-spacing: 0.5px;">Truck Models</a></li>
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
                            <a href="{{route('cart')}}" class="text-dark d-flex align-items-center" style="font-size: 22px;">
                                <i class="ti-shopping-cart"></i> 
                                <span class="total-count" style="position: absolute; top: -8px; right: -12px; background: var(--accent); color: #000; width: 18px; height: 18px; border-radius: 50%; font-size: 10px; font-weight: bold; display: flex; align-items: center; justify-content: center;">{{Helper::cartCount()}}</span>
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
                    <form method="POST" action="{{route('product.search')}}" class="w-100">
                        @csrf
                        <div class="d-flex w-100" style="height: 40px; border-radius: 4px; overflow: hidden;">
                            <input name="search" placeholder="Search by Part No., OEM, Vehicle VIN" type="text" class="px-3" style="flex-grow: 1; border: none; outline: none; font-size: 14px; color: var(--text-main);">
                            <button type="submit" style="width: 50px; border: none; background: var(--accent); color: #000; font-size: 18px; cursor: pointer; transition: background 0.2s;">
                                <i class="ti-search"></i>
                            </button>
                        </div>
                    </form>
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
