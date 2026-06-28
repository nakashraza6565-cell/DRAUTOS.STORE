<nav class="navbar navbar-expand navbar-light topbar mb-4 static-top" style="
    background: #ffffff;
    border-bottom: 3px solid transparent;
    border-image: linear-gradient(90deg, #083259, #facc15, #083259) 1;
    box-shadow: 0 4px 20px rgba(8,50,89,0.08);
    padding: 0.6rem 1.5rem;
">

    <!-- Brand (desktop & mobile) — DR SVG + text -->
    <a href="{{route('admin')}}" class="navbar-brand d-flex align-items-center mr-auto" style="text-decoration: none; position: relative; z-index: 9999;">
        <svg width="48" height="42" viewBox="0 0 75 65" xmlns="http://www.w3.org/2000/svg" style="flex-shrink:0;">
            <path d="M 22.74 58.73 L 13.17 58.73 L 13.17 25.29 L 24.94 25.29 L 24.94 48.61 C 28.98 48.47 32.29 46.74 34.87 43.44 C 37.46 40.14 38.75 36.49 38.75 32.5 C 38.75 29.89 38.25 28.01 37.26 26.83 C 36.27 25.66 35.19 24.87 34.02 24.47 C 33.25 24.21 32.51 24.06 31.79 24.03 C 31.08 23.99 30.5 23.97 30.06 23.97 L 10.97 23.92 L 13.72 13.8 L 32.81 13.8 C 39.52 13.83 44.19 15.65 46.83 19.24 C 49.47 22.84 50.79 26.91 50.79 31.45 C 50.79 39.92 47.99 46.59 42.38 51.45 C 36.77 56.3 30.22 58.73 22.74 58.73 " fill="#083259"/>
            <path d="M 36.48 20.54 L 56.18 20.54 C 60.18 20.58 63.08 21.43 64.9 23.11 C 66.72 24.78 67.86 26.64 68.33 28.68 C 68.39 29.08 68.45 29.5 68.5 29.94 C 68.55 30.37 68.58 30.79 68.58 31.19 C 68.58 34.16 67.71 36.67 65.98 38.73 C 64.24 40.79 62.19 42.57 59.83 44.07 C 61.39 47.2 63.18 50.21 65.2 53.11 C 67.22 56 69.38 58.76 71.68 61.39 L 62.73 67.59 C 59.43 63.86 56.49 59.84 53.93 55.53 C 51.36 51.22 49.06 46.79 47.03 42.23 C 47.53 41.83 48.14 41.38 48.88 40.88 C 49.61 40.38 50.36 39.85 51.13 39.28 C 52.46 38.28 53.68 37.19 54.8 36.01 C 55.92 34.83 56.48 33.62 56.48 32.39 C 56.48 31.43 56.13 30.78 55.45 30.44 C 54.77 30.11 54.03 29.93 53.23 29.89 C 52.96 29.86 52.7 29.84 52.45 29.84 C 52.2 29.84 51.96 29.84 51.73 29.84 C 51.66 29.84 51.59 29.84 51.53 29.84 C 51.46 29.84 51.39 29.84 51.33 29.84 L 33.98 29.79 L 36.48 20.54 Z M 46.68 31.04 L 46.68 61.39 L 35.98 61.39 L 35.98 31.04 L 46.68 31.04 Z" fill="#facc15" stroke="#fff" stroke-width="1.5" stroke-linejoin="round"/>
        </svg>
        <div class="ml-2" style="line-height:1;">
            <div style="font-family:'Montserrat',sans-serif; font-weight:900; font-size:16px; color:#083259; letter-spacing:0;">DANYAL AUTOS</div>
            <div style="font-family:'Inter',sans-serif; font-size:9px; font-weight:700; color:#a3b1c6; text-transform:uppercase; letter-spacing:1.5px;">Admin Portal</div>
        </div>
    </a>

    <!-- Mobile Menu Trigger (Launcher) -->
    <button class="mobile-menu-trigger d-md-none border-0 mr-2 shadow-lg" id="launcherTrigger" style="position: fixed !important; top: 50% !important; left: 0 !important; right: auto !important; bottom: auto !important; transform: translateY(-50%) !important; width: 44px; height: 56px; border-radius: 0 12px 12px 0 !important; background: linear-gradient(135deg, #083259, #1a4b7c) !important; color: #facc15 !important; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 999999 !important;">
        <i class="fas fa-th-large" style="font-size: 1.2rem;"></i>
    </button>

    <!-- Top navigation links (desktop) -->
    <ul class="navbar-nav d-none d-md-flex align-items-center top-nav-categories" id="topNavCategories">
        <!-- HR Dropdown -->
        @hasrole('admin')
        <li class="nav-item dropdown mx-1 top-nav-category" id="hr-nav-item">
            <a class="nav-link dropdown-toggle font-weight-bold px-1 top-nav-link" href="#" id="hrDropdown" role="button" aria-haspopup="true" aria-expanded="false" style="color: #083259; font-size: 13px;">
                <i class="fas fa-users mr-1"></i> HR
            </a>
            <div class="dropdown-menu shadow-sm border-0 animated--grow-in top-category-menu" aria-labelledby="hrDropdown" style="border-radius: 8px; border-top: 3px solid #083259 !important;">
                <a class="dropdown-item py-2" href="{{route('staff.index')}}">👥 Staff Management</a>
                <a class="dropdown-item py-2" href="{{route('attendance.index')}}">🕐 Attendance</a>
                <a class="dropdown-item py-2" href="{{route('expenses.index')}}">💸 Expenses</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item py-2" href="{{route('tasks.index')}}">✅ Tasks List</a>
                <a class="dropdown-item py-2" href="{{route('tasks.calendar')}}">📅 Task Calendar</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item py-2" href="{{route('commissions.index')}}">💰 Commissions</a>
            </div>
        </li>
        @endhasrole
    </ul>

    <!-- Topbar Navbar (right side) -->
    <ul class="navbar-nav ml-auto align-items-center">

      <style>
          .btn-nav-action {
              width: 36px;
              height: 36px;
              border-radius: 50%;
              display: inline-flex;
              align-items: center;
              justify-content: center;
              padding: 0;
              margin: auto 4px;
              transition: all 0.2s ease-in-out;
              background-color: #f8fafc;
              border: 1px solid #cbd5e1;
              position: relative;
          }
          .btn-nav-action:hover {
              background-color: #f1f5f9;
              transform: scale(1.08);
              text-decoration: none;
          }
          .btn-nav-expense {
              color: #dc2626 !important;
          }
          .btn-nav-expense:hover {
              color: #b91c1c !important;
              background-color: rgba(220, 38, 38, 0.05);
              border-color: rgba(220, 38, 38, 0.2);
          }
          .btn-nav-bilty {
              color: #0284c7 !important;
          }
          .btn-nav-bilty:hover {
              color: #0369a1 !important;
              background-color: rgba(2, 132, 199, 0.05);
              border-color: rgba(2, 132, 199, 0.2);
          }
          .btn-nav-cache {
              color: #083259 !important;
          }
          .btn-nav-cache:hover {
              color: #062038 !important;
              background-color: rgba(8, 50, 89, 0.05);
              border-color: rgba(8, 50, 89, 0.2);
          }
          .btn-nav-alerts {
              color: #475569 !important;
          }
          .btn-nav-alerts:hover {
              color: #1e293b !important;
              background-color: rgba(71, 85, 105, 0.05);
              border-color: rgba(71, 85, 105, 0.2);
          }
          .btn-nav-messages {
              color: #475569 !important;
          }
          .btn-nav-messages:hover {
              color: #1e293b !important;
              background-color: rgba(71, 85, 105, 0.05);
              border-color: rgba(71, 85, 105, 0.2);
          }
      </style>

      <li class="nav-item mx-1 d-none d-md-inline-flex align-items-center">
          <a class="nav-link btn-nav-action btn-nav-expense" href="#" data-toggle="modal" data-target="#quickExpenseModal" role="button" title="Quick Expense">
              <i class="fas fa-minus-circle fa-fw"></i>
          </a>
      </li>

      <li class="nav-item mx-1 d-none d-md-inline-flex align-items-center">
          <a class="nav-link btn-nav-action btn-nav-bilty" href="#" data-toggle="modal" data-target="#quickBiltyModal" role="button" title="Quick Delivery Receipt">
              <i class="fas fa-truck fa-fw"></i>
          </a>
      </li>

      <li class="nav-item mx-1 d-none d-md-inline-flex align-items-center">
          <a class="nav-link btn-nav-action btn-nav-cache" href="{{route('cache.clear')}}" role="button" title="Clear Cache">
              <i class="fas fa-sync-alt fa-fw"></i>
          </a>
      </li>

      <!-- Removed icons per request -->
      
      <!-- Alerts -->
      <li class="nav-item dropdown no-arrow mx-1 d-inline-flex align-items-center">
       @include('backend.notification.show')
      </li>

      <!-- Messages -->
      <li class="nav-item dropdown no-arrow mx-1 d-inline-flex align-items-center" id="messageT" data-url="{{route('messages.five')}}">
        @include('backend.message.message')
      </li>

      <div class="topbar-divider d-none d-sm-block"></div>

      <!-- User Dropdown -->
      <li class="nav-item dropdown no-arrow ml-2">
        <a class="nav-link dropdown-toggle p-0 d-flex align-items-center" href="#" id="userDropdown" role="button"
           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <div class="d-none d-md-flex flex-column align-items-end mr-2">
              <span style="font-family:'Montserrat',sans-serif; font-size:12px; font-weight:800; color:#083259; line-height:1;">{{Auth()->user()->name ?? 'Admin'}}</span>
              <span style="font-size:9px; font-weight:600; color:#a3b1c6; text-transform:uppercase; letter-spacing:0.5px;">Admin</span>
          </div>
          <div class="position-relative">
              @if(Auth()->user() && Auth()->user()->photo)
                <img class="img-profile rounded-circle" src="{{Auth()->user()->photo}}" style="width:40px; height:40px; border:2px solid #facc15; box-shadow: 0 2px 8px rgba(8,50,89,0.2);">
              @else
                <img class="img-profile rounded-circle" src="{{asset('backend/img/avatar.png')}}" style="width:40px; height:40px; border:2px solid #facc15; box-shadow: 0 2px 8px rgba(8,50,89,0.2);">
              @endif
              <span class="position-absolute border border-white rounded-circle bg-success" style="width:10px; height:10px; bottom:1px; right:1px;"></span>
          </div>
        </a>
        <!-- Dropdown - User Information -->
        <div class="dropdown-menu dropdown-menu-right shadow border-0 animated--grow-in" aria-labelledby="userDropdown"
             style="border-radius:12px; margin-top:10px; min-width:180px; border-top: 3px solid #facc15 !important;">
          <div class="px-3 py-2 mb-1" style="background:#f8fafc; border-radius:9px 9px 0 0;">
              <div style="font-family:'Montserrat',sans-serif; font-weight:800; font-size:13px; color:#083259;">{{Auth()->user()->name ?? 'Admin'}}</div>
              <div style="font-size:11px; color:#94a3b8; font-weight:500;">System Administrator</div>
          </div>
          <a class="dropdown-item py-2" href="{{route('admin-profile')}}">
            👤 Profile
          </a>
          <a class="dropdown-item py-2" href="{{route('change.password.form')}}">
            🔑 Change Password
          </a>
          <a class="dropdown-item py-2" href="{{route('settings')}}">
            ⚙️ Settings
          </a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item py-2" style="color:#dc2626; font-weight:700;" href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
             🚪 {{ __('Logout') }}
          </a>
          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
              @csrf
          </form>
        </div>
      </li>

    </ul>

</nav>
