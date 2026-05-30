<ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" style="background: var(--sidebar-bg) !important; border-right: 1px solid rgba(255,255,255,0.05); transition: all 0.3s ease;">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center px-4 py-4" href="{{route('user')}}" style="height: auto; opacity: 1; text-decoration: none; border-bottom: 1px solid rgba(255,255,255,0.06); margin-bottom: 8px;">
        <!-- DR SVG Logo -->
        <div style="flex-shrink: 0; width: 52px; height: 46px; display: flex; align-items: center;">
            <svg width="52" height="46" viewBox="0 0 75 65" xmlns="http://www.w3.org/2000/svg">
                <path d="M 22.74 58.73 L 13.17 58.73 L 13.17 25.29 L 24.94 25.29 L 24.94 48.61 C 28.98 48.47 32.29 46.74 34.87 43.44 C 37.46 40.14 38.75 36.49 38.75 32.5 C 38.75 29.89 38.25 28.01 37.26 26.83 C 36.27 25.66 35.19 24.87 34.02 24.47 C 33.25 24.21 32.51 24.06 31.79 24.03 C 31.08 23.99 30.5 23.97 30.06 23.97 L 10.97 23.92 L 13.72 13.8 L 32.81 13.8 C 39.52 13.83 44.19 15.65 46.83 19.24 C 49.47 22.84 50.79 26.91 50.79 31.45 C 50.79 39.92 47.99 46.59 42.38 51.45 C 36.77 56.3 30.22 58.73 22.74 58.73 " fill="#ffffff"/>
                <path d="M 36.48 20.54 L 56.18 20.54 C 60.18 20.58 63.08 21.43 64.9 23.11 C 66.72 24.78 67.86 26.64 68.33 28.68 C 68.39 29.08 68.45 29.5 68.5 29.94 C 68.55 30.37 68.58 30.79 68.58 31.19 C 68.58 34.16 67.71 36.67 65.98 38.73 C 64.24 40.79 62.19 42.57 59.83 44.07 C 61.39 47.2 63.18 50.21 65.2 53.11 C 67.22 56 69.38 58.76 71.68 61.39 L 62.73 67.59 C 59.43 63.86 56.49 59.84 53.93 55.53 C 51.36 51.22 49.06 46.79 47.03 42.23 C 47.53 41.83 48.14 41.38 48.88 40.88 C 49.61 40.38 50.36 39.85 51.13 39.28 C 52.46 38.28 53.68 37.19 54.8 36.01 C 55.92 34.83 56.48 33.62 56.48 32.39 C 56.48 31.43 56.13 30.78 55.45 30.44 C 54.77 30.11 54.03 29.93 53.23 29.89 C 52.96 29.86 52.7 29.84 52.45 29.84 C 52.2 29.84 51.96 29.84 51.73 29.84 C 51.66 29.84 51.59 29.84 51.53 29.84 C 51.46 29.84 51.39 29.84 51.33 29.84 L 33.98 29.79 L 36.48 20.54 Z M 46.68 31.04 L 46.68 61.39 L 35.98 61.39 L 35.98 31.04 L 46.68 31.04 Z" fill="#facc15" stroke="#062038" stroke-width="1.5" stroke-linejoin="round"/>
            </svg>
        </div>
        <div class="ml-3 text-left" style="line-height: 1;">
            <div style="font-family: 'Montserrat', sans-serif; font-weight: 900; font-size: 17px; color: #ffffff; letter-spacing: 0px;">DANYAL AUTOS</div>
            <div style="font-family: 'Inter', sans-serif; font-weight: 600; font-size: 9px; color: #a3b1c6; text-transform: uppercase; letter-spacing: 1.5px; margin-top: 3px;">Customer Portal</div>
        </div>
    </a>

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{Request::routeIs('user') ? 'active' : ''}}" style="margin: 0.2rem 0.8rem;">
      <a class="nav-link" href="{{route('user')}}" style="border-radius: 8px; padding: 0.8rem 1rem;">
        <i class="fas fa-fw fa-th-large" style="color: {{Request::routeIs('user') ? 'var(--accent)' : 'rgba(255,255,255,0.6)'}};"></i>
        <span style="color: {{Request::routeIs('user') ? '#fff' : 'rgba(255,255,255,0.7)'}}; font-weight: {{Request::routeIs('user') ? '700' : '500'}};">Dashboard</span>
      </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider" style="border-top: 1px solid rgba(255,255,255,0.05); margin: 1rem;">

    <!-- Heading -->
    <div class="sidebar-heading px-4 mb-2" style="color: #64748b; font-weight: 700; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.15em;">
        Store
    </div>
    
    <!-- Online Order -->
    <li class="nav-item {{Request::routeIs('user.online-order') ? 'active' : ''}}" style="margin: 0.2rem 0.8rem;">
        <a class="nav-link" href="{{route('user.online-order')}}" style="border-radius: 8px; padding: 0.8rem 1rem;">
            <i class="fas fa-cart-plus" style="color: {{Request::routeIs('user.online-order') ? 'var(--accent)' : 'rgba(255,255,255,0.6)'}};"></i>
            <span style="color: {{Request::routeIs('user.online-order') ? '#fff' : 'rgba(255,255,255,0.7)'}}; font-weight: {{Request::routeIs('user.online-order') ? '700' : '500'}};">New Order</span>
        </a>
    </li>

    <!--Orders -->
    <li class="nav-item {{Request::routeIs('user.order.*') || Request::routeIs('user.sales-order.*') ? 'active' : ''}}" style="margin: 0.2rem 0.8rem;">
        <a class="nav-link" href="{{route('user.order.index')}}" style="border-radius: 8px; padding: 0.8rem 1rem;">
            <i class="fas fa-history" style="color: {{(Request::routeIs('user.order.*') || Request::routeIs('user.sales-order.*')) ? 'var(--accent)' : 'rgba(255,255,255,0.6)'}};"></i>
            <span style="color: {{(Request::routeIs('user.order.*') || Request::routeIs('user.sales-order.*')) ? '#fff' : 'rgba(255,255,255,0.7)'}}; font-weight: {{(Request::routeIs('user.order.*') || Request::routeIs('user.sales-order.*')) ? '700' : '500'}};">My Orders</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider" style="border-top: 1px solid rgba(255,255,255,0.05); margin: 1rem;">

    <div class="sidebar-heading px-4 mb-2" style="color: #64748b; font-weight: 700; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.15em;">
        Account
    </div>

    <!-- Returns & Claims -->
    <li class="nav-item {{Request::routeIs('user.returns.*') ? 'active' : ''}}" style="margin: 0.2rem 0.8rem;">
        <a class="nav-link" href="{{route('user.returns.index')}}" style="border-radius: 8px; padding: 0.8rem 1rem;">
            <i class="fas fa-undo-alt" style="color: {{Request::routeIs('user.returns.*') ? 'var(--accent)' : 'rgba(255,255,255,0.6)'}};"></i>
            <span style="color: {{Request::routeIs('user.returns.*') ? '#fff' : 'rgba(255,255,255,0.7)'}}; font-weight: {{Request::routeIs('user.returns.*') ? '700' : '500'}};">Returns & Claims</span>
        </a>
    </li>

    <!-- Account Ledger -->
    <li class="nav-item {{Request::routeIs('user.ledger') ? 'active' : ''}}" style="margin: 0.2rem 0.8rem;">
        <a class="nav-link" href="{{route('user.ledger')}}" style="border-radius: 8px; padding: 0.8rem 1rem;">
            <i class="fas fa-file-invoice-dollar" style="color: {{Request::routeIs('user.ledger') ? 'var(--accent)' : 'rgba(255,255,255,0.6)'}};"></i>
            <span style="color: {{Request::routeIs('user.ledger') ? '#fff' : 'rgba(255,255,255,0.7)'}}; font-weight: {{Request::routeIs('user.ledger') ? '700' : '500'}};">My Ledger</span>
        </a>
    </li>

    <!-- Settings -->
    <li class="nav-item {{Request::routeIs('user.setting') ? 'active' : ''}}" style="margin: 0.2rem 0.8rem;">
        <a class="nav-link" href="{{route('user.setting')}}" style="border-radius: 8px; padding: 0.8rem 1rem;">
            <i class="fas fa-cog" style="color: {{Request::routeIs('user.setting') ? 'var(--accent)' : 'rgba(255,255,255,0.6)'}};"></i>
            <span style="color: {{Request::routeIs('user.setting') ? '#fff' : 'rgba(255,255,255,0.7)'}}; font-weight: {{Request::routeIs('user.setting') ? '700' : '500'}};">Settings</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block" style="border-top: 1px solid rgba(255,255,255,0.05); margin: 1rem;">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline mt-4 mb-4">
      <button class="rounded-circle border-0" id="sidebarToggle" style="background-color: rgba(255,255,255,0.08); width: 32px; height: 32px; font-size: 0.8rem;"></button>
    </div>

</ul>

<style>
    /* User Sidebar Active State styling matching backend */
    .sidebar-dark .nav-item.active {
        background: rgba(255, 255, 255, 0.08);
        border-radius: 8px;
    }
    .sidebar-dark .nav-item.active .nav-link {
        position: relative;
    }
    .sidebar-dark .nav-item.active .nav-link::before {
        content: '';
        position: absolute;
        left: 0;
        top: 15%;
        height: 70%;
        width: 3px;
        background: var(--accent);
        border-radius: 0 3px 3px 0;
    }
</style>
