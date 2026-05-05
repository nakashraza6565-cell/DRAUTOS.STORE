@extends('backend.layouts.master')
@section('title','Danyal Autos || PREMIUM DASHBOARD')
@section('main-content')

<div class="container-fluid premium-bg" style="min-height: 100vh; padding: 1rem;">
    @include('backend.layouts.notification')
    
    <!-- Header Section: Welcome, Clock, and Staff Attendance -->
    <div class="row mb-4 align-items-center">
        <div class="col-lg-6 mb-3 mb-lg-0 text-center text-lg-left">
            <h1 class="font-weight-bolder text-gray-900 mb-1 d-none d-md-block" style="font-size: 2.2rem; letter-spacing: -0.5px;">
                Good {{ (date('H') < 12) ? 'Morning' : ((date('H') < 17) ? 'Afternoon' : 'Evening') }}, {{ Auth::user()->name ?? 'Admin' }}! 👋
            </h1>
            <h1 class="font-weight-bolder text-gray-900 mb-1 d-md-none" style="font-size: 1.5rem; letter-spacing: -0.5px;">
                Hello, {{ Auth::user()->name ?? 'Admin' }}! 👋
            </h1>
            <p class="text-muted mb-0" style="font-size: 0.95rem;">Here is what's happening today.</p>
        </div>
        <div class="col-lg-6 d-flex flex-column flex-md-row justify-content-lg-end gap-3 align-items-center mt-3 mt-lg-0">
            <!-- Staff Attendance Glass Card -->
            <a href="javascript:void(0)" data-toggle="modal" data-target="#quickAttendanceModal" class="text-decoration-none w-100 w-md-auto mb-2 mb-md-0">
                <div class="glass-card px-3 py-2 mr-0 mr-md-3 d-flex align-items-center shadow-sm justify-content-center" style="cursor: pointer; transition: transform 0.2s;">
                    <div class="mr-3">
                        <i class="fas fa-users-viewfinder fa-2x" style="color: #6366f1;"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-uppercase" style="color: #6366f1; letter-spacing: 1px;">Staff Attendance</div>
                        <div class="h5 mb-0 font-weight-bolder text-gray-800">{{ $present_staff_count }} / {{ $staff_count }} Present</div>
                    </div>
                </div>
            </a>
            
            <!-- Live Clock Glass Card -->
            <div class="glass-card px-3 py-2 d-flex align-items-center shadow-sm justify-content-center w-100 w-md-auto">
                <div>
                    <div class="text-xs font-weight-bold text-muted text-uppercase text-center text-md-left" style="letter-spacing: 1px;">{{ date('l, M d, Y') }}</div>
                    <div id="live-clock" class="h5 mb-0 font-weight-bolder text-primary text-center text-md-left">--:--:--</div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Newspaper: News Ticker Section -->
    <div class="newspaper-ticker-container mb-4 shadow-sm">
        <div class="ticker-label">
            <i class="fas fa-bolt mr-2 pulse-icon"></i> HEADLINES
        </div>
        <div class="ticker-content">
            <div class="ticker-track">
                @if(!empty($ai_headlines))
                    <div class="ticker-item">
                        <i class="fas fa-robot mr-2"></i>
                        <span class="ticker-text"><strong>AI INSIGHT:</strong> {{ $ai_headlines }}</span>
                    </div>
                    {{-- Duplicate for seamless loop --}}
                    <div class="ticker-item">
                        <i class="fas fa-robot mr-2"></i>
                        <span class="ticker-text"><strong>AI INSIGHT:</strong> {{ $ai_headlines }}</span>
                    </div>
                @else
                    @foreach($activity_logs as $log)
                        <div class="ticker-item">
                            <i class="fas {{ $log->icon }}"></i>
                            <span class="ticker-text">
                                <strong>{{ $log->action }}:</strong> {!! strip_tags($log->description) !!}
                                <span class="ticker-time">({{ $log->created_at->diffForHumans() }})</span>
                            </span>
                        </div>
                    @endforeach
                    @if($activity_logs->isEmpty())
                        <div class="ticker-item">
                            <i class="fas fa-info-circle"></i>
                            <span class="ticker-text">The newsroom is quiet... No major activities recorded in the last 24 hours.</span>
                        </div>
                    @else
                        <div class="ticker-item">
                            <a href="{{ route('admin.activity-logs') }}" class="text-white font-weight-bold" style="text-decoration: underline;">
                                <i class="fas fa-list-ul mr-1"></i> VIEW FULL HISTORY
                            </a>
                        </div>
                    @endif
                    {{-- Duplicate for loop --}}
                    @foreach($activity_logs as $log)
                        <div class="ticker-item">
                            <i class="fas {{ $log->icon }}"></i>
                            <span class="ticker-text">
                                <strong>{{ $log->action }}:</strong> {!! strip_tags($log->description) !!}
                                <span class="ticker-time">({{ $log->created_at->diffForHumans() }})</span>
                            </span>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <!-- Row 1: Core Financials (Glassmorphism & Gradients) -->
    <div class="row mb-4">
        <!-- Receivables -->
        <div class="col-6 col-xl-3 col-md-6 mb-3">
            <div class="premium-card gradient-success text-white shadow-lg h-100">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-xs font-weight-bold text-uppercase opacity-75 d-none d-md-block" style="letter-spacing: 1px;">Account Receivables</div>
                        <div class="text-xs font-weight-bold text-uppercase opacity-75 d-md-none">Receivables</div>
                        <i class="fas fa-hand-holding-dollar fa-lg opacity-50"></i>
                    </div>
                    <div class="h4 h2-md mb-0 font-weight-bolder text-truncate">Rs. {{ number_format($total_receivables) }}</div>
                    <div class="mt-2 small opacity-75 d-none d-md-block">
                        <i class="fas fa-arrow-up mr-1"></i> Money owed to you
                    </div>
                </div>
            </div>
        </div>

        <!-- Payables -->
        <div class="col-6 col-xl-3 col-md-6 mb-3">
            <div class="premium-card gradient-danger text-white shadow-lg h-100">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-xs font-weight-bold text-uppercase opacity-75 d-none d-md-block" style="letter-spacing: 1px;">Account Payables</div>
                        <div class="text-xs font-weight-bold text-uppercase opacity-75 d-md-none">Payables</div>
                        <i class="fas fa-money-bill-transfer fa-lg opacity-50"></i>
                    </div>
                    <div class="h4 h2-md mb-0 font-weight-bolder text-truncate">Rs. {{ number_format($total_payables) }}</div>
                    <div class="mt-2 small opacity-75 d-none d-md-block">
                        <i class="fas fa-arrow-down mr-1"></i> Money you owe
                    </div>
                </div>
            </div>
        </div>

        <!-- Cash Register -->
        <div class="col-6 col-xl-3 col-md-6 mb-3">
            <div class="premium-card gradient-primary text-white shadow-lg h-100">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-xs font-weight-bold text-uppercase opacity-75 d-none d-md-block" style="letter-spacing: 1px;">Active Cash Drawer</div>
                        <div class="text-xs font-weight-bold text-uppercase opacity-75 d-md-none">Register</div>
                        <i class="fas fa-cash-register fa-lg opacity-50"></i>
                    </div>
                    <div class="h4 h2-md mb-0 font-weight-bolder text-truncate">Rs. {{ number_format($register_balance) }}</div>
                    <div class="mt-2 small font-weight-bold">
                        <span class="badge badge-light text-primary px-2 py-1">{{ $active_register ? 'OPEN' : 'CLOSED' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Value -->
        <div class="col-6 col-xl-3 col-md-6 mb-3">
            <div class="premium-card gradient-info text-white shadow-lg h-100">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-xs font-weight-bold text-uppercase opacity-75 d-none d-md-block" style="letter-spacing: 1px;">Inventory Value</div>
                        <div class="text-xs font-weight-bold text-uppercase opacity-75 d-md-none">Stock</div>
                        <i class="fas fa-boxes-stacked fa-lg opacity-50"></i>
                    </div>
                    <div class="h4 h2-md mb-0 font-weight-bolder text-truncate">Rs. {{ number_format($total_stock_value / 1000) }}k</div>
                    <div class="mt-2 small opacity-75 d-none d-md-block">
                        {{ $product_count }} Active Items
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Operations & Tasks -->
    <div class="row mb-5">
        <!-- Task Calendar (2/3 width) -->
        <div class="col-xl-8 mb-4">
            <div class="premium-panel h-100 shadow-sm">
                <div class="panel-header d-flex justify-content-between align-items-center">
                    <h5 class="m-0 font-weight-bolder text-gray-800">
                        <div class="icon-box bg-primary-light mr-3"><i class="fas fa-calendar-check text-primary"></i></div>
                        Operational Calendar
                    </h5>
                    <a href="{{ route('tasks.calendar') }}" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm font-weight-bold">Full Calendar</a>
                </div>
                <div class="panel-body p-4">
                    <div id="dashboard-calendar"></div>
                </div>
            </div>
        </div>

        <!-- Payment Reminders (1/3 width) -->
        <div class="col-xl-4 mb-4">
            <div class="premium-panel h-100 shadow-sm overflow-hidden">
                <div class="panel-header border-bottom-0 pb-0">
                    <h5 class="m-0 font-weight-bolder text-gray-800 mb-2">
                        <div class="icon-box bg-warning-light mr-3"><i class="fas fa-bell text-warning"></i></div>
                        Today's Reminders
                    </h5>
                    <p class="text-muted small mb-0 ml-5 pl-2">Critical payments due today</p>
                </div>
                <div class="panel-body p-0 mt-3">
                    <div class="reminder-list px-3 pb-3" style="max-height: 400px; overflow-y: auto;">
                        @forelse($today_reminders as $reminder)
                            <div class="reminder-item mb-3 p-3 {{ $reminder->type == 'receivable' ? 'border-success-left' : 'border-danger-left' }}">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge {{ $reminder->type == 'receivable' ? 'bg-success-light text-success' : 'bg-danger-light text-danger' }} px-2 py-1" style="border-radius: 6px;">
                                        {{ strtoupper($reminder->type == 'receivable' ? 'To Receive' : 'To Pay') }}
                                    </span>
                                    <span class="font-weight-bolder text-dark">Rs. {{ number_format($reminder->amount - $reminder->paid_amount) }}</span>
                                </div>
                                <div class="font-weight-bold text-gray-800">{{ $reminder->party->name ?? 'Unknown Party' }}</div>
                                <div class="small text-muted mt-1 text-truncate"><i class="fas fa-info-circle mr-1"></i> {{ $reminder->notes ?: 'No description' }}</div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <div class="empty-icon bg-gray-100 mb-3 mx-auto"><i class="fas fa-check-double text-gray-400 fa-2x"></i></div>
                                <h6 class="font-weight-bold text-gray-600">All clear!</h6>
                                <p class="text-muted small">No pending payments due today.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: Sales Analytics & Trending -->
    <div class="row">
        <!-- Sales Chart -->
        <div class="col-xl-8 mb-4">
            <div class="premium-panel shadow-sm">
                <div class="panel-header d-flex justify-content-between align-items-center">
                    <h5 class="m-0 font-weight-bolder text-gray-800">
                        <div class="icon-box bg-info-light mr-3"><i class="fas fa-chart-line text-info"></i></div>
                        Sales Performance (Last 7 Days)
                    </h5>
                </div>
                <div class="panel-body p-4">
                    <div class="chart-area" style="height: 350px;">
                        <canvas id="salesTrendsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trending Products -->
        <div class="col-xl-4 mb-4">
            <div class="premium-panel shadow-sm h-100">
                <div class="panel-header">
                    <h5 class="m-0 font-weight-bolder text-gray-800">
                        <div class="icon-box bg-warning-light mr-3"><i class="fas fa-fire text-warning"></i></div>
                        Top Sellers
                    </h5>
                </div>
                <div class="panel-body p-4 pt-2">
                    @foreach($best_sellers as $index => $item)
                        <div class="d-flex align-items-center mb-4 {{ $loop->last ? '' : 'border-bottom pb-3' }}">
                            <div class="rank-badge {{ $index == 0 ? 'bg-warning text-white shadow-warning' : 'bg-gray-200 text-gray-600' }} mr-3">
                                #{{ $index + 1 }}
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="font-weight-bold text-gray-800 mb-1 text-truncate" style="max-width: 200px;">{{ $item->product->title ?? 'N/A' }}</h6>
                                <div class="progress" style="height: 6px; border-radius: 3px;">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ min(100, $item->total_qty * 5) }}%"></div>
                                </div>
                            </div>
                            <div class="ml-3 text-right">
                                <div class="h5 font-weight-bolder text-dark mb-0">{{ $item->total_qty }}</div>
                                <span class="text-muted small">Sold</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</div>

  <!-- Quick Attendance Modal -->
  <div class="modal fade" id="quickAttendanceModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-primary text-white p-4">
                <div class="d-flex align-items-center">
                    <div style="background: rgba(255,255,255,0.2); width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                        <i class="fas fa-clipboard-user fa-lg text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title font-weight-bold text-white">Daily Staff Attendance</h5>
                        <p class="mb-0 small text-white-50">Mark present or check-out staff for {{ date('M d, Y') }}</p>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 responsive-table-to-cards" style="font-size: 0.95rem;">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3 border-0">STAFF MEMBER</th>
                                <th class="py-3 border-0">ROLE</th>
                                <th class="py-3 border-0 text-center">STATUS TODAY</th>
                                <th class="pr-4 py-3 border-0 text-right">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($all_staff as $member)
                                @php
                                    $record = $today_attendance->where('user_id', $member->id)->first();
                                @endphp
                            <tr style="border-bottom: 1px solid rgba(0,0,0,0.03);">
                                <td class="px-4 py-3 align-middle" data-title="Staff Member">
                                    <div class="font-weight-bold text-gray-800">{{ $member->name }}</div>
                                </td>
                                <td class="py-3 align-middle text-muted text-capitalize" data-title="Role">
                                    {{ $member->role }}
                                </td>
                                <td class="py-3 align-middle text-center" data-title="Status">
                                    @if($record)
                                        @if($record->clock_out)
                                            <span class="badge badge-secondary px-3 py-2" style="border-radius: 8px;">Checked Out</span>
                                        @else
                                            <span class="badge badge-success px-3 py-2" style="border-radius: 8px;">Present</span>
                                        @endif
                                    @else
                                        <span class="badge badge-danger px-3 py-2" style="border-radius: 8px;">Absent / Not Marked</span>
                                    @endif
                                </td>
                                <td class="pr-4 py-3 align-middle text-right" data-title="Action">
                                    @if(!$record)
                                        <form action="{{ route('attendance.checkin') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="staff_id" value="{{ $member->id }}">
                                            <button type="submit" class="btn btn-sm btn-success font-weight-bold px-3 shadow-sm w-100 w-md-auto" style="border-radius: 8px;">
                                                <i class="fas fa-check-circle mr-1"></i> Mark Present
                                            </button>
                                        </form>
                                    @elseif(!$record->clock_out)
                                        <form action="{{ route('attendance.checkout') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="staff_id" value="{{ $member->id }}">
                                            <button type="submit" class="btn btn-sm btn-warning font-weight-bold px-3 shadow-sm w-100 w-md-auto" style="border-radius: 8px;">
                                                <i class="fas fa-door-open mr-1"></i> Check Out
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-sm btn-light font-weight-bold px-3 text-muted w-100 w-md-auto" disabled style="border-radius: 8px;">
                                            Completed
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
  </div>

