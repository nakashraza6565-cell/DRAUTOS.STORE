<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function index() {
        return view('backend.hr.commission');
    }
}
