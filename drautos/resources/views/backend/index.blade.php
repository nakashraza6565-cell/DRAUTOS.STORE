@extends('backend.layouts.master')
@section('title','Danyal Autos || PREMIUM DASHBOARD')
@section('main-content')

<div class="container-fluid premium-bg" style="padding: 1rem;">
    @include('backend.layouts.notification')
    
    <!-- Header Section: Welcome, Clock, and Staff Attendance -->
    <div class="row mb-4 align-items-center">
        <div class="col-lg-6 mb-3 mb-lg-0 text-center text-lg-left">
            <h1 class="font-weight-bolder text-gray-900 mb-1 d-none d-md-block" style="font-size: 2.2rem; letter-spacing: -0.5px;">
                Good {{ (date('H') < 12) ? 'Morning' : ((date('H') < 17) ? 'Afternoon' : 'Evening') }}, {{ auth()->user()->name ?? 'Admin' }}! 👋
            </h1>
            <h1 class="font-weight-bolder text-gray-900 mb-1 d-md-none" style="font-size: 1.5rem; letter-spacing: -0.5px;">
                Hello, {{ auth()->user()->name ?? 'Admin' }}! 👋
            </h1>
            <p class="text-muted mb-0" style="font-size: 0.95rem;">Here is what's happening today.</p>
        </div>
            <!-- Quick Expense Button -->
            <button data-toggle="modal" data-target="#quickExpenseModal" class="btn btn-sm rounded-pill px-3 shadow-sm font-weight-bold mr-0 mr-md-2 w-100 w-md-auto mb-2 mb-md-0" style="font-size: 11px; height: 32px; background: #083259; color: #facc15; border: 1px solid #facc15;">
                <i class="fas fa-minus-circle mr-1"></i> EXPENSE
            </button>

            <!-- Quick Bilty Button -->
            <button data-toggle="modal" data-target="#quickBiltyModal" class="btn btn-sm rounded-pill px-3 shadow-sm font-weight-bold mr-0 mr-md-2 w-100 w-md-auto mb-2 mb-md-0" style="font-size: 11px; height: 32px; background: #083259; color: #fff; border: 1px solid #fff;">
                <i class="fas fa-truck mr-1"></i> DELIVERY RECEIPT
            </button>

            <!-- Staff Attendance Glass Card -->
            <a href="javascript:void(0)" data-toggle="modal" data-target="#quickAttendanceModal" class="text-decoration-none w-100 w-md-auto mb-2 mb-md-0">
                <div class="glass-card px-3 py-2 mr-0 mr-md-3 d-flex align-items-center shadow-sm justify-content-center" style="cursor: pointer; transition: transform 0.2s;">
                    <div class="mr-3">
                        <i class="fas fa-users-viewfinder fa-2x" style="color: #083259;"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-uppercase" style="color: #083259; letter-spacing: 1px;">Staff Attendance</div>
                        <div class="h5 mb-0 font-weight-bolder" style="color: #facc15; text-shadow: 1px 1px 0px rgba(8,50,89,0.2);">{{ $present_staff_count }} / {{ $staff_count }} Present</div>
                    </div>
                </div>
            </a>
            
            <!-- Live Clock Glass Card -->
            <div class="glass-card px-3 py-2 d-flex align-items-center shadow-sm justify-content-center w-100 w-md-auto">
                <div>
                    <div class="text-xs font-weight-bold text-uppercase text-center text-md-left" style="letter-spacing: 1px; color: #083259;">{{ date('l, M d, Y') }}</div>
                    <div id="live-clock" class="h5 mb-0 font-weight-bolder text-center text-md-left" style="color: #facc15; text-shadow: 1px 1px 0px rgba(8,50,89,0.2);">--:--:--</div>
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

    <!-- Row 0: General Overview Stats -->
    <div class="row mb-4">
        <!-- Categories -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Categories</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $category_count }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-sitemap fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Products -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Products</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $product_count }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cubes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Orders -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $order_count }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                                    <span class="font-weight-bolder text-dark">Rs. {{ number_format(($reminder->amount ?? 0) - ($reminder->paid_amount ?? 0)) }}</span>
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

    <!-- Row 3: Top Customers (Revenue & Orders) -->
    <div class="row">
        <!-- Top Customers by Revenue (Bar Chart) -->
        <div class="col-xl-6 mb-4">
            <div class="premium-panel shadow-sm">
                <div class="panel-header d-flex justify-content-between align-items-center bg-light-soft">
                    <h5 class="m-0 font-weight-bolder text-gray-800">
                        <div class="icon-box bg-success-light mr-3"><i class="fas fa-crown text-success"></i></div>
                        Top 5 Customers (Revenue)
                        <span class="small text-muted ml-2">
                            (@if(request('start_date')) {{ \Carbon\Carbon::parse(request('start_date'))->format('M d') }} - {{ \Carbon\Carbon::parse(request('end_date'))->format('M d') }} @else Last 30 Days @endif)
                        </span>
                    </h5>
                </div>
                <div class="panel-body p-4">
                    <div class="chart-area" style="height: 350px;">
                        <canvas id="topRevenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Customers by Order Count (Doughnut/Bar Chart) -->
        <div class="col-xl-6 mb-4">
            <div class="premium-panel shadow-sm h-100">
                <div class="panel-header bg-light-soft">
                    <h5 class="m-0 font-weight-bolder text-gray-800">
                        <div class="icon-box bg-primary-light mr-3"><i class="fas fa-hand-holding-usd text-primary"></i></div>
                        Top 5 Customers (Recovery Amount)
                        <span class="small text-muted ml-2">
                            (@if(request('start_date')) {{ \Carbon\Carbon::parse(request('start_date'))->format('M d') }} - {{ \Carbon\Carbon::parse(request('end_date'))->format('M d') }} @else Last 30 Days @endif)
                        </span>
                    </h5>
                </div>
                <div class="panel-body p-4">
                    <div class="recovery-stats-container" style="height: 350px; overflow-y: auto; padding-right: 10px;">
                        @forelse($top_recovery_stats as $stat)
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-end mb-1">
                                    <span class="font-weight-bold text-gray-800" style="font-size: 15px;">{{ $stat['name'] }}</span>
                                    <span class="text-{{ $stat['recovery_rate'] >= 100 ? 'success' : ($stat['recovery_rate'] >= 50 ? 'warning' : 'danger') }} font-weight-bolder" style="font-size: 14px;">{{ number_format($stat['recovery_rate'], 1) }}% Recovery</span>
                                </div>
                                <div class="progress mb-2" style="height: 10px; border-radius: 5px; background-color: #e2e8f0;">
                                    <div class="progress-bar bg-{{ $stat['recovery_rate'] >= 100 ? 'success' : ($stat['recovery_rate'] >= 50 ? 'warning' : 'danger') }}" role="progressbar" style="width: {{ min(100, $stat['recovery_rate']) }}%" aria-valuenow="{{ $stat['recovery_rate'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="d-flex justify-content-between small text-muted">
                                    <span>Ordered: <strong>Rs. {{ number_format($stat['ordered_amount']) }}</strong></span>
                                    <span>Recovered: <strong>Rs. {{ number_format($stat['recovered_amount']) }}</strong></span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-chart-bar fa-2x mb-3 text-gray-300"></i>
                                <p>No recovery data for this period.</p>
                            </div>
                        @endforelse
                    </div>
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

    <!-- Row 4: Cash Flow Chart -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="premium-panel shadow-sm border-left-primary">
                <div class="panel-header d-flex justify-content-between align-items-center bg-light-soft">
                    <h5 class="m-0 font-weight-bolder text-gray-800">
                        <div class="icon-box bg-success-light mr-3"><i class="fas fa-money-bill-trend-up text-success"></i></div>
                        Money In vs Money Out (Cash Flow)
                    </h5>
                    <div class="text-right">
                        <div class="small text-muted font-weight-bold mb-1">
                            @if(request('start_date') && request('end_date'))
                                {{ \Carbon\Carbon::parse(request('start_date'))->format('M d') }} - {{ \Carbon\Carbon::parse(request('end_date'))->format('M d, Y') }}
                            @else
                                Last 7 Days Analysis
                            @endif
                        </div>
                        <form action="{{ route('admin') }}" method="GET" class="d-flex align-items-center mb-2 justify-content-end" style="gap: 5px;">
                            <input type="date" name="start_date" value="{{ request('start_date', \Carbon\Carbon::today()->subDays(6)->format('Y-m-d')) }}" class="form-control form-control-sm" style="width: auto; height: 26px; padding: 2px 6px; font-size: 11px;">
                            <span class="small text-muted">to</span>
                            <input type="date" name="end_date" value="{{ request('end_date', \Carbon\Carbon::today()->format('Y-m-d')) }}" class="form-control form-control-sm" style="width: auto; height: 26px; padding: 2px 6px; font-size: 11px;">
                            <button type="submit" class="btn btn-sm btn-primary" style="height: 26px; padding: 2px 8px; font-size: 11px;">Filter</button>
                            @if(request('start_date'))
                                <a href="{{ route('admin') }}" class="btn btn-sm btn-light" style="height: 26px; padding: 2px 8px; font-size: 11px;"><i class="fas fa-times"></i></a>
                            @endif
                        </form>
                        <div class="d-flex align-items-center" style="gap: 10px; justify-content: flex-end;">
                            <div style="background: rgba(16,185,129,0.1); border-radius: 8px; padding: 4px 10px; border: 1px solid rgba(16,185,129,0.3);">
                                <span style="font-size: 10px; color: #059669; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">&#9650; Money In</span>
                                <div style="font-size: 13px; font-weight: 800; color: #065f46;">Rs. {{ number_format($total_money_in_7d) }}</div>
                            </div>
                            <div style="background: rgba(239,68,68,0.1); border-radius: 8px; padding: 4px 10px; border: 1px solid rgba(239,68,68,0.3);">
                                <span style="font-size: 10px; color: #dc2626; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">&#9660; Money Out</span>
                                <div style="font-size: 13px; font-weight: 800; color: #7f1d1d;">Rs. {{ number_format($total_money_out_7d) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-body p-4">
                    <div class="chart-area" style="height: 300px;">
                        <canvas id="cashFlowChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 5: Incoming Goods vs Customer Sales Comparison Chart -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="premium-panel shadow-sm border-left-info">
                <div class="panel-header d-flex justify-content-between align-items-center bg-light-soft">
                    <h5 class="m-0 font-weight-bolder text-gray-800">
                        <div class="icon-box bg-info-light mr-3"><i class="fas fa-boxes-packing text-info"></i></div>
                        Incoming Goods vs Customer Sales
                        <span class="small text-muted ml-2">
                            (@if(request('start_date')) {{ \Carbon\Carbon::parse(request('start_date'))->format('M d') }} - {{ \Carbon\Carbon::parse(request('end_date'))->format('M d') }} @else Last 7 Days @endif)
                        </span>
                    </h5>
                    <div class="text-right">
                        <div class="small text-muted font-weight-bold mb-1">Daily Amount Comparison</div>
                        <div class="d-flex align-items-center" style="gap: 10px; justify-content: flex-end;">
                            <div style="background: rgba(163, 177, 198, 0.1); border-radius: 8px; padding: 4px 10px; border: 1px solid rgba(163, 177, 198, 0.3);">
                                <span style="font-size: 10px; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">&#9679; Incoming Goods</span>
                                <div style="font-size: 13px; font-weight: 800; color: #475569;">Rs. {{ number_format($total_incoming_amount) }}</div>
                            </div>
                            <div style="background: rgba(250, 204, 21, 0.1); border-radius: 8px; padding: 4px 10px; border: 1px solid rgba(250, 204, 21, 0.3);">
                                <span style="font-size: 10px; color: #d97706; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">&#9675; Customer Sales</span>
                                <div style="font-size: 13px; font-weight: 800; color: #b45309;">Rs. {{ number_format($total_sales_amount) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-body p-4">
                    <div class="chart-area" style="height: 300px;">
                        <canvas id="incomingVsSalesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Quick Expense Modal -->
<div class="modal fade" id="quickExpenseModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-minus-circle mr-2"></i> Record Quick Expense</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('expenses.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="small font-weight-bold">Expense Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" list="expense-titles" class="form-control border-0 bg-light" placeholder="Type or select expense title (e.g. Office tea, Rent)" required autofocus autocomplete="off">
                        <datalist id="expense-titles">
                            @foreach($recent_expense_titles as $title)
                                <option value="{{ $title }}">
                            @endforeach
                        </datalist>
                    </div>

                    <div class="form-group mb-3">
                        <label class="small font-weight-bold">Amount (Rs.) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-0">Rs.</span>
                            </div>
                            <input type="number" step="0.01" name="amount" class="form-control form-control-lg border-0 bg-light" placeholder="0.00" required>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="small font-weight-bold">Deduct From <span class="text-danger">*</span></label>
                        <select name="financial_account_id" class="form-control border-0 bg-light" required>
                            <option value="">-- Select Account --</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}" {{ $acc->id == ($staffAccId ?? null) ? 'selected' : '' }}>
                                    {{ $acc->name }} (Bal: Rs. {{ number_format($acc->current_balance ?? 0) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="small font-weight-bold">Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control border-0 bg-light" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="form-group mb-0">
                        <label class="small font-weight-bold">Description</label>
                        <textarea name="description" class="form-control border-0 bg-light" rows="3" placeholder="What was this expense for? (Optional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 shadow">SAVE EXPENSE</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Quick Bilty (Delivery Receipt) Modal -->
<div class="modal fade" id="quickBiltyModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-truck mr-2 text-primary"></i> Create Delivery Receipt (Bilty)</h5>
                <button type="button" class="close text-muted" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('delivery-receipts.store') }}" method="POST" target="_blank" onsubmit="var f = this; setTimeout(function(){ $('#quickBiltyModal').modal('hide'); f.reset(); }, 500);">
                @csrf
                <div class="modal-body px-4 pt-3">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="small font-weight-bold">Date <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small font-weight-bold">Courier Company</label>
                            <input type="text" name="courier_company" id="bilty_courier" class="form-control" placeholder="e.g. TCS, Leopard">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small font-weight-bold">Sender Name</label>
                            <input type="text" name="sender_name" class="form-control" value="Danyal Autos (Lahore)" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small font-weight-bold">Receiver Name (Customer) <span class="text-danger">*</span></label>
                            <input type="hidden" name="customer_id" id="bilty_customer_id">
                            <input type="text" name="receiver_name" list="bilty-customers" id="bilty_receiver" class="form-control" placeholder="Type or select customer" required autocomplete="off">
                            <datalist id="bilty-customers">
                                @php
                                    $customers = \App\User::where('role','user')->get();
                                @endphp
                                @foreach($customers as $cust)
                                    <option value="{{$cust->name}}" data-id="{{$cust->id}}"></option>
                                @endforeach
                            </datalist>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small font-weight-bold">Address</label>
                            <textarea name="address" id="bilty_address" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small font-weight-bold">City</label>
                            <input type="text" name="city" id="bilty_city" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="small font-weight-bold">No. of Cartons</label>
                            <input type="number" name="no_of_cartons" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="small font-weight-bold">No. of Bags</label>
                            <input type="number" name="no_of_bags" class="form-control" value="0" min="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4 justify-content-between">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow">SAVE & PRINT</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Quick Add Calendar Task Modal -->
<div class="modal fade" id="quickAddCalendarModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-calendar-plus mr-2"></i> Quick Add to Calendar</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="quickAddCalendarForm" action="{{ route('tasks.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <input type="hidden" name="all_day" value="1">
                    <input type="hidden" name="priority" value="medium">
                    <input type="hidden" name="task_type" value="general">
                    
                    <div class="form-group mb-3">
                        <label class="small font-weight-bold">Date <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" id="quickAddDate" class="form-control border-0 bg-light" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="small font-weight-bold">Task / Reminder Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control border-0 bg-light" placeholder="e.g. Call Customer, Follow up on delivery" required autofocus autocomplete="off">
                    </div>

                    <div class="form-group mb-0">
                        <label class="small font-weight-bold">Details (Optional)</label>
                        <textarea name="description" class="form-control border-0 bg-light" rows="2" placeholder="Any extra details..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow">Save to Calendar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Chart Details Modal -->
<div class="modal fade" id="chartDetailsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold" id="chartDetailsModalTitle"><i class="fas fa-chart-pie mr-2"></i> Date Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-4" id="chartDetailsModalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Fetching breakdown...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const receiverInput = document.getElementById('bilty_receiver');
    receiverInput.addEventListener('change', function() {
        const val = this.value;
        const options = document.getElementById('bilty-customers').options;
        let customerId = null;
        for(let i=0; i<options.length; i++) {
            if(options[i].value === val) {
                customerId = options[i].getAttribute('data-id');
                break;
            }
        }
        
        document.getElementById('bilty_customer_id').value = customerId || '';

        if(customerId) {
            fetch(`/admin/delivery-receipts/get-customer/${customerId}`)
            .then(res => res.json())
            .then(data => {
                if(data.status) {
                    if(data.address) document.getElementById('bilty_address').value = data.address;
                    if(data.city) document.getElementById('bilty_city').value = data.city;
                    if(data.courier_company) document.getElementById('bilty_courier').value = data.courier_company;
                }
            });
        }
    });
});
</script>

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
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid rgba(226, 232, 240, 0.6);
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

    .gradient-success { background: linear-gradient(135deg, #083259 0%, #0d467a 100%); }
    .gradient-danger { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); }
    .gradient-primary { background: linear-gradient(135deg, #083259 0%, #0a2540 100%); }
    .gradient-info { background: linear-gradient(135deg, #facc15 0%, #f59e0b 100%); color: #083259 !important; }

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

    /* Premium Google-Like Calendar Overrides */
    .fc-theme-standard th { 
        border: none !important; 
        color: #64748b; 
        text-transform: uppercase; 
        font-size: 0.75rem; 
        padding: 12px 0;
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    .fc-theme-standard td { border-color: rgba(226, 232, 240, 0.6); }
    .fc-day-today { background-color: transparent !important; }
    .fc-day-today .fc-daygrid-day-number {
        background-color: #083259;
        color: white;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 4px;
    }
    .fc-button-primary { 
        background: #083259 !important; 
        border: none !important; 
        border-radius: 20px !important; 
        text-transform: capitalize; 
        font-weight: 600;
        padding: 0.4rem 1.2rem !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: all 0.2s;
    }
    .fc-button-primary:hover {
        background: #0a2540 !important;
        transform: translateY(-1px);
    }
    .fc-button-active {
        background: #facc15 !important;
        color: #083259 !important;
    }
    .fc-toolbar-title { 
        font-weight: 800 !important; 
        color: #1e293b; 
        font-size: 1.4rem !important;
    }
    .fc-event { 
        border-radius: 6px; 
        border: none; 
        padding: 4px 8px; 
        font-size: 0.75rem; 
        font-weight: 600; 
        cursor: pointer;
        margin-bottom: 2px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        transition: transform 0.1s;
    }
    .fc-event:hover {
        transform: scale(1.02);
    }
    .fc-daygrid-day-frame {
        cursor: pointer;
        transition: background 0.2s;
    }
    .fc-daygrid-day-frame:hover {
        background: rgba(248, 250, 252, 0.8);
    }
    /* Event Popover Customization */
    .popover {
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .popover-header {
        background-color: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
        font-weight: 700;
        color: #1e293b;
    }
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
        background: #facc15;
        color: #083259;
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
        animation: ticker-scroll 140s linear infinite; /* Slowed down from 80s to 140s */
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
    .ticker-item strong {
        color: #818cf8; /* Indigo for AI */
        margin-right: 6px;
        letter-spacing: 0.5px;
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
    // Live Clock (Synced to Pakistan/Lahore Time)
    setInterval(() => {
        document.getElementById('live-clock').innerText = new Date().toLocaleTimeString('en-US', { 
            timeZone: 'Asia/Karachi',
            hour12: true, 
            hour: '2-digit', 
            minute: '2-digit', 
            second: '2-digit' 
        });
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
            editable: true,
            eventAllow: function(dropInfo, draggedEvent) {
                // Only allow dragging of actual Tasks
                return draggedEvent.extendedProps.isTask;
            },
            eventDrop: function(info) {
                if(!info.event.extendedProps.isTask) {
                    info.revert();
                    return;
                }
                // AJAX call to update task date
                $.ajax({
                    url: '/admin/tasks/' + info.event.id,
                    type: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        start_date: info.event.startStr,
                        end_date: info.event.endStr || info.event.startStr
                    },
                    success: function(res) {
                        if(res.success) {
                            console.log('Task rescheduled');
                        } else {
                            info.revert();
                        }
                    },
                    error: function() {
                        info.revert();
                        alert('Error rescheduling task.');
                    }
                });
            },
            dateClick: function(info) {
                $('#quickAddDate').val(info.dateStr);
                $('#quickAddCalendarModal').modal('show');
            },
            eventDidMount: function(info) {
                // Add Bootstrap Popover for Google Calendar-like tooltips
                $(info.el).popover({
                    title: info.event.title,
                    placement: 'top',
                    trigger: 'hover',
                    html: true,
                    content: `
                        <div class="small">
                            <strong>Details:</strong> ${info.event.extendedProps.description || 'No details'}<br>
                            <strong>Status:</strong> <span class="badge badge-light border">${info.event.extendedProps.status || 'N/A'}</span><br>
                            <strong>Assignee:</strong> ${info.event.extendedProps.assignee || 'Unassigned'}
                        </div>
                    `,
                    container: 'body'
                });
                
                // Support custom text colors
                if (info.event.extendedProps.textColor) {
                    info.el.style.color = info.event.extendedProps.textColor;
                }
            },
            eventClick: function(info) {
                info.jsEvent.preventDefault();
            }
        });
        calendar.render();
        
        window.calendarObj = calendar;

        // Handle Quick Add Form via AJAX
        $('#quickAddCalendarForm').on('submit', function(e) {
            e.preventDefault();
            var $btn = $(this).find('button[type="submit"]');
            var originalText = $btn.text();
            $btn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
            
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if(response.success) {
                        $('#quickAddCalendarModal').modal('hide');
                        $('#quickAddCalendarForm')[0].reset();
                        if(window.calendarObj) {
                            window.calendarObj.refetchEvents();
                        }
                        Swal.fire({
                            icon: 'success',
                            title: 'Added!',
                            text: 'Task saved to calendar.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                },
                error: function(xhr) {
                    alert('Failed to save task.');
                },
                complete: function() {
                    $btn.html(originalText).prop('disabled', false);
                }
            });
        });

        // Chart defaults
        Chart.defaults.global.defaultFontFamily = "'Plus Jakarta Sans', sans-serif";
        Chart.defaults.global.defaultFontColor = '#64748b';

        var rawDates = {!! $raw_dates !!};
        
        function openChartDetails(date, chartType) {
            $('#chartDetailsModalTitle').html('<i class="fas fa-search-dollar mr-2"></i> Details for ' + date);
            $('#chartDetailsModalBody').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div><p class="mt-2 text-muted">Fetching breakdown...</p></div>');
            $('#chartDetailsModal').modal('show');
            
            $.get('{{ route("admin.chart.details") }}', { date: date, type: chartType }, function(data) {
                $('#chartDetailsModalBody').html(data);
            }).fail(function() {
                $('#chartDetailsModalBody').html('<div class="alert alert-danger">Failed to load details.</div>');
            });
        }

                // Top 5 Customers by Revenue (Horizontal Bar Chart)
        var ctxRev = document.getElementById("topRevenueChart").getContext('2d');
        new Chart(ctxRev, {
            type: 'horizontalBar',
            data: {
                labels: {!! $topRevNamesJson !!},
                datasets: [{
                    label: "Total Spent",
                    backgroundColor: "#10b981",
                    hoverBackgroundColor: "#059669",
                    borderColor: "#10b981",
                    data: {!! $topRevAmountsJson !!},
                    barPercentage: 0.5
                }]
            },
            options: {
                maintainAspectRatio: false,
                layout: { padding: { left: 10, right: 25, top: 25, bottom: 0 } },
                scales: {
                    xAxes: [{
                        ticks: {
                            callback: function(value) { return 'Rs ' + Number(value).toLocaleString(); }
                        },
                        gridLines: { display: true, drawBorder: false, borderDash: [5, 5] }
                    }],
                    yAxes: [{
                        gridLines: { display: false, drawBorder: false }
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
                            return 'Spent: Rs. ' + Number(tooltipItem.xLabel).toLocaleString();
                        }
                    }
                }
            }
        });


// Cash Flow Bar Chart
        var ctxFlow = document.getElementById("cashFlowChart").getContext('2d');
        new Chart(ctxFlow, {
            type: 'bar',
            data: {
                labels: {!! $order_labels !!},
                datasets: [
                    {
                        label: "Money In",
                        backgroundColor: "#facc15",
                        hoverBackgroundColor: "#d97706",
                        borderColor: "#facc15",
                        data: {!! $money_in !!},
                        barPercentage: 0.6,
                        categoryPercentage: 0.5
                    },
                    {
                        label: "Money Out",
                        backgroundColor: "#083259",
                        hoverBackgroundColor: "#0e4a7a",
                        borderColor: "#083259",
                        data: {!! $money_out !!},
                        barPercentage: 0.6,
                        categoryPercentage: 0.5
                    }
                ]
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
                legend: { display: true, position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 6, fontStyle: 'bold' } },
                tooltips: {
                    backgroundColor: "#1e293b",
                    bodyFontColor: "#fff",
                    titleMarginBottom: 10,
                    titleFontColor: '#e2e8f0',
                    borderColor: 'rgba(255,255,255,0.1)',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    callbacks: {
                        label: function(tooltipItem, chart) {
                            var label = chart.datasets[tooltipItem.datasetIndex].label || '';
                            return label + ': Rs. ' + Number(tooltipItem.yLabel).toLocaleString();
                        }
                    }
                },
                onClick: function(evt, activeElements) {
                    if (activeElements.length > 0) {
                        var index = activeElements[0]._index;
                        openChartDetails(rawDates[index], 'cash_flow');
                    }
                }
            }
        });

        // Incoming Goods vs Customer Sales Chart
        var ctxIncomingSales = document.getElementById("incomingVsSalesChart").getContext('2d');
        
        var gradientIncoming = ctxIncomingSales.createLinearGradient(0, 0, 0, 400);
        gradientIncoming.addColorStop(0, "rgba(163, 177, 198, 0.4)");
        gradientIncoming.addColorStop(1, "rgba(163, 177, 198, 0.05)");

        var gradientSales = ctxIncomingSales.createLinearGradient(0, 0, 0, 400);
        gradientSales.addColorStop(0, "rgba(250, 204, 21, 0.4)");
        gradientSales.addColorStop(1, "rgba(250, 204, 21, 0.05)");

        new Chart(ctxIncomingSales, {
            type: 'line',
            data: {
                labels: {!! $order_labels !!},
                datasets: [
                    {
                        label: "Incoming Goods Amount",
                        lineTension: 0.3,
                        backgroundColor: gradientIncoming,
                        borderColor: "#a3b1c6",
                        pointRadius: 4,
                        pointBackgroundColor: "#fff",
                        pointBorderColor: "#a3b1c6",
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: "#a3b1c6",
                        pointHoverBorderColor: "#fff",
                        pointBorderWidth: 2,
                        data: {!! $incoming_amounts !!},
                    },
                    {
                        label: "Customer Sales Amount",
                        lineTension: 0.3,
                        backgroundColor: gradientSales,
                        borderColor: "#facc15",
                        pointRadius: 4,
                        pointBackgroundColor: "#fff",
                        pointBorderColor: "#facc15",
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: "#facc15",
                        pointHoverBorderColor: "#fff",
                        pointBorderWidth: 2,
                        data: {!! $order_amounts !!},
                    }
                ]
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
                legend: { display: true, position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 6, fontStyle: 'bold' } },
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
                            var label = chart.datasets[tooltipItem.datasetIndex].label || '';
                            return label + ': Rs. ' + Number(tooltipItem.yLabel).toLocaleString();
                        }
                    }
                },
                onClick: function(evt, activeElements) {
                    if (activeElements.length > 0) {
                        var index = activeElements[0]._index;
                        openChartDetails(rawDates[index], 'incoming_sales');
                    }
                }
            }
        });
    });
</script>
@endpush
