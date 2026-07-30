<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\ExamAttempt;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamReportController extends Controller
{
    public function byCourse(Request $request)
    {
        $operator = Auth::guard('employee')->user();

        $courses = Course::whereHas('questions')->orderBy('course_name')->get();

        $selectedCourse = null;
        $attempts = collect();

        if ($courseId = $request->get('course_id')) {
            $selectedCourse = Course::with('questions')->findOrFail($courseId);

            $attempts = ExamAttempt::with([
                'enrollment.employee',
                'enrollment.course',
                'answers.question',
                'courseTodo',
            ])
                ->whereHas('enrollment', function ($q) use ($operator, $courseId) {
                    $q->where('course_id', $courseId)
                      ->whereHas('employee', fn($eq) => $eq->where('company_id', $operator->company_id));
                })
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy(fn($a) => $a->enrollment->employee->full_name . ' (#' . $a->enrollment->employee_id . ')');
        }

        return view('admin.exam-reports.by-course', compact('courses', 'selectedCourse', 'attempts'));
    }

    public function byEmployee(Request $request)
    {
        $operator = Auth::guard('employee')->user();
        $companyId = $operator->company_id;

        $employees = Employee::where('company_id', $companyId)
            ->whereHas('enrollments', fn($q) => $q->whereHas('course.questions'))
            ->orderBy('full_name')
            ->get();

        $selectedEmployee = null;
        $attempts = collect();

        if ($employeeId = $request->get('employee_id')) {
            $selectedEmployee = Employee::findOrFail($employeeId);

            $attempts = ExamAttempt::with([
                'enrollment.course',
                'answers.question',
                'courseTodo',
            ])
                ->whereHas('enrollment', fn($q) => $q->where('employee_id', $employeeId))
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy(fn($a) => $a->enrollment->course->course_name);
        }

        return view('admin.exam-reports.by-employee', compact('employees', 'selectedEmployee', 'attempts'));
    }
}
