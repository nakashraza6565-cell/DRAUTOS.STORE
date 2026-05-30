<nav class="navbar navbar-expand navbar-light topbar mb-4 static-top px-4" style="
    background: #ffffff;
    border-bottom: 3px solid transparent;
    border-image: linear-gradient(90deg, #083259, #facc15, #083259) 1;
    box-shadow: 0 4px 20px rgba(8,50,89,0.08);
">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link rounded-circle mr-3 d-md-none" style="color:#083259;">
      <i class="fa fa-bars"></i>
    </button>

    <div class="d-none d-md-block">
        <h6 class="m-0" style="font-family:'Montserrat',sans-serif; font-weight:800; font-size:12px; color:#083259; text-transform:uppercase; letter-spacing:1px;">Customer Portal</h6>
    </div>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto align-items-center">

      {{-- Home page --}}
      <li class="nav-item mx-1">
        <a class="nav-link d-flex align-items-center justify-content-center" href="{{route('home')}}" target="_blank"
           style="width:36px; height:36px; border-radius:8px; background:#f0f4f8; color:#083259;" title="View Storefront">
          <i class="fas fa-store" style="font-size:14px;"></i>
        </a>
      </li>

      <div class="topbar-divider d-none d-sm-block"></div>

      <!-- Nav Item - User Information -->
      <li class="nav-item dropdown no-arrow ml-2">
        <a class="nav-link dropdown-toggle pr-0 d-flex align-items-center" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <div class="d-none d-md-flex flex-column align-items-end mr-3">
              <span style="font-family:'Montserrat',sans-serif; font-size:12px; font-weight:800; color:#083259; line-height:1;">{{Auth()->user()->name}}</span>
              <span style="font-size:9px; font-weight:600; color:#a3b1c6; text-transform:uppercase; letter-spacing:0.5px;">Premium Customer</span>
          </div>
          <div class="position-relative">
              @if(Auth()->user()->photo)
                <img class="img-profile rounded-circle" src="{{Auth()->user()->photo}}" style="width:40px; height:40px; border:2px solid #facc15; box-shadow: 0 2px 8px rgba(8,50,89,0.2);">
              @else
                <img class="img-profile rounded-circle" src="{{asset('backend/img/avatar.png')}}" style="width:40px; height:40px; border:2px solid #facc15; box-shadow: 0 2px 8px rgba(8,50,89,0.2);">
              @endif
              <span class="position-absolute border border-white rounded-circle bg-success" style="width:10px; height:10px; bottom:1px; right:1px;"></span>
          </div>
        </a>
        <!-- Dropdown - User Information -->
        <div class="dropdown-menu dropdown-menu-right shadow border-0 animated--grow-in mt-3" aria-labelledby="userDropdown" style="border-radius:12px; min-width:180px; border-top: 3px solid #facc15 !important;">
          <div class="px-4 py-3 mb-1 d-lg-none" style="background:#f8fafc; border-radius:9px 9px 0 0;">
              <p class="mb-0" style="font-family:'Montserrat',sans-serif; font-weight:800; font-size:13px; color:#083259;">{{Auth()->user()->name}}</p>
              <p class="mb-0 text-muted extra-small" style="font-size:10px; color:#94a3b8 !important; font-weight:500;">{{Auth()->user()->email}}</p>
          </div>
          <a class="dropdown-item py-2 px-4" href="{{route('user.setting')}}">
            <i class="fas fa-user fa-sm fa-fw mr-3" style="color:#083259; opacity:0.6;"></i>
            My Profile
          </a>
          <a class="dropdown-item py-2 px-4" href="{{route('user.change.password.form')}}">
            <i class="fas fa-key fa-sm fa-fw mr-3" style="color:#083259; opacity:0.6;"></i>
            Security
          </a>
          <div class="dropdown-divider mx-4"></div>
          <a class="dropdown-item py-2 px-4 text-danger font-weight-700" href="{{ route('logout') }}"
                onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                 <i class="fas fa-sign-out-alt fa-sm fa-fw mr-3"></i> {{ __('Logout') }}
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
      </li>

    </ul>

</nav>
