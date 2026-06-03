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
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<style>
#calendar {
    max-width: 100%;
    margin: 0 auto;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
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
    z-index: 1060;
}
.popover-header {
    background-color: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
    font-weight: 700;
    color: #1e293b;
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
        select: function(info) {
            addTask(info.startStr);
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
            if(info.event.extendedProps.isTask) {
                viewTask(info.event.id);
            }
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
    // Show task details
    Swal.fire({
        title: 'Task Details',
        text: 'Loading...',
        icon: 'info'
    });
}
</script>
@endpush
