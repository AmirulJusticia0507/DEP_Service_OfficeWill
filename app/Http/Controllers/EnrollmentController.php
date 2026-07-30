<?php

namespace App\Http\Controllers;

use App\Helpers\NotificationHelper;
use App\Mail\CourseConfirmationMail;
use App\Models\CourseEnrollment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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

        $oldStatus = $enrollment->status;
        $enrollment->update($data);

        if ($data['status'] === 'CANCELLED' && $oldStatus !== 'CANCELLED') {
            $enrollment->load(['employee', 'course']);
            $employee = $enrollment->employee;
            if ($employee && $employee->email) {
                Mail::to($employee->email)->queue(new \App\Mail\CourseCancelledMail(
                    $employee->full_name,
                    $enrollment->course->course_name
                ));

                NotificationHelper::send(
                    $employee,
                    'course_cancelled',
                    'Course Cancelled',
                    "Your enrollment in {$enrollment->course->course_name} has been cancelled."
                );
            }
        }

        return back()->with('success', 'Status enrollment berhasil diperbarui.');
    }

    public function sendConfirmation(CourseEnrollment $enrollment): RedirectResponse
    {
        $enrollment->load(['employee', 'course']);
        $employee = $enrollment->employee;

        if ($employee && $employee->email) {
            Mail::to($employee->email)->queue(new CourseConfirmationMail(
                $employee->full_name,
                $enrollment->course->course_name,
                $enrollment->enrollment_deadline ? (string)$enrollment->enrollment_deadline : '-',
                route('attendance.show', $enrollment->course_id),
                $enrollment->status
            ));

            NotificationHelper::send(
                $employee,
                'course_confirmation',
                'Konfirmasi Kursus',
                "Email konfirmasi pendaftaran untuk kursus: {$enrollment->course->course_name} telah dikirim.",
                route('attendance.show', $enrollment->course_id)
            );

            return back()->with('success', "Email konfirmasi pendaftaran berhasil dikirim ke {$employee->email}.");
        }

        return back()->with('error', 'Gagal mengirim email: Alamat email karyawan tidak ditemukan.');
    }

    public function sendCancellation(CourseEnrollment $enrollment): RedirectResponse
    {
        $enrollment->load(['employee', 'course']);
        $employee = $enrollment->employee;

        if ($employee && $employee->email) {
            Mail::to($employee->email)->queue(new \App\Mail\CourseCancelledMail(
                $employee->full_name,
                $enrollment->course->course_name
            ));

            NotificationHelper::send(
                $employee,
                'course_cancelled',
                'Course Cancelled',
                "Your enrollment in {$enrollment->course->course_name} has been cancelled."
            );

            return back()->with('success', "Email pembatalan kursus berhasil dikirim ke {$employee->email}.");
        }

        return back()->with('error', 'Gagal mengirim email: Alamat email karyawan tidak ditemukan.');
    }

    public function destroy(CourseEnrollment $enrollment): RedirectResponse
    {
        $enrollment->load(['employee', 'course']);
        $enrollment->update(['status' => 'CANCELLED']);

        $employee = $enrollment->employee;
        if ($employee && $employee->email) {
            Mail::to($employee->email)->queue(new \App\Mail\CourseCancelledMail(
                $employee->full_name,
                $enrollment->course->course_name
            ));

            NotificationHelper::send(
                $employee,
                'course_cancelled',
                'Course Cancelled',
                "Your enrollment in {$enrollment->course->course_name} has been cancelled."
            );
        }

        return back()->with('success', 'Enrollment berhasil dibatalkan dan email pemberitahuan telah dikirim.');
    }
}
