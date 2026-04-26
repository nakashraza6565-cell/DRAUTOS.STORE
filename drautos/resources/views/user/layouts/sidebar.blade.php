<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{route('user')}}">
      <div class="sidebar-brand-icon">
        <i class="fas fa-user-circle"></i>
      </div>
      <div class="sidebar-brand-text mx-3">USER</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{Request::routeIs('user') ? 'active' : ''}}">
      <a class="nav-link" href="{{route('user')}}">
        <i class="fas fa-fw fa-tachometer-alt"></i>
        <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Shopping
        </div>
    <!-- Online Order (POS) -->
    <li class="nav-item {{Request::routeIs('user.online-order') ? 'active' : ''}}">
        <a class="nav-link" href="{{route('user.online-order')}}">
            <i class="fas fa-cart-plus"></i>
            <span>Online Order</span>
        </a>
    </li>

    <!--Orders -->
    <li class="nav-item {{Request::routeIs('user.order.*') ? 'active' : ''}}">
        <a class="nav-link" href="{{route('user.order.index')}}">
            <i class="fas fa-shopping-basket"></i>
            <span>My Orders</span>
        </a>
    </li>

    <!-- Returns & Claims -->
    <li class="nav-item {{Request::routeIs('user.returns.*') ? 'active' : ''}}">
        <a class="nav-link" href="{{route('user.returns.index')}}">
            <i class="fas fa-undo"></i>
            <span>Returns & Claims</span>
        </a>
    </li>

    <!-- Account Ledger -->
    <li class="nav-item {{Request::routeIs('user.ledger') ? 'active' : ''}}">
        <a class="nav-link" href="{{route('user.ledger')}}">
            <i class="fas fa-file-invoice-dollar"></i>
            <span>Account Ledger</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">
    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
      <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
