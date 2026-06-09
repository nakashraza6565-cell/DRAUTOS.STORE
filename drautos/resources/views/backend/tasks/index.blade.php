@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid p-0">
    {{-- GAMIFIED KPI DASHBOARD --}}
    @php
        $total_active = $stats['pending'] + $stats['in_progress'];
        $total_all = $total_active + $stats['completed_today'];
        $completion_rate = $total_all > 0 ? round(($stats['completed_today'] / $total_all) * 100) : 0;
    @endphp
    
    <div class="row mb-4">
        <!-- Circular Progress KPI -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow border-0 h-100 kpi-glass-card">
                <div class="card-body text-center d-flex flex-column justify-content-center align-items-center">
                    <h6 class="font-weight-bold text-uppercase text-secondary mb-3"><i class="fas fa-trophy text-warning mr-2"></i>Daily Goal Progress</h6>
                    <div class="circular-progress position-relative" style="width: 120px; height: 120px; border-radius: 50%; background: conic-gradient(#1cc88a {{ $completion_rate }}%, #eaecf4 0);">
                        <div class="inner-circle position-absolute bg-white d-flex align-items-center justify-content-center shadow-sm" style="width: 100px; height: 100px; border-radius: 50%; top: 10px; left: 10px;">
                            <h3 class="m-0 font-weight-bold text-dark">{{ $completion_rate }}%</h3>
                        </div>
                    </div>
                    <p class="mt-3 mb-0 small text-muted">You've completed <span class="font-weight-bold text-success">{{ $stats['completed_today'] }}</span> tasks today!</p>
                </div>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="col-xl-8 col-md-12 mb-4">
            <div class="row h-100">
                <div class="col-md-6 mb-4 metric-card" data-filter="pending" style="cursor: pointer;">
                    <div class="card border-left-primary shadow h-100 py-2 transition hover-card kpi-glass-card">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Pending Tasks</div>
                                    <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $stats['pending'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-list fa-3x text-primary" style="opacity: 0.2;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4 metric-card" data-filter="in_progress" style="cursor: pointer;">
                    <div class="card border-left-info shadow h-100 py-2 transition hover-card kpi-glass-card">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">In Progress</div>
                                    <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $stats['in_progress'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-spinner fa-3x text-info" style="opacity: 0.2;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4 mb-md-0 metric-card" data-filter="high_priority" style="cursor: pointer;">
                    <div class="card border-left-danger shadow h-100 py-2 transition hover-card kpi-glass-card">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">High Priority</div>
                                    <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $stats['high_priority'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-fire fa-3x text-danger" style="opacity: 0.2;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 metric-card" data-filter="completed_today" style="cursor: pointer;">
                    <div class="card border-left-success shadow h-100 py-2 transition hover-card kpi-glass-card bg-success text-white">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-uppercase mb-1">Completed Today</div>
                                    <div class="h3 mb-0 font-weight-bold">{{ $stats['completed_today'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-3x text-white" style="opacity: 0.3;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KANBAN BOARD --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center">
            <h5 class="font-weight-bold text-dark m-0 mr-3">Business Task Board</h5>
            <div id="filter-indicator" style="display: none;">
                <span class="badge badge-pill badge-dark py-2 px-3">
                    <i class="fas fa-filter mr-1"></i> <span id="filter-text"></span>
                    <i class="fas fa-times ml-2" id="clear-filter" style="cursor: pointer;"></i>
                </span>
            </div>
        </div>
        <div>
            <a href="{{ route('tasks.calendar') }}" class="btn btn-outline-info btn-sm shadow-sm mr-2"><i class="fas fa-calendar-alt mr-1"></i> Calendar View</a>
            <button class="btn btn-primary btn-sm shadow-sm px-3" data-toggle="modal" data-target="#addTaskModal"><i class="fas fa-plus mr-1"></i> NEW TASK</button>
        </div>
    </div>

    <div class="kanban-container row flex-nowrap" style="overflow-x: auto; padding-bottom: 20px;">
        {{-- COLUMN: TO DO --}}
        <div class="col-md-4">
            <div class="kanban-column bg-light p-3 rounded shadow-sm border-top border-secondary" style="min-height: 70vh;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="font-weight-bold text-uppercase small m-0"><i class="fas fa-clock mr-2 text-secondary"></i> TO DO</h6>
                    <span class="badge badge-secondary badge-pill">{{ $tasks->where('status', 'pending')->count() }}</span>
                </div>
                <div class="task-list" id="pending-tasks">
                    @foreach($tasks->where('status', 'pending') as $task)
                        @include('backend.tasks.partials.task_card', ['task' => $task])
                    @endforeach
                </div>
            </div>
        </div>

        {{-- COLUMN: IN PROGRESS --}}
        <div class="col-md-4">
            <div class="kanban-column bg-light p-3 rounded shadow-sm border-top border-primary" style="min-height: 70vh; background-color: #f0f4ff !important;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="font-weight-bold text-uppercase small m-0 text-primary"><i class="fas fa-running mr-2"></i> IN PROGRESS</h6>
                    <span class="badge badge-primary badge-pill">{{ $tasks->where('status', 'in_progress')->count() }}</span>
                </div>
                <div class="task-list" id="in-progress-tasks">
                    @foreach($tasks->where('status', 'in_progress') as $task)
                        @include('backend.tasks.partials.task_card', ['task' => $task])
                    @endforeach
                </div>
            </div>
        </div>

        {{-- COLUMN: COMPLETED --}}
        <div class="col-md-4">
            <div class="kanban-column bg-light p-3 rounded shadow-sm border-top border-success" style="min-height: 70vh; background-color: #f6fff9 !important;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="font-weight-bold text-uppercase small m-0 text-success"><i class="fas fa-check-double mr-2"></i> COMPLETED</h6>
                    <span class="badge badge-success badge-pill">{{ $tasks->where('status', 'completed')->count() }}</span>
                </div>
                <div class="task-list" id="completed-tasks">
                    @foreach($tasks->where('status', 'completed') as $task)
                        @include('backend.tasks.partials.task_card', ['task' => $task])
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Original Task Modals... --}}
<div class="modal fade" id="addTaskModal" tabindex="-1" role="dialog" aria-labelledby="addTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold" id="addTaskModalLabel">Create New Operational Task</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addTaskForm">
                <div class="modal-body bg-light">
                    @csrf
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="small font-weight-bold text-uppercase">Task Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control form-control-lg shadow-sm" placeholder="What needs to be done?" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold text-uppercase">Task Type</label>
                                <select name="task_type" class="form-control shadow-sm" required>
                                    <option value="general">General Office</option>
                                    <option value="meeting">Client Meeting</option>
                                    <option value="payment">Accounts / Payment</option>
                                    <option value="delivery">Logistics / Delivery</option>
                                    <option value="cheque">Cheque Handling</option>
                                    <option value="other">Other Task</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Priority</label>
                                <select name="priority" class="form-control" required>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Task Type</label>
                                <select name="task_type" class="form-control" required>
                                    <option value="general">General</option>
                                    <option value="meeting">Meeting</option>
                                    <option value="payment">Payment</option>
                                    <option value="delivery">Delivery</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Assign To (Staff)</label>
                                <select name="assigned_to" class="form-control select2">
                                    <option value="">-- Unassigned --</option>
                                    @foreach($users as $user)
                                        <option value="{{$user->id}}">{{$user->name}} ({{ucfirst($user->role)}})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Color</label>
                        <input type="color" name="color" class="form-control" value="#4e73df">
                    </div>
                    <input type="hidden" name="all_day" value="0">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Task</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.1.9/sweetalert2.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css">
<style>
    .select2-container--bootstrap4 .select2-selection--single {
        height: calc(1.5em + 0.75rem + 2px) !important;
    }
    .kanban-container::-webkit-scrollbar {
        height: 8px;
    }
    .kanban-container::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    .kanban-container::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 10px;
    }
    .kanban-column {
        transition: background-color 0.2s ease;
    }
    .sortable-ghost {
        opacity: 0.4;
        background-color: #e2e6ea !important;
    }
    .sortable-drag {
        transform: rotate(2deg);
    }
    /* Modern Gamified UI Styles */
    .kpi-glass-card {
        background: rgba(255, 255, 255, 0.8) !important;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 15px;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.05) !important;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .kpi-glass-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.1) !important;
    }
    .circular-progress {
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: inset 0 0 10px rgba(0,0,0,0.1);
        transition: all 1s ease-in-out;
    }
    .circular-progress:hover {
        transform: scale(1.05);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.1.9/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%',
            dropdownParent: $('#addTaskModal')
        });

        // Initialize Sortable for each column
        ['pending-tasks', 'in-progress-tasks', 'completed-tasks'].forEach(id => {
            new Sortable(document.getElementById(id), {
                group: 'tasks',
                animation: 150,
                ghostClass: 'sortable-ghost',
                dragClass: 'sortable-drag',
                onEnd: function (evt) {
                    // Extract ID from the card (we'll add a data-id attribute to the partial)
                    let taskId = evt.item.getAttribute('data-id');
                    
                    let newStatus = '';
                    if(evt.to.id === 'pending-tasks') newStatus = 'pending';
                    if(evt.to.id === 'in-progress-tasks') newStatus = 'in_progress';
                    if(evt.to.id === 'completed-tasks') newStatus = 'completed';
                    
                    if(evt.from.id !== evt.to.id) {
                        updateTaskStatus(taskId, newStatus);
                    }
                }
            });
        });

        // Handle Metric Card Clicks
        $('.metric-card').click(function() {
            let filter = $(this).data('filter');
            $('.task-card').hide();
            
            if (filter === 'pending') {
                $('.task-card[data-status="pending"]').fadeIn();
            } else if (filter === 'in_progress') {
                $('.task-card[data-status="in_progress"]').fadeIn();
            } else if (filter === 'high_priority') {
                $('.task-card[data-priority="high"], .task-card[data-priority="urgent"]').fadeIn();
            } else if (filter === 'completed_today') {
                $('.task-card[data-today="1"]').fadeIn();
            }
            
            $('#filter-text').text($(this).find('.text-uppercase').text());
            $('#filter-indicator').fadeIn();
            
            // Scroll to top of board
            $('html, body').animate({
                scrollTop: $(".kanban-container").offset().top - 100
            }, 500);
        });

        // Clear Filter
        $('#clear-filter').click(function() {
            $('.task-card').fadeIn();
            $('#filter-indicator').fadeOut();
        });

        // Handle Add Task Form
        $('#addTaskForm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var submitBtn = form.find('button[type="submit"]');
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
            
            $.ajax({
                url: "{{ route('tasks.store') }}",
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    $('#addTaskModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Task Created!',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(err) {
                    submitBtn.prop('disabled', false).html('Save Task');
                    Swal.fire({ icon: 'error', title: 'Oops...', text: 'Failed to create task.' });
                }
            });
        });
    });

    function updateTaskStatus(id, status) {
        $.ajax({
            url: `/admin/tasks/${id}`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                _method: 'PATCH',
                status: status
            },
            success: function() {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                });
                Toast.fire({
                    icon: 'success',
                    title: 'Status Updated'
                });
            },
            error: function() {
                Swal.fire('Error', 'Failed to update task status', 'error').then(() => location.reload());
            }
        });
    }

    function completeTask(id) {
        Swal.fire({
            title: 'Complete Task?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Complete'
        }).then((result) => {
            if (result.isConfirmed) {
                updateTaskStatus(id, 'completed');
                setTimeout(() => location.reload(), 1000);
            }
        });
    }

    function deleteTask(id) {
        Swal.fire({
            title: "Delete Task?",
            text: "This action cannot be undone!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/tasks/${id}`,
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
                    success: function() {
                        location.reload();
                    }
                });
            }
        });
    }
</script>
@endpush
