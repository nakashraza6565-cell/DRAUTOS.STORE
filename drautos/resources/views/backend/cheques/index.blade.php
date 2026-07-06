@extends('backend.layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary float-left">Cheque Management</h6>
      <a href="{{route('cheques.create')}}" class="btn btn-primary btn-sm float-right"><i class="fas fa-plus"></i> Add Cheque</a>
    </div>
    <div class="row">
        <div class="col-md-12">
           @include('backend.layouts.notification')
        </div>
    </div>
    <div class="card-body">
        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <a href="{{route('cheques.index', ['filter' => 'pending_received'])}}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 py-3 card-hover" style="border-radius: 15px; background: #10b981; color: #fff;">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-white-50 text-uppercase mb-1">Pending Received</div>
                            <div class="h5 mb-0 font-weight-bold text-white">PKR {{ number_format($stats['pending_received'] ?? 0, 2) }}</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{route('cheques.index', ['filter' => 'pending_paid'])}}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 py-3 card-hover" style="border-radius: 15px; background: #ef4444; color: #fff;">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-white-50 text-uppercase mb-1">Pending Paid</div>
                            <div class="h5 mb-0 font-weight-bold text-white">PKR {{ number_format($stats['pending_paid'] ?? 0, 2) }}</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{route('cheques.index', ['filter' => 'clearing_today'])}}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 py-3 card-hover" style="border-radius: 15px; background: #f59e0b; color: #fff;">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-white-50 text-uppercase mb-1">Clearing Today</div>
                            <div class="h5 mb-0 font-weight-bold text-white">{{ $stats['cleared_today'] ?? 0 }} Cheques</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{route('cheques.index', ['filter' => 'overdue'])}}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 py-3 card-hover" style="border-radius: 15px; background: #1e293b; color: #fff;">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-white-50 text-uppercase mb-1">Overdue</div>
                            <div class="h5 mb-0 font-weight-bold text-white">{{ $stats['overdue'] ?? 0 }} Pending</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <ul class="nav nav-pills mb-4 bg-light p-2 rounded-lg" id="chequeTabs" role="tablist" style="border: 1px solid rgba(0,0,0,0.05);">
            <li class="nav-item">
                <a class="nav-link active font-weight-bold px-4" id="list-tab" data-toggle="pill" href="#list-view" role="tab" style="border-radius: 10px;">
                    <i class="fas fa-list mr-2"></i>List View
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold px-4" id="calendar-tab" data-toggle="pill" href="#calendar-view" role="tab" style="border-radius: 10px;">
                    <i class="fas fa-calendar-alt mr-2"></i>Calendar Tracker
                </a>
            </li>
        </ul>

        <div class="tab-content" id="chequeTabContent">
            <!-- List View -->
            <div class="tab-pane fade show active" id="list-view" role="tabpanel">
                <div class="table-responsive">
                    @if(count($cheques)>0)
                    <table class="table table-hover mb-0 order-table-to-cards cheque-table-to-cards" id="cheque-table" width="100%" cellspacing="0" style="font-size: 0.9rem;">
                    <thead style="background: #f8fafc; color: #64748b; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em;">
                        <tr>
                        <th class="border-0">Cheque #</th>
                        <th class="border-0">Type</th>
                        <th class="border-0">Party</th>
                        <th class="border-0">Amount</th>
                        <th class="border-0">Date</th>
                        <th class="border-0">Clearing</th>
                        <th class="border-0">Bank</th>
                        <th class="border-0 text-center">Status</th>
                        <th class="border-0 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cheques as $cheque)   
                            <tr style="border-bottom: 1px solid rgba(0,0,0,0.03);">
                                <td class="align-middle" data-title="Cheque #">
                                    <strong>{{$cheque->cheque_number}}</strong>
                                    @if($cheque->status == 'transferred' && $cheque->transferredTo)
                                        <div class="small text-info mt-1">
                                            <i class="fas fa-exchange-alt mr-1"></i> Transferred
                                        </div>
                                    @endif
                                </td>
                                <td class="align-middle" data-title="Type">
                                    <span class="badge badge-pill badge-{{ $cheque->type == 'received' ? 'success' : 'danger' }} px-3 py-1">
                                        {{ strtoupper($cheque->type) }}
                                    </span>
                                </td>
                                <td class="align-middle font-weight-bold text-gray-700" data-title="Party">
                                    <div class="small text-muted">From:</div>
                                    {{$cheque->party->name ?? 'N/A'}}
                                    @if($cheque->status == 'transferred' && $cheque->transferredTo)
                                        <div class="mt-1">
                                            <div class="small text-muted">To:</div>
                                            <span class="text-primary">{{$cheque->transferredTo->name}}</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="align-middle font-weight-bold" data-title="Amount">Rs. {{number_format($cheque->amount, 2)}}</td>
                                <td class="align-middle text-gray-500" data-title="Date">{{$cheque->cheque_date->format('d M Y')}}</td>
                                <td class="align-middle font-weight-bold text-primary" data-title="Clearing">{{$cheque->clearing_date->format('d M Y')}}</td>
                                <td class="align-middle small" data-title="Bank">{{$cheque->bank_name ?: '-'}}</td>
                                <td class="align-middle text-center" data-title="Status">
                                    @if($cheque->status == 'pending')
                                        <span class="badge badge-warning" style="border-radius:6px; font-weight: 600;">PENDING</span>
                                    @elseif($cheque->status == 'cleared')
                                        <span class="badge badge-success" style="border-radius:6px; font-weight: 600;">CLEARED</span>
                                    @elseif($cheque->status == 'bounced')
                                        <span class="badge badge-danger" style="border-radius:6px; font-weight: 600;">BOUNCED</span>
                                    @elseif($cheque->status == 'transferred')
                                        <span class="badge badge-info" style="border-radius:6px; font-weight: 600;">TRANSFERRED</span>
                                    @else
                                        <span class="badge badge-secondary" style="border-radius:6px; font-weight: 600;">CANCELLED</span>
                                    @endif
                                </td>
                                <td class="align-middle text-center" data-title="Action">
                                    <div class="d-flex flex-nowrap justify-content-end align-items-center" style="gap: 4px;">
                                        <a href="{{route('cheques.show',$cheque->id)}}" class="btn btn-info btn-sm btn-circle act-view" style="height:28px; width:28px; padding:0; display:flex; align-items:center; justify-content:center; font-size: 11px;" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($cheque->status == 'pending')
                                        <button type="button"
                                            class="btn btn-success btn-sm btn-circle btn-mark-cleared"
                                            style="height:28px; width:28px; padding:0; display:flex; align-items:center; justify-content:center; font-size: 11px;"
                                            title="Mark Cleared"
                                            data-id="{{ $cheque->id }}"
                                            data-number="{{ $cheque->cheque_number }}"
                                            data-amount="{{ number_format($cheque->amount, 2) }}"
                                            data-party="{{ $cheque->party->name ?? 'N/A' }}"
                                            data-type="{{ $cheque->type }}"
                                            data-transferred="{{ $cheque->transferred_to_id ? '1' : '0' }}"
                                            data-toggle="modal" data-target="#clearChequeModal">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <form method="POST" action="{{route('cheques.mark-bounced',$cheque->id)}}" class="act-bounced" style="display:inline; margin:0;" onsubmit="return confirm('Mark this cheque as bounced?')">
                                        @csrf
                                        <button class="btn btn-danger btn-sm btn-circle" style="height:28px; width:28px; padding:0; display:flex; align-items:center; justify-content:center; font-size: 11px;" title="Mark Bounced">
                                            <i class="fas fa-times-circle"></i>
                                        </button>
                                        </form>
                                        @endif
                                        <form method="POST" action="{{route('cheques.destroy',[$cheque->id])}}" class="act-delete" style="display:inline; margin:0;">
                                          @csrf 
                                          @method('delete')
                                              <button class="btn btn-danger btn-sm btn-circle dltBtn" data-id="{{$cheque->id}}" style="height:28px; width:28px; padding:0; display:flex; align-items:center; justify-content:center; font-size: 11px;" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>  
                        @endforeach
                    </tbody>
                    </table>
                    <div class="p-3">
                        {{ $cheques->links() }}
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-money-check fa-4x text-gray-200 mb-3"></i>
                        <h6 class="text-gray-400">No Cheques Records Found!</h6>
                        <a href="{{route('cheques.create')}}" class="btn btn-sm btn-primary mt-2">Add First Cheque</a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Calendar View -->
            <div class="tab-pane fade" id="calendar-view" role="tabpanel">
                <div id="cheque-calendar" style="min-height: 500px;"></div>
            </div>
        </div>
    </div>
