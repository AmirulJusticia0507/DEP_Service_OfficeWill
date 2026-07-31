<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Course\AssignCourseToEmployeesAction;
use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Mail\CourseCancelledMail;
use App\Mail\CourseConfirmationMail;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Employee;
use App\Services\ActivityLogger;
use App\Services\ScopeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CourseAssignmentController extends Controller
{
    public function __construct(
        private AssignCourseToEmployeesAction $assignAction,
        private ScopeService $scope
    ) {}

    public function create()
    {
        $operator = Auth::guard('employee')->user();

        $courses = Course::orderBy('course_name')->get();
        $employees = $this->scope->scopeEmployeeQuery(
            Employee::where('company_id', $operator->company_id)
                ->where('account_status', 'ACTIVE'),
            $operator
        )->orderBy('full_name')->get();

        return view('admin.assignments.create', compact('courses', 'employees'));
    }

    public function store(Request $request): RedirectResponse
    {
        $operator = Auth::guard('employee')->user();

        $data = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
            'enrollment_deadline' => 'required|date|after:today',
        ]);

        $targets = Employee::whereIn('id', $data['employee_ids'])->get();

        abort_unless($targets->count() === count($data['employee_ids']), 422);

        foreach ($targets as $target) {
            abort_unless($this->scope->canAccessEmployee($operator, $target), 403);
        }

        $course = Course::findOrFail($data['course_id']);

        $count = $this->assignAction->execute(
            $course,
            $targets->pluck('id')->all(),
            $data['enrollment_deadline']
        );

        ActivityLogger::log('course_assignment', "Assigned course {$course->course_name} to {$count} employees (deadline {$data['enrollment_deadline']}).", $course, [
            'employee_ids' => $targets->pluck('id')->all(),
            'count' => $count,
            'enrollment_deadline' => $data['enrollment_deadline'],
        ]);

        foreach ($targets as $employee) {
            Mail::to($employee->email)->queue(new CourseConfirmationMail(
                $employee->full_name,
                $course->course_name,
                $data['enrollment_deadline'],
                route('attendance.show', $course->id),
                'TERKONFIRMASI'
            ));

            NotificationHelper::send(
                $employee,
                'course_assigned',
                'Course Assigned',
                "You have been assigned to course: {$course->course_name}",
                route('attendance.show', $course)
            );
        }

        return redirect('/admin/assignments')->with('success', "Kursus ditugaskan ke {$count} karyawan.");
    }

    public function index()
    {
        $operator = Auth::guard('employee')->user();

        $assignments = $this->scope->scopeEnrollmentQuery(
            CourseEnrollment::with('course', 'employee'),
            $operator
        )->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.assignments.index', compact('assignments'));
    }

    public function cancel(CourseEnrollment $enrollment): RedirectResponse
    {
        $operator = Auth::guard('employee')->user();

        abort_unless($this->scope->canAccessEnrollment($operator, $enrollment), 403);

        $employee = $enrollment->employee;

        $enrollment->update(['status' => 'CANCELLED']);

        ActivityLogger::log('assignment_cancel', "Cancelled enrollment of {$employee->full_name} in {$enrollment->course->course_name}.", $enrollment);

        Mail::to($employee->email)->queue(new CourseCancelledMail(
            $employee->full_name,
            $enrollment->course->course_name,
        ));

        NotificationHelper::send(
            $employee,
            'course_cancelled',
            'Course Cancelled',
            "Your enrollment in {$enrollment->course->course_name} has been cancelled.",
        );

        return back()->with('success', 'Enrollment berhasil dibatalkan.');
    }
}
