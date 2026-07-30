<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InquiryController extends Controller
{
    public function byCourse(Request $request)
    {
        $courses = Course::orderBy('course_name')->get();
        $selectedCourse = null;
        $enrollments = collect();

        if ($courseId = $request->get('course_id')) {
            $selectedCourse = Course::find($courseId);
            $enrollments = CourseEnrollment::with('employee', 'todoResponses')
                ->where('course_id', $courseId)
                ->whereHas('employee', function ($q) {
                    $q->where('company_id', Auth::guard('employee')->user()->company_id);
                })
                ->paginate(20);
        }

        return view('admin.inquiries.by-course', compact('courses', 'selectedCourse', 'enrollments'));
    }

    public function byEmployee(Request $request)
    {
        $employees = Employee::where('company_id', Auth::guard('employee')->user()->company_id)
            ->orderBy('full_name')
            ->get();
        $selectedEmployee = null;
        $enrollments = collect();

        if ($employeeId = $request->get('employee_id')) {
            $selectedEmployee = Employee::find($employeeId);
            $enrollments = CourseEnrollment::with('course', 'todoResponses')
                ->where('employee_id', $employeeId)
                ->paginate(20);
        }

        return view('admin.inquiries.by-employee', compact('employees', 'selectedEmployee', 'enrollments'));
    }
}
