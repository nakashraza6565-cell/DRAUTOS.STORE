<ul class="navbar-nav bg-dark sidebar sidebar-dark accordion" id="accordionSidebar" style="background: #0f172a !important; border-right: 1px solid rgba(255,255,255,0.05); transition: all 0.3s ease;">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center my-4" href="{{route('admin')}}">
      <div class="sidebar-brand-icon">
        <div style="background: linear-gradient(135deg, var(--accent) 0%, #d97706 100%); width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);">
            <i class="fa-solid fa-car-side" style="color: #1e293b; font-size: 1.3rem;"></i>
        </div>
      </div>
      <div class="sidebar-brand-text mx-3" style="text-transform: none; font-weight: 800; letter-spacing: -0.5px; color: #f8fafc; font-size: 1.25rem;">DrAutos<span style="color: var(--accent);">Store</span></div>
    </a>

    @can('view-dashboard')
    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{Request::is('admin') ? 'active' : ''}}">
      <a class="nav-link d-flex align-items-center py-3" href="{{route('admin')}}">
        <i class="fas fa-fw fa-house-chimney-window mr-2" style="font-size: 1.1rem;"></i>
        <span style="font-weight: 600;">Dashboard</span></a>
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

    @can('view-banner')
    <li class="nav-item">
      <a class="nav-link collapsed py-2" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
        <i class="fas fa-scroll mr-2"></i>
        <span>Banners</span>
      </a>
      <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded-lg shadow-sm">
          <a class="collapse-item" href="{{route('banner.index')}}">Active Banners</a>
          <a class="collapse-item" href="{{route('banner.create')}}">Create New</a>
        </div>
      </div>
    </li>
    @endcan

    <!-- Section: Sales & POS -->
    <div class="sidebar-heading px-4 mt-4 mb-2" style="color: #64748b; font-weight: 700; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.15em;">
        Point of Sale
    </div>

    @can('view-order')
    {{-- POS --}}
    <li class="nav-item">
        <a class="nav-link py-2" href="{{route('admin.pos')}}">
            <i class="fas fa-desktop mr-2"></i>
            <span>Local Sales (POS)</span>
        </a>
    </li>
    @endcan

    @can('view-cash-register')
    <li class="nav-item">
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
    {{-- Returns Management --}}
    <li class="nav-item">
        <a class="nav-link collapsed py-2" href="#" data-toggle="collapse" data-target="#returnsCollapse" aria-expanded="true" aria-controls="returnsCollapse">
          <i class="fas fa-undo mr-2"></i>
          <span>Returns</span>
        </a>
        <div id="returnsCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded-lg shadow-sm">
            <a class="collapse-item" href="{{route('returns.sale.index')}}">Sale Returns</a>
            <a class="collapse-item" href="{{route('returns.purchase.index')}}">Purchase Returns</a>
          </div>
        </div>
    </li>
    @endcan

    @can('view-die')
    {{-- Die Management --}}
    <li class="nav-item">
        <a class="nav-link py-2" href="{{route('die-management.index')}}">
            <i class="fas fa-gears mr-2"></i>
            <span>Die Management</span>
        </a>
    </li>
    @endcan

    @can('view-manufacturing')
    <!-- Manufacturing (BOM) -->
    <li class="nav-item">
        <a class="nav-link collapsed py-2" href="#" data-toggle="collapse" data-target="#manufacturingCollapse" aria-expanded="true" aria-controls="manufacturingCollapse">
          <i class="fas fa-industry mr-2"></i>
          <span>Manufacturing</span>
        </a>
        <div id="manufacturingCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded-lg shadow-sm">
            <a class="collapse-item" href="{{route('manufacturing.index')}}">Bill of Materials</a>
            <a class="collapse-item" href="{{route('manufacturing.create')}}">Create New BOM</a>
            <a class="collapse-item" href="{{route('manufacturing.production.index')}}">Production Log</a>
            <a class="collapse-item" href="{{route('manufacturing.production.create')}}">Record Production</a>
          </div>
        </div>
    </li>
    @endcan

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

    {{-- HR --}}
    @hasrole('admin')
    <li class="nav-item">
        <a class="nav-link collapsed py-2" href="#" data-toggle="collapse" data-target="#hrCollapse" aria-expanded="true" aria-controls="hrCollapse">
          <i class="fas fa-user-tie mr-2"></i>
          <span>Human Resources</span>
        </a>
        <div id="hrCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded-lg shadow-sm">
            <a class="collapse-item" href="{{route('attendance.index')}}">Attendance</a>
            <!-- <a class="collapse-item" href="{{route('payroll.index')}}">Payroll & Salaries</a> -->
            <a class="collapse-item" href="{{route('commissions.index')}}">Commissions</a>
          </div>
        </div>
    </li>

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
