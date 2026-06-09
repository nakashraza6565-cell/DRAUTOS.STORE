<ul class="navbar-nav bg-dark sidebar sidebar-dark accordion" id="accordionSidebar" style="border-right: 1px solid rgba(255,255,255,0.05); transition: all 0.3s ease;">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center px-4 py-4" href="{{route('admin')}}" style="height: auto; opacity: 1; text-decoration: none; border-bottom: 1px solid rgba(255,255,255,0.06); margin-bottom: 8px;">
        <!-- DR SVG Logo — same paths as frontend header -->
        <div style="flex-shrink: 0; width: 52px; height: 46px; display: flex; align-items: center;">
            <svg width="52" height="46" viewBox="0 0 75 65" xmlns="http://www.w3.org/2000/svg">
                <path d="M 22.74 58.73 L 13.17 58.73 L 13.17 25.29 L 24.94 25.29 L 24.94 48.61 C 28.98 48.47 32.29 46.74 34.87 43.44 C 37.46 40.14 38.75 36.49 38.75 32.5 C 38.75 29.89 38.25 28.01 37.26 26.83 C 36.27 25.66 35.19 24.87 34.02 24.47 C 33.25 24.21 32.51 24.06 31.79 24.03 C 31.08 23.99 30.5 23.97 30.06 23.97 L 10.97 23.92 L 13.72 13.8 L 32.81 13.8 C 39.52 13.83 44.19 15.65 46.83 19.24 C 49.47 22.84 50.79 26.91 50.79 31.45 C 50.79 39.92 47.99 46.59 42.38 51.45 C 36.77 56.3 30.22 58.73 22.74 58.73 " fill="#ffffff"/>
                <path d="M 36.48 20.54 L 56.18 20.54 C 60.18 20.58 63.08 21.43 64.9 23.11 C 66.72 24.78 67.86 26.64 68.33 28.68 C 68.39 29.08 68.45 29.5 68.5 29.94 C 68.55 30.37 68.58 30.79 68.58 31.19 C 68.58 34.16 67.71 36.67 65.98 38.73 C 64.24 40.79 62.19 42.57 59.83 44.07 C 61.39 47.2 63.18 50.21 65.2 53.11 C 67.22 56 69.38 58.76 71.68 61.39 L 62.73 67.59 C 59.43 63.86 56.49 59.84 53.93 55.53 C 51.36 51.22 49.06 46.79 47.03 42.23 C 47.53 41.83 48.14 41.38 48.88 40.88 C 49.61 40.38 50.36 39.85 51.13 39.28 C 52.46 38.28 53.68 37.19 54.8 36.01 C 55.92 34.83 56.48 33.62 56.48 32.39 C 56.48 31.43 56.13 30.78 55.45 30.44 C 54.77 30.11 54.03 29.93 53.23 29.89 C 52.96 29.86 52.7 29.84 52.45 29.84 C 52.2 29.84 51.96 29.84 51.73 29.84 C 51.66 29.84 51.59 29.84 51.53 29.84 C 51.46 29.84 51.39 29.84 51.33 29.84 L 33.98 29.79 L 36.48 20.54 Z M 46.68 31.04 L 46.68 61.39 L 35.98 61.39 L 35.98 31.04 L 46.68 31.04 Z" fill="#facc15" stroke="#062038" stroke-width="1.5" stroke-linejoin="round"/>
            </svg>
        </div>
        <div class="ml-3 text-left" style="line-height: 1;">
            <div style="font-family: 'Montserrat', sans-serif; font-weight: 900; font-size: 17px; color: #ffffff; letter-spacing: 0px;">DANYAL AUTOS</div>
            <div style="font-family: 'Inter', sans-serif; font-weight: 600; font-size: 9px; color: #a3b1c6; text-transform: uppercase; letter-spacing: 1.5px; margin-top: 3px;">Spare Parts Portal</div>
        </div>
    </a>

    @can('view-dashboard')
    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{Request::is('admin') ? 'active' : ''}}">
      <a class="nav-link d-flex align-items-center py-2" href="{{route('admin')}}">
        <i class="fas fa-fw fa-house-chimney-window mr-2"></i>
        <span>Dashboard</span></a>
    </li>
    @endcan

    <!-- Divider -->
    <hr class="sidebar-divider" style="border-top: 1px solid rgba(255,255,255,0.05); margin: 0 1rem 1rem 1rem;">

    <!-- Heading -->
    <div class="sidebar-heading px-4 mb-2" style="color: #64748b; font-weight: 700; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.15em;">
        Main Content
    </div>

    @can('view-media')
    <!-- Media Manager -->
    <li class="nav-item {{Request::is('admin/file-manager') ? 'active' : ''}}">
        <a class="nav-link py-2" href="{{route('file-manager')}}">
            <i class="fas fa-fw fa-photo-film mr-2"></i>
            <span>Media Assets</span></a>
    </li>
    @endcan

    @canany(['view-dashboard', 'view-banner'])
    <!-- Content Dropdown -->
    <li class="nav-item {{ Request::is('admin/activity-logs*') || Request::is('admin/banner*') ? 'active' : '' }}">
      <a class="nav-link collapsed py-2" href="#" data-toggle="collapse" data-target="#collapseContent" aria-expanded="true" aria-controls="collapseContent">
        <i class="fas fa-folder-open mr-2"></i>
        <span>Content</span>
      </a>
      <div id="collapseContent" class="collapse {{ Request::is('admin/activity-logs*') || Request::is('admin/banner*') ? 'show' : '' }}" aria-labelledby="headingContent" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded-lg shadow-sm">
          @can('view-dashboard')
          <a class="collapse-item {{ Request::is('admin/activity-logs*') ? 'active' : '' }}" href="{{route('admin.activity-logs')}}">Activity Log</a>
          @endcan
          @can('view-banner')
          <a class="collapse-item {{ Request::is('admin/banner') ? 'active' : '' }}" href="{{route('banner.index')}}">Active Banners</a>
          <a class="collapse-item {{ Request::is('admin/banner/create') ? 'active' : '' }}" href="{{route('banner.create')}}">Create Banner</a>
          @endcan
        </div>
      </div>
    </li>
    @endcanany

    <!-- Section: Sales & POS -->
    <div class="sidebar-heading px-4 mt-4 mb-2" style="color: #64748b; font-weight: 700; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.15em;">
        Point of Sale
    </div>

    @can('view-order')
    {{-- POS --}}
    <li class="nav-item {{ Request::is('admin/pos*') ? 'active' : '' }}">
        <a class="nav-link py-2" href="{{route('admin.pos')}}">
            <i class="fas fa-desktop mr-2"></i>
            <span>Local Sales (POS)</span>
        </a>
    </li>
    <li class="nav-item {{ Request::is('admin/sales-orders*') ? 'active' : '' }}">
        <a class="nav-link py-2" href="{{route('sales-orders.index')}}">
            <i class="fas fa-receipt mr-2"></i>
            <span>Sale Orders</span>
        </a>
    </li>
    @endcan

    @can('view-cash-register')
    <li class="nav-item {{ Request::is('admin/cash-register*') ? 'active' : '' }}">
        <a class="nav-link py-2" href="{{route('admin.cash-register')}}">
          <i class="fas fa-cash-register mr-2"></i>
          <span>Cash Register</span>
        </a>
    </li>
    @endcan

    @can('view-order')
    <li class="nav-item {{ Request::is('admin/order*') ? 'active' : '' }}">
        <a class="nav-link py-2" href="{{route('order.index')}}">
          <i class="fas fa-cart-shopping mr-2"></i>
          <span>Orders & Billing</span>
        </a>
    </li>
    @endcan

    @can('view-return')
    {{-- Sale Returns shifted here from Inventory --}}
    <li class="nav-item {{ Request::is('admin/returns/sale*') ? 'active' : '' }}">
        <a class="nav-link py-2" href="{{route('returns.sale.index')}}">
            <i class="fas fa-undo mr-2"></i>
            <span>Sale Returns</span>
        </a>
    </li>
    @endcan

    <!-- Section: Inventory -->
    <div class="sidebar-heading px-4 mt-4 mb-2" style="color: #64748b; font-weight: 700; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.15em;">
        Inventory & Assets
    </div>

    @can('view-product')
    {{-- Products --}}
    <li class="nav-item">
        <a class="nav-link collapsed py-2" href="#" data-toggle="collapse" data-target="#productCollapse" aria-expanded="true" aria-controls="productCollapse">
          <i class="fas fa-box-open mr-2"></i>
          <span>Stock Control</span>
        </a>
        <div id="productCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded-lg shadow-sm">
            <a class="collapse-item" href="{{route('product.index')}}">All Products</a>
            <a class="collapse-item" href="{{route('product.create')}}">Add New Item</a>
            <a class="collapse-item" href="{{route('product.price-list')}}">Price List</a>
            @can('view-category')
            <a class="collapse-item" href="{{route('category.index')}}">Product Categories</a>
            @endcan
            <a class="collapse-item" href="{{route('brand.index')}}">Brands</a>
            @can('view-bundle')
            <a class="collapse-item" href="{{route('bundles.index')}}">Bundles / Kitting</a>
            @endcan
          </div>
        </div>
    </li>
    @endcan

    @can('view-purchase')
    <li class="nav-item">
        <a class="nav-link collapsed py-2" href="#" data-toggle="collapse" data-target="#supplyCollapse" aria-expanded="true" aria-controls="supplyCollapse">
          <i class="fas fa-truck-fast mr-2"></i>
          <span>Supply Chain</span>
        </a>
        <div id="supplyCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded-lg shadow-sm">
            <a class="collapse-item" href="{{route('suppliers.index')}}">Suppliers / Vendors</a>
            <a class="collapse-item" href="{{route('warehouses.index')}}">Warehouses</a>
            <a class="collapse-item" href="{{route('purchase-orders.index')}}">Purchase Orders</a>
          </div>
        </div>
    </li>
    @endcan

    @can('view-incoming-goods')
    {{-- Inventory Incoming --}}
    <li class="nav-item">
        <a class="nav-link py-2" href="{{route('inventory-incoming.index')}}">
            <i class="fas fa-boxes-packing mr-2"></i>
            <span>Incoming Goods</span>
        </a>
    </li>
    @endcan

    @can('view-packaging')
    {{-- Packaging & Stock Handling --}}
    <li class="nav-item">
        <a class="nav-link collapsed py-2" href="#" data-toggle="collapse" data-target="#packagingCollapse" aria-expanded="true" aria-controls="packagingCollapse">
          <i class="fas fa-box mr-2"></i>
          <span>Packaging Handling</span>
        </a>
        <div id="packagingCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded-lg shadow-sm">
            <a class="collapse-item" href="{{route('packaging.index')}}">Stock Inventory</a>
            <a class="collapse-item" href="{{route('packaging.purchases.index')}}">New Purchases</a>
            <a class="collapse-item" href="{{route('packaging.usage.index')}}">Usage History</a>
          </div>
        </div>
    </li>
    @endcan

    @can('view-return')
    {{-- Purchase Returns standalone --}}
    <li class="nav-item {{ Request::is('admin/returns/purchase*') ? 'active' : '' }}">
        <a class="nav-link py-2" href="{{route('returns.purchase.index')}}">
            <i class="fas fa-file-import mr-2"></i>
            <span>Purchase Returns</span>
        </a>
    </li>
    @endcan

    @canany(['view-die', 'view-manufacturing'])
    <!-- Manufacturing (BOM) -->
    <li class="nav-item">
        <a class="nav-link collapsed py-2" href="#" data-toggle="collapse" data-target="#manufacturingCollapse" aria-expanded="true" aria-controls="manufacturingCollapse">
          <i class="fas fa-industry mr-2"></i>
          <span>Manufacturing</span>
        </a>
        <div id="manufacturingCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded-lg shadow-sm">
            @can('view-die')
            <a class="collapse-item" href="{{route('die-management.index')}}">Die Management</a>
            @endcan
            @can('view-manufacturing')
            <a class="collapse-item" href="{{route('manufacturing.production-factors.index')}}">Raw Materials & Labor</a>
            <a class="collapse-item" href="{{route('manufacturing.production-factors.invoices')}}">Raw Material Invoices</a>
            <a class="collapse-item" href="{{route('manufacturing.index')}}">Bill of Materials</a>
            <a class="collapse-item" href="{{route('manufacturing.create')}}">Create New BOM</a>
            @endcan
          </div>
        </div>
    </li>
    @endcanany

    <!-- Section: Financial Management -->
    <div class="sidebar-heading px-4 mt-4 mb-2" style="color: #64748b; font-weight: 700; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.15em;">
        Financial Management
    </div>

    @can('view-payment-reminder')
    {{-- Payment Reminders --}}
    <li class="nav-item">
        <a class="nav-link py-2" href="{{route('payment-reminders.index')}}">
            <i class="fas fa-bell mr-2"></i>
            <span>Payment Reminders</span>
        </a>
    </li>
    @endcan

    @can('view-customer-ledger')
    {{-- Customer Ledgers --}}
    <li class="nav-item {{Request::is('admin/customer-ledger*') ? 'active' : ''}}">
        <a class="nav-link py-2" href="{{route('admin.customer-ledger.index')}}">
            <i class="fas fa-file-invoice-dollar mr-2"></i>
            <span>Customer Ledgers</span>
        </a>
    </li>
    @endcan

    @can('view-purchase')
    {{-- Supplier Ledgers --}}
    <li class="nav-item {{Request::is('admin/supplier-ledger*') ? 'active' : ''}}">
        <a class="nav-link py-2" href="{{route('admin.supplier-ledger.index')}}">
            <i class="fas fa-file-contract mr-2"></i>
            <span>Supplier Ledgers</span>
        </a>
    </li>
    @endcan

    @can('view-cheque')
    {{-- Cheque Management --}}
    <li class="nav-item">
        <a class="nav-link py-2" href="{{route('cheques.index')}}">
            <i class="fas fa-money-check mr-2"></i>
            <span>Cheque Management</span>
        </a>
    </li>
    @endcan

    <!-- Section: Human Resources -->
    <div class="sidebar-heading px-4 mt-4 mb-2" style="color: #64748b; font-weight: 700; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.15em;">
        Human Resources
    </div>

    @hasrole('admin')
    <li class="nav-item">
        <a class="nav-link collapsed py-2" href="#" data-toggle="collapse" data-target="#hrCollapse" aria-expanded="true" aria-controls="hrCollapse">
          <i class="fas fa-user-tie mr-2"></i>
          <span>Employee Management</span>
        </a>
        <div id="hrCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded-lg shadow-sm">
            <a class="collapse-item" href="{{route('attendance.index')}}">Attendance</a>
            <a class="collapse-item" href="{{route('payroll.index')}}">Payroll & Salaries</a>
            <a class="collapse-item" href="{{route('expenses.index')}}">Expenses</a>
            <!-- <a class="collapse-item" href="{{route('commissions.index')}}">Commissions</a> -->
          </div>
        </div>
    </li>
    @endhasrole

    @can('view-task')
    {{-- Tasks & Calendar --}}
    <li class="nav-item">
        <a class="nav-link collapsed py-2" href="#" data-toggle="collapse" data-target="#tasksCollapse" aria-expanded="true" aria-controls="tasksCollapse">
          <i class="fas fa-calendar-check mr-2"></i>
          <span>Tasks & Calendar</span>
        </a>
        <div id="tasksCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded-lg shadow-sm">
            <a class="collapse-item" href="{{route('tasks.index')}}">Task List</a>
            <a class="collapse-item" href="{{route('tasks.calendar')}}">Calendar View</a>
          </div>
        </div>
    </li>
    @endcan

    <!-- Section: Enterprise -->
    <div class="sidebar-heading px-4 mt-4 mb-2" style="color: #64748b; font-weight: 700; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.15em;">
        Danyal Autos Enterprise
    </div>

    {{-- Delivery Receipts (Bilty) --}}
    <li class="nav-item">
        <a class="nav-link collapsed py-2" href="#" data-toggle="collapse" data-target="#biltyCollapse" aria-expanded="true" aria-controls="biltyCollapse">
          <i class="fas fa-receipt mr-2"></i>
          <span>Delivery (Bilty)</span>
        </a>
        <div id="biltyCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded-lg shadow-sm">
            <a class="collapse-item" href="{{route('delivery-receipts.index')}}">All Receipts</a>
          </div>
        </div>
    </li>

    @can('view-banner')
    {{-- Marketing --}}
    <li class="nav-item">
        <a class="nav-link collapsed py-2" href="#" data-toggle="collapse" data-target="#marketingCollapse" aria-expanded="true" aria-controls="marketingCollapse">
          <i class="fas fa-bullhorn mr-2"></i>
          <span>Marketing</span>
        </a>
        <div id="marketingCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded-lg shadow-sm">
            <a class="collapse-item" href="{{route('banner.index')}}">Banners</a>
            @can('view-coupon')
            <a class="collapse-item" href="{{route('coupon.index')}}">Coupons</a>
            @endcan
          </div>
        </div>
    </li>
    @endcan

    <!-- HR Moved to its own section above -->

    @can('view-analytics')
    {{-- Analytics --}}
    <li class="nav-item">
        <a class="nav-link py-2" href="{{route('global.analytics')}}">
            <i class="fas fa-chart-pie mr-2"></i>
            <span>Global Analytics</span>
        </a>
    </li>
    @endcan
    @endhasrole

    <!-- Section: Reports -->
    <div class="sidebar-heading px-4 mt-4 mb-2" style="color: #64748b; font-weight: 700; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.15em;">
        Business Intelligence
    </div>

    @can('view-report')
    {{-- Reports --}}
    <li class="nav-item">
        <a class="nav-link collapsed py-2" href="#" data-toggle="collapse" data-target="#reportsCollapse" aria-expanded="true" aria-controls="reportsCollapse">
          <i class="fas fa-chart-line mr-2"></i>
          <span>Reports</span>
        </a>
        <div id="reportsCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded-lg shadow-sm">
            <a class="collapse-item" href="{{route('reports.sales')}}">Sales Reports</a>
            <a class="collapse-item" href="{{route('reports.stock')}}">Stock Reports</a>
            <a class="collapse-item" href="{{route('reports.dead-products')}}">Dead Products</a>
            <a class="collapse-item" href="{{route('reports.profit-loss')}}">Profit & Loss</a>
            <a class="collapse-item" href="{{route('reports.payables')}}">Payable Charts</a>
            <a class="collapse-item" href="{{route('reports.receivables')}}">Receivable Charts</a>
            <a class="collapse-item" href="{{route('reports.product-analysis')}}">Product Analysis</a>
            <a class="collapse-item" href="{{route('reports.customer')}}">Customer Reports</a>
          </div>
        </div>
    </li>
    @endcan

    <div class="sidebar-heading px-4 mt-4 mb-2" style="color: #64748b; font-weight: 700; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.15em;">
        System Configuration
    </div>

    <!-- Administration -->
    @hasrole('admin')
    <li class="nav-item">
        <a class="nav-link collapsed py-2" href="#" data-toggle="collapse" data-target="#adminCollapse" aria-expanded="true" aria-controls="adminCollapse">
          <i class="fas fa-screwdriver-wrench mr-2"></i>
          <span>System Admin</span>
        </a>
        <div id="adminCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded-lg shadow-sm">
            <a class="collapse-item" href="{{route('staff.index')}}">Staff Management</a>
            <a class="collapse-item" href="{{route('roles.index')}}">Roles & Permissions</a>
            <a class="collapse-item" href="{{route('expenses.index')}}">Expenses</a>
            <a class="collapse-item" href="{{route('users.index')}}">Customers (Users)</a>
            <a class="collapse-item" href="{{route('users.pending')}}" style="color: #e6a817; font-weight: 600;">
                <i class="fas fa-user-clock fa-sm mr-1"></i> Pending Registrations
                @php $pendingSidebarCount = \App\User::where('status','pending')->count(); @endphp
                @if($pendingSidebarCount > 0)
                    <span class="badge badge-warning ml-1" style="font-size:9px;">{{ $pendingSidebarCount }}</span>
                @endif
            </a>
            <a class="collapse-item" href="{{route('settings')}}">General Settings</a>
            <a class="collapse-item" href="{{route('admin.whatsapp-settings')}}">WhatsApp Settings</a>
            <a class="collapse-item" href="{{route('whatsapp.campaign')}}">WhatsApp Campaigns</a>
            <a class="collapse-item" href="{{route('whatsapp.test')}}">WhatsApp Test Tool</a>
          </div>
        </div>
    </li>
    @endhasrole

    <li class="nav-item mt-2">
        <a class="nav-link py-2" href="{{route('home')}}" target="_blank">
            <i class="fas fa-up-right-from-square mr-2"></i>
            <span>Visit Storefront</span></a>
    </li>

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline mt-5 mb-4">
      <button class="rounded-circle border-0" id="sidebarToggle" style="background-color: rgba(255,255,255,0.08); width: 32px; height: 32px; font-size: 0.8rem;"></button>
    </div>

</ul>
