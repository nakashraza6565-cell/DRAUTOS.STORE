<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display a listing of employee profiles for attendance tracking.
     */
    public function index()
    {
        $staff = User::whereIn('role', ['admin', 'manager', 'staff'])->orderBy('id', 'ASC')->get();
        return view('backend.attendance.index')->with('staff', $staff);
    }

    /**
     * Show the attendance detail for a specific employee.
     */
    public function show(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $start_date = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $end_date = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $query = Attendance::where('user_id', $id)
            ->whereBetween('date', [$start_date, $end_date])
            ->orderBy('date', 'DESC');

        $attendances = $query->get();

        // Calculate hours
        $total_worked_hours = $attendances->sum('total_hours');
        $total_overtime_hours = $attendances->sum('overtime_hours');

        return view('backend.attendance.show', compact('user', 'attendances', 'start_date', 'end_date', 'total_worked_hours', 'total_overtime_hours'));
    }

    public function create()
    {
        $staff = User::whereIn('role', ['admin', 'manager', 'staff'])->get();
        return view('backend.attendance.create', compact('staff'));
    }

    /**
     * Manual Entry by Admin
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'clock_in' => 'nullable',
            'clock_out' => 'nullable',
            'status' => 'required|in:present,absent,late,on_leave',
            'notes' => 'nullable|string'
        ]);

        $data = $request->all();
        $data['is_manual'] = true;

        if ($request->clock_in && $request->clock_out) {
            $in = Carbon::parse($request->clock_in);
            $out = Carbon::parse($request->clock_out);
            $total_minutes = $out->diffInMinutes($in);
            $total_hours = round($total_minutes / 60, 2);
            $data['total_hours'] = $total_hours;
            
            // Overtime logic: if more than 9 hours
            if ($total_hours > 9) {
                $data['overtime_hours'] = $total_hours - 9;
            } else {
                $data['overtime_hours'] = 0;
            }
        }

        $status = Attendance::create($data);
        
        if ($status) {
            request()->session()->flash('success', 'Attendance record created successfully');
        } else {
            request()->session()->flash('error', 'Error occurred!');
        }

        return redirect()->route('attendance.show', $request->user_id);
    }

    public function checkIn(Request $request) 
    {
        $user_id = $request->staff_id ?? Auth::id(); // Support admin doing it for staff
        
        $exists = Attendance::where('user_id', $user_id)->where('date', date('Y-m-d'))->first();
        if($exists) {
            return back()->with('error', 'Already attendance recorded for today!');
        }

        $now = date('H:i:s');
        $status = ($now > '09:30:00') ? 'late' : 'present';

        Attendance::create([
            'user_id' => $user_id,
            'date' => date('Y-m-d'),
            'clock_in' => $now,
            'status' => $status,
            'is_manual' => Auth::id() != $user_id // it's manual if someone else does it
        ]);

        return back()->with('success', 'Checked In Successfully at ' . $now);
    }

    public function checkOut(Request $request)
    {
        $user_id = $request->staff_id ?? Auth::id();
        $attendance = Attendance::where('user_id', $user_id)->where('date', date('Y-m-d'))->first();
        
        if(!$attendance) {
            return back()->with('error', 'No check-in record found for today!');
        }

        $now = date('H:i:s');
        $in = Carbon::parse($attendance->clock_in);
        $out = Carbon::parse($now);
        $total_minutes = $out->diffInMinutes($in);
        $total_hours = round($total_minutes / 60, 2);
        
        $overtime = 0;
        if($total_hours > 9) {
            $overtime = $total_hours - 9;
        }

        $attendance->update([
            'clock_out' => $now,
            'total_hours' => $total_hours,
            'overtime_hours' => $overtime
        ]);

        return back()->with('success', 'Checked Out Successfully');
    }

    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);
        
        $this->validate($request, [
            'date' => 'required|date',
            'clock_in' => 'nullable',
            'clock_out' => 'nullable',
            'status' => 'required|in:present,absent,late,on_leave',
            'notes' => 'nullable|string'
        ]);

        $data = $request->all();

        if ($request->clock_in && $request->clock_out) {
            $in = Carbon::parse($request->clock_in);
            $out = Carbon::parse($request->clock_out);
            $total_minutes = $out->diffInMinutes($in);
            $total_hours = round($total_minutes / 60, 2);
            $data['total_hours'] = $total_hours;
            
            // Overtime logic
            if ($total_hours > 9) {
                $data['overtime_hours'] = $total_hours - 9;
            } else {
                $data['overtime_hours'] = 0;
            }
        } else {
            $data['total_hours'] = 0;
            $data['overtime_hours'] = 0;
        }

        $status = $attendance->update($data);
        
        if ($status) {
            request()->session()->flash('success', 'Attendance record updated successfully');
        } else {
            request()->session()->flash('error', 'Error occurred!');
        }

        return redirect()->route('attendance.show', $attendance->user_id);
    }

    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);
        $user_id = $attendance->user_id;
        $status = $attendance->delete();
        
        if ($status) {
            request()->session()->flash('success', 'Attendance record deleted');
        } else {
            request()->session()->flash('error', 'Error occurred!');
        }
        
        return redirect()->route('attendance.show', $user_id);
    }

    public function exportCSV(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        $attendances = Attendance::where('user_id', $id)
            ->whereBetween('date', [$start_date, $end_date])
            ->orderBy('date', 'ASC')
            ->get();

        $filename = "attendance_" . str_replace(' ', '_', $user->name) . "_" . $start_date . "_to_" . $end_date . ".csv";
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Header
        fputcsv($handle, ['Date', 'Clock In', 'Clock Out', 'Total Hours', 'Overtime Hours', 'Status', 'Manual', 'Notes']);

        foreach ($attendances as $row) {
            fputcsv($handle, [
                $row->date,
                $row->clock_in,
                $row->clock_out,
                $row->total_hours,
                $row->overtime_hours,
                $row->status,
                $row->is_manual ? 'Yes' : 'No',
                $row->notes
            ]);
        }

        fclose($handle);
        exit();
    }
}
