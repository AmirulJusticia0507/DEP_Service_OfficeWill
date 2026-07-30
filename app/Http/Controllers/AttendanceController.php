<?php

namespace App\Http\Controllers;

use App\Helpers\NotificationHelper;
use App\Mail\CourseCompletedMail;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseEnrollment;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    public function index()
    {
        $employee = Auth::guard('employee')->user();

        $enrollments = CourseEnrollment::with('course.materials', 'course.todos', 'course.categoryDetail', 'todoResponses')
            ->where('employee_id', $employee->id)
            ->where('status', 'ENROLLED')
            ->where('enrollment_deadline', '>=', Carbon::today())
            ->paginate(10)->withQueryString();

        return view('attendance.index', compact('enrollments'));
    }

    public function show(Course $course)
    {
        $employee = Auth::guard('employee')->user();

        $enrollment = CourseEnrollment::where('course_id', $course->id)
            ->where('employee_id', $employee->id)
            ->firstOrFail();

        $course->load('materials', 'todos', 'questions');

        return view('attendance.show', compact('course', 'enrollment'));
    }

    public function todos(CourseEnrollment $enrollment)
    {
        $employee = Auth::guard('employee')->user();

        if ($enrollment->employee_id !== $employee->id) {
            abort(403);
        }

        $enrollment->load('course.materials', 'course.todos', 'course.questions', 'todoResponses');

        return view('attendance.todos', compact('enrollment'));
    }

    public function score(CourseEnrollment $enrollment)
    {
        $employee = Auth::guard('employee')->user();

        if ($enrollment->employee_id !== $employee->id) {
            abort(403);
        }

        $enrollment->load('course.todos', 'todoResponses.courseTodo');

        // Load exam attempts for each todo
        $examAttempts = $enrollment->load('course.todos');
        $attempts = \App\Models\ExamAttempt::where('enrollment_id', $enrollment->id)
            ->with('answers.question')
            ->get()
            ->keyBy('course_todo_id');

        return view('attendance.score', compact('enrollment', 'attempts'));
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

        // Send in-app notification
        NotificationHelper::send(
            $employee,
            'course_completed',
            'Course Completed',
            "Congratulations! You completed: {$enrollment->course->course_name}",
            route('profile.certificates')
        );

        // Auto-generate certificate
        $certUrl = null;
        $existing = Certificate::where('enrollment_id', $enrollment->id)->first();
        if (!$existing) {
            $course = $enrollment->course;
            $certNumber = 'OW-YOG-' . str_pad($enrollment->id, 5, '0', STR_PAD_LEFT) . '-' . now()->format('Ymd');

            try {
                $pdf = Pdf::loadView('certificates.template', [
                    'employee' => $employee,
                    'course' => $course,
                    'certificate_number' => $certNumber,
                    'issued_at' => now(),
                ]);

                $filename = 'certificates/' . $certNumber . '.pdf';
                Storage::disk('public')->put($filename, $pdf->output());

                Certificate::create([
                    'enrollment_id' => $enrollment->id,
                    'employee_id' => $employee->id,
                    'course_id' => $course->id,
                    'certificate_number' => $certNumber,
                    'file_path' => $filename,
                    'issued_at' => now(),
                ]);

                $certUrl = route('certificates.download', Certificate::where('enrollment_id', $enrollment->id)->first());
            } catch (\Exception $e) {
                // PDF generation failed silently
            }
        } else {
            $certUrl = route('certificates.download', $existing);
        }

        // Send email
        try {
            Mail::to($employee->email)->queue(new CourseCompletedMail(
                $employee->full_name,
                $enrollment->course->course_name,
                $certUrl ?? route('profile.certificates'),
            ));
        } catch (\Exception $e) {
            // email failed silently
        }

        return redirect('/attendance')->with('success', 'Kursus selesai! Sertifikat telah diterbitkan.');
    }
}
