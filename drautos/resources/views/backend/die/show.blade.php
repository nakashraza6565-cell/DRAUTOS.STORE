@extends('backend.layouts.master')

@section('title', 'Die Profile - ' . $die->name)

@section('main-content')
<div class="container-fluid px-2 px-md-4">
    <!-- Header Block -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-3 gap-2">
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold"><i class="fas fa-tools mr-2 text-primary"></i>{{ $die->name }}</h1>
            <p class="text-muted mb-0 small">Registered on: {{ $die->created_at->format('d M Y') }}</p>
        </div>
        <a href="{{ route('die-management.index') }}" class="btn btn-secondary btn-sm mt-2 mt-sm-0"><i class="fas fa-arrow-left mr-1"></i> Back to List</a>
    </div>

    @include('backend.layouts.notification')

    <!-- Main Grid -->
    <div class="row">
        <!-- Sidebar / Overview Column -->
        <div class="col-lg-4 col-md-5 mb-4">
            <!-- QR Code & Status Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body text-center">
                    <!-- Status Badge -->
                    <div class="mb-3">
                        @php
                            $statusClass = 'badge-secondary';
                            if($die->quality_status == 'good') $statusClass = 'badge-success';
                            if($die->quality_status == 'maintenance_required') $statusClass = 'badge-warning';
                            if($die->quality_status == 'damaged') $statusClass = 'badge-danger';
                        @endphp
                        <span class="badge badge-lg p-2 font-weight-bold {{ $statusClass }}" style="font-size: 0.9rem; border-radius: 20px;">
                            <i class="fas fa-circle mr-1" style="font-size: 0.7rem;"></i>{{ str_replace('_', ' ', strtoupper($die->quality_status ?? 'UNKNOWN')) }}
                        </span>
                        @if($die->status == 'active')
                            <span class="badge badge-primary p-2 font-weight-bold" style="font-size: 0.9rem; border-radius: 20px;">ACTIVE</span>
                        @else
                            <span class="badge badge-light border p-2 font-weight-bold" style="font-size: 0.9rem; border-radius: 20px;">INACTIVE</span>
                        @endif
                    </div>

                    <!-- QR Code -->
                    <div class="bg-light p-3 rounded d-inline-block border mb-3 shadow-sm">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data={{ urlencode($qrCodeUrl) }}" alt="Die Engrave QR Code" class="img-fluid" style="max-width: 160px;">
                        <div class="mt-2 text-xs font-weight-bold text-muted">ENGRAVE QR CODE</div>
                    </div>
                    
                    <div class="mb-3">
                        <button onclick="window.print()" class="btn btn-outline-dark btn-sm"><i class="fas fa-print mr-1"></i> Print QR</button>
                    </div>

                    <h5 class="font-weight-bold text-gray-800">{{ $die->name }}</h5>
                    <p class="text-sm text-muted mb-0"><strong>Rack Location:</strong> <span class="badge badge-light border px-2 py-1">{{ $die->rack_number ?? 'N/A' }}</span></p>
                </div>
            </div>

            <!-- Photos & Gallery Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-images mr-2"></i>Photo Gallery</h6>
                </div>
                <div class="card-body p-2">
                    @php
                        $allPhotos = $die->photos ?: [];
                        if($die->photo && !in_array($die->photo, $allPhotos)) {
                            array_unshift($allPhotos, $die->photo);
                        }
                    @endphp

                    @if(count($allPhotos) > 0)
                        <div id="dieGallery" class="carousel slide" data-ride="carousel">
                            <div class="carousel-inner rounded">
                                @foreach($allPhotos as $index => $p)
                                    <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                        <img src="{{ $p }}" class="d-block w-100" style="height: 250px; object-fit: cover;" alt="Die Image">
                                    </div>
                                @endforeach
                            </div>
                            @if(count($allPhotos) > 1)
                                <a class="carousel-control-prev" href="#dieGallery" role="button" data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#dieGallery" role="button" data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-5 bg-gray-100 rounded text-muted">
                            <i class="fas fa-tools fa-3x mb-2 text-gray-400"></i>
                            <p class="mb-0 text-sm">No photos uploaded</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Specs Card -->
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-list-alt mr-2"></i>Key Details</h6>
                    <div class="small">
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Associated Product:</span>
                            <span class="font-weight-bold text-right">
                                @if($die->product)
                                    <a href="{{ route('product.edit', $die->product->id) }}">{{ $die->product->title }}</a>
                                @else
                                    <span class="text-muted">None</span>
                                @endif
                            </span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Maker (Supplier):</span>
                            <span class="font-weight-bold">{{ $die->makerSupplier->name ?? $die->maker ?? 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Die Type:</span>
                            <span class="font-weight-bold">{{ $die->die_type ?? 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom text-success font-weight-bold">
                            <span>Goods Produced:</span>
                            <span>{{ number_format($die->goods_produced) }} units</span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Current Custody:</span>
                            <span class="font-weight-bold">{{ $die->custody_of ?? 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                            <span class="text-muted">Custody Phone:</span>
                            <span class="font-weight-bold">{{ $die->custody_phone ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details / History Tabs Column -->
        <div class="col-lg-8 col-md-7 mb-4">
            <!-- Financial Card Summary -->
            <div class="row mb-4">
                <div class="col-sm-6 mb-3 mb-sm-0">
                    <div class="card border-left-primary shadow-sm h-100 py-2 border-0">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Initial Cost</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">PKR {{ number_format($die->making_cost, 2) }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-coins fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card border-left-success shadow-sm h-100 py-2 border-0">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Maintenance Spent</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">PKR {{ number_format($die->expenses->sum('amount'), 2) }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-wrench fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Center (Large Mobile Touch Targets) -->
            <div class="card shadow-sm border-0 mb-4 bg-light">
                <div class="card-body p-3">
                    <h6 class="font-weight-bold text-gray-800 mb-3"><i class="fas fa-cogs mr-2 text-secondary"></i>Quick Mobile Actions Center</h6>
                    <div class="row gap-2 px-2">
                        <button class="btn btn-primary btn-lg col mr-2 mb-2 d-flex align-items-center justify-content-center gap-2" style="min-height: 50px;" data-toggle="modal" data-target="#handoverModal">
                            <i class="fas fa-user-friends"></i> Transfer Custody
                        </button>
                        <button class="btn btn-warning btn-lg col mr-2 mb-2 text-white d-flex align-items-center justify-content-center gap-2" style="min-height: 50px;" data-toggle="modal" data-target="#qualityModal">
                            <i class="fas fa-check-circle"></i> Change Status
                        </button>
                        <button class="btn btn-success btn-lg col mb-2 d-flex align-items-center justify-content-center gap-2" style="min-height: 50px;" data-toggle="modal" data-target="#expenseModal">
                            <i class="fas fa-receipt"></i> Log Expense
                        </button>
                    </div>
                </div>
            </div>

            <!-- History Timelines Card -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3">
                    <!-- Tab Headers -->
                    <ul class="nav nav-tabs card-header-tabs" id="dieHistoryTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active font-weight-bold" id="custody-tab" data-toggle="tab" href="#custody" role="tab" aria-controls="custody" aria-selected="true">Custody Log</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold" id="production-tab" data-toggle="tab" href="#production" role="tab" aria-controls="production" aria-selected="false">Production Run</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold" id="expenses-tab" data-toggle="tab" href="#expenses" role="tab" aria-controls="expenses" aria-selected="false">Expense History</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold" id="quality-tab" data-toggle="tab" href="#quality" role="tab" aria-controls="quality" aria-selected="false">Quality Reports</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="dieHistoryTabContent">
                        
                        <!-- Custody Log Timeline -->
                        <div class="tab-pane fade show active" id="custody" role="tabpanel" aria-labelledby="custody-tab">
                            @if(count($die->custodyLogs) > 0)
                                <div class="timeline-wrapper">
                                    @foreach($die->custodyLogs as $log)
                                        <div class="pb-3 border-left pl-3 position-relative" style="border-color: #cbd5e1 !important;">
                                            <div class="position-absolute bg-primary rounded-circle text-white d-flex align-items-center justify-content-center shadow-sm" style="width:24px; height:24px; left:-12px; top:0;">
                                                <i class="fas fa-user-tag text-xs" style="font-size: 0.65rem;"></i>
                                            </div>
                                            <div class="font-weight-bold text-gray-800">{{ $log->custody_of }}</div>
                                            <div class="text-xs text-muted">
                                                📅 {{ $log->handover_date->format('d M Y - h:i A') }} 
                                                @if($log->custody_phone) &nbsp;|&nbsp; 📞 {{ $log->custody_phone }} @endif
                                                &nbsp;|&nbsp; 👤 By: {{ $log->creator->name ?? 'System' }}
                                            </div>
                                            @if($log->notes)
                                                <p class="bg-light p-2 rounded text-sm mt-1 mb-0 border">{{ $log->notes }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-center py-4 text-muted">No custody changes recorded yet.</p>
                            @endif
                        </div>

                        <!-- Production runs -->
                        <div class="tab-pane fade" id="production" role="tabpanel" aria-labelledby="production-tab">
                            @if(count($die->productions) > 0)
                                <div class="timeline-wrapper">
                                    @foreach($die->productions as $run)
                                        <div class="pb-3 border-left pl-3 position-relative" style="border-color: #cbd5e1 !important;">
                                            <div class="position-absolute bg-success rounded-circle text-white d-flex align-items-center justify-content-center shadow-sm" style="width:24px; height:24px; left:-12px; top:0;">
                                                <i class="fas fa-boxes text-xs" style="font-size: 0.65rem;"></i>
                                            </div>
                                            <div class="font-weight-bold text-gray-800">Produced {{ number_format($run->quantity_produced) }} units</div>
                                            <div class="text-xs text-muted">
                                                📅 {{ $run->production_date->format('d M Y') }} &nbsp;|&nbsp; 🧾 Run #: {{ $run->production_number }} &nbsp;|&nbsp; 👤 Operator: {{ $run->producer->name ?? 'N/A' }}
                                            </div>
                                            @if($run->notes)
                                                <p class="text-muted text-sm mt-1 mb-0">{{ $run->notes }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-center py-4 text-muted">No manufacturing runs linked to this die yet.</p>
                            @endif
                        </div>

                        <!-- Expense Log -->
                        <div class="tab-pane fade" id="expenses" role="tabpanel" aria-labelledby="expenses-tab">
                            @if(count($die->expenses) > 0)
                                <div class="timeline-wrapper">
                                    @foreach($die->expenses as $exp)
                                        <div class="pb-3 border-left pl-3 position-relative" style="border-color: #cbd5e1 !important;">
                                            <div class="position-absolute bg-danger rounded-circle text-white d-flex align-items-center justify-content-center shadow-sm" style="width:24px; height:24px; left:-12px; top:0;">
                                                <i class="fas fa-wallet text-xs" style="font-size: 0.65rem;"></i>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="font-weight-bold text-gray-800">{{ $exp->description }}</div>
                                                <div class="badge badge-danger">PKR {{ number_format($exp->amount, 2) }}</div>
                                            </div>
                                            <div class="text-xs text-muted">
                                                📅 {{ $exp->expense_date->format('d M Y') }} 
                                                @if($exp->supplier) &nbsp;|&nbsp; 🔧 Workshop: {{ $exp->supplier->name }} @endif
                                                @if($exp->financialAccount) &nbsp;|&nbsp; 💵 Paid From: {{ $exp->financialAccount->name }} @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-center py-4 text-muted">No maintenance or setup expenses logged yet.</p>
                            @endif
                        </div>

                        <!-- Quality Reports -->
                        <div class="tab-pane fade" id="quality" role="tabpanel" aria-labelledby="quality-tab">
                            @if(count($die->qualityReports) > 0)
                                <div class="timeline-wrapper">
                                    @foreach($die->qualityReports as $rep)
                                        <div class="pb-3 border-left pl-3 position-relative" style="border-color: #cbd5e1 !important;">
                                            @php
                                                $repClass = 'bg-secondary';
                                                if($rep->quality_status == 'good') $repClass = 'bg-success';
                                                if($rep->quality_status == 'maintenance_required') $repClass = 'bg-warning';
                                                if($rep->quality_status == 'damaged') $repClass = 'bg-danger';
                                            @endphp
                                            <div class="position-absolute {{ $repClass }} rounded-circle text-white d-flex align-items-center justify-content-center shadow-sm" style="width:24px; height:24px; left:-12px; top:0;">
                                                <i class="fas fa-clipboard-check text-xs" style="font-size: 0.65rem;"></i>
                                            </div>
                                            <div class="font-weight-bold text-gray-800">Status Changed to: <span class="text-uppercase">{{ str_replace('_', ' ', $rep->quality_status) }}</span></div>
                                            <div class="text-xs text-muted">
                                                📅 {{ $rep->report_date->format('d M Y - h:i A') }} &nbsp;|&nbsp; 👤 By: {{ $rep->reporter->name ?? 'System' }}
                                            </div>
                                            @if($rep->notes)
                                                <p class="bg-light p-2 rounded text-sm mt-1 mb-0 border">{{ $rep->notes }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-center py-4 text-muted">No quality reports logged yet.</p>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODALS ================= -->

<!-- 1. Handover Modal -->
<div class="modal fade" id="handoverModal" tabindex="-1" role="dialog" aria-labelledby="handoverModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold" id="handoverModalLabel"><i class="fas fa-people-arrows mr-2"></i>Record Custody Handover</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('die-management.handover', $die->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Handover Custody To *</label>
                        <input type="text" name="custody_of" class="form-control" placeholder="E.g. Workshop A, Staff Name" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Custody Contact Phone</label>
                        <input type="text" name="custody_phone" class="form-control" placeholder="E.g. 03001234567">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Handover Date/Time *</label>
                        <input type="datetime-local" name="handover_date" class="form-control" value="{{ date('Y-m-d\TH:i') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Handover Notes</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Specify terms, delivery context, or notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary font-weight-bold">Save Handover</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 2. Quality Modal -->
<div class="modal fade" id="qualityModal" tabindex="-1" role="dialog" aria-labelledby="qualityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title font-weight-bold" id="qualityModalLabel"><i class="fas fa-clipboard-list mr-2"></i>Report Condition/Status</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('die-management.quality', $die->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Quality Condition Status *</label>
                        <select name="quality_status" class="form-control" required>
                            <option value="good" {{ $die->quality_status == 'good' ? 'selected' : '' }}>✅ Good (Ready to Use)</option>
                            <option value="maintenance_required" {{ $die->quality_status == 'maintenance_required' ? 'selected' : '' }}>🛠️ Maintenance Required (Needs Servicing)</option>
                            <option value="damaged" {{ $die->quality_status == 'damaged' ? 'selected' : '' }}>❌ Damaged (Do Not Use)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Reporting Date/Time *</label>
                        <input type="datetime-local" name="report_date" class="form-control" value="{{ date('Y-m-d\TH:i') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Report Notes / Findings</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Describe the physical condition, wear, or repair details..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning text-white font-weight-bold">Save Report</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 3. Expense Modal -->
<div class="modal fade" id="expenseModal" tabindex="-1" role="dialog" aria-labelledby="expenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title font-weight-bold" id="expenseModalLabel"><i class="fas fa-wallet mr-2"></i>Log Maintenance Expense</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('die-management.expense', $die->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Expense Description *</label>
                        <input type="text" name="description" class="form-control" placeholder="E.g. Welding repairs, Polishing, Repainting" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Expense Amount (PKR) *</label>
                        <input type="number" name="amount" step="0.01" min="0.01" class="form-control" placeholder="0.00" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Supplier / Technician (Optional)</label>
                        <select name="supplier_id" class="form-control select2">
                            <option value="">-- Select Supplier to Attribute Invoice Bill --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">If selected, this creates a purchase bill/debit in their supplier ledger.</small>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label class="font-weight-bold">Amount Paid Now (PKR) *</label>
                        <input type="number" name="amount_paid" step="0.01" min="0" class="form-control" value="0.00" required>
                        <small class="text-muted">If paid, money will be deducted from your cash/bank account.</small>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Cash/Bank Payment Account</label>
                        <select name="financial_account_id" class="form-control">
                            <option value="">-- Active Staff Cash Account --</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->name }} (Bal: Rs. {{ number_format($acc->current_balance) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Expense Date *</label>
                        <input type="date" name="expense_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success font-weight-bold">Log Payout</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .timeline-wrapper {
        border-left: 2px solid #e2e8f0;
        margin-left: 10px;
        padding-left: 10px;
    }
    @media print {
        .navbar-nav, .no-print, .footer, .btn, .nav-tabs, .social-info, .Social, .SocialShare, #handoverModal, #qualityModal, #expenseModal, .bg-light, hr {
            display: none !important;
        }
        body, .container-fluid, .card, .card-body {
            padding: 0 !important;
            margin: 0 !important;
            box-shadow: none !important;
            background: transparent !important;
        }
        .col-lg-4, .col-lg-8 {
            width: 100% !important;
        }
    }
</style>
@endpush
