<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LogActivity;

class LogActivityController extends Controller
{
    // GET /api/logs — cuma buat Super Admin, butuh token
    public function index()
    {
        $logs = LogActivity::latest()->paginate(20);
        return response()->json($logs);
    }
}
