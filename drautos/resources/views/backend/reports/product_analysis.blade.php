@extends('backend.layouts.master')
@section('title', 'Product Sales Analysis')

@section('main-content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<div id="product-analysis-dashboard" class="container-fluid py-4">
    <!-- Header Page Title -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-slate-800 font-weight-extrabold tracking-tight">
            <i class="fas fa-chart-pie mr-2 text-indigo-600"></i>Advanced Product Analysis
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="{{route('admin')}}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Product Analysis</li>
            </ol>
        </nav>
    </div>

    <!-- Premium Filter Panel -->
    <div class="card glass-card border-0 mb-4" id="filterCard">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('reports.product-analysis') }}" id="analysisFilterForm">
                <!-- Row 1: Product Selector + Analyze -->
                <div class="row align-items-end mb-3">
                    <div class="col-lg-7 col-md-8 mb-3 mb-md-0">
                        <label class="font-weight-bold text-xs text-uppercase tracking-wider text-slate-500 mb-2 d-block">Select Product <span class="text-slate-400 font-weight-normal text-lowercase">(leave blank for full leaderboard)</span></label>
                        <select name="product_id" id="productSelector" class="form-control select2 select-premium">
                            <option value="">-- All Products (Leaderboard View) --</option>
                            @foreach($products as $prod)
                                <option value="{{ $prod->id }}" {{ request('product_id') == $prod->id ? 'selected' : '' }}>
                                    {{ $prod->title }} (SKU: {{ $prod->sku }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-4 mb-3 mb-md-0">
                        <button type="submit" class="btn btn-premium-primary btn-block h-45 d-flex align-items-center justify-content-center">
                            <i class="fas fa-magic mr-2"></i> Analyze
                        </button>
                    </div>
                    <div class="col-lg-2 col-md-12">
                        @if($selectedProduct)
                            <a href="{{ route('reports.product-analysis.pdf', ['product_id' => request('product_id'), 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                               class="btn btn-premium-danger btn-block h-45 d-flex align-items-center justify-content-center">
                                <i class="fas fa-file-pdf mr-2"></i> Export PDF
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Row 2: Date Preset Chips -->
                <div class="d-flex align-items-center flex-wrap" style="gap:8px;">
                    <span class="text-xs font-weight-bold text-uppercase tracking-wider text-slate-500 mr-2">Period:</span>

                    <button type="button" id="presetAllTime" onclick="applyPreset('all-time')"
                        class="preset-chip {{ $isAllTime ? 'preset-chip-active' : '' }}">
                        <i class="fas fa-infinity mr-1"></i> All Time
                    </button>

                    <button type="button" id="presetThisYear" onclick="applyPreset('this-year')"
                        class="preset-chip {{ (!$isAllTime && request('start_date') == now()->startOfYear()->format('Y-m-d')) ? 'preset-chip-active' : '' }}">
                        <i class="fas fa-calendar-alt mr-1"></i> This Year
                    </button>

                    <button type="button" id="presetThisMonth" onclick="applyPreset('this-month')"
                        class="preset-chip {{ (!$isAllTime && request('start_date') == now()->startOfMonth()->format('Y-m-d')) ? 'preset-chip-active' : '' }}">
                        <i class="fas fa-calendar-week mr-1"></i> This Month
                    </button>

                    <button type="button" id="presetCustom" onclick="toggleCustomDates()"
                        class="preset-chip {{ (!$isAllTime && request('start_date') && request('start_date') != now()->startOfMonth()->format('Y-m-d') && request('start_date') != now()->startOfYear()->format('Y-m-d')) ? 'preset-chip-active' : '' }}">
                        <i class="fas fa-sliders-h mr-1"></i> Custom Range
                    </button>

                    @if(!$isAllTime)
                        <span class="text-xs text-slate-400 ml-2">
                            <i class="far fa-calendar mr-1"></i>
                            Showing: <strong class="text-slate-600">{{ $startDate->format('d M Y') }}</strong>
                            → <strong class="text-slate-600">{{ $endDate->format('d M Y') }}</strong>
                        </span>
                    @else
                        <span class="text-xs text-slate-400 ml-2">
                            <i class="fas fa-infinity mr-1"></i>
                            Showing: <strong class="text-indigo-600">All Time</strong>
                            (from <strong class="text-slate-600">{{ $startDate->format('d M Y') }}</strong>)
                        </span>
                    @endif
                </div>

                <!-- Row 3: Custom Date Range (hidden by default) -->
                <div id="customDateRange" class="mt-3 pt-3" style="border-top:1px dashed #e2e8f0; display:{{ (!$isAllTime && request('start_date') && request('start_date') != now()->startOfMonth()->format('Y-m-d') && request('start_date') != now()->startOfYear()->format('Y-m-d')) ? 'flex' : 'none' }}; gap:12px; align-items:center; flex-wrap:wrap;">
                    <div>
                        <label class="font-weight-bold text-xs text-uppercase tracking-wider text-slate-500 mb-1 d-block">Start Date</label>
                        <input type="date" name="start_date" id="startDateInput" value="{{ !$isAllTime ? $startDate->format('Y-m-d') : '' }}" class="form-control form-premium" style="width:180px;">
                    </div>
                    <div>
                        <label class="font-weight-bold text-xs text-uppercase tracking-wider text-slate-500 mb-1 d-block">End Date</label>
                        <input type="date" name="end_date" id="endDateInput" value="{{ !$isAllTime ? $endDate->format('Y-m-d') : '' }}" class="form-control form-premium" style="width:180px;">
                    </div>
                    <div style="padding-top:20px;">
                        <button type="submit" class="btn btn-premium-primary px-4 h-45 d-flex align-items-center">
                            <i class="fas fa-check mr-2"></i> Apply
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <!-- Alert for Missing Cost -->
    @if($selectedProduct && $selectedProduct->purchase_price == 0)
        <div class="alert premium-alert-warning d-flex align-items-center mb-4 border-0 shadow-sm" role="alert">
            <div class="alert-icon-box mr-3">
                <i class="fas fa-exclamation-triangle fa-lg"></i>
            </div>
            <div>
                <strong class="font-weight-bold">Cost Configuration Missing:</strong> The purchase price of this product is not set. We have calculated margins using fallback average cost from incoming goods or $0.
                <a href="{{ route('product.edit', $selectedProduct->id) }}" class="alert-link font-weight-bold ml-2 text-decoration-underline" target="_blank">
                    Configure Product Cost <i class="fas fa-external-link-alt ml-1 small"></i>
                </a>
            </div>
        </div>
    @endif

    @if($selectedProduct || count($salesHistory) > 0 || $topProducts->count() > 0)
        <!-- KPI Metrics Grid -->
        <div class="row mb-4">
            <!-- Net Sold Volume -->
            <div class="col-xl-3 col-md-6 mb-4 mb-xl-0">
                <div class="card gradient-card-indigo h-100">
                    <div class="card-body d-flex flex-column justify-content-between p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="text-xs font-weight-bold text-uppercase tracking-wider opacity-75">Net Sold Volume</span>
                            <div class="kpi-icon-wrapper">
                                <i class="fas fa-box fa-lg"></i>
                            </div>
                        </div>
                        <div>
                            <h2 class="font-weight-extrabold mb-1 tracking-tight">{{ number_format($stats['net_sold']) }}</h2>
                            <span class="badge badge-pill badge-light-danger font-weight-bold text-xs py-1 px-2">
                                <i class="fas fa-undo-alt mr-1"></i> Return Rate: {{ number_format($stats['return_ratio'], 1) }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Net Revenue -->
            <div class="col-xl-3 col-md-6 mb-4 mb-xl-0">
                <div class="card gradient-card-emerald h-100">
                    <div class="card-body d-flex flex-column justify-content-between p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="text-xs font-weight-bold text-uppercase tracking-wider opacity-75">Net Revenue</span>
                            <div class="kpi-icon-wrapper">
                                <i class="fas fa-wallet fa-lg"></i>
                            </div>
                        </div>
                        <div>
                            <h2 class="font-weight-extrabold mb-1 tracking-tight">Rs. {{ number_format($stats['net_revenue'], 2) }}</h2>
                            <span class="badge badge-pill badge-light-success font-weight-bold text-xs py-1 px-2">
                                Refunds: Rs. {{ number_format($stats['refunded_revenue'], 0) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Goods Received -->
            <div class="col-xl-3 col-md-6 mb-4 mb-md-0">
                <div class="card gradient-card-violet h-100">
                    <div class="card-body d-flex flex-column justify-content-between p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="text-xs font-weight-bold text-uppercase tracking-wider opacity-75">Goods Received</span>
                            <div class="kpi-icon-wrapper">
                                <i class="fas fa-truck-loading fa-lg"></i>
                            </div>
                        </div>
                        <div>
                            <h2 class="font-weight-extrabold mb-1 tracking-tight">{{ number_format($stats['purchased_qty']) }} Units</h2>
                            <span class="badge badge-pill badge-light-violet font-weight-bold text-xs py-1 px-2">
                                Cost: Rs. {{ number_format($stats['total_purchased_cost'], 0) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profit / Margin -->
            <div class="col-xl-3 col-md-6">
                <div class="card gradient-card-{{ $stats['gross_profit'] >= 0 ? 'amber' : 'rose' }} h-100">
                    <div class="card-body d-flex flex-column justify-content-between p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="text-xs font-weight-bold text-uppercase tracking-wider opacity-75">Gross Profit / Margin</span>
                            <div class="kpi-icon-wrapper">
                                <i class="fas fa-chart-line fa-lg"></i>
                            </div>
                        </div>
                        <div>
                            <h2 class="font-weight-extrabold mb-1 tracking-tight">Rs. {{ number_format($stats['gross_profit'], 2) }}</h2>
                            @if($stats['net_revenue'] > 0)
                                <span class="badge badge-pill badge-light-gold font-weight-bold text-xs py-1 px-2">
                                    Margin: {{ number_format(($stats['gross_profit'] / $stats['net_revenue']) * 100, 1) }}%
                                </span>
                            @else
                                <span class="badge badge-pill badge-light-gold font-weight-bold text-xs py-1 px-2">
                                    Margin: 0%
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trend Chart Container -->
        @if($selectedProduct && count($chartLabels) > 0)
            <div class="card glass-card border-0 mb-5">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="font-weight-bold text-slate-800 mb-0">Sales vs Purchases Trend</h5>
                        <p class="text-xs text-slate-400 mb-0">Daily monitoring flow of transaction volumes</p>
                    </div>
                    <div class="chart-badges d-flex">
                        <span class="badge-legend mr-3 text-xs font-weight-bold text-slate-600"><i class="fas fa-circle mr-1 text-indigo-500"></i> Sales</span>
                        <span class="badge-legend mr-3 text-xs font-weight-bold text-slate-600"><i class="fas fa-circle mr-1 text-emerald-500"></i> Purchases</span>
                        <span class="badge-legend text-xs font-weight-bold text-slate-600"><i class="fas fa-circle mr-1 text-rose-500"></i> Returns</span>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    <div style="height: 350px; position: relative;">
                        <canvas id="salesVelocityChart"></canvas>
                    </div>
                </div>
            </div>
        @endif

        @if(!$selectedProduct && $topProducts->count() > 0)
        {{-- ===== Product Sales Leaderboard (All-Products View) ===== --}}
        <div class="card glass-card border-0 mb-0" id="leaderboardPanel">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center flex-wrap" style="gap:8px;">
                <div>
                    <h5 class="font-weight-bold text-slate-800 mb-0">
                        <i class="fas fa-trophy mr-2" style="color:#f59e0b;"></i>Product Sales Leaderboard
                    </h5>
                    <p class="text-xs text-slate-400 mb-0">Ranked by total revenue — highest selling product first</p>
                </div>
                <div class="d-flex align-items-center" style="gap:10px;">
                    <span class="badge badge-soft-indigo font-weight-bold px-3 py-2 text-xs">
                        <i class="fas fa-layer-group mr-1"></i>{{ $topProducts->count() }} Products
                    </span>
                    <div class="lb-search-wrap" style="position:relative;">
                        <i class="fas fa-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:0.75rem;"></i>
                        <input type="text" id="lbSearchInput" placeholder="Search product..." class="form-control form-premium" style="padding-left:30px;height:36px;font-size:0.83rem;width:200px;">
                    </div>
                </div>
            </div>
            <div class="card-body p-4 pt-2">
                <div class="table-responsive">
                    <table class="table table-modern" id="leaderboardTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width:52px;">Rank</th>
                                <th>Product</th>
                                <th class="text-center">Gross Sold</th>
                                <th class="text-center">Returns</th>
                                <th class="text-center">Net Sold</th>
                                <th class="text-center">Return Rate</th>
                                <th class="text-right">Gross Revenue</th>
                                <th class="text-right">Net Revenue</th>
                                <th class="text-center" style="width:90px;">Analyze</th>
                            </tr>
                        </thead>
                        <tbody id="leaderboardBody">
                            @php $maxRevenue = $topProducts->first()->total_revenue ?? 1; @endphp
                            @foreach($topProducts as $idx => $p)
                            @php
                                $rank = $idx + 1;
                                $barPct = $maxRevenue > 0 ? round(($p->net_revenue / $maxRevenue) * 100) : 0;
                                $rankIcon = $rank === 1 ? '🥇' : ($rank === 2 ? '🥈' : ($rank === 3 ? '🥉' : '#'.$rank));
                                $rateColor = $p->return_rate > 15 ? '#f43f5e' : ($p->return_rate > 5 ? '#f59e0b' : '#10b981');
                                $rateClass = $p->return_rate > 15 ? 'badge-soft-rose' : ($p->return_rate > 5 ? 'badge-soft-amber' : 'badge-soft-emerald');
                            @endphp
                            <tr class="lb-row row-sale" data-name="{{ strtolower($p->product_title) }}" data-sku="{{ strtolower($p->sku ?? '') }}">
                                <td>
                                    <span class="lb-rank-badge" style="font-size:{{ $rank <= 3 ? '1.3rem' : '0.85rem' }};font-weight:800;color:#64748b;">
                                        {{ $rankIcon }}
                                    </span>
                                </td>
                                <td>
                                    <div class="font-weight-bold text-slate-800" style="font-size:0.88rem;line-height:1.3;">{{ $p->product_title }}</div>
                                    <div class="text-xs text-slate-400 mt-1">SKU: {{ $p->sku ?? '—' }}</div>
                                    <div class="lb-bar-wrap mt-2" style="background:#f1f5f9;border-radius:6px;height:5px;overflow:hidden;">
                                        <div class="lb-bar" style="width:{{ $barPct }}%;height:5px;background:linear-gradient(90deg,#6366f1,#818cf8);border-radius:6px;transition:width 1s ease;"></div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="font-weight-bold text-slate-700" style="font-size:0.95rem;">{{ number_format($p->gross_qty) }}</span>
                                    <div class="text-xs text-slate-400">units</div>
                                </td>
                                <td class="text-center">
                                    @if($p->returned_qty > 0)
                                        <span class="font-weight-bold" style="color:#f43f5e;font-size:0.95rem;">{{ number_format($p->returned_qty) }}</span>
                                        <div class="text-xs text-slate-400">Rs. {{ number_format($p->refunded_amount, 0) }}</div>
                                    @else
                                        <span class="text-slate-300 font-weight-bold">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="font-weight-bold text-emerald-600" style="font-size:0.95rem;">{{ number_format($p->net_qty) }}</span>
                                    <div class="text-xs text-slate-400">units</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $rateClass }} font-weight-bold px-2 py-1" style="font-size:0.78rem;border-radius:6px;">
                                        {{ $p->return_rate }}%
                                    </span>
                                </td>
                                <td class="text-right">
                                    <span class="font-weight-semibold text-slate-600" style="font-size:0.88rem;">Rs. {{ number_format($p->total_revenue, 0) }}</span>
                                </td>
                                <td class="text-right">
                                    <span class="font-weight-extrabold text-slate-800" style="font-size:0.95rem;">Rs. {{ number_format($p->net_revenue, 0) }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('reports.product-analysis', ['product_id' => $p->product_id, 'start_date' => request('start_date', $startDate->format('Y-m-d')), 'end_date' => request('end_date', $endDate->format('Y-m-d'))]) }}"
                                       class="btn btn-sm btn-premium-primary px-3" style="border-radius:8px;font-size:0.75rem;height:30px;line-height:18px;" title="Deep-dive into {{ $p->product_title }}">
                                        <i class="fas fa-search mr-1"></i> Drill
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @else
        {{-- ===== Flow Ledger (Single Product View) ===== --}}
        <div class="card glass-card border-0">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="font-weight-bold text-slate-800 mb-0">Inventory Flow Ledger</h5>
                <p class="text-xs text-slate-400 mb-0">Chronological list of all sales, returns, and incoming shipments</p>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-modern" id="analysisTable" width="100%">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Event Type</th>
                                <th>Reference #</th>
                                <th>Customer / Supplier</th>
                                <th class="text-center">Quantity Change</th>
                                <th class="text-right">Unit Price/Cost</th>
                                <th class="text-right">Total Flow Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($salesHistory as $event)
                            @php
                                $rowClass = '';
                                $badgeClass = '';
                                $typeLabel = '';
                                $qtyPrefix = '';
                                $qtyColor = '';
                                
                                if ($event->type == 'sale') {
                                    $rowClass = 'row-sale';
                                    $badgeClass = 'badge-soft-indigo';
                                    $typeLabel = 'Sale (Outgoing)';
                                    $qtyPrefix = '-';
                                    $qtyColor = 'text-indigo-600 font-weight-bold';
                                } elseif ($event->type == 'purchase') {
                                    $rowClass = 'row-purchase';
                                    $badgeClass = 'badge-soft-emerald';
                                    $typeLabel = 'Incoming Goods';
                                    $qtyPrefix = '+';
                                    $qtyColor = 'text-emerald-600 font-weight-bold';
                                } elseif ($event->type == 'return') {
                                    $rowClass = 'row-return';
                                    $badgeClass = 'badge-soft-rose';
                                    $typeLabel = 'Sale Return';
                                    $qtyPrefix = '+';
                                    $qtyColor = 'text-rose-600 font-weight-bold';
                                }
                            @endphp
                            <tr class="{{ $rowClass }}">
                                <td>
                                    <div class="text-xs font-weight-semibold text-slate-400">
                                        <i class="far fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($event->date)->format('d M Y, h:i A') }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ $badgeClass }} font-weight-bold px-2.5 py-1.5 text-xs">
                                        {{ $typeLabel }}
                                    </span>
                                </td>
                                <td>
                                    @if($event->ref_url)
                                        <a href="{{ $event->ref_url }}" class="font-weight-bold text-indigo-600 hover-underline" target="_blank">
                                            {{ $event->ref }} <i class="fas fa-external-link-alt ml-1 small opacity-50"></i>
                                        </a>
                                    @else
                                        <span class="font-weight-bold text-slate-700">{{ $event->ref }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($event->party_url)
                                        <a href="{{ $event->party_url }}" class="font-weight-semibold text-slate-700 hover-underline" target="_blank">
                                            <i class="fas fa-link mr-2 text-slate-400 small"></i>{{ $event->party_name }}
                                        </a>
                                    @else
                                        <span class="font-weight-semibold text-slate-600">{{ $event->party_name }}</span>
                                    @endif
                                </td>
                                <td class="text-center {{ $qtyColor }}" style="font-size: 1.05rem;">
                                    {{ $qtyPrefix }}{{ abs($event->qty) }}
                                </td>
                                <td class="text-right text-slate-700 font-weight-semibold">
                                    Rs. {{ number_format($event->unit_price, 2) }}
                                </td>
                                <td class="text-right text-slate-800 font-weight-extrabold">
                                    Rs. {{ number_format($event->total, 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    @else
        <div class="card glass-card border-0 shadow-sm py-5 text-center">
            <div class="card-body">
                <div class="empty-state-icon mb-3">
                    <i class="fas fa-folder-open fa-3x text-slate-300"></i>
                </div>
                <h5 class="font-weight-bold text-slate-700">No Data Loaded</h5>
                <p class="text-slate-400 text-sm max-w-md mx-auto mb-4">Please select a product and pick a date range to generate visual charts and interactive chronological flow ledgers.</p>
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Premium Styling Overrides */
    #product-analysis-dashboard {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: #f8fafc;
        min-height: 100vh;
    }
    
    /* Fix for FontAwesome icons font override */
    #product-analysis-dashboard i.fas, 
    #product-analysis-dashboard i.far, 
    #product-analysis-dashboard i.fab,
    #product-analysis-dashboard i.fa {
        font-family: "Font Awesome 5 Free", "Font Awesome 6 Free", "FontAwesome", sans-serif !important;
    }
    
    .font-weight-extrabold { font-weight: 800 !important; }
    .font-weight-semibold { font-weight: 600 !important; }
    
    /* Colors & Typography */
    .text-slate-800 { color: #1e293b; }
    .text-slate-700 { color: #334155; }
    .text-slate-600 { color: #475569; }
    .text-slate-500 { color: #64748b; }
    .text-slate-400 { color: #94a3b8; }
    .text-slate-300 { color: #cbd5e1; }
    
    .tracking-tight { letter-spacing: -0.025em; }
    .tracking-wider { letter-spacing: 0.05em; }
    .hover-underline:hover { text-decoration: underline !important; }
    
    /* Card Glassmorphism */
    #product-analysis-dashboard .glass-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 20px -2px rgba(148, 163, 184, 0.08), 0 2px 8px -1px rgba(148, 163, 184, 0.04);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
    }
    #product-analysis-dashboard .glass-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px -4px rgba(148, 163, 184, 0.14), 0 4px 12px -2px rgba(148, 163, 184, 0.08);
    }
    
    /* Gaps & Height Controls */
    .h-45 { height: 45px !important; }
    .max-w-md { max-w: 28rem; }
    .mx-auto { margin-left: auto; margin-right: auto; }
    
    /* Premium KPI Gradients */
    #product-analysis-dashboard .gradient-card-indigo {
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        color: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.3);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    #product-analysis-dashboard .gradient-card-indigo:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 35px -8px rgba(99, 102, 241, 0.5);
    }
    
    #product-analysis-dashboard .gradient-card-emerald {
        background: linear-gradient(135deg, #10b981, #059669);
        color: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.3);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    #product-analysis-dashboard .gradient-card-emerald:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 35px -8px rgba(16, 185, 129, 0.5);
    }
    
    #product-analysis-dashboard .gradient-card-violet {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        color: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(139, 92, 246, 0.3);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    #product-analysis-dashboard .gradient-card-violet:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 35px -8px rgba(139, 92, 246, 0.5);
    }
    
    #product-analysis-dashboard .gradient-card-amber {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(245, 158, 11, 0.3);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    #product-analysis-dashboard .gradient-card-amber:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 35px -8px rgba(245, 158, 11, 0.5);
    }

    #product-analysis-dashboard .gradient-card-rose {
        background: linear-gradient(135deg, #f43f5e, #e11d48);
        color: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(244, 63, 94, 0.3);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    #product-analysis-dashboard .gradient-card-rose:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 35px -8px rgba(244, 63, 94, 0.5);
    }
    
    /* KPI Icons and Badges */
    .kpi-icon-wrapper {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.18);
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(4px);
        box-shadow: inset 0 1px 1px rgba(255,255,255,0.2);
    }
    .badge-light-danger {
        background-color: rgba(255, 255, 255, 0.2);
        color: #fff;
    }
    .badge-light-success {
        background-color: rgba(255, 255, 255, 0.2);
        color: #fff;
    }
    .badge-light-violet {
        background-color: rgba(255, 255, 255, 0.2);
        color: #fff;
    }
    .badge-light-gold {
        background-color: rgba(255, 255, 255, 0.2);
        color: #fff;
    }
    
    /* Form Premium Inputs */
    #product-analysis-dashboard .form-premium {
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        padding: 10px 14px;
        height: 45px !important;
        font-size: 0.9rem;
        transition: all 0.2s ease;
        background-color: #fff;
    }
    #product-analysis-dashboard .form-premium:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
        background-color: #fff;
    }
    
    /* Premium Buttons */
    #product-analysis-dashboard .btn-premium-primary {
        background: linear-gradient(135deg, #4f46e5, #4338ca);
        color: #fff !important;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
        transition: all 0.25s ease;
    }
    #product-analysis-dashboard .btn-premium-primary:hover {
        background: linear-gradient(135deg, #4338ca, #3730a3);
        box-shadow: 0 8px 18px rgba(79, 70, 229, 0.35);
        transform: translateY(-1.5px);
    }
    
    #product-analysis-dashboard .btn-premium-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #fff !important;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15);
        transition: all 0.25s ease;
    }
    #product-analysis-dashboard .btn-premium-danger:hover {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        box-shadow: 0 8px 18px rgba(239, 68, 68, 0.3);
        transform: translateY(-1.5px);
    }
    
    /* Warning Alert Premium */
    #product-analysis-dashboard .premium-alert-warning {
        background: #fef3c7;
        color: #92400e;
        border-radius: 12px;
        padding: 16px 20px;
        box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.05);
    }
    #product-analysis-dashboard .alert-icon-box {
        width: 36px;
        height: 36px;
        background-color: rgba(245, 158, 11, 0.15);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #d97706;
    }
    
    /* Modern Ledger Floating Rows */
    #product-analysis-dashboard .table-modern {
        border-collapse: separate !important;
        border-spacing: 0 10px !important;
        background: transparent;
        width: 100% !important;
        border: none !important;
    }
    #product-analysis-dashboard .table-modern th {
        border: none !important;
        background: transparent !important;
        color: #64748b !important;
        font-weight: 700 !important;
        font-size: 0.75rem !important;
        text-transform: uppercase !important;
        letter-spacing: 0.08em !important;
        padding: 8px 16px !important;
    }
    #product-analysis-dashboard .table-modern tbody tr {
        background-color: #ffffff;
        box-shadow: 0 2px 4px rgba(148, 163, 184, 0.04), 0 1px 2px rgba(148, 163, 184, 0.02);
        transition: all 0.2s ease;
        border-radius: 12px;
    }
    #product-analysis-dashboard .table-modern tbody tr:hover {
        transform: translateY(-1.5px);
        box-shadow: 0 10px 20px rgba(148, 163, 184, 0.08), 0 4px 6px rgba(148, 163, 184, 0.04);
    }
    #product-analysis-dashboard .table-modern td {
        border: none !important;
        padding: 16px !important;
        vertical-align: middle !important;
        background-color: #ffffff;
    }
    #product-analysis-dashboard .table-modern tbody td:first-child {
        border-top-left-radius: 12px !important;
        border-bottom-left-radius: 12px !important;
    }
    #product-analysis-dashboard .table-modern tbody td:last-child {
        border-top-right-radius: 12px !important;
        border-bottom-right-radius: 12px !important;
    }
    
    /* Table left indicator lines via absolute positioning to prevent curved borders */
    #product-analysis-dashboard .table-modern tbody tr td:first-child {
        position: relative;
        padding-left: 24px !important;
    }
    #product-analysis-dashboard .table-modern tbody tr.row-sale td:first-child::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 6px;
        background-color: #6366f1;
        border-top-left-radius: 12px;
        border-bottom-left-radius: 12px;
    }
    #product-analysis-dashboard .table-modern tbody tr.row-purchase td:first-child::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 6px;
        background-color: #10b981;
        border-top-left-radius: 12px;
        border-bottom-left-radius: 12px;
    }
    #product-analysis-dashboard .table-modern tbody tr.row-return td:first-child::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 6px;
        background-color: #f43f5e;
        border-top-left-radius: 12px;
        border-bottom-left-radius: 12px;
    }
    
    /* Soft color badges for table tags */
    .badge-soft-indigo {
        background-color: #e0e7ff;
        color: #4f46e5;
        border-radius: 6px;
    }
    .badge-soft-emerald {
        background-color: #d1fae5;
        color: #059669;
        border-radius: 6px;
    }
    .badge-soft-rose {
        background-color: #ffe4e6;
        color: #e11d48;
        border-radius: 6px;
    }
    .badge-soft-amber {
        background-color: #fef3c7;
        color: #b45309;
        border-radius: 6px;
    }
    /* Leaderboard row hover animation */
    #leaderboardPanel .lb-row:hover .lb-bar {
        filter: brightness(1.15);
    }
    .text-emerald-600 { color: #059669 !important; }
    
    /* Select2 custom override inside dashboard wrapper */
    #product-analysis-dashboard .select2-container--default .select2-selection--single {
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        height: 45px;
        padding: 8px 12px;
        background-color: #fff;
    }
    #product-analysis-dashboard .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 27px;
        color: #1e293b;
        font-size: 0.9rem;
    }
    #product-analysis-dashboard .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 43px;
    }
    
    /* DataTables search/pagination overrides */
    #product-analysis-dashboard .dataTables_wrapper .dataTables_filter input {
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        padding: 6px 12px;
        margin-left: 0.5em;
        font-size: 0.85rem;
    }
    #product-analysis-dashboard .dataTables_wrapper .dataTables_length select {
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        padding: 4px 8px;
        font-size: 0.85rem;
    }

    /* ── Preset Chip Buttons ─────────────────────────────────── */
    .preset-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 6px 14px;
        border-radius: 20px;
        border: 1.5px solid #e2e8f0;
        background: #f8fafc;
        color: #64748b;
        font-size: 0.78rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        font-family: 'Plus Jakarta Sans', sans-serif;
        white-space: nowrap;
    }
    .preset-chip:hover {
        border-color: #6366f1;
        color: #4f46e5;
        background: #eef2ff;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(99,102,241,0.1);
    }
    .preset-chip-active {
        background: linear-gradient(135deg, #6366f1, #4f46e5) !important;
        border-color: #4f46e5 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(99,102,241,0.25);
    }
    .preset-chip-active:hover {
        background: linear-gradient(135deg, #4f46e5, #4338ca) !important;
        color: #ffffff !important;
        transform: translateY(-1px);
    }
    .text-indigo-600 { color: #4f46e5 !important; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%'
        });
        
        $('#analysisTable').DataTable({
            "order": [[ 0, "desc" ]],
            "pageLength": 25,
            "dom": "<'row mb-3 align-items-center'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 text-right'f>>" +
                   "<'row'<'col-sm-12'tr>>" +
                   "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 text-right'p>>"
        });

        @if($selectedProduct && count($chartLabels) > 0)
        // Setup Chart
        const ctx = document.getElementById('salesVelocityChart').getContext('2d');
        
        // Create canvas line gradients
        const gradientSales = ctx.createLinearGradient(0, 0, 0, 300);
        gradientSales.addColorStop(0, 'rgba(99, 102, 241, 0.2)');
        gradientSales.addColorStop(1, 'rgba(99, 102, 241, 0.0)');
        
        const gradientPurchases = ctx.createLinearGradient(0, 0, 0, 300);
        gradientPurchases.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
        gradientPurchases.addColorStop(1, 'rgba(16, 185, 129, 0.0)');
        
        const gradientReturns = ctx.createLinearGradient(0, 0, 0, 300);
        gradientReturns.addColorStop(0, 'rgba(244, 63, 94, 0.15)');
        gradientReturns.addColorStop(1, 'rgba(244, 63, 94, 0.0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [
                    {
                        label: 'Units Sold (Sales)',
                        data: @json($chartSalesData),
                        borderColor: '#6366f1',
                        backgroundColor: gradientSales,
                        borderWidth: 3,
                        pointBackgroundColor: '#6366f1',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointHoverRadius: 7,
                        pointHoverBorderWidth: 3,
                        pointRadius: 4,
                        tension: 0.35,
                        fill: true
                    },
                    {
                        label: 'Units Bought (Incoming Goods)',
                        data: @json($chartPurchasesData),
                        borderColor: '#10b981',
                        backgroundColor: gradientPurchases,
                        borderWidth: 3,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointHoverRadius: 7,
                        pointHoverBorderWidth: 3,
                        pointRadius: 4,
                        tension: 0.35,
                        fill: true
                    },
                    {
                        label: 'Units Returned (Sales Return)',
                        data: @json($chartReturnsData),
                        borderColor: '#f43f5e',
                        backgroundColor: gradientReturns,
                        borderWidth: 2,
                        borderDash: [5, 5],
                        pointBackgroundColor: '#f43f5e',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 1.5,
                        pointHoverRadius: 6,
                        pointHoverBorderWidth: 2,
                        pointRadius: 3,
                        tension: 0.35,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f5f9',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#64748b',
                            font: {
                                family: "'Plus Jakarta Sans', sans-serif",
                                size: 11,
                                weight: 500
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#64748b',
                            font: {
                                family: "'Plus Jakarta Sans', sans-serif",
                                size: 11,
                                weight: 500
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false /* Custom HTML legend implemented in header */
                    },
                    tooltip: {
                        padding: 14,
                        backgroundColor: '#1e293b',
                        titleFont: {
                            family: "'Plus Jakarta Sans', sans-serif",
                            size: 13,
                            weight: 'bold'
                        },
                        bodyFont: {
                            family: "'Plus Jakarta Sans', sans-serif",
                            size: 12
                        },
                        cornerRadius: 12,
                        boxPadding: 6,
                        usePointStyle: true
                    }
                }
            }
        });
        @endif

        // Leaderboard live search
        var lbInput = document.getElementById('lbSearchInput');
        if (lbInput) {
            lbInput.addEventListener('keyup', function() {
                var q = this.value.toLowerCase().trim();
                document.querySelectorAll('#leaderboardBody .lb-row').forEach(function(tr) {
                    var name = tr.getAttribute('data-name') || '';
                    var sku  = tr.getAttribute('data-sku') || '';
                    tr.style.display = (name.indexOf(q) !== -1 || sku.indexOf(q) !== -1) ? '' : 'none';
                });
            });
        }
    });

    // ── Date Preset Chip Functions ──────────────────────────────
    function applyPreset(preset) {
        var form   = document.getElementById('analysisFilterForm');
        var sInput = document.getElementById('startDateInput');
        var eInput = document.getElementById('endDateInput');
        var today  = new Date();

        function fmt(d) {
            return d.toISOString().split('T')[0];
        }

        if (preset === 'all-time') {
            // Remove date params → controller will use all-time
            if (sInput) sInput.name = '';
            if (eInput) eInput.name = '';
            form.submit();
            return;
        }

        // Ensure inputs have their names
        if (sInput) sInput.name = 'start_date';
        if (eInput) eInput.name = 'end_date';

        var start, end = fmt(today);

        if (preset === 'this-year') {
            start = fmt(new Date(today.getFullYear(), 0, 1));
        } else if (preset === 'this-month') {
            start = fmt(new Date(today.getFullYear(), today.getMonth(), 1));
        }

        if (sInput) sInput.value = start;
        if (eInput) eInput.value = end;

        // Show custom area with filled values, then submit
        var customArea = document.getElementById('customDateRange');
        if (customArea) customArea.style.display = 'flex';

        form.submit();
    }

    function toggleCustomDates() {
        var customArea = document.getElementById('customDateRange');
        if (!customArea) return;
        var isVisible = customArea.style.display !== 'none';
        customArea.style.display = isVisible ? 'none' : 'flex';
        // Make chip look active when open
        var btn = document.getElementById('presetCustom');
        if (btn) btn.classList.toggle('preset-chip-active', !isVisible);
    }
</script>
@endpush
