@extends('backend.layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="row">
        <div class="col-md-12">
           @include('backend.layouts.notification')
        </div>
    </div>
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">All Tasks</h6>
        <div>
            <a href="{{ route('tasks.calendar') }}" class="btn btn-info btn-sm shadow-sm mr-2" data-toggle="tooltip" title="Calendar View"><i class="fas fa-calendar-alt"></i> Calendar View</a>
            <button class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#addTaskModal"><i class="fas fa-plus"></i> Add Task</button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="task-dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Start Date</th>
                        <th>Due Date</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Assigned To</th>
                        <th>Type</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $task)
                    <tr>
                        <td style="border-left: 4px solid {{ $task->color ?? '#4e73df' }};">{{ $task->title }}</td>
                        <td>{{ $task->start_date->format('d M Y H:i') }}</td>
                        <td>{{ $task->end_date ? $task->end_date->format('d M Y H:i') : '-' }}</td>
                        <td>
                            <span class="badge badge-{{ $task->priority == 'high' ? 'danger' : ($task->priority == 'medium' ? 'warning' : 'success') }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $task->status == 'completed' ? 'success' : ($task->status == 'pending' ? 'secondary' : 'primary') }}">
                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user-circle text-gray-400 mr-2"></i>
                                <span>{{ $task->assignee->name ?? 'Unassigned' }}</span>
                            </div>
                        </td>
                        <td>{{ ucfirst($task->task_type) }}</td>
                        <td>
                            @if($task->status != 'completed')
                            <button class="btn btn-success btn-sm btn-circle" onclick="completeTask({{ $task->id }})" title="Mark Completed">
                                <i class="fas fa-check"></i>
                            </button>
                            @endif
                            <form method="POST" action="{{ route('tasks.destroy', [$task->id]) }}" class="d-inline">
                                @csrf 
                                @method('delete')
                                <button class="btn btn-danger btn-sm btn-circle" data-id="{{ $task->id }}" title="Delete" data-toggle="tooltip">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <span style="float:right">{{ $tasks->links() }}</span>
        </div>
    </div>
</div>

<!-- Add Task Modal -->
<div class="modal fade" id="addTaskModal" tabindex="-1" role="dialog" aria-labelledby="addTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTaskModalLabel">Add New Task</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addTaskForm">
                <div class="modal-body">
                    @csrf
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Start Date & Time</label>
                                <input type="datetime-local" name="start_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>End Date & Time</label>
                                <input type="datetime-local" name="end_date" class="form-control">
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
<link href="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.1.9/sweetalert2.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css">
<style>
    .select2-container--bootstrap4 .select2-selection--single {
        height: calc(1.5em + 0.75rem + 2px) !important;
    }
</style>
@endpush

@push('scripts')
<script src="{{asset('backend/vendor/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.1.9/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%',
            dropdownParent: $('#addTaskModal')
        });
    });
    $('#task-dataTable').DataTable({
        "columnDefs": [
            {
                "orderable": false,
                "targets": [7]
            }
        ],
        "paging": false // Since pagination is handled by Laravel
    });

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.dltBtn').click(function(e) {
            var form = $(this).closest('form');
            var dataID = $(this).data('id');
            e.preventDefault();
            Swal.fire({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this task!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
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
                        title: 'Success',
                        text: 'Task created successfully'
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(err) {
                    submitBtn.prop('disabled', false).html('Save Task');
                    console.error('Task error:', err);
                    var msg = 'Failed to create task';
                    if(err.responseJSON && err.responseJSON.message) {
                        msg = err.responseJSON.message;
                    }
                    if(err.responseJSON && err.responseJSON.errors) {
                        msg = Object.values(err.responseJSON.errors).flat().join('<br>');
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: msg
                    });
                }
            });
        });
    });

    function completeTask(id) {
        Swal.fire({
            title: 'Mark Completed?',
            text: "This will mark the task as completed",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/tasks/${id}/complete`,
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function() {
                        location.reload();
                    },
                    error: function() {
                        Swal.fire('Error', 'Action failed', 'error');
                    }
                });
            }
        });
    }
</script>
@endpush
