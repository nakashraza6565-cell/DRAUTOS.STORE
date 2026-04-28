<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow-sm px-4">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link rounded-circle mr-3 d-md-none text-primary">
      <i class="fa fa-bars"></i>
    </button>

    <div class="d-none d-md-block">
        <h6 class="m-0 font-weight-800 text-primary text-uppercase small" style="letter-spacing:1px;">Customer Portal</h6>
    </div>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto align-items-center">

      {{-- Home page --}}
      <li class="nav-item mx-1">
        <a class="nav-link text-gray-500" href="{{route('home')}}" target="_blank" title="Store Front">
          <i class="fas fa-external-link-alt"></i>
        </a>
      </li>

      <div class="topbar-divider d-none d-sm-block"></div>

      <!-- Nav Item - User Information -->
      <li class="nav-item dropdown no-arrow">
        <a class="nav-link dropdown-toggle pr-0" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="mr-3 d-none d-lg-inline text-gray-700 font-weight-700 small">{{Auth()->user()->name}}</span>
          <img class="img-profile rounded-circle shadow-sm" style="border: 2px solid #f8fafc;" 
               src="{{Auth()->user()->photo ? Auth()->user()->photo : asset('backend/img/avatar.png')}}">
        </a>
        <!-- Dropdown - User Information -->
        <div class="dropdown-menu dropdown-menu-right shadow border-0 animated--fade-in mt-3" aria-labelledby="userDropdown" style="border-radius:12px;">
          <div class="px-4 py-3 border-bottom d-lg-none">
              <p class="mb-0 font-weight-800 text-primary small">{{Auth()->user()->name}}</p>
              <p class="mb-0 text-muted extra-small" style="font-size:0.65rem;">{{Auth()->user()->email}}</p>
          </div>
          <a class="dropdown-item py-2 px-4" href="{{route('user.setting')}}">
            <i class="fas fa-user fa-sm fa-fw mr-3 text-gray-400"></i>
            My Profile
          </a>
          <a class="dropdown-item py-2 px-4" href="{{route('user.change.password.form')}}">
            <i class="fas fa-key fa-sm fa-fw mr-3 text-gray-400"></i>
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
