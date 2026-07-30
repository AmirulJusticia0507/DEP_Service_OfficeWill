<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseTodo;
use App\Models\CourseTodoResponse;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $operator = Auth::guard('employee')->user();
        $companyId = $operator->company_id;

        $stats = [
            'total_employees' => Employee::where('company_id', $companyId)->count(),
            'total_courses' => Course::count(),
            'active_enrollments' => CourseEnrollment::whereHas('employee', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })->where('status', 'ENROLLED')->count(),
            'completed_enrollments' => CourseEnrollment::whereHas('employee', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })->where('status', 'COMPLETED')->count(),
            'certificates_issued' => Certificate::whereHas('employee', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })->count(),
        ];

        // Course completion rates
        $courses = Course::withCount(['enrollments as total_enrolled' => function ($q) use ($companyId) {
            $q->whereHas('employee', fn($eq) => $eq->where('company_id', $companyId));
        }, 'enrollments as completed_count' => function ($q) use ($companyId) {
            $q->where('status', 'COMPLETED')->whereHas('employee', fn($eq) => $eq->where('company_id', $companyId));
        }])->limit(10)->get();

        $chartLabels = $courses->pluck('course_name')->map(fn($n) => \Illuminate\Support\Str::limit($n, 20))->toJson();
        $chartEnrolled = $courses->pluck('total_enrolled')->toJson();
        $chartCompleted = $courses->pluck('completed_count')->toJson();

        // Monthly completions (last 6 months)
        $monthly = collect(range(5, 0))->map(function ($i) use ($companyId) {
            $date = now()->subMonths($i);
            $count = CourseEnrollment::where('status', 'COMPLETED')
                ->whereHas('employee', fn($q) => $q->where('company_id', $companyId))
                ->whereYear('updated_at', $date->year)
                ->whereMonth('updated_at', $date->month)
                ->count();
            return [
                'month' => $date->format('M Y'),
                'count' => $count,
            ];
        });

        $monthlyLabels = $monthly->pluck('month')->toJson();
        $monthlyCounts = $monthly->pluck('count')->toJson();

        return view('admin.dashboard', compact(
            'stats', 'chartLabels', 'chartEnrolled', 'chartCompleted',
            'monthlyLabels', 'monthlyCounts'
        ));
    }
}
