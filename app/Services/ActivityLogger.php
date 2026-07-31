<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log(string $action, string $description, ?Model $subject = null, array $metadata = []): ActivityLog
    {
        $actor = Auth::guard('employee')->user();

        return ActivityLog::create([
            'employee_id' => $actor?->id,
            'company_id' => $actor?->company_id,
            'action' => $action,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'description' => $description,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
        ]);
    }
}
