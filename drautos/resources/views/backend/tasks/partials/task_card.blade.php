<div class="card shadow-sm mb-3 task-card border-0 hover-shadow transition" 
     data-id="{{ $task->id }}" 
     data-status="{{ $task->status }}" 
     data-priority="{{ $task->priority }}"
     data-today="{{ $task->status == 'completed' && $task->updated_at >= today() ? '1' : '0' }}"
     style="border-left: 4px solid {{ $task->color ?? '#4e73df' }} !important; border-radius: 8px;">
    <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <span class="badge badge-{{ $task->priority == 'urgent' ? 'danger' : ($task->priority == 'high' ? 'warning' : ($task->priority == 'medium' ? 'info' : 'success')) }} text-uppercase px-2" style="font-size: 9px; letter-spacing: 0.5px;">
                {{ $task->priority }}
            </span>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-xs fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in border-0">
                    @if($task->status != 'completed')
                        <a class="dropdown-item text-success small font-weight-bold" href="javascript:void(0)" onclick="completeTask({{ $task->id }})">
                            <i class="fas fa-check-circle mr-2"></i> Mark Complete
                        </a>
                    @endif
                    <a class="dropdown-item text-danger small font-weight-bold" href="javascript:void(0)" onclick="deleteTask({{ $task->id }})">
                        <i class="fas fa-trash-alt mr-2"></i> Delete Task
                    </a>
                </div>
            </div>
        </div>
        
        <h6 class="font-weight-bold text-dark mb-1" style="font-size: 14px; line-height: 1.4;">{{ $task->title }}</h6>
        @if($task->description)
            <p class="small text-muted mb-3" style="font-size: 11px;">{{ Str::limit($task->description, 70) }}</p>
        @else
            <div class="mb-3"></div>
        @endif
        
        <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top">
            <div class="d-flex align-items-center">
                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mr-2 shadow-sm" 
                     style="width: 24px; height: 24px; border: 1.5px solid #fff;" 
                     title="Assigned to: {{ $task->assignee->name ?? 'Unassigned' }}">
                    <span class="small font-weight-bold text-white" style="font-size: 10px;">
                        {{ $task->assignee ? strtoupper(substr($task->assignee->name, 0, 1)) : '?' }}
                    </span>
                </div>
                <span class="text-uppercase font-weight-bold text-gray-500" style="font-size: 9px;">{{ $task->task_type }}</span>
            </div>
            <div class="small {{ $task->end_date && $task->end_date->isPast() && $task->status != 'completed' ? 'text-danger font-weight-bold' : 'text-muted' }}" style="font-size: 10px;">
                <i class="far fa-calendar-alt mr-1"></i> {{ $task->start_date->format('d M') }}
            </div>
        </div>
    </div>
</div>

<style>
    .task-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        cursor: pointer;
    }
    .task-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    .transition {
        transition: all 0.3s ease;
    }
</style>
