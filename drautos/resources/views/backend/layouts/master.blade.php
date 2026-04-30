<!DOCTYPE html>
<html lang="en">

@include('backend.layouts.head')

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Sidebar -->
    @include('backend.layouts.sidebar')
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        @include('backend.layouts.header')
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        @yield('main-content')
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->
  <!-- Admin App Launcher Overlay (Mobile Only) -->
  <div id="adminAppLauncher">
      <button class="launcher-close" id="launcherClose"><i class="fas fa-times"></i></button>
      
      <div class="launcher-section-title">Sales & Billing</div>
      <div class="launcher-grid">
          @can('view-order')
          <a href="{{route('admin.pos')}}" class="launcher-item">
              <div class="launcher-icon bg-primary"><i class="fas fa-desktop text-white"></i></div>
              <span class="launcher-label">POS</span>
          </a>
          <a href="{{route('sales-orders.index')}}" class="launcher-item">
              <div class="launcher-icon bg-info"><i class="fas fa-receipt text-white"></i></div>
              <span class="launcher-label">Sale Orders</span>
          </a>
          <a href="{{route('order.index')}}" class="launcher-item">
              <div class="launcher-icon bg-success"><i class="fas fa-shopping-cart text-white"></i></div>
              <span class="launcher-label">Billing</span>
          </a>
          @endcan
          <a href="{{route('admin.cash-register')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #64748b;"><i class="fas fa-cash-register text-white"></i></div>
              <span class="launcher-label">Register</span>
          </a>
      </div>

      <div class="launcher-section-title">Ledgers & Finance</div>
      <div class="launcher-grid">
          @can('view-customer-ledger')
          <a href="{{route('admin.customer-ledger.index')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #10b981;"><i class="fas fa-user-tag text-white"></i></div>
              <span class="launcher-label">Customer Ledger</span>
          </a>
          @endcan
          <a href="{{route('admin.supplier-ledger.index')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #059669;"><i class="fas fa-file-contract text-white"></i></div>
              <span class="launcher-label">Supplier Ledger</span>
          </a>
          <a href="{{route('cheques.index')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #3b82f6;"><i class="fas fa-money-check text-white"></i></div>
              <span class="launcher-label">Cheques</span>
          </a>
          <a href="{{route('expenses.index')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #ef4444;"><i class="fas fa-money-bill-wave text-white"></i></div>
              <span class="launcher-label">Expenses</span>
          </a>
      </div>

      <div class="launcher-section-title">Inventory & Stock</div>
      <div class="launcher-grid">
          @can('view-product')
          <a href="{{route('product.index')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #6366f1;"><i class="fas fa-boxes text-white"></i></div>
              <span class="launcher-label">Products</span>
          </a>
          <a href="{{route('inventory-incoming.index')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #8b5cf6;"><i class="fas fa-truck-loading text-white"></i></div>
              <span class="launcher-label">Inward</span>
          </a>
          <a href="{{route('purchase-orders.index')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #4f46e5;"><i class="fas fa-file-invoice text-white"></i></div>
              <span class="launcher-label">Purchase Orders</span>
          </a>
          <a href="{{route('category.index')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #d946ef;"><i class="fas fa-tags text-white"></i></div>
              <span class="launcher-label">Categories</span>
          </a>
          @endcan
      </div>

      <div class="launcher-section-title">Manufacturing & HR</div>
      <div class="launcher-grid">
          <a href="{{route('manufacturing.index')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #f59e0b;"><i class="fas fa-industry text-white"></i></div>
              <span class="launcher-label">Manufacturing</span>
          </a>
          <a href="{{route('attendance.index')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #fbbf24;"><i class="fas fa-user-clock text-white"></i></div>
              <span class="launcher-label">Attendance</span>
          </a>
          <a href="{{route('commissions.index')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #f43f5e;"><i class="fas fa-percent text-white"></i></div>
              <span class="launcher-label">Commissions</span>
          </a>
          <a href="{{route('tasks.index')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #8b5cf6;"><i class="fas fa-calendar-check text-white"></i></div>
              <span class="launcher-label">Tasks</span>
          </a>
      </div>

      <div class="launcher-section-title">Reports & System</div>
      <div class="launcher-grid">
          @can('view-report')
          <a href="{{route('reports.sales')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #0ea5e9;"><i class="fas fa-chart-line text-white"></i></div>
              <span class="launcher-label">Reports</span>
          </a>
          @endcan
          <a href="{{route('staff.index')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #475569;"><i class="fas fa-users-cog text-white"></i></div>
              <span class="launcher-label">Staff</span>
          </a>
          <a href="{{route('settings')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #334155;"><i class="fas fa-cog text-white"></i></div>
              <span class="launcher-label">Settings</span>
          </a>
          <a href="{{route('home')}}" target="_blank" class="launcher-item">
              <div class="launcher-icon" style="background: #1e293b;"><i class="fas fa-external-link-alt text-white"></i></div>
              <span class="launcher-label">Storefront</span>
          </a>
      </div>
      
      <div style="height: 100px;"></div>
  </div>

  @include('backend.layouts.footer')

  <script>
      $(document).ready(function() {
          $('#launcherTrigger').on('click', function() {
              $('#adminAppLauncher').fadeIn(300).addClass('active');
              $('body').css('overflow', 'hidden');
          });

          $('#launcherClose, .launcher-item').on('click', function() {
              $('#adminAppLauncher').fadeOut(300).removeClass('active');
              $('body').css('overflow', 'auto');
          });
      });
  </script>
</body>
</html>
