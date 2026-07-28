@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid" id="customerReportPage">

    {{-- ════════════════════════════════════════════════════════ --}}
    {{-- PAGE HEADER                                              --}}
    {{-- ════════════════════════════════════════════════════════ --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Customer Report</h1>
            <p class="text-muted small mb-0">Full account statement, order & payment history</p>
        </div>
        @if($selectedCustomer)
        <div class="d-flex gap-2 mt-2 mt-sm-0">
            <button onclick="window.print()" class="btn btn-sm btn-outline-secondary shadow-sm">
                <i class="fas fa-print fa-sm mr-1"></i> Print
            </button>
            <a href="{{ route('reports.customer.pdf', array_merge(request()->all(), ['customer_id' => $selectedCustomer->id])) }}"
               class="btn btn-sm btn-success shadow-sm">
                <i class="fas fa-file-pdf fa-sm mr-1"></i> Download PDF
            </a>
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $selectedCustomer->phone) }}?text={{ urlencode('Hello '.$selectedCustomer->name.', please find your account statement from Danyal Autos.') }}"
               target="_blank" class="btn btn-sm btn-success shadow-sm" style="background:#25D366;border-color:#25D366;">
                <i class="fab fa-whatsapp fa-sm mr-1"></i> WhatsApp
            </a>
        </div>
        @endif
    </div>

    {{-- ════════════════════════════════════════════════════════ --}}
    {{-- CUSTOMER RANKINGS LEADERBOARD                            --}}
    {{-- ════════════════════════════════════════════════════════ --}}
    @if($customerRankings->count() > 0)
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center justify-content-between bg-white"
             data-toggle="collapse" data-target="#rankingsSection" aria-expanded="true" style="cursor:pointer;">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-trophy mr-2 text-warning"></i>
                Customer Rankings — {{ $customerRankings->count() }} customers
            </h6>
            <div class="d-flex align-items-center" style="gap:8px;">
                <span class="badge badge-success">⭐⭐⭐⭐⭐ {{ $customerRankings->where('star_rating',5)->count() }} Excellent</span>
                <span class="badge badge-info">⭐⭐⭐⭐ {{ $customerRankings->where('star_rating',4)->count() }} Good</span>
                <span class="badge badge-warning">⭐⭐⭐ {{ $customerRankings->where('star_rating',3)->count() }} Average</span>
                <span class="badge badge-danger">⭐⭐ {{ $customerRankings->whereIn('star_rating',[1,2])->count() }} Risky</span>
                <i class="fas fa-chevron-down text-muted" style="font-size:12px;"></i>
            </div>
        </div>
        <div class="collapse show" id="rankingsSection">
            @php
                $cities = $customerRankings->pluck('city')->map('trim')->filter()->map(function($c) {
                    return ucwords(strtolower($c));
                })->unique()->sort();
            @endphp
            <div class="card-body p-0">
                {{-- Leaderboard Search Bar --}}
                <div class="px-3 py-2 bg-light border-bottom d-flex align-items-center flex-wrap" style="gap:10px;">
                    <div class="input-group input-group-sm" style="max-width:350px;">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                        </div>
                        <input type="text" id="leaderboardSearch" class="form-control border-left-0" placeholder="Search customer, phone, city...">
                    </div>

                    {{-- City Filter Dropdown --}}
                    <select id="leaderboardCityFilter" class="form-control form-control-sm custom-select custom-select-sm" style="max-width:180px; height: 31px;">
                        <option value="">All Cities</option>
                        @foreach($cities as $city)
                            <option value="{{ strtolower($city) }}">{{ $city }}</option>
                        @endforeach
                    </select>

                    <!-- Advanced Filters Toggle -->
                    <button type="button" id="advFiltersToggle" class="btn btn-sm btn-outline-secondary" style="height:31px; padding:0 10px; font-size:12px; white-space:nowrap;">
                        <i class="fas fa-sliders-h mr-1"></i> Filters
                        <span id="advFiltersCount" class="badge badge-primary ml-1" style="display:none; font-size:9px;">0</span>
                    </button>

                    <div class="ml-auto text-muted small" id="leaderboardSearchCount" style="font-size:11px; font-weight:600;">
                        Showing {{ $customerRankings->count() }} of {{ $customerRankings->count() }}
                    </div>
                </div>

                <!-- Advanced Filters Panel -->
                <div id="advFiltersPanel" style="display:none; padding:10px 15px 12px; background:#f8f9fc; border-bottom:1px solid #e3e6f0;">
                    <div class="row align-items-end" style="gap:0;">

                        <!-- Total Sales Range -->
                        <div class="col-12 col-md-4 mb-2">
                            <label style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.8px; color:#4e73df; margin-bottom:4px;">
                                <i class="fas fa-chart-bar mr-1"></i> Total Sales (Rs.)
                            </label>
                            <div class="d-flex align-items-center" style="gap:6px;">
                                <input type="number" id="filterSalesMin" placeholder="Min e.g. 100000"
                                    class="form-control form-control-sm" style="font-size:12px; border-radius:6px;">
                                <span class="text-muted" style="font-size:11px; white-space:nowrap;">to</span>
                                <input type="number" id="filterSalesMax" placeholder="Max e.g. 500000"
                                    class="form-control form-control-sm" style="font-size:12px; border-radius:6px;">
                            </div>
                        </div>

                        <!-- Outstanding Range -->
                        <div class="col-12 col-md-3 mb-2">
                            <label style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.8px; color:#e74a3b; margin-bottom:4px;">
                                <i class="fas fa-exclamation-circle mr-1"></i> Outstanding (Rs.)
                            </label>
                            <div class="d-flex align-items-center" style="gap:6px;">
                                <input type="number" id="filterOsMin" placeholder="Min e.g. 0"
                                    class="form-control form-control-sm" style="font-size:12px; border-radius:6px;">
                                <span class="text-muted" style="font-size:11px; white-space:nowrap;">to</span>
                                <input type="number" id="filterOsMax" placeholder="Max e.g. 50000"
                                    class="form-control form-control-sm" style="font-size:12px; border-radius:6px;">
                            </div>
                        </div>

                        <!-- Last Order Days Range -->
                        <div class="col-12 col-md-3 mb-2">
                            <label style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.8px; color:#1cc88a; margin-bottom:4px;">
                                <i class="fas fa-clock mr-1"></i> Last Order (days ago)
                            </label>
                            <div class="d-flex align-items-center" style="gap:6px;">
                                <input type="number" id="filterDaysMin" placeholder="Min e.g. 0"
                                    class="form-control form-control-sm" style="font-size:12px; border-radius:6px;">
                                <span class="text-muted" style="font-size:11px; white-space:nowrap;">to</span>
                                <input type="number" id="filterDaysMax" placeholder="Max e.g. 30"
                                    class="form-control form-control-sm" style="font-size:12px; border-radius:6px;">
                            </div>
                        </div>

                        <!-- Clear Button -->
                        <div class="col-12 col-md-auto mb-2 d-flex align-items-end">
                            <button type="button" id="clearAdvFilters" class="btn btn-sm btn-outline-danger" style="height:31px; padding:0 12px; font-size:12px; border-radius:6px; white-space:nowrap;">
                                <i class="fas fa-times mr-1"></i> Clear Filters
                            </button>
                        </div>

                    </div>
                    <div class="text-muted mt-1" style="font-size:10px;">
                        <i class="fas fa-info-circle mr-1"></i> Leave a field empty to skip that filter. Results update instantly as you type.
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0" id="rankingsTable">
                        <thead class="thead-light">
                            <tr>
                                <th style="width:40px;" class="text-center">#</th>
                                <th>Customer</th>
                                <th class="text-center">Rating</th>
                                <th class="text-center">Recovery</th>
                                <th class="text-right">Total Sales</th>
                                <th class="text-right">Outstanding</th>
                                <th class="text-center">Last Order</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customerRankings as $i => $rc)
                            @php
                                $rank = $i + 1;
                                $medalColor = $rank == 1 ? '#FFD700' : ($rank == 2 ? '#C0C0C0' : ($rank == 3 ? '#CD7F32' : '#aaa'));
                                $medalIcon  = $rank <= 3 ? 'fas fa-medal' : 'fas fa-hashtag';
                            @endphp
                            <tr class="leaderboard-row"
                                data-search="{{ strtolower($rc->name) }} {{ strtolower($rc->phone ?? '') }} {{ strtolower($rc->city ?? '') }} {{ strtolower($rc->customer_type ?? '') }} {{ strtolower($rc->health_label ?? '') }}"
                                data-city="{{ strtolower(trim($rc->city ?? '')) }}"
                                data-total-sales="{{ intval($rc->total_sales) }}"
                                data-outstanding="{{ intval($rc->outstanding) }}"
                                data-days="{{ $rc->days_since_last !== null ? intval($rc->days_since_last) : '' }}"
                                style="border-left:3px solid {{ $rc->health_color }};">
                                <td class="text-center font-weight-bold" style="color:{{ $medalColor }};">
                                    <i class="{{ $medalIcon }}" style="font-size:{{ $rank<=3?'14px':'11px' }};"></i>
                                    {{ $rank }}
                                </td>
                                <td>
                                    <div class="font-weight-bold" style="font-size:13px;">{{ $rc->name }}</div>
                                    <div class="text-muted" style="font-size:10px;">
                                        @if($rc->phone)<span class="mr-2">{{ $rc->phone }}</span>@endif
                                        @if($rc->city)<span>{{ $rc->city }}</span>@endif
                                        @if($rc->customer_type)<span class="badge badge-light ml-1" style="font-size:9px;">{{ ucfirst($rc->customer_type) }}</span>@endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div style="color:{{ $rc->health_color }};font-size:13px;letter-spacing:-1px;">
                                        @for($s=1;$s<=5;$s++)
                                            <i class="fas fa-star" style="color:{{ $s<=$rc->star_rating ? $rc->health_color : '#e0e0e0' }};font-size:12px;"></i>
                                        @endfor
                                    </div>
                                    <div style="font-size:9px;color:{{ $rc->health_color }};font-weight:700;">{{ $rc->health_label }}</div>
                                </td>
                                <td class="text-center">
                                    <div class="font-weight-bold" style="color:{{ $rc->recovery_rate >= 75 ? '#1cc88a' : ($rc->recovery_rate >= 50 ? '#f6c23e' : '#e74a3b') }}; font-size:14px;">
                                        {{ $rc->recovery_rate }}%
                                    </div>
                                    <div style="margin:2px auto;width:60px;height:5px;background:#e9ecef;border-radius:3px;">
                                        <div style="width:{{ min($rc->recovery_rate,100) }}%;height:100%;background:{{ $rc->recovery_rate >= 75 ? '#1cc88a' : ($rc->recovery_rate >= 50 ? '#f6c23e' : '#e74a3b') }};border-radius:3px;"></div>
                                    </div>
                                </td>
                                <td class="text-right font-weight-bold" style="font-size:13px;">
                                    Rs. {{ number_format($rc->total_sales, 0) }}
                                    <div class="text-muted" style="font-size:10px;">{{ $rc->total_orders }} orders</div>
                                </td>
                                <td class="text-right">
                                    @if($rc->outstanding > 0)
                                        <span class="font-weight-bold text-danger" style="font-size:13px;">Rs. {{ number_format($rc->outstanding, 0) }}</span>
                                    @else
                                        <span class="text-success font-weight-bold" style="font-size:13px;">Cleared ✓</span>
                                    @endif
                                </td>
                                <td class="text-center" style="font-size:11px;">
                                    @if($rc->days_since_last !== null)
                                        <span style="color:{{ $rc->days_since_last <= 30 ? '#1cc88a' : ($rc->days_since_last <= 90 ? '#f6c23e' : '#e74a3b') }};">
                                            {{ $rc->days_since_last }} days ago
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('reports.customer', ['customer_id' => $rc->id, 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                                       class="btn btn-xs btn-primary" style="font-size:11px;padding:3px 8px;">
                                        <i class="fas fa-chart-line mr-1"></i>View
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ════════════════════════════════════════════════════════ --}}
    {{-- CUSTOMER FILTER FORM                                     --}}
    {{-- ════════════════════════════════════════════════════════ --}}
    <div class="card shadow mb-4">

        <div class="card-body py-3">
            <form action="{{ route('reports.customer') }}" method="GET" id="customerFilterForm">
                <div class="form-row align-items-end">
                    <div class="col-md-5 mb-2">
                        <label class="small font-weight-bold text-gray-700" for="customer_id">Select Customer</label>
                        <select name="customer_id" id="customer_id" class="form-control form-control-sm select2-customer" required>
                            <option value="">-- Select Customer --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}"
                                    {{ $selectedCustomer && $selectedCustomer->id == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                    ({{ $customer->phone ?? $customer->email ?? 'N/A' }})
                                    @if($customer->current_balance > 0)
                                        | Due: Rs.{{ number_format($customer->current_balance,0) }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="small font-weight-bold text-gray-700" for="start_date">From</label>
                        <input type="date" name="start_date" id="start_date" class="form-control form-control-sm"
                               value="{{ $startDate->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="small font-weight-bold text-gray-700" for="end_date">To</label>
                        <input type="date" name="end_date" id="end_date" class="form-control form-control-sm"
                               value="{{ $endDate->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-3 mb-2 d-flex" style="gap:6px;">
                        <button type="submit" class="btn btn-primary btn-sm flex-fill">
                            <i class="fas fa-search mr-1"></i> Generate
                        </button>
                        <a href="{{ route('reports.customer') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($selectedCustomer)
    <!-- Customer Details Modal -->
    <div class="modal fade" id="customerDetailsModal" tabindex="-1" role="dialog" aria-labelledby="customerDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white py-2 px-3">
                    <h5 class="modal-title font-weight-bold" id="customerDetailsModalLabel" style="font-size:15px;">
                        <i class="fas fa-chart-line mr-2"></i>Customer Analysis: {{ $selectedCustomer->name }}
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="outline:none;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light" style="max-height:85vh; overflow-y:auto; padding:20px;">

    {{-- ════════════════════════════════════════════════════════ --}}
    {{-- CUSTOMER PROFILE CARD                                    --}}
    {{-- ════════════════════════════════════════════════════════ --}}
    <div class="card shadow mb-4 border-left-primary">
        <div class="card-body py-3">
            <div class="row align-items-center">
                {{-- Avatar / initials --}}
                <div class="col-auto">
                    <div class="customer-avatar">
                        {{ strtoupper(substr($selectedCustomer->name, 0, 1)) }}
                    </div>
                </div>
                {{-- Info --}}
                <div class="col">
                    <div class="h5 font-weight-bold mb-0 text-gray-800">
                        {{ $selectedCustomer->name }}
                        @if($selectedCustomer->business_name)
                            <small class="text-muted font-weight-normal"> — {{ $selectedCustomer->business_name }}</small>
                        @endif
                    </div>
                    <div class="text-xs text-muted mt-1">
                        @if($selectedCustomer->phone)
                            <span class="mr-3"><i class="fas fa-phone fa-xs mr-1"></i>{{ $selectedCustomer->phone }}</span>
                        @endif
                        @if($selectedCustomer->city)
                            <span class="mr-3"><i class="fas fa-map-marker-alt fa-xs mr-1"></i>{{ $selectedCustomer->city }}</span>
                        @endif
                        @if($selectedCustomer->customer_type)
                            <span class="badge badge-info" style="font-size:10px;text-transform:capitalize;">{{ $selectedCustomer->customer_type }}</span>
                        @endif
                        @if($lifetimeStats['customer_since'])
                            <span class="ml-2"><i class="fas fa-calendar-check fa-xs mr-1 text-success"></i>
                                Customer since {{ \Carbon\Carbon::parse($lifetimeStats['customer_since'])->format('M Y') }}
                            </span>
                        @endif
                    </div>
                </div>
                {{-- Balance badge --}}
                <div class="col-auto text-right">
                    <div class="small font-weight-bold text-muted text-uppercase mb-1">Current Balance Due</div>
                    <div class="h4 font-weight-extrabold mb-0 {{ $lifetimeStats['outstanding'] > 0 ? 'text-danger' : 'text-success' }}">
                        Rs. {{ number_format($lifetimeStats['outstanding'], 0) }}
                    </div>
                    <div class="text-xs text-muted">{{ $lifetimeStats['outstanding'] > 0 ? 'Outstanding' : 'Cleared' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════ --}}
    {{-- CUSTOMER HEALTH SCORECARD                                --}}
    {{-- ════════════════════════════════════════════════════════ --}}
    @if($healthScorecard)
    @php
        $hs    = $healthScorecard;
        $hc    = $hs['health_color'];
        $stars = $hs['star_rating'];
        $trendIcons = ['up' => '▲', 'down' => '▼', 'stable' => '●', 'new' => '★'];
        $trendColors= ['up' => '#1cc88a', 'down' => '#e74a3b', 'stable' => '#858796', 'new' => '#4e73df'];
    @endphp
    <div class="card shadow mb-4" style="border:2px solid {{ $hc }}; border-radius:10px;">
        <div class="card-body py-3">
            {{-- Header row --}}
            <div class="d-flex align-items-center justify-content-between flex-wrap mb-3">
                <div class="d-flex align-items-center">
                    <div class="health-ring mr-3" style="border-color:{{ $hc }};">
                        <span style="color:{{ $hc }};font-size:22px;font-weight:800;">{{ $hs['recovery_rate'] }}%</span>
                        <span style="font-size:9px;color:#888;display:block;margin-top:-2px;">Recovery</span>
                    </div>
                    <div>
                        <div class="h5 font-weight-bold mb-0" style="color:{{ $hc }};">
                            {{ $hs['health_label'] }} Customer
                        </div>
                        <div class="stars-row mt-1">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star" style="color:{{ $i <= $stars ? $hc : '#d1d3e2' }};font-size:18px;"></i>
                            @endfor
                            <span class="ml-2 text-muted small">({{ $stars }}/5 stars)</span>
                        </div>
                    </div>
                </div>
                {{-- Star Legend --}}
                <div class="d-none d-md-flex flex-column text-right">
                    <div class="small font-weight-bold text-muted mb-1">RATING GUIDE</div>
                    <div style="font-size:10px;line-height:1.7;">
                        <span style="color:#1cc88a;">★★★★★</span> Excellent &nbsp;
                        <span style="color:#36b9cc;">★★★★</span> Good &nbsp;
                        <span style="color:#f6c23e;">★★★</span> Average &nbsp;
                        <span style="color:#fd7e14;">★★</span> Watch Out &nbsp;
                        <span style="color:#e74a3b;">★</span> Risky
                    </div>
                </div>
            </div>

            {{-- Metric tiles --}}
            <div class="row no-gutters" style="gap:0;">

                {{-- Recovery Rate --}}
                <div class="col-6 col-md-3 p-2">
                    <div class="scorecard-tile">
                        <div class="scorecard-tile-label">Recovery Rate</div>
                        <div class="scorecard-tile-value" style="color:{{ $hc }};">{{ $hs['recovery_rate'] }}%</div>
                        <div class="scorecard-tile-sub">of total sales collected</div>
                        <div class="progress mt-2" style="height:6px;border-radius:3px;">
                            <div class="progress-bar" style="width:{{ min($hs['recovery_rate'],100) }}%;background:{{ $hc }};border-radius:3px;"></div>
                        </div>
                        <div class="scorecard-stars mt-1">
                            @for($i=1;$i<=5;$i++)<i class="fas fa-star" style="color:{{ $i<=$hs['score_breakdown']['recovery'] ? $hc : '#d1d3e2' }};font-size:9px;"></i>@endfor
                        </div>
                    </div>
                </div>

                {{-- Avg Recovery Days --}}
                <div class="col-6 col-md-3 p-2">
                    <div class="scorecard-tile">
                        <div class="scorecard-tile-label">Avg Payment Speed</div>
                        @if($hs['avg_recovery_days'] !== null)
                            <div class="scorecard-tile-value" style="color:{{ $hs['avg_recovery_days'] <= 30 ? '#1cc88a' : ($hs['avg_recovery_days'] <= 60 ? '#f6c23e' : '#e74a3b') }};">
                                {{ $hs['avg_recovery_days'] }} days
                            </div>
                            <div class="scorecard-tile-sub">avg time to pay after order</div>
                            <div class="mt-1" style="font-size:10px;color:#888;">
                                @if($hs['avg_recovery_days'] <= 15) ⚡ Pays very fast
                                @elseif($hs['avg_recovery_days'] <= 30) ✅ Pays promptly
                                @elseif($hs['avg_recovery_days'] <= 60) ⏳ Takes a month
                                @elseif($hs['avg_recovery_days'] <= 90) ⚠️ Slow payer
                                @else 🔴 Very slow payer
                                @endif
                            </div>
                        @else
                            <div class="scorecard-tile-value text-muted">—</div>
                            <div class="scorecard-tile-sub">no payment data yet</div>
                        @endif
                        <div class="scorecard-stars mt-1">
                            @for($i=1;$i<=5;$i++)<i class="fas fa-star" style="color:{{ $i<=$hs['score_breakdown']['speed'] ? $hc : '#d1d3e2' }};font-size:9px;"></i>@endfor
                        </div>
                    </div>
                </div>

                {{-- Business Trend --}}
                <div class="col-6 col-md-3 p-2">
                    <div class="scorecard-tile">
                        <div class="scorecard-tile-label">Business Trend</div>
                        <div class="scorecard-tile-value" style="color:{{ $trendColors[$hs['trend_dir']] }};">
                            {{ $trendIcons[$hs['trend_dir']] }}
                            @if($hs['trend_dir'] === 'new') New
                            @elseif($hs['trend_dir'] === 'stable') Stable
                            @elseif($hs['trend_pct'] > 0) +{{ $hs['trend_pct'] }}%
                            @else {{ $hs['trend_pct'] }}%
                            @endif
                        </div>
                        <div class="scorecard-tile-sub">vs previous same period</div>
                        <div class="mt-1" style="font-size:10px;color:#888;">
                            @if($hs['trend_dir'] === 'up') 📈 Growing — buying more
                            @elseif($hs['trend_dir'] === 'down') 📉 Declining — buying less
                            @elseif($hs['trend_dir'] === 'new') 🆕 First purchase this period
                            @else ➡️ Consistent buyer
                            @endif
                        </div>
                        @if($hs['prev_period_sales'] > 0)
                        <div style="font-size:9px;color:#aaa;margin-top:3px;">
                            Prev: Rs. {{ number_format($hs['prev_period_sales'],0) }}
                        </div>
                        @endif
                        <div class="scorecard-stars mt-1">
                            @for($i=1;$i<=5;$i++)<i class="fas fa-star" style="color:{{ $i<=$hs['score_breakdown']['trend'] ? $hc : '#d1d3e2' }};font-size:9px;"></i>@endfor
                        </div>
                    </div>
                </div>

                {{-- Activity --}}
                <div class="col-6 col-md-3 p-2">
                    <div class="scorecard-tile">
                        <div class="scorecard-tile-label">Last Activity</div>
                        @if($hs['days_since_last'] !== null)
                            <div class="scorecard-tile-value" style="color:{{ $hs['days_since_last'] <= 30 ? '#1cc88a' : ($hs['days_since_last'] <= 90 ? '#f6c23e' : '#e74a3b') }};">
                                {{ $hs['days_since_last'] }} days ago
                            </div>
                            <div class="scorecard-tile-sub">since last order</div>
                            <div class="mt-1" style="font-size:10px;color:#888;">
                                @if($hs['days_since_last'] <= 30) 🟢 Active customer
                                @elseif($hs['days_since_last'] <= 60) 🟡 Recently active
                                @elseif($hs['days_since_last'] <= 90) 🟠 Getting inactive
                                @elseif($hs['days_since_last'] <= 180) 🔴 Inactive — follow up!
                                @else ⚫ Dormant — may have left
                                @endif
                            </div>
                        @else
                            <div class="scorecard-tile-value text-muted">—</div>
                            <div class="scorecard-tile-sub">no orders on record</div>
                        @endif
                        <div class="scorecard-stars mt-1">
                            @for($i=1;$i<=5;$i++)<i class="fas fa-star" style="color:{{ $i<=$hs['score_breakdown']['activity'] ? $hc : '#d1d3e2' }};font-size:9px;"></i>@endfor
                        </div>
                    </div>
                </div>

            </div>{{-- end row --}}

            {{-- Score Formula explanation --}}
            <div class="mt-2 pt-2 border-top text-center">
                <span class="text-muted" style="font-size:10px;">
                    <i class="fas fa-info-circle mr-1"></i>
                    Star rating = Recovery Rate (40%) + Payment Speed (30%) + Business Trend (15%) + Activity (15%)
                </span>
            </div>
        </div>
    </div>
    @endif

    {{-- ════════════════════════════════════════════════════════ --}}
    {{-- KPI ROW 1 — LIFETIME                                     --}}

    {{-- ════════════════════════════════════════════════════════ --}}
    <div class="row mb-1">
        <div class="col-12 mb-2">
            <span class="badge badge-dark px-3 py-1" style="font-size:11px;letter-spacing:1px;">
                <i class="fas fa-infinity mr-1"></i> LIFETIME (ALL TIME)
            </span>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body py-2 px-3">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Sales</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{ number_format($lifetimeStats['total_sales'], 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body py-2 px-3">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Paid</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{ number_format($lifetimeStats['total_paid'], 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body py-2 px-3">
                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Outstanding</div>
                    <div class="h5 mb-0 font-weight-bold {{ $lifetimeStats['outstanding'] > 0 ? 'text-danger' : 'text-success' }}">
                        Rs. {{ number_format(abs($lifetimeStats['outstanding']), 0) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body py-2 px-3">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Orders</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $lifetimeStats['orders_count'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body py-2 px-3">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Returns Value</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{ number_format($lifetimeStats['returns_total'], 0) }}</div>
                </div>
            </div>
        </div>
        @php
            $recoveryRate = $lifetimeStats['total_sales'] > 0
                ? round(($lifetimeStats['total_paid'] / $lifetimeStats['total_sales']) * 100, 1)
                : 0;
        @endphp
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body py-2 px-3">
                    <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Recovery Rate</div>
                    <div class="h5 mb-0 font-weight-bold {{ $recoveryRate >= 80 ? 'text-success' : ($recoveryRate >= 50 ? 'text-warning' : 'text-danger') }}">
                        {{ $recoveryRate }}%
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════ --}}
    {{-- KPI ROW 2 — PERIOD                                       --}}
    {{-- ════════════════════════════════════════════════════════ --}}
    <div class="row mb-1">
        <div class="col-12 mb-2">
            <span class="badge badge-primary px-3 py-1" style="font-size:11px;letter-spacing:1px;">
                <i class="fas fa-calendar-alt mr-1"></i>
                PERIOD: {{ $startDate->format('d M Y') }} — {{ $endDate->format('d M Y') }}
            </span>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 col-6 mb-3">
            <div class="card shadow h-100 py-2" style="border-left:4px solid #4e73df;">
                <div class="card-body py-2 px-3">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Sales (Period)</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{ number_format($periodStats['total_sales'], 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-6 mb-3">
            <div class="card shadow h-100 py-2" style="border-left:4px solid #1cc88a;">
                <div class="card-body py-2 px-3">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Paid (Period)</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{ number_format($periodStats['total_paid'], 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-6 mb-3">
            <div class="card shadow h-100 py-2" style="border-left:4px solid #36b9cc;">
                <div class="card-body py-2 px-3">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Orders (Period)</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $periodStats['orders_count'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-6 mb-3">
            <div class="card shadow h-100 py-2" style="border-left:4px solid #f6c23e;">
                <div class="card-body py-2 px-3">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Avg Order Value</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{ number_format($periodStats['avg_order'], 0) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════ --}}
    {{-- ORDER HISTORY TABLE (with expandable rows)               --}}
    {{-- ════════════════════════════════════════════════════════ --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center justify-content-between bg-white">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-shopping-bag mr-2"></i>Order History — {{ $startDate->format('d M Y') }} to {{ $endDate->format('d M Y') }}
            </h6>
            <span class="badge badge-primary">{{ $orders->count() }} orders</span>
        </div>
        <div class="card-body p-0">
            @if($orders->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0" id="orderHistoryTable">
                    <thead class="thead-light">
                        <tr>
                            <th style="width:30px;"></th>
                            <th>Date</th>
                            <th>Order #</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th class="text-right">Total</th>
                            <th class="text-right">Paid</th>
                            <th class="text-right">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        @php
                            $orderPaid  = $order->payment_status == 'paid' ? $order->total_amount : ($order->amount_paid ?? 0);
                            $orderDue   = $order->total_amount - $orderPaid;
                            $statusColors = [
                                'delivered'  => 'success',
                                'processing' => 'info',
                                'pending'    => 'warning',
                                'new'        => 'secondary',
                                'cancelled'  => 'danger',
                            ];
                            $sc = $statusColors[$order->status] ?? 'secondary';
                        @endphp
                        {{-- MAIN ROW --}}
                        <tr class="order-main-row {{ $order->status == 'cancelled' ? 'text-muted' : '' }}"
                            data-order-id="{{ $order->id }}"
                            style="cursor:pointer;">
                            <td class="text-center expand-toggle">
                                <i class="fas fa-chevron-right text-muted" style="font-size:10px;transition:transform .2s;"></i>
                            </td>
                            <td>{{ $order->created_at->format('d M Y') }}</td>
                            <td>
                                <span class="font-weight-bold">{{ $order->order_number }}</span>
                                @if($order->staff)
                                    <br><small class="text-muted">by {{ $order->staff->name }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $sc }}">{{ ucfirst($order->status) }}</span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $order->payment_status == 'paid' ? 'success' : 'danger' }}">
                                    {{ ucfirst($order->payment_status ?? 'unpaid') }}
                                </span>
                            </td>
                            <td class="text-right font-weight-bold">Rs. {{ number_format($order->total_amount, 0) }}</td>
                            <td class="text-right text-success">Rs. {{ number_format($orderPaid, 0) }}</td>
                            <td class="text-right {{ $orderDue > 0 ? 'text-danger font-weight-bold' : 'text-success' }}">
                                Rs. {{ number_format($orderDue, 0) }}
                            </td>
                        </tr>
                        {{-- EXPANDED ITEMS ROW --}}
                        <tr class="order-items-row d-none" id="items-{{ $order->id }}">
                            <td colspan="8" class="p-0">
                                <div class="bg-light px-4 py-3 border-top">
                                    @if($order->cart_info && $order->cart_info->count() > 0)
                                    <table class="table table-sm mb-0" style="font-size:12px;">
                                        <thead>
                                            <tr class="text-muted">
                                                <th>Product</th>
                                                <th class="text-center">Qty</th>
                                                <th class="text-right">Unit Price</th>
                                                <th class="text-right">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($order->cart_info as $item)
                                            <tr>
                                                <td>
                                                    @if($item->product)
                                                        {{ $item->product->title }}
                                                        @if($item->product->sku)
                                                            <span class="text-muted">({{ $item->product->sku }})</span>
                                                        @endif
                                                    @else
                                                        {{ $item->product_id ? 'Product #'.$item->product_id : 'Bundle/Other' }}
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $item->quantity }}
                                                    @if($item->product && $item->product->unit)
                                                        <small class="text-muted">{{ $item->product->unit }}</small>
                                                    @endif
                                                </td>
                                                <td class="text-right">Rs. {{ $item->quantity > 0 ? number_format($item->amount / $item->quantity, 0) : 0 }}</td>
                                                <td class="text-right font-weight-bold">Rs. {{ number_format($item->amount, 0) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="font-weight-bold">
                                                <td colspan="3" class="text-right">Order Total:</td>
                                                <td class="text-right">Rs. {{ number_format($order->total_amount, 0) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    @else
                                        <p class="text-muted small mb-0"><i class="fas fa-info-circle mr-1"></i>No item details available.</p>
                                    @endif
                                    @if($order->pending_items_note)
                                        <div class="alert alert-warning py-1 px-2 mt-2 mb-0 small">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>{{ $order->pending_items_note }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="thead-light">
                        <tr>
                            <th colspan="5" class="text-right">Period Total:</th>
                            <th class="text-right">Rs. {{ number_format($periodStats['total_sales'], 0) }}</th>
                            <th class="text-right text-success">Rs. {{ number_format($periodStats['total_paid'], 0) }}</th>
                            <th class="text-right text-danger">Rs. {{ number_format($periodStats['total_sales'] - $periodStats['total_paid'], 0) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @else
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                    No orders found in the selected date range.
                </div>
            @endif
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════ --}}
    {{-- LEDGER HISTORY (collapsible)                             --}}
    {{-- ════════════════════════════════════════════════════════ --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center justify-content-between bg-white"
             data-toggle="collapse" data-target="#ledgerSection" style="cursor:pointer;">
            <h6 class="m-0 font-weight-bold text-success">
                <i class="fas fa-book mr-2"></i>Payment / Ledger History (All Time)
            </h6>
            <div class="d-flex align-items-center">
                <span class="badge badge-success mr-2">{{ $ledger->count() }} entries</span>
                <i class="fas fa-chevron-down text-muted" style="font-size:12px;"></i>
            </div>
        </div>
        <div class="collapse" id="ledgerSection">
            <div class="card-body p-0">
                @if($ledger->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th class="text-right">Amount</th>
                                <th class="text-right">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ledger as $entry)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($entry->transaction_date)->format('d M Y') }}</td>
                                <td>
                                    <span class="badge badge-{{ $entry->type == 'credit' ? 'success' : 'danger' }}">
                                        {{ ucfirst($entry->type) }}
                                    </span>
                                </td>
                                <td><span class="text-capitalize text-muted small">{{ $entry->category }}</span></td>
                                <td class="small">{{ $entry->description }}</td>
                                <td class="text-right {{ $entry->type == 'credit' ? 'text-success' : 'text-danger' }} font-weight-bold">
                                    {{ $entry->type == 'credit' ? '-' : '+' }} Rs. {{ number_format($entry->amount, 0) }}
                                </td>
                                <td class="text-right font-weight-bold {{ $entry->balance > 0 ? 'text-danger' : 'text-success' }}">
                                    Rs. {{ number_format($entry->balance, 0) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                    <div class="text-center py-3 text-muted">
                        <i class="fas fa-inbox mr-1"></i> No ledger entries found.
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════ --}}
    {{-- RETURNS HISTORY (collapsible)                            --}}
    {{-- ════════════════════════════════════════════════════════ --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center justify-content-between bg-white"
             data-toggle="collapse" data-target="#returnsSection" style="cursor:pointer;">
            <h6 class="m-0 font-weight-bold text-warning">
                <i class="fas fa-undo-alt mr-2"></i>Returns History (All Time)
            </h6>
            <div class="d-flex align-items-center">
                <span class="badge badge-warning mr-2">{{ $returns->count() }} returns</span>
                <i class="fas fa-chevron-down text-muted" style="font-size:12px;"></i>
            </div>
        </div>
        <div class="collapse" id="returnsSection">
            <div class="card-body p-0">
                @if($returns->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Date</th>
                                <th>Return #</th>
                                <th>Items</th>
                                <th>Reason</th>
                                <th>Refund Method</th>
                                <th class="text-right">Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($returns as $ret)
                            <tr>
                                <td>{{ $ret->return_date->format('d M Y') }}</td>
                                <td class="font-weight-bold">{{ $ret->return_number }}</td>
                                <td>
                                    @foreach($ret->items as $ri)
                                        <div class="small">{{ $ri->product->title ?? 'Item #'.$ri->id }}
                                            <span class="text-muted">×{{ $ri->quantity }}</span>
                                        </div>
                                    @endforeach
                                </td>
                                <td class="small text-muted">{{ $ret->reason ?? '—' }}</td>
                                <td class="small">{{ $ret->refund_method ?? '—' }}</td>
                                <td class="text-right font-weight-bold text-warning">Rs. {{ number_format($ret->total_return_amount, 0) }}</td>
                                <td>
                                    <span class="badge badge-{{ $ret->status == 'completed' ? 'success' : 'warning' }}">
                                        {{ ucfirst($ret->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="thead-light">
                            <tr>
                                <th colspan="5" class="text-right">Total Returned:</th>
                                <th class="text-right text-warning">Rs. {{ number_format($lifetimeStats['returns_total'], 0) }}</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                    <div class="text-center py-3 text-muted">
                        <i class="fas fa-check-circle mr-1 text-success"></i> No returns on record for this customer.
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════ --}}
    {{-- TOP PRODUCTS (collapsible)                               --}}
    {{-- ════════════════════════════════════════════════════════ --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center justify-content-between bg-white"
             data-toggle="collapse" data-target="#topProductsSection" style="cursor:pointer;">
            <h6 class="m-0 font-weight-bold text-info">
                <i class="fas fa-star mr-2"></i>Top Products Purchased (All Time)
            </h6>
            <div class="d-flex align-items-center">
                <span class="badge badge-info mr-2">Top {{ $topProducts->count() }}</span>
                <i class="fas fa-chevron-down text-muted" style="font-size:12px;"></i>
            </div>
        </div>
        <div class="collapse" id="topProductsSection">
            <div class="card-body p-0">
                @if($topProducts->count() > 0)
                @php $maxVal = $topProducts->max('total_value'); @endphp
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th class="text-center">Orders</th>
                                <th class="text-center">Total Qty</th>
                                <th class="text-right">Total Value</th>
                                <th style="width:120px;">Share</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topProducts as $i => $tp)
                            @php $barPct = $maxVal > 0 ? round(($tp->total_value / $maxVal) * 100) : 0; @endphp
                            <tr>
                                <td class="font-weight-bold text-muted">{{ $i + 1 }}</td>
                                <td class="font-weight-bold">{{ $tp->title }}</td>
                                <td class="text-center">{{ $tp->times_ordered }}</td>
                                <td class="text-center">{{ number_format($tp->total_qty, 0) }}
                                    @if($tp->unit)<small class="text-muted">{{ $tp->unit }}</small>@endif
                                </td>
                                <td class="text-right font-weight-bold">Rs. {{ number_format($tp->total_value, 0) }}</td>
                                <td>
                                    <div class="progress" style="height:8px;">
                                        <div class="progress-bar bg-info" role="progressbar"
                                             style="width:{{ $barPct }}%"></div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                    <div class="text-center py-3 text-muted">
                        <i class="fas fa-inbox mr-1"></i> No product purchase data available.
                    </div>
                @endif
            </div>
        </div>
    </div>
                </div> {{-- Closing modal-body --}}
                <div class="modal-footer py-2 bg-white">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @else
    {{-- No customer selected yet --}}
    <div class="card shadow mb-4">
        <div class="card-body text-center py-5">
            <i class="fas fa-user-circle fa-4x text-gray-300 mb-3 d-block"></i>
            <h5 class="text-gray-500">Select a customer above to generate their full report.</h5>
            <p class="text-muted small">Shows lifetime & period stats, order history, payments, returns, and top products.</p>
        </div>
    </div>
    @endif

</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
<style>
    /* ── Customer Avatar ─────────────────── */
    .customer-avatar {
        width: 56px; height: 56px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4e73df, #36b9cc);
        color: #fff;
        font-size: 24px;
        font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 2px 8px rgba(78,115,223,.3);
    }
    /* ── Health Ring ─────────────────────── */
    .health-ring {
        width: 80px; height: 80px;
        border-radius: 50%;
        border: 4px solid #1cc88a;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        background: #fff;
        box-shadow: 0 2px 12px rgba(0,0,0,.08);
        flex-shrink: 0;
    }
    /* ── Scorecard Tiles ─────────────────── */
    .scorecard-tile {
        background: #f8f9fc;
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        padding: 10px 12px;
        height: 100%;
    }
    .scorecard-tile-label {
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .8px;
        color: #858796;
        margin-bottom: 4px;
    }
    .scorecard-tile-value {
        font-size: 22px;
        font-weight: 800;
        line-height: 1.1;
        color: #2c3e50;
    }
    .scorecard-tile-sub {
        font-size: 10px;
        color: #aaa;
        margin-top: 2px;
    }
    .scorecard-stars { line-height: 1; }

    /* ── Expand row ──────────────────────── */
    .order-main-row:hover { background: #f8f9fc !important; }
    .order-main-row.expanded .expand-toggle i { transform: rotate(90deg); }
    .order-items-row td { border-top: none !important; }
    /* ── Collapsible chevron rotate ──────── */
    [data-toggle="collapse"] .fa-chevron-down { transition: transform .2s; }
    [data-toggle="collapse"][aria-expanded="true"] .fa-chevron-down { transform: rotate(180deg); }
    /* ── Gap utility (BS4 doesn't have it) ── */
    .gap-2 { gap: .5rem; }

    /* ── Mobile Optimization Queries ──────── */
    @media (max-width: 576px) {
        #customerDetailsModal .modal-body {
            padding: 10px !important;
        }
        #customerDetailsModal .card-body {
            padding: 12px 10px !important;
        }
        .scorecard-tile {
            padding: 8px 8px !important;
        }
        .scorecard-tile-value {
            font-size: 16px !important;
        }
        .scorecard-tile-label {
            font-size: 8px !important;
            letter-spacing: 0.2px !important;
        }
        .scorecard-tile-sub {
            font-size: 8px !important;
        }
        .customer-avatar {
            width: 44px !important;
            height: 44px !important;
            font-size: 20px !important;
            line-height: 44px !important;
        }
        #customerFilterForm .col-md-5, 
        #customerFilterForm .col-md-2, 
        #customerFilterForm .col-md-3 {
            margin-bottom: 12px !important;
        }
    }

    /* ══════════════════════════════════════ */
    /* PRINT STYLES                           */
    /* ══════════════════════════════════════ */
    @media print {
        .no-print, .navbar-nav, .sticky-footer, form, .btn,
        #sidebarToggle, #sidebarToggleTop, .sidebar,
        .topbar, .d-sm-flex.align-items-center.justify-content-between .d-flex { display: none !important; }
        .content-wrapper, #content-wrapper { margin-left: 0 !important; }
        .card { box-shadow: none !important; border: 1px solid #dee2e6 !important; page-break-inside: avoid; }
        .collapse { display: block !important; }
        body { background: #fff !important; }
        .container-fluid { padding: 0 !important; }
        a[href]:after { content: '' !important; }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function () {
    // ── Select2 ─────────────────────────────────────────────────
    $('.select2-customer').select2({
        theme: 'bootstrap4',
        placeholder: 'Search customer by name or phone...',
        allowClear: true,
        width: '100%'
    });

    // ── Leaderboard Search, City & Advanced Range Filters ────────
    function filterLeaderboard() {
        var query       = $('#leaderboardSearch').val().toLowerCase().trim();
        var selectedCity = $('#leaderboardCityFilter').val() || '';

        // Advanced range filters
        var salesMin = $('#filterSalesMin').val() !== '' ? parseFloat($('#filterSalesMin').val()) : null;
        var salesMax = $('#filterSalesMax').val() !== '' ? parseFloat($('#filterSalesMax').val()) : null;
        var osMin    = $('#filterOsMin').val()    !== '' ? parseFloat($('#filterOsMin').val())    : null;
        var osMax    = $('#filterOsMax').val()    !== '' ? parseFloat($('#filterOsMax').val())    : null;
        var daysMin  = $('#filterDaysMin').val()  !== '' ? parseFloat($('#filterDaysMin').val())  : null;
        var daysMax  = $('#filterDaysMax').val()  !== '' ? parseFloat($('#filterDaysMax').val())  : null;

        var rows = $('.leaderboard-row');
        var visibleCount = 0;

        // Count active advanced filters for badge
        var activeCount = [salesMin, salesMax, osMin, osMax, daysMin, daysMax].filter(function(v){ return v !== null; }).length;
        if (activeCount > 0) {
            $('#advFiltersCount').text(activeCount).show();
            $('#advFiltersToggle').addClass('btn-primary').removeClass('btn-outline-secondary');
        } else {
            $('#advFiltersCount').hide();
            $('#advFiltersToggle').addClass('btn-outline-secondary').removeClass('btn-primary');
        }

        rows.each(function () {
            var searchData  = $(this).attr('data-search') || '';
            var rowCity     = $(this).attr('data-city')   || '';
            var totalSales  = parseFloat($(this).attr('data-total-sales') || 0);
            var outstanding = parseFloat($(this).attr('data-outstanding')  || 0);
            var daysRaw     = $(this).attr('data-days');
            var days        = daysRaw !== '' && daysRaw !== undefined ? parseFloat(daysRaw) : null;

            var matchesQuery = !query || searchData.indexOf(query) > -1;
            var matchesCity  = !selectedCity || rowCity === selectedCity;

            // Sales range
            var matchesSales = true;
            if (salesMin !== null && totalSales < salesMin) matchesSales = false;
            if (salesMax !== null && totalSales > salesMax) matchesSales = false;

            // Outstanding range
            var matchesOs = true;
            if (osMin !== null && outstanding < osMin) matchesOs = false;
            if (osMax !== null && outstanding > osMax) matchesOs = false;

            // Last Order days range
            var matchesDays = true;
            if (daysMin !== null || daysMax !== null) {
                if (days === null) {
                    matchesDays = false; // no order on record — exclude from days filter
                } else {
                    if (daysMin !== null && days < daysMin) matchesDays = false;
                    if (daysMax !== null && days > daysMax) matchesDays = false;
                }
            }

            if (matchesQuery && matchesCity && matchesSales && matchesOs && matchesDays) {
                $(this).removeClass('d-none');
                visibleCount++;
            } else {
                $(this).addClass('d-none');
            }
        });

        $('#leaderboardSearchCount').text('Showing ' + visibleCount + ' of ' + rows.length);
    }

    $('#leaderboardSearch').on('input', filterLeaderboard);
    $('#leaderboardCityFilter').on('change', filterLeaderboard);
    $('#filterSalesMin, #filterSalesMax, #filterOsMin, #filterOsMax, #filterDaysMin, #filterDaysMax').on('input', filterLeaderboard);

    // Toggle Advanced Filters Panel
    $('#advFiltersToggle').on('click', function() {
        $('#advFiltersPanel').slideToggle(200);
    });

    // Clear Advanced Filters
    $('#clearAdvFilters').on('click', function() {
        $('#filterSalesMin, #filterSalesMax, #filterOsMin, #filterOsMax, #filterDaysMin, #filterDaysMax').val('');
        filterLeaderboard();
    });

    // ── Expandable order rows ────────────────────────────────────
    $(document).on('click', '.order-main-row', function () {
        var orderId  = $(this).data('order-id');
        var itemsRow = $('#items-' + orderId);
        var icon     = $(this).find('.expand-toggle i');

        if (itemsRow.hasClass('d-none')) {
            itemsRow.removeClass('d-none');
            $(this).addClass('expanded');
            icon.css('transform', 'rotate(90deg)');
        } else {
            itemsRow.addClass('d-none');
            $(this).removeClass('expanded');
            icon.css('transform', 'rotate(0deg)');
        }
    });

    // ── Collapsible section chevrons ─────────────────────────────
    $('#ledgerSection, #returnsSection, #topProductsSection').on('show.bs.collapse', function () {
        $(this).prev('.card-header').find('.fa-chevron-down').css('transform', 'rotate(180deg)');
    }).on('hide.bs.collapse', function () {
        $(this).prev('.card-header').find('.fa-chevron-down').css('transform', 'rotate(0deg)');
    });

    @if($selectedCustomer)
    // ── Auto-open Customer Details Modal ─────────────────────────
    $('#customerDetailsModal').modal('show');
    @endif
});
</script>
@endpush
