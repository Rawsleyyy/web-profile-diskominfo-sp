<?php

namespace App\Helpers;

use App\Models\LogActivity;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    public static function log(
        string $subject,
        string $method,
        string $status = 'success',
        ?int $userId = null,
        ?string $description = null
    ): LogActivity {
        $ipAddress = Request::ip() ?? 'unknown';

        $activity = LogActivity::create([
            'user_id' => $userId,
            'subject' => $subject,
            'method' => strtoupper($method),
            'description' => $description,
            'ip_address' => $ipAddress,
            'status' => $status,
        ]);

        Log::channel('audit')->info('Admin activity', [
            'activity_id' => $activity->id,
            'user_id' => $userId,
            'subject' => $subject,
            'method' => strtoupper($method),
            'description' => $description,
            'ip_address' => $ipAddress,
            'status' => $status,
        ]);

        return $activity;
    }
}
