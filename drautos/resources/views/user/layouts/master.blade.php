<!DOCTYPE html>
<html lang="en">

@include('user.layouts.head')

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Sidebar -->
    @include('user.layouts.sidebar')
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        @include('user.layouts.header')
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        @yield('main-content')
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->
      @include('user.layouts.footer')

      <!-- Mobile Bottom Navigation -->
      <nav class="mobile-nav">
          <a href="{{route('user')}}" class="nav-item-mobile {{Request::is('user') ? 'active' : ''}}">
              <i class="fas fa-home"></i>
              <span>Home</span>
          </a>
          <a href="{{route('user.order.index')}}" class="nav-item-mobile {{Request::is('user/order*') ? 'active' : ''}}">
              <i class="fas fa-box"></i>
              <span>Orders</span>
          </a>
          <a href="{{route('user.online-order')}}" class="nav-item-mobile {{Request::is('user/online-order*') ? 'active' : ''}}">
              <i class="fas fa-cart-plus"></i>
              <span>New</span>
          </a>
          <a href="{{route('user.returns.index')}}" class="nav-item-mobile {{Request::is('user/returns*') ? 'active' : ''}}">
              <i class="fas fa-undo-alt"></i>
              <span>Returns</span>
          </a>
          <a href="{{route('user.setting')}}" class="nav-item-mobile {{Request::is('user/setting*') ? 'active' : ''}}">
              <i class="fas fa-user-circle"></i>
              <span>Profile</span>
          </a>
      </nav>

</body>

</html>
