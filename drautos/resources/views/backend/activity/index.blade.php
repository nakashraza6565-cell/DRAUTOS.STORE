@extends('backend.layouts.master')
@section('title','Danyal Autos || Activity Logs')
@section('main-content')

<div class="container-fluid premium-bg" style="min-height: 100vh; padding: 2rem;">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
            <i class="fas fa-newspaper mr-2 text-primary"></i> Detailed System Activity Log
        </h1>
        <a href="{{route('admin')}}" class="btn btn-primary btn-sm shadow-sm font-weight-bold" style="border-radius: 10px;">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Back to Dashboard
        </a>
    </div>

    <!-- Activity Log Table -->
    <div class="premium-panel shadow-lg overflow-hidden border-0" style="border-radius: 20px;">
        <div class="panel-header bg-dark text-white p-4 d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold">Operational History</h5>
            <span class="badge badge-primary px-3 py-2" style="border-radius: 30px;">Total Logs: {{ $activities->total() }}</span>
        </div>
        <div class="table-responsive p-0" style="background: #fff;">
            <table class="table table-hover mb-0" id="activity-table">
                <thead class="bg-light">
                    <tr class="text-uppercase small font-weight-bold text-muted">
                        <th class="px-4 py-3 border-0">Timestamp</th>
                        <th class="py-3 border-0">User</th>
                        <th class="py-3 border-0">Action</th>
                        <th class="py-3 border-0">Details</th>
                        <th class="py-3 border-0 text-center">Reference</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $activity)
                    <tr style="transition: all 0.2s;">
                        <td class="px-4 py-3 align-middle">
                            <div class="font-weight-bold text-dark">{{ $activity->created_at->format('M d, Y') }}</div>
                            <div class="text-muted small">{{ $activity->created_at->format('h:i A') }}</div>
                        </td>
                        <td class="py-3 align-middle">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary-light text-primary rounded-circle mr-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: bold;">
                                    {{ substr($activity->user->name ?? 'A', 0, 1) }}
                                </div>
                                <span class="font-weight-600">{{ $activity->user->name ?? 'System' }}</span>
                            </div>
                        </td>
                        <td class="py-3 align-middle">
                            <span class="badge badge-pill py-2 px-3 {{ $activity->log_type == 'sale' ? 'badge-success' : ($activity->log_type == 'inventory' ? 'badge-info' : ($activity->log_type == 'price' ? 'badge-warning' : 'badge-secondary')) }}">
                                <i class="fas {{ $activity->icon }} mr-1"></i> {{ $activity->action }}
                            </span>
                        </td>
                        <td class="py-3 align-middle">
                            <p class="mb-0 text-gray-700" style="max-width: 400px; font-size: 0.95rem;">{!! $activity->description !!}</p>
                        </td>
                        <td class="py-3 align-middle text-center">
                            @if($activity->link)
                                <a href="{{ $activity->link }}" class="btn btn-circle btn-sm btn-outline-primary" title="View Related Record">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            @else
                                <span class="text-muted small">N/A</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <img src="{{asset('backend/img/empty.svg')}}" alt="No Activity" style="width: 150px; opacity: 0.3;">
                            <h5 class="mt-4 text-muted">No activity logs found.</h5>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="panel-footer bg-light p-4 d-flex justify-content-center">
            {{ $activities->links() }}
        </div>
    </div>
</div>

<style>
    .premium-bg {
        background-color: #f8fafc;
        background-image: radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%);
        background-blend-mode: overlay;
    }
    .font-weight-600 { font-weight: 600; }
    .bg-primary-light { background: rgba(99, 102, 241, 0.1); }
    #activity-table tr:hover {
        background-color: #f1f5f9;
        transform: scale(1.002);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    .badge-pill { font-weight: 700; letter-spacing: 0.5px; }
    .pagination { margin-bottom: 0; }
    .page-item.active .page-link { background-color: #1e293b; border-color: #1e293b; }
</style>

@endsection
