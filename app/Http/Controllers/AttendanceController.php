<?php

namespace App\Http\Controllers;

use App\Helpers\NotificationHelper;
use App\Mail\CourseCompletedMail;
use App\Models\Attendance;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\ExamAttempt;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $attempts = ExamAttempt::where('enrollment_id', $enrollment->id)
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

        if (! $allPassed) {
            return back()->with('error', 'Selesaikan semua todo terlebih dahulu.');
        }

        // Business lock: tidak bisa diselesaikan setelah deadline (edit after lock).
        if ($enrollment->enrollment_deadline !== null
            && Carbon::parse($enrollment->enrollment_deadline)->lt(Carbon::today())) {
            return back()->with('error', 'Deadline kursus telah lewat. Hubungi admin untuk perpanjangan.');
        }

        // Idempotency fast-fail: sudah pernah diselesaikan.
        if ($this->alreadyCompleted($enrollment, $employee->id)) {
            return redirect('/attendance')->with('info', 'Kursus ini sudah selesai dikerjakan.');
        }

        $certNumber = 'OW-YOG-'.str_pad($enrollment->id, 5, '0', STR_PAD_LEFT).'-'.now()->format('Ymd');
        $filename = 'certificates/'.$certNumber.'.pdf';

        try {
            $completed = DB::transaction(function () use ($enrollment, $employee, $certNumber, $filename) {
                $locked = CourseEnrollment::whereKey($enrollment->id)->lockForUpdate()->first();

                if ($this->alreadyCompleted($locked, $employee->id)) {
                    return false;
                }

                // Satu attendance per karyawan per enrollment (UNIQUE(employee_id, enrollment_id)).
                Attendance::create([
                    'employee_id' => $employee->id,
                    'enrollment_id' => $locked->id,
                    'course_id' => $locked->course_id,
                    'status' => 'COMPLETED',
                    'attended_at' => now(),
                ]);

                $locked->update(['status' => 'COMPLETED']);

                $existing = Certificate::where('enrollment_id', $locked->id)->first();
                if (! $existing) {
                    $pdf = Pdf::loadView('certificates.template', [
                        'employee' => $employee,
                        'course' => $locked->course,
                        'certificate_number' => $certNumber,
                        'issued_at' => now(),
                    ]);
                    Storage::disk('public')->put($filename, $pdf->output());

                    Certificate::create([
                        'enrollment_id' => $locked->id,
                        'employee_id' => $employee->id,
                        'course_id' => $locked->course_id,
                        'certificate_number' => $certNumber,
                        'file_path' => $filename,
                        'issued_at' => now(),
                    ]);
                }

                return true;
            });
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($filename);
            throw $e;
        }

        if (! $completed) {
            return redirect('/attendance')->with('info', 'Kursus ini sudah selesai dikerjakan.');
        }

        $certificate = Certificate::where('enrollment_id', $enrollment->id)->first();

        // Send in-app notification
        NotificationHelper::send(
            $employee,
            'course_completed',
            'Course Completed',
            "Congratulations! You completed: {$enrollment->course->course_name}",
            route('profile.certificates')
        );

        // Send email
        try {
            Mail::to($employee->email)->queue(new CourseCompletedMail(
                $employee->full_name,
                $enrollment->course->course_name,
                $certificate ? route('certificates.download', $certificate) : route('profile.certificates'),
            ));
        } catch (\Exception $e) {
            // email failed silently
        }

        return redirect('/attendance')->with('success', 'Kursus selesai! Sertifikat telah diterbitkan.');
    }

    private function alreadyCompleted(CourseEnrollment $enrollment, int $employeeId): bool
    {
        return Attendance::where('employee_id', $employeeId)
            ->where('enrollment_id', $enrollment->id)
            ->exists()
            || $enrollment->status === 'COMPLETED';
    }
}
