@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tasks & Calendar</h1>
        <button class="btn btn-primary btn-sm" onclick="addTask()">
            <i class="fas fa-plus"></i> Add Task
        </button>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Calendar View</h6>
                </div>
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<style>
#calendar {
    max-width: 100%;
    margin: 0 auto;
}
.fc-event {
    cursor: pointer;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        editable: true,
        selectable: true,
        select: function(info) {
            addTask(info.startStr);
        },
        eventClick: function(info) {
            viewTask(info.event.id);
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            $.ajax({
                url: '{{ route("tasks.calendar-events") }}',
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
        }
    });
    calendar.render();
    
    window.calendarObj = calendar;
});

function addTask(date = null) {
    Swal.fire({
        title: 'Add New Task',
        html: `
            <div class="form-group text-left">
                <label>Title</label>
                <input id="task-title" class="swal2-input" placeholder="Task title" style="width: 100%;">
            </div>
            <div class="form-group text-left">
                <label>Description</label>
                <textarea id="task-desc" class="swal2-textarea" placeholder="Description" style="width: 100%;"></textarea>
            </div>
            <div class="form-group text-left">
                <label>Start Date</label>
                <input type="datetime-local" id="task-start" class="swal2-input" value="${date || new Date().toISOString().slice(0,16)}" style="width: 100%;">
            </div>
            <div class="form-group text-left">
                <label>Priority</label>
                <select id="task-priority" class="swal2-select" style="width: 100%;">
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Create',
        width: '600px',
        preConfirm: () => {
            return {
                title: document.getElementById('task-title').value,
                description: document.getElementById('task-desc').value,
                start_date: document.getElementById('task-start').value,
                priority: document.getElementById('task-priority').value,
                task_type: 'general'
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("tasks.store") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    ...result.value
                },
                success: function() {
                    Swal.fire('Success!', 'Task created', 'success');
                    window.calendarObj.refetchEvents();
                },
                error: function() {
                    Swal.fire('Error!', 'Failed to create task', 'error');
                }
            });
        }
    });
}

function viewTask(taskId) {
    const event = window.calendarObj.getEventById(taskId);
    if (!event) return;

    const props = event.extendedProps;
    let icon = 'info';
    if (props.task_type === 'cheque') icon = 'money-bill-alt';
    
    Swal.fire({
        title: event.title,
        html: `
            <div class="text-left">
                <p><strong>Status:</strong> <span class="badge badge-${props.status === 'completed' ? 'success' : 'warning'}">${props.status.toUpperCase()}</span></p>
                <p><strong>Priority:</strong> <span class="badge badge-${props.priority === 'urgent' || props.priority === 'high' ? 'danger' : 'info'}">${props.priority.toUpperCase()}</span></p>
                <p><strong>Description:</strong><br>${props.description || 'No description'}</p>
                <p><strong>Start:</strong> ${event.start.toLocaleString()}</p>
                ${event.end ? `<p><strong>End:</strong> ${event.end.toLocaleString()}</p>` : ''}
            </div>
        `,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Mark as Completed',
        cancelButtonText: 'Close',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            if (props.status === 'completed') return;
            return $.ajax({
                url: `/admin/tasks/${taskId}/complete`,
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' }
            });
        }
    }).then((result) => {
        if (result.isConfirmed && props.status !== 'completed') {
            Swal.fire('Completed!', 'Task has been marked as completed.', 'success');
            window.calendarObj.refetchEvents();
        }
    });
}
</script>
@endpush
