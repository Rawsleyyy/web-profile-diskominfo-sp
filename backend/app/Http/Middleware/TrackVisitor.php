<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\VisitorLog;

class TrackVisitor
{
    public function handle(Request $request, Closure $next)
    {
        // Hanya catat jika BUKAN request admin atau AJAX
        if (!$request->is('admin*') && !$request->ajax()) {
            VisitorLog::firstOrCreate([
                'ip_address' => $request->ip(),
                'visit_date' => now()->toDateString(),
            ]);
        }

        return $next($request);
    }
}
