<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $operator = Auth::guard('employee')->user();

        $query = ActivityLog::with('employee')
            ->where('company_id', $operator->company_id);

        if ($search = trim((string) $request->get('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'LIKE', "%{$search}%")
                    ->orWhere('action', 'LIKE', "%{$search}%")
                    ->orWhere('ip_address', 'LIKE', "%{$search}%")
                    ->orWhereHas('employee', fn ($e) => $e->where('full_name', 'LIKE', "%{$search}%"));
            });
        }

        if ($action = $request->get('action')) {
            $query->where('action', $action);
        }

        $logs = $query->orderByDesc('created_at')->paginate(30)->withQueryString();

        $actions = ActivityLog::where('company_id', $operator->company_id)
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('admin.logs.index', compact('logs', 'actions'));
    }
}
