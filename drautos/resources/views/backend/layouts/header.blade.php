<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow-sm" style="border-bottom: 1px solid rgba(0,0,0,0.05);">

    <!-- Brand (desktop only; mobile keeps original launcher-first header) -->
    <a href="{{route('admin')}}" class="navbar-brand d-none d-md-flex align-items-center font-weight-bold" style="font-weight: 800; letter-spacing: -0.5px;">
        <i class="fas fa-truck-front mr-2" style="color: var(--accent); font-size: 1.2rem;"></i>
        <span style="font-size: 0.95rem;">DR AUTOS</span>
    </a>

    <!-- Mobile Menu Trigger (Launcher) -->
    <button class="mobile-menu-trigger d-md-none border-0 mr-2" id="launcherTrigger" style="width: 45px; height: 45px; border-radius: 12px; background: #f1f5f9; color: #1e293b;">
        <i class="fas fa-th-large" style="font-size: 1.25rem;"></i>
    </button>

    <!-- Tabler-like top navigation (desktop only — each sidebar category becomes its own dropdown) -->
    <ul class="navbar-nav d-none d-md-flex align-items-center top-nav-categories" id="topNavCategories">
        @can('view-dashboard')
        <li class="nav-item">
            <a class="nav-link px-2 top-nav-link" href="{{route('admin')}}">Dashboard</a>
        </li>
        <li class="nav-item">
            <a class="nav-link px-2 top-nav-link" href="{{route('admin.activity-logs')}}">Activity Log</a>
        </li>
        @endcan
        {{-- Category dropdowns (Point of Sale, Inventory & Assets, etc.) injected from sidebar --}}
    </ul>

    <a href="{{route('cache.clear')}}" class="btn btn-outline-danger btn-sm mr-3 d-none d-md-inline-block">
        Cache Clear
    </a>

    <!-- Topbar Navbar (right side) -->
    <ul class="navbar-nav ml-auto align-items-center">



      {{-- Home page --}}
      <li class="nav-item dropdown no-arrow mx-1">
        <a class="nav-link dropdown-toggle" href="{{route('home')}}" target="_blank" data-toggle="tooltip" data-placement="bottom" title="home"  role="button" style="padding: 10px;">
          <i class="fas fa-home fa-fw" style="font-size: 1.2rem;"></i>
        </a>
      </li>

      {{-- Push Notifications Subscribe --}}
      <li class="nav-item mx-1">
        <a class="nav-link" href="#" id="onesignal-manual-subscribe" data-toggle="tooltip" data-placement="bottom" title="Enable Phone Notifications" style="padding: 10px;">
          <i class="fas fa-bell text-danger fa-fw" style="font-size: 1.2rem;"></i>
        </a>
      </li>

      <!-- Nav Item - Alerts -->
      <li class="nav-item dropdown no-arrow mx-1">
       @include('backend.notification.show')
      </li>

      <!-- Nav Item - Messages -->
      <li class="nav-item dropdown no-arrow mx-1" id="messageT" data-url="{{route('messages.five')}}">
        @include('backend.message.message')
      </li>

      <div class="topbar-divider d-none d-sm-block"></div>

      <!-- Nav Item - User Information -->
      <li class="nav-item dropdown no-arrow ml-2">
        <a class="nav-link dropdown-toggle p-0 d-flex align-items-center" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <div class="d-none d-md-flex flex-column align-items-end mr-2">
              <span class="text-gray-800 small font-weight-bold" style="line-height: 1;">{{Auth()->user()->name}}</span>
              <span class="text-gray-500" style="font-size: 0.7rem;">Admin</span>
          </div>
          <div class="position-relative">
              @if(Auth()->user()->photo)
                <img class="img-profile rounded-circle" src="{{Auth()->user()->photo}}" style="width: 42px; height: 42px; border: 2px solid #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
              @else
                <img class="img-profile rounded-circle" src="{{asset('backend/img/avatar.png')}}" style="width: 42px; height: 42px; border: 2px solid #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
              @endif
              <span class="position-absolute border border-white rounded-circle bg-success" style="width: 12px; height: 12px; bottom: 0; right: 0;"></span>
          </div>
        </a>
        <!-- Dropdown - User Information -->
        <div class="dropdown-menu dropdown-menu-right shadow border-0 animated--grow-in" aria-labelledby="userDropdown" style="border-radius: 12px; margin-top: 10px;">
          <a class="dropdown-item py-2" href="{{route('admin-profile')}}">
            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
            Profile
          </a>
          <a class="dropdown-item py-2" href="{{route('change.password.form')}}">
            <i class="fas fa-key fa-sm fa-fw mr-2 text-gray-400"></i>
            Change Password
          </a>
          <a class="dropdown-item py-2" href="{{route('settings')}}">
            <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
            Settings
          </a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item py-2 text-danger" href="{{ route('logout') }}"
                onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                 <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-danger opacity-50"></i> {{ __('Logout') }}
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
      </li>

    </ul>

</nav>
