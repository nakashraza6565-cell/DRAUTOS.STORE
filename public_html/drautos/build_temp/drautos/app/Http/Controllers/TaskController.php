<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskReminder;
use App\User;
use App\Models\Cheque;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display tasks and calendar
     */
    public function index()
    {
        $tasks = Task::with(['assignee', 'creator', 'related'])
            ->where(function($query) {
                $query->where('created_by', Auth::id())
                      ->orWhere('assigned_to', Auth::id());
            })
            ->orderBy('start_date', 'desc')
            ->paginate(5000);

        $users = User::whereIn('role', ['admin', 'staff'])->orderBy('name', 'ASC')->get();
        return view('backend.tasks.index', compact('tasks', 'users'));
    }

    /**
     * Calendar view
     */
    public function calendar()
    {
        return view('backend.tasks.calendar');
    }

    /**
     * Store new task
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'priority' => 'required|in:low,medium,high,urgent',
            'task_type' => 'required|in:general,cheque,payment,delivery,meeting,other',
            'assigned_to' => 'nullable|exists:users,id',
            'all_day' => 'nullable|boolean',
            'color' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'pending';
        $validated['all_day'] = $request->has('all_day');

        $task = Task::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully',
            'task' => $task
        ]);
    }

    /**
     * Update task
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'status' => 'nullable|in:pending,in_progress,completed,cancelled',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $task->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully'
        ]);
    }

    /**
     * Mark task as completed
     */
    public function markCompleted(Task $task)
    {
        $task->update(['status' => 'completed']);

        return response()->json([
            'success' => true,
            'message' => 'Task marked as completed'
        ]);
    }

    /**
     * Get calendar events
     */
    public function getCalendarEvents(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');

        $tasks = Task::whereBetween('start_date', [$start, $end])
            ->where(function($query) {
                $query->where('created_by', Auth::id())
                      ->orWhere('assigned_to', Auth::id());
            })
            ->with(['assignee', 'related'])
            ->get();

        $events = [];
        foreach ($tasks as $task) {
            $events[] = [
                'id' => $task->id,
                'title' => $task->title,
                'start' => $task->start_date->format('Y-m-d H:i:s'),
                'end' => $task->end_date ? $task->end_date->format('Y-m-d H:i:s') : null,
                'allDay' => $task->all_day,
                'color' => $task->color,
                'extendedProps' => [
                    'description' => $task->description,
                    'priority' => $task->priority,
                    'status' => $task->status,
                    'task_type' => $task->task_type,
                    'assignee' => $task->assignee->name ?? 'Unassigned',
                ],
            ];
        }

        return response()->json($events);
    }

    /**
     * Get pending tasks for dashboard
     */
    public function getPendingTasks()
    {
        $tasks = Task::pending()
            ->where(function($query) {
                $query->where('created_by', Auth::id())
                      ->orWhere('assigned_to', Auth::id());
            })
            ->with(['assignee'])
            ->orderBy('start_date')
            ->limit(10)
            ->get();

        return response()->json($tasks);
    }

    /**
     * Get today's tasks
     */
    public function getTodayTasks()
    {
        $tasks = Task::today()
            ->where(function($query) {
                $query->where('created_by', Auth::id())
                      ->orWhere('assigned_to', Auth::id());
            })
            ->with(['assignee'])
            ->get();

        return response()->json($tasks);
    }

    /**
     * Delete task
     */
    public function destroy(Task $task)
    {
        // Check authorization
        if ($task->created_by !== Auth::id() && !Auth::user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully'
        ]);
    }
}
