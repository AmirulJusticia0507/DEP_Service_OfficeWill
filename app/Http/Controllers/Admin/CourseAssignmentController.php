<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Course\AssignCourseToEmployeesAction;
use App\Http\Controllers\Controller;
use App\Mail\CourseAssignedMail;
use App\Mail\CourseCancelledMail;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CourseAssignmentController extends Controller
{
    public function __construct(
        private AssignCourseToEmployeesAction $assignAction
    ) {}

    public function create()
    {
        $courses = Course::orderBy('course_name')->get();
        $employees = Employee::where('company_id', Auth::guard('employee')->user()->company_id)
            ->where('account_status', 'ACTIVE')
            ->orderBy('full_name')
            ->get();

        return view('admin.assignments.create', compact('courses', 'employees'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
            'enrollment_deadline' => 'required|date|after:today',
        ]);

        $course = Course::findOrFail($data['course_id']);

        $count = $this->assignAction->execute(
            $course,
            $data['employee_ids'],
            $data['enrollment_deadline']
        );

        $employees = Employee::whereIn('id', $data['employee_ids'])->get();
        foreach ($employees as $employee) {
            Mail::to($employee->email)->queue(new CourseAssignedMail(
                $employee->full_name,
                $course->course_name,
                $data['enrollment_deadline'],
                config('app.url') . '/attendance/' . $course->id,
            ));
        }

        return redirect('/admin/assignments')->with('success', "Kursus ditugaskan ke {$count} karyawan.");
    }

    public function index()
    {
        $assignments = CourseEnrollment::with('course', 'employee')
            ->whereHas('employee', function ($q) {
                $q->where('company_id', Auth::guard('employee')->user()->company_id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.assignments.index', compact('assignments'));
    }

    public function cancel(CourseEnrollment $enrollment): RedirectResponse
    {
        $employee = $enrollment->employee;

        $enrollment->update(['status' => 'CANCELLED']);

        Mail::to($employee->email)->queue(new CourseCancelledMail(
            $employee->full_name,
            $enrollment->course->course_name,
        ));

        return back()->with('success', 'Enrollment berhasil dibatalkan.');
    }
}
