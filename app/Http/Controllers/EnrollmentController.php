<?php

namespace App\Http\Controllers;

use App\Models\CourseEnrollment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $query = CourseEnrollment::with('course', 'employee');

        if ($courseId = $request->get('course_id')) {
            $query->where('course_id', $courseId);
        }

        if ($employeeId = $request->get('employee_id')) {
            $query->where('employee_id', $employeeId);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $enrollments = $query->paginate(20)->withQueryString();

        return view('enrollments.index', compact('enrollments'));
    }

    public function update(Request $request, CourseEnrollment $enrollment): RedirectResponse
    {
        $data = $request->validate([
            'status' => 'required|in:ENROLLED,COMPLETED,CANCELLED',
            'enrollment_deadline' => 'nullable|date',
        ]);

        $enrollment->update($data);

        return back()->with('success', 'Status enrollment berhasil diperbarui.');
    }

    public function destroy(CourseEnrollment $enrollment): RedirectResponse
    {
        $enrollment->update(['status' => 'CANCELLED']);

        return back()->with('success', 'Enrollment berhasil dibatalkan.');
    }
}
