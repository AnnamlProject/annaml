<?php

namespace App\Http\Controllers;

use App\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    //
    public function index()
    {
        $data = ActivityLog::with('user')->get();
        return view('activity_log.index', compact('data'));
    }
}
