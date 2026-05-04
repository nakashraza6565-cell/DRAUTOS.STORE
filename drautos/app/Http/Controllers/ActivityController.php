<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = ActivityLog::with('user')
            ->orderBy('created_at', 'DESC')
            ->paginate(50);
            
        return view('backend.activity.index')->with('activities', $activities);
    }
}