@endsection

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<style>
    /* Premium Dashboard Styles */
    body {
        font-family: 'Plus Jakarta Sans', sans-serif !important;
    }
    
    .premium-bg {
        background-color: #f1f5f9;
        background-image: radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%), radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%);
        background-blend-mode: overlay;
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 1rem;
    }

    .premium-card {
        border-radius: 1.25rem;
        border: none;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .premium-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
    }
    .premium-card::after {
        content: '';
        position: absolute;
        top: 0; right: 0; bottom: 0; left: 0;
        background: linear-gradient(180deg, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 100%);
        pointer-events: none;
    }

    .gradient-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .gradient-danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
    .gradient-primary { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
    .gradient-info { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); }

    .premium-panel {
        background: #ffffff;
        border-radius: 1.25rem;
        border: 1px solid rgba(226, 232, 240, 0.8);
    }
    .panel-header {
        padding: 1.5rem 1.5rem 1rem;
        border-bottom: 1px solid rgba(226, 232, 240, 0.6);
    }
    .icon-box {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 10px;
        vertical-align: middle;
    }

    .bg-primary-light { background: rgba(99, 102, 241, 0.1); }
    .bg-warning-light { background: rgba(245, 158, 11, 0.1); }
    .bg-info-light { background: rgba(14, 165, 233, 0.1); }
    .bg-success-light { background: rgba(16, 185, 129, 0.15); }
    .bg-danger-light { background: rgba(239, 68, 68, 0.15); }

    .reminder-item {
        background: #f8fafc;
        border-radius: 0.75rem;
        transition: background 0.2s;
    }
    .reminder-item:hover { background: #f1f5f9; }
    .border-success-left { border-left: 4px solid #10b981; }
    .border-danger-left { border-left: 4px solid #ef4444; }

    .empty-icon {
        width: 64px; height: 64px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
    }

    .rank-badge {
        width: 35px; height: 35px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 0.9rem;
    }
    .shadow-warning { box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3); }

    /* Calendar Overrides */
    .fc-theme-standard th { border: none !important; color: #64748b; text-transform: uppercase; font-size: 0.8rem; padding: 10px 0;}
    .fc-theme-standard td { border-color: #f1f5f9; }
    .fc-day-today { background-color: rgba(99, 102, 241, 0.05) !important; }
    .fc-button-primary { background: #6366f1 !important; border: none !important; border-radius: 8px !important; text-transform: capitalize; font-weight: 600;}
    .fc-toolbar-title { font-weight: 800 !important; color: #1e293b; font-size: 1.2rem !important;}
    .fc-event { border-radius: 4px; border: none; padding: 2px 4px; font-size: 0.75rem; font-weight: 600; cursor: pointer;}
    /* Ticker Styles */
    .newspaper-ticker-container {
        background: #1e293b;
        color: #fff;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.1);
        position: relative;
    }
    .ticker-label {
        background: #ef4444;
        color: white;
        padding: 0 1.25rem;
        height: 100%;
        display: flex;
        align-items: center;
        font-weight: 800;
        font-size: 0.75rem;
        letter-spacing: 1.5px;
        z-index: 10;
        box-shadow: 10px 0 20px rgba(0,0,0,0.4);
        white-space: nowrap;
        border-right: 1px solid rgba(255,255,255,0.1);
    }
    .ticker-content {
        flex-grow: 1;
        overflow: hidden;
        position: relative;
        display: flex;
        align-items: center;
    }
    .ticker-track {
        display: flex;
        white-space: nowrap;
        animation: ticker-scroll 80s linear infinite;
        padding-left: 20px;
    }
    .ticker-track:hover {
        animation-play-state: paused;
    }
    .ticker-item {
        display: inline-flex;
        align-items: center;
        padding-right: 60px;
        font-size: 0.9rem;
        color: #e2e8f0;
    }
    .ticker-item i {
        color: #fbbf24;
        margin-right: 10px;
        font-size: 1.1rem;
    }
    .ticker-time {
        color: #94a3b8;
        font-size: 0.8rem;
        margin-left: 8px;
        font-weight: 600;
    }
    .pulse-icon {
        animation: pulse 1.5s infinite;
    }
    @keyframes ticker-scroll {
        0% { transform: translateX(0); }
        100% { transform: translateX(-100%); }
    }
    @keyframes pulse {
        0% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.6; transform: scale(0.9); }
        100% { opacity: 1; transform: scale(1); }
    }
</style>

@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script>
    // Live Clock
    setInterval(() => {
        document.getElementById('live-clock').innerText = new Date().toLocaleTimeString('en-US', { hour12: true, hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }, 1000);

    // Calendar
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('dashboard-calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            height: 400,
            headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek' },
            events: "{{ route('tasks.calendar-events') }}",
            eventColor: '#6366f1',
            eventClick: function(info) {
                alert(info.event.title + "\n\n" + (info.event.extendedProps.description || "No details."));
            }
        });
        calendar.render();

        // Chart defaults
        Chart.defaults.global.defaultFontFamily = "'Plus Jakarta Sans', sans-serif";
        Chart.defaults.global.defaultFontColor = '#64748b';

        // Sales Line/Bar Chart (Hybrid)
        var ctxSales = document.getElementById("salesTrendsChart").getContext('2d');
        
        // Create Gradient for line chart
        var gradientFill = ctxSales.createLinearGradient(0, 0, 0, 400);
        gradientFill.addColorStop(0, "rgba(99, 102, 241, 0.4)");
        gradientFill.addColorStop(1, "rgba(99, 102, 241, 0.05)");

        new Chart(ctxSales, {
            type: 'line',
            data: {
                labels: {!! $order_labels !!},
                datasets: [{
                    label: "Revenue",
                    lineTension: 0.3,
                    backgroundColor: gradientFill,
                    borderColor: "#6366f1",
                    pointRadius: 4,
                    pointBackgroundColor: "#fff",
                    pointBorderColor: "#6366f1",
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: "#6366f1",
                    pointHoverBorderColor: "#fff",
                    pointBorderWidth: 2,
                    data: {!! $order_amounts !!},
                }]
            },
            options: {
                maintainAspectRatio: false,
                layout: { padding: { left: 10, right: 25, top: 25, bottom: 0 } },
                scales: {
                    xAxes: [{ gridLines: { display: false, drawBorder: false } }],
                    yAxes: [{
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            callback: function(value) { return 'Rs ' + Number(value).toLocaleString(); }
                        },
                        gridLines: { color: "rgba(0, 0, 0, .05)", zeroLineColor: "transparent", drawBorder: false, borderDash: [5, 5] }
                    }],
                },
                legend: { display: false },
                tooltips: {
                    backgroundColor: "#1e293b",
                    bodyFontColor: "#fff",
                    titleMarginBottom: 10,
                    titleFontColor: '#e2e8f0',
                    titleFontSize: 13,
                    borderColor: 'rgba(255,255,255,0.1)',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    callbacks: {
                        label: function(tooltipItem, chart) {
                            return 'Revenue: Rs. ' + Number(tooltipItem.yLabel).toLocaleString();
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