</div>

{{-- ─── Mark Cleared Modal ────────────────────────────────────────────── --}}
<div class="modal fade" id="clearChequeModal" tabindex="-1" role="dialog" aria-labelledby="clearChequeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:16px; border:none; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">

            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg,#10b981,#059669); border-radius:16px 16px 0 0; padding:24px 28px;">
                <div>
                    <h5 class="modal-title text-white font-weight-bold mb-1" id="clearChequeModalLabel">
                        <i class="fas fa-check-circle mr-2"></i>Mark Cheque as Cleared
                    </h5>
                    <p class="text-white-50 mb-0" id="modal-cheque-subtitle" style="font-size:0.85rem;"></p>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;">
                    <span aria-hidden="true" style="font-size:1.5rem;">&times;</span>
                </button>
            </div>

            <form id="clearChequeForm" method="POST" action="">
                @csrf
                <div class="modal-body" style="padding:28px;">

                    {{-- Info badge --}}
                    <div class="rounded-lg p-3 mb-4" id="modal-info-box" style="background:#f0fdf4; border:1px solid #bbf7d0;">
                        <div class="d-flex align-items-center">
                            <div style="width:42px;height:42px;border-radius:50%;background:#10b981;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="fas fa-money-check-alt text-white"></i>
                            </div>
                            <div class="ml-3">
                                <div class="font-weight-bold text-dark" id="modal-cheque-info">—</div>
                                <div class="small text-muted" id="modal-flow-info">—</div>
                            </div>
                        </div>
                    </div>

                    {{-- Account Selection --}}
                    <div class="form-group">
                        <label class="font-weight-bold text-dark mb-2">
                            <i class="fas fa-university mr-1 text-primary"></i>
                            Select Account <span class="text-danger">*</span>
                        </label>
                        <select name="financial_account_id" id="modal-account-select"
                            class="form-control" style="border-radius:10px; border:2px solid #e2e8f0; padding:10px 14px;" required>
                            <option value="">-- Select Account --</option>
                            @foreach($financialAccounts as $account)
                                <option value="{{ $account->id }}">
                                    {{ $account->name }}
                                    ({{ ucfirst($account->type) }})
                                    — Balance: Rs. {{ number_format($account->current_balance, 2) }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted mt-1 d-block" id="modal-account-hint"></small>
                    </div>

                    {{-- Actual Clearing Date --}}
                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-dark mb-2">
                            <i class="fas fa-calendar-check mr-1 text-primary"></i>
                            Actual Clearing Date <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="actual_clearing_date" id="modal-clearing-date"
                            class="form-control" style="border-radius:10px; border:2px solid #e2e8f0; padding:10px 14px;"
                            value="{{ date('Y-m-d') }}" required>
                    </div>

                </div>

                <div class="modal-footer border-0" style="padding:0 28px 24px;">
                    <button type="button" class="btn btn-light rounded-pill px-4 border" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-5 font-weight-bold shadow-sm">
                        <i class="fas fa-check-circle mr-1"></i> Confirm & Update Cash Flow
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<style>
    .nav-pills .nav-link.active { background-color: #3b82f6; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); }
    .nav-pills .nav-link { color: #64748b; }
    #cheque-calendar { background: #fff; padding: 20px; border-radius: 15px; }
    .fc-event { border: none !important; padding: 2px 5px !important; border-radius: 4px !important; cursor: pointer; }
    .fc-toolbar-title { font-weight: 800 !important; color: #1e293b !important; }
    .fc-button-primary { background-color: #3b82f6 !important; border-color: #3b82f6 !important; border-radius: 8px !important; font-weight: 600 !important; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script>
$(document).ready(function() {
    // Tab persistent state
    $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
        if(e.target.id === 'calendar-tab') {
            calendar.render();
        }
    });

    // Calendar Initialization
    var calendarEl = document.getElementById('cheque-calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,listWeek'
        },
        height: 'auto',
        events: function(fetchInfo, successCallback, failureCallback) {
            $.ajax({
                url: '{{ route("cheques.calendar-events") }}',
                data: {
                    start: fetchInfo.startStr,
                    end: fetchInfo.endStr
                },
                success: function(data) {
                    successCallback(data);
                },
                error: function() {
                    failureCallback();
                }
            });
        },
        eventClick: function(info) {
            window.location.href = "{{ url('admin/cheques') }}/" + info.event.extendedProps.cheque_id;
        }
    });

    // ─── Mark Cleared Modal Population ────────────────────────────────────────
    $(document).on('click', '.btn-mark-cleared', function () {
        var id          = $(this).data('id');
        var number      = $(this).data('number');
        var amount      = $(this).data('amount');
        var party       = $(this).data('party');
        var type        = $(this).data('type');
        var transferred = $(this).data('transferred');

        // Set form action dynamically
        var baseUrl = "{{ url('admin/cheques') }}";
        $('#clearChequeForm').attr('action', baseUrl + '/' + id + '/mark-cleared');

        // Set subtitle in header
        $('#modal-cheque-subtitle').text('Cheque #' + number + ' — Rs. ' + amount);

        // Set info box
        $('#modal-cheque-info').text('Cheque #' + number + ' | Rs. ' + amount + ' | ' + party);

        // Set cash flow direction hint
        var flowText = '';
        var hintText = '';
        if (transferred == '1') {
            flowText = '↕ This is a transferred cheque — both a Cash IN (customer paid) and Cash OUT (supplier paid) will be recorded on the selected account.';
            hintText = 'Net effect on account balance = Rs. 0 (both legs shown for audit trail)';
            $('#modal-info-box').css({'background':'#eff6ff','border-color':'#bfdbfe'});
        } else if (type === 'received') {
            flowText = '↑ Cash IN — Rs. ' + amount + ' will be added to the selected account on the clearing date.';
            hintText = 'Money received from customer via cheque';
            $('#modal-info-box').css({'background':'#f0fdf4','border-color':'#bbf7d0'});
        } else {
            flowText = '↓ Cash OUT — Rs. ' + amount + ' will be deducted from the selected account on the clearing date.';
            hintText = 'Payment to supplier via cheque';
            $('#modal-info-box').css({'background':'#fff7ed','border-color':'#fed7aa'});
        }
        $('#modal-flow-info').text(flowText);
        $('#modal-account-hint').text(hintText);

        // Reset date to today
        $('#modal-clearing-date').val('{{ date("Y-m-d") }}');
        $('#modal-account-select').val('');
    });
});
</script>
@endpush
