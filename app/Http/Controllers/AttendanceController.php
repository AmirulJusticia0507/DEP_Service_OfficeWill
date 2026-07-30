<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        $employee = Auth::guard('employee')->user();

        $enrollments = CourseEnrollment::with('course.materials')
            ->where('employee_id', $employee->id)
            ->where('status', 'ENROLLED')
            ->where('enrollment_deadline', '>=', Carbon::today())
            ->paginate(10);

        return view('attendance.index', compact('enrollments'));
    }

    public function show(Course $course)
    {
        $employee = Auth::guard('employee')->user();

        $enrollment = CourseEnrollment::where('course_id', $course->id)
            ->where('employee_id', $employee->id)
            ->firstOrFail();

        $course->load('materials', 'todos');

        return view('attendance.show', compact('course', 'enrollment'));
    }

    public function complete(CourseEnrollment $enrollment): RedirectResponse
    {
        $employee = Auth::guard('employee')->user();

        if ($enrollment->employee_id !== $employee->id) {
            abort(403);
        }

        $allPassed = $enrollment->todoResponses()
            ->where('status', 'FAILED')
            ->doesntExist();

        if (!$allPassed) {
            return back()->with('error', 'Selesaikan semua todo terlebih dahulu.');
        }

        $enrollment->update(['status' => 'COMPLETED']);

        return redirect('/attendance')->with('success', 'Kursus selesai!');
    }
}
