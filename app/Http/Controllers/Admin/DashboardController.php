<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseEnrollment;
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
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
