<ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" style="background: var(--primary) !important;">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center my-3" href="{{route('user')}}">
      <div class="sidebar-brand-icon rotate-n-15">
        <i class="fas fa-tools text-accent"></i>
      </div>
      <div class="sidebar-brand-text mx-3">DR AUTOS</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0 opacity-1">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{Request::routeIs('user') ? 'active' : ''}}">
      <a class="nav-link" href="{{route('user')}}">
        <i class="fas fa-fw fa-th-large"></i>
        <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading opacity-5 text-uppercase small" style="font-size:0.65rem; letter-spacing:1px;">
        Store
    </div>
    
    <!-- Online Order -->
    <li class="nav-item {{Request::routeIs('user.online-order') ? 'active' : ''}}">
        <a class="nav-link" href="{{route('user.online-order')}}">
            <i class="fas fa-cart-plus"></i>
            <span>New Order</span>
        </a>
    </li>

    <!--Orders -->
    <li class="nav-item {{Request::routeIs('user.order.*') ? 'active' : ''}}">
        <a class="nav-link" href="{{route('user.order.index')}}">
            <i class="fas fa-history"></i>
            <span>Order History</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <div class="sidebar-heading opacity-5 text-uppercase small" style="font-size:0.65rem; letter-spacing:1px;">
        Account
    </div>

    <!-- Returns & Claims -->
    <li class="nav-item {{Request::routeIs('user.returns.*') ? 'active' : ''}}">
        <a class="nav-link" href="{{route('user.returns.index')}}">
            <i class="fas fa-undo-alt"></i>
            <span>Returns & Claims</span>
        </a>
    </li>

    <!-- Account Ledger -->
    <li class="nav-item {{Request::routeIs('user.ledger') ? 'active' : ''}}">
        <a class="nav-link" href="{{route('user.ledger')}}">
            <i class="fas fa-file-invoice-dollar"></i>
            <span>My Ledger</span>
        </a>
    </li>

    <!-- Settings -->
    <li class="nav-item {{Request::routeIs('user.setting') ? 'active' : ''}}">
        <a class="nav-link" href="{{route('user.setting')}}">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline mt-4">
      <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
