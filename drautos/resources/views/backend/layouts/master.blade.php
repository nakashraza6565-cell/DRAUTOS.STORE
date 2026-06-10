<!DOCTYPE html>
<html lang="en">

@include('backend.layouts.head')

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Sidebar (mobile: slide-out drawer + launcher; hidden on desktop via CSS) -->
    @include('backend.layouts.sidebar')

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
      
      <div class="launcher-section-title" style="color: #facc15;">Sales & Billing</div>
      <div class="launcher-grid">
          @can('view-order')
          <a href="{{route('admin.pos')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #083259; border: 1px solid #facc15;"><i class="fas fa-desktop" style="color: #facc15;"></i></div>
              <span class="launcher-label">POS</span>
          </a>
          <a href="{{route('sales-orders.index')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #083259; border: 1px solid #facc15;"><i class="fas fa-receipt" style="color: #facc15;"></i></div>
              <span class="launcher-label">Sale Orders</span>
          </a>
          <a href="{{route('order.index')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #083259; border: 1px solid #facc15;"><i class="fas fa-shopping-cart" style="color: #facc15;"></i></div>
              <span class="launcher-label">Billing</span>
          </a>
          @endcan
          <a href="{{route('admin.cash-register')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #083259; border: 1px solid #facc15;"><i class="fas fa-cash-register" style="color: #facc15;"></i></div>
              <span class="launcher-label">Register</span>
          </a>
      </div>

      <div class="launcher-section-title" style="color: #facc15;">Ledgers & Finance</div>
      <div class="launcher-grid">
          @can('view-customer-ledger')
          <a href="{{route('admin.customer-ledger.index')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #083259; border: 1px solid #facc15;"><i class="fas fa-user-tag" style="color: #facc15;"></i></div>
              <span class="launcher-label">Customer Ledger</span>
          </a>
          @endcan
          <a href="{{route('admin.supplier-ledger.index')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #083259; border: 1px solid #facc15;"><i class="fas fa-file-contract" style="color: #facc15;"></i></div>
              <span class="launcher-label">Supplier Ledger</span>
          </a>
          <a href="{{route('cheques.index')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #083259; border: 1px solid #facc15;"><i class="fas fa-money-check" style="color: #facc15;"></i></div>
              <span class="launcher-label">Cheques</span>
          </a>
          <a href="{{route('expenses.index')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #083259; border: 1px solid #facc15;"><i class="fas fa-money-bill-wave" style="color: #facc15;"></i></div>
              <span class="launcher-label">Expenses</span>
          </a>
      </div>

      <div class="launcher-section-title" style="color: #facc15;">Inventory & Stock</div>
      <div class="launcher-grid">
          @can('view-product')
          <a href="{{route('product.index')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #083259; border: 1px solid #facc15;"><i class="fas fa-boxes" style="color: #facc15;"></i></div>
              <span class="launcher-label">Products</span>
          </a>
          <a href="{{route('inventory-incoming.index')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #083259; border: 1px solid #facc15;"><i class="fas fa-truck-loading" style="color: #facc15;"></i></div>
              <span class="launcher-label">Inward</span>
          </a>
          <a href="{{route('purchase-orders.index')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #083259; border: 1px solid #facc15;"><i class="fas fa-file-invoice" style="color: #facc15;"></i></div>
              <span class="launcher-label">Purchase Orders</span>
          </a>
          <a href="{{route('category.index')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #083259; border: 1px solid #facc15;"><i class="fas fa-tags" style="color: #facc15;"></i></div>
              <span class="launcher-label">Categories</span>
          </a>
          @endcan
      </div>

      <div class="launcher-section-title" style="color: #facc15;">Manufacturing</div>
      <div class="launcher-grid">
          @can('view-die')
          <a href="{{route('die-management.index')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #083259; border: 1px solid #facc15;"><i class="fas fa-gears" style="color: #facc15;"></i></div>
              <span class="launcher-label">Die Management</span>
          </a>
          @endcan
          @can('view-manufacturing')
          <a href="{{route('manufacturing.production-factors.index')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #083259; border: 1px solid #facc15;"><i class="fas fa-cubes" style="color: #facc15;"></i></div>
              <span class="launcher-label">Raw Materials</span>
          </a>
          <a href="{{route('manufacturing.index')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #083259; border: 1px solid #facc15;"><i class="fas fa-industry" style="color: #facc15;"></i></div>
              <span class="launcher-label">BOM List</span>
          </a>
          <a href="{{route('manufacturing.create')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #083259; border: 1px solid #facc15;"><i class="fas fa-plus" style="color: #facc15;"></i></div>
              <span class="launcher-label">Create BOM</span>
          </a>
          @endcan
      </div>

      <div class="launcher-section-title" style="color: #facc15;">Reports & System</div>
      <div class="launcher-grid">
          @can('view-report')
          <a href="{{route('reports.sales')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #083259; border: 1px solid #facc15;"><i class="fas fa-chart-line" style="color: #facc15;"></i></div>
              <span class="launcher-label">Reports</span>
          </a>
          @endcan
          <a href="{{route('staff.index')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #083259; border: 1px solid #facc15;"><i class="fas fa-users-cog" style="color: #facc15;"></i></div>
              <span class="launcher-label">Staff</span>
          </a>
          <a href="{{route('settings')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #083259; border: 1px solid #facc15;"><i class="fas fa-cog" style="color: #facc15;"></i></div>
              <span class="launcher-label">Settings</span>
          </a>
          <a href="{{route('home')}}" target="_blank" class="launcher-item">
              <div class="launcher-icon" style="background: #083259; border: 1px solid #facc15;"><i class="fas fa-external-link-alt" style="color: #facc15;"></i></div>
              <span class="launcher-label">Storefront</span>
          </a>
      </div>

      <div class="launcher-section-title" style="color: #facc15;">Customers & Users</div>
      <div class="launcher-grid">
          <a href="{{route('users.index')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #083259; border: 1px solid #facc15;"><i class="fas fa-users" style="color: #facc15;"></i></div>
              <span class="launcher-label">Customers</span>
          </a>
          <a href="{{route('order.index')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #083259; border: 1px solid #facc15;"><i class="fas fa-shopping-bag" style="color: #facc15;"></i></div>
              <span class="launcher-label">Online Orders</span>
          </a>
          <a href="{{route('admin.customer-ledger.index')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #083259; border: 1px solid #facc15;"><i class="fas fa-file-invoice-dollar" style="color: #facc15;"></i></div>
              <span class="launcher-label">Ledger</span>
          </a>
          <a href="{{route('whatsapp.campaign')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #083259; border: 1px solid #facc15;"><i class="fab fa-whatsapp" style="color: #facc15;"></i></div>
              <span class="launcher-label">WA Campaign</span>
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

          // Global Bootstrap Modal Focus Fix for Select2
          if ($.fn.modal) {
              $.fn.modal.Constructor.prototype._enforceFocus = function() {};
          }

          // Ledger Table Accordion Logic
          $(document).on('click', '.ledger-table-to-cards tr', function(e) {
              // Prevent expanding/collapsing if clicking on an interactive element
              if ($(e.target).closest('button, a, input, select, form').length > 0) return;
              
              // Toggle this row
              $(this).toggleClass('expanded');
          });
      });
  </script>
  @include('backend.layouts.chat_widget')
</body>
</html>
