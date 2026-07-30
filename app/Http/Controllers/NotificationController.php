<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        $employee = Auth::guard('employee')->user();
        $notifications = Notification::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(fn($n) => [
                'id' => $n->id,
                'type' => $n->type,
                'title' => $n->title,
                'message' => $n->message,
                'link' => $n->link,
                'is_read' => $n->is_read,
                'created_at' => $n->created_at->diffForHumans(),
            ]);

        return response()->json($notifications);
    }

    public function markRead(Notification $notification): JsonResponse
    {
        $employee = Auth::guard('employee')->user();
        if ($notification->employee_id !== $employee->id) {
            abort(403);
        }
        $notification->update(['is_read' => true, 'read_at' => now()]);
        return response()->json(['success' => true]);
    }

    public function markAllRead(): JsonResponse
    {
        $employee = Auth::guard('employee')->user();
        Notification::where('employee_id', $employee->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
        return response()->json(['success' => true]);
    }

    public function unreadCount(): JsonResponse
    {
        $employee = Auth::guard('employee')->user();
        $count = Notification::where('employee_id', $employee->id)
            ->where('is_read', false)
            ->count();
        return response()->json(['count' => $count]);
    }
}
