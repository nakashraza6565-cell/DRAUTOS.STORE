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
                    <table class="table table-hover mb-0" id="cheque-table" width="100%" cellspacing="0" style="font-size: 0.9rem;">
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
                                <td class="align-middle"><strong>{{$cheque->cheque_number}}</strong></td>
                                <td class="align-middle">
                                    <span class="badge badge-pill badge-{{ $cheque->type == 'received' ? 'success' : 'danger' }} px-3 py-1">
                                        {{ strtoupper($cheque->type) }}
                                    </span>
                                </td>
                                <td class="align-middle font-weight-bold text-gray-700">{{$cheque->party->name ?? 'N/A'}}</td>
                                <td class="align-middle font-weight-bold">Rs. {{number_format($cheque->amount, 2)}}</td>
                                <td class="align-middle text-gray-500">{{$cheque->cheque_date->format('d M Y')}}</td>
                                <td class="align-middle font-weight-bold text-primary">{{$cheque->clearing_date->format('d M Y')}}</td>
                                <td class="align-middle small">{{$cheque->bank_name ?: '-'}}</td>
                                <td class="align-middle text-center">
                                    @if($cheque->status == 'pending')
                                        <span class="badge badge-warning" style="border-radius:6px; font-weight: 600;">PENDING</span>
                                    @elseif($cheque->status == 'cleared')
                                        <span class="badge badge-success" style="border-radius:6px; font-weight: 600;">CLEARED</span>
                                    @elseif($cheque->status == 'bounced')
                                        <span class="badge badge-danger" style="border-radius:6px; font-weight: 600;">BOUNCED</span>
                                    @else
                                        <span class="badge badge-secondary" style="border-radius:6px; font-weight: 600;">CANCELLED</span>
                                    @endif
                                </td>
                                <td class="align-middle text-center">
                                    <div class="btn-group shadow-sm" style="border-radius: 10px; overflow: hidden;">
                                        <a href="{{route('cheques.show',$cheque->id)}}" class="btn btn-sm btn-info border-0" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($cheque->status == 'pending')
                                        <form method="POST" action="{{route('cheques.mark-cleared',$cheque->id)}}" style="display:inline;" onsubmit="return confirm('Mark this cheque as cleared?')">
                                        @csrf
                                        <button class="btn btn-sm btn-success border-0" title="Mark Cleared">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        </form>
                                        <form method="POST" action="{{route('cheques.mark-bounced',$cheque->id)}}" style="display:inline;" onsubmit="return confirm('Mark this cheque as bounced?')">
                                        @csrf
                                        <button class="btn btn-sm btn-danger border-0" title="Mark Bounced">
                                            <i class="fas fa-times-circle"></i>
                                        </button>
                                        </form>
                                        @endif
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
});
</script>
@endpush
