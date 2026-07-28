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
          @can('view-return')
          <a href="{{route('returns.sale.index')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #083259; border: 1px solid #facc15;"><i class="fas fa-undo" style="color: #facc15;"></i></div>
              <span class="launcher-label">Sale Returns</span>
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
          @can('view-purchase')
          <a href="{{route('suppliers.index')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #083259; border: 1px solid #facc15;"><i class="fas fa-truck-fast" style="color: #facc15;"></i></div>
              <span class="launcher-label">Suppliers</span>
          </a>
          @endcan
          @can('view-packaging')
          <a href="{{route('packaging.purchases.index')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #083259; border: 1px solid #facc15;"><i class="fas fa-shopping-bag" style="color: #facc15;"></i></div>
              <span class="launcher-label">New Purchases</span>
          </a>
          @endcan
          @can('view-return')
          <a href="{{route('returns.purchase.index')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #083259; border: 1px solid #facc15;"><i class="fas fa-file-import" style="color: #facc15;"></i></div>
              <span class="launcher-label">Purchase Returns</span>
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
          <a href="{{route('manufacturing.production-factors.invoices')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #083259; border: 1px solid #facc15;"><i class="fas fa-file-invoice-dollar" style="color: #facc15;"></i></div>
              <span class="launcher-label">Material Invs</span>
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
              <span class="launcher-label">Sales Reports</span>
          </a>
          <a href="{{route('reports.payables')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #083259; border: 1px solid #facc15;"><i class="fas fa-chart-bar" style="color: #facc15;"></i></div>
              <span class="launcher-label">Payable Charts</span>
          </a>
          <a href="{{route('reports.receivables')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #083259; border: 1px solid #facc15;"><i class="fas fa-chart-area" style="color: #facc15;"></i></div>
              <span class="launcher-label">Receivable Charts</span>
          </a>
          <a href="{{route('reports.product-analysis')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #083259; border: 1px solid #facc15;"><i class="fas fa-flask" style="color: #facc15;"></i></div>
              <span class="launcher-label">Product Analysis</span>
          </a>
          <a href="{{route('reports.customer')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #083259; border: 1px solid #facc15;"><i class="fas fa-users" style="color: #facc15;"></i></div>
              <span class="launcher-label">Customer Reports</span>
          </a>
          @endcan
          @can('view-dashboard')
          <a href="{{route('admin.activity-logs')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #083259; border: 1px solid #facc15;"><i class="fas fa-history" style="color: #facc15;"></i></div>
              <span class="launcher-label">Activity Log</span>
          </a>
          @endcan
          <a href="{{route('delivery-receipts.index')}}" class="launcher-item">
              <div class="launcher-icon" style="background: #083259; border: 1px solid #facc15;"><i class="fas fa-receipt" style="color: #facc15;"></i></div>
              <span class="launcher-label">All Receipts</span>
          </a>
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

          // Ledger & Order Table Accordion Logic
          $(document).on('click', '.ledger-table-to-cards tr, .order-table-to-cards tr', function(e) {
              // Prevent expanding/collapsing if clicking on an interactive element
              // (Removed 'form' from this list because the entire table might be wrapped in a form)
              if ($(e.target).closest('button, a, input, select, textarea').length > 0) return;
              
              // Toggle this row
              $(this).toggleClass('expanded');
          });
      });
  </script>
  @include('backend.layouts.cart_drawer')
  @include('backend.layouts.chat_widget')
  @include('backend.layouts.global_modals')

  {{-- ============================================================
       SMART SUBMIT PROTECTION
       Prevents duplicate form submissions when internet drops.
       • Disables submit buttons immediately on first click
       • Shows "Saving..." spinner
       • Auto re-enables after 15s timeout if server never responds
       • GET forms (search/filter) are automatically excluded
       • Exposes window.smartSubmitReset() for AJAX error handlers
  ============================================================ --}}
  <div id="smart-submit-toast"></div>
  <script>
  (function() {
      // --- Styles ---
      var s = document.createElement('style');
      s.textContent = [
          '#smart-submit-toast{',
              'position:fixed;bottom:24px;left:50%;transform:translateX(-50%);',
              'background:#dc2626;color:#fff;padding:12px 22px;border-radius:12px;',
              'font-weight:700;font-size:0.85rem;z-index:999999;display:none;',
              'box-shadow:0 6px 20px rgba(0,0,0,0.25);max-width:340px;text-align:center;',
              'pointer-events:none;',
          '}',
          '#smart-submit-toast.ssp-show{display:block;animation:sspUp .3s ease;}',
          '@keyframes sspUp{from{opacity:0;transform:translate(-50%,16px)}to{opacity:1;transform:translate(-50%,0)}}',
          '.ssp-saving{opacity:.75!important;cursor:not-allowed!important;pointer-events:none!important;}',
      ].join('');
      document.head.appendChild(s);

      var toast = document.getElementById('smart-submit-toast');
      var toastTimer;

      function showToast(msg, color) {
          toast.textContent = msg;
          toast.style.background = color || '#dc2626';
          toast.classList.add('ssp-show');
          clearTimeout(toastTimer);
          toastTimer = setTimeout(function(){ toast.classList.remove('ssp-show'); }, 5000);
      }

      var TIMEOUT_MS = 15000; // 15 seconds safety net

      // --- Global form submit listener ---
      document.addEventListener('submit', function(e) {
          var form = e.target;

          // Skip GET forms entirely (search, filter, date pickers — they don't save data)
          if ((form.getAttribute('method') || 'get').toLowerCase() === 'get') return;

          // Skip forms that opt-out
          if (form.hasAttribute('data-no-ssp')) return;

          var buttons = form.querySelectorAll('[type="submit"]:not([disabled]), button:not([type="button"]):not([type="reset"]):not([disabled])');
          if (!buttons.length) return;

          buttons.forEach(function(btn) {
              btn.dataset.sspOrigHtml = btn.innerHTML;

              // Disable + spinner
              btn.disabled = true;
              btn.classList.add('ssp-saving');
              btn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right:5px;"></i>Saving...';

              // Safety timer — re-enable if page never navigates away
              var timer = setTimeout(function() {
                  sspReset(btn);
                  showToast('⚠️ No response from server — check your internet and try again.');
              }, TIMEOUT_MS);

              btn._sspTimer = timer;
          });

      }, true); // capture phase so it fires before any other handler

      // --- Re-enable a single button ---
      function sspReset(btn, errorMsg) {
          if (!btn) return;
          if (btn._sspTimer) { clearTimeout(btn._sspTimer); btn._sspTimer = null; }
          btn.disabled = false;
          btn.classList.remove('ssp-saving');
          if (btn.dataset.sspOrigHtml) btn.innerHTML = btn.dataset.sspOrigHtml;
          if (errorMsg) showToast('⚠️ ' + errorMsg);
      }

      // --- Public API for AJAX handlers ---

      // Call on AJAX error to re-enable button and show message
      // Usage: window.sspReset('#my-btn', 'Failed to save. Try again.');
      window.sspReset = function(selector, errorMsg) {
          document.querySelectorAll(selector).forEach(function(btn) { sspReset(btn, errorMsg); });
      };

      // Call on AJAX success (for modal forms that need the button enabled for next use)
      // Usage: window.sspSuccess('#my-btn');
      window.sspSuccess = function(selector) {
          document.querySelectorAll(selector).forEach(function(btn) {
              if (btn._sspTimer) { clearTimeout(btn._sspTimer); btn._sspTimer = null; }
              btn.disabled = false;
              btn.classList.remove('ssp-saving');
              if (btn.dataset.sspOrigHtml) btn.innerHTML = btn.dataset.sspOrigHtml;
          });
      };

      // --- Offline banner ---
      var offlineBanner = null;
      window.addEventListener('offline', function() {
          if (!offlineBanner) {
              offlineBanner = document.createElement('div');
              offlineBanner.style.cssText = [
                  'position:fixed;top:0;left:0;right:0;z-index:9999999;',
                  'background:#b91c1c;color:#fff;text-align:center;',
                  'padding:10px;font-weight:700;font-size:0.85rem;',
                  'letter-spacing:0.3px;',
              ].join('');
              offlineBanner.innerHTML = '🔴 &nbsp;You are offline — do NOT press Save until connection is restored.';
              document.body.prepend(offlineBanner);
          }
      });
      window.addEventListener('online', function() {
          if (offlineBanner) { offlineBanner.remove(); offlineBanner = null; }
          showToast('✅ Back online — you can save now.', '#16a34a');
      });

  })();
  </script>

</body>
</html>

