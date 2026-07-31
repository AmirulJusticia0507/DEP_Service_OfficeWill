<?php

namespace App\Http\Controllers;

use App\Actions\Course\EvaluateTestResultAction;
use App\Models\CourseEnrollment;
use App\Models\CourseTodo;
use App\Models\CourseTodoResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TodoController extends Controller
{
    public function __construct(
        private EvaluateTestResultAction $evaluateTest
    ) {}

    public function submitQuestionnaire(Request $request, CourseTodo $todo): RedirectResponse
    {
        $employee = Auth::guard('employee')->user();

        $enrollment = CourseEnrollment::where('course_id', $todo->course_id)
            ->where('employee_id', $employee->id)
            ->firstOrFail();

        return DB::transaction(function () use ($request, $enrollment, $todo) {
            $locked = CourseEnrollment::whereKey($enrollment->id)->lockForUpdate()->first();

            $lockReason = $locked->submissionLockReason();
            if ($lockReason !== null) {
                return back()->with('error', $lockReason);
            }

            CourseTodoResponse::updateOrCreate(
                [
                    'enrollment_id' => $locked->id,
                    'course_todo_id' => $todo->id,
                ],
                [
                    'response_content' => $request->input('response_content'),
                    'status' => 'PASSED',
                ]
            );

            return back()->with('success', 'Kuesioner berhasil dikirim.');
        });
    }

    public function submitReport(Request $request, CourseTodo $todo): RedirectResponse
    {
        $employee = Auth::guard('employee')->user();

        $enrollment = CourseEnrollment::where('course_id', $todo->course_id)
            ->where('employee_id', $employee->id)
            ->firstOrFail();

        $request->validate([
            'report_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        $file = $request->file('report_file');
        $path = $file ? $file->store('reports') : null;

        try {
            return DB::transaction(function () use ($path, $enrollment, $todo) {
                $locked = CourseEnrollment::whereKey($enrollment->id)->lockForUpdate()->first();

                $lockReason = $locked->submissionLockReason();
                if ($lockReason !== null) {
                    return back()->with('error', $lockReason);
                }

                $previous = CourseTodoResponse::where('enrollment_id', $locked->id)
                    ->where('course_todo_id', $todo->id)
                    ->value('response_content');

                CourseTodoResponse::updateOrCreate(
                    [
                        'enrollment_id' => $locked->id,
                        'course_todo_id' => $todo->id,
                    ],
                    [
                        'response_content' => $path,
                        'status' => 'PASSED',
                    ]
                );

                if ($previous !== null && $previous !== $path) {
                    Storage::delete($previous);
                }

                return back()->with('success', 'Laporan berhasil diunggah.');
            });
        } catch (\Throwable $e) {
            if ($path !== null) {
                Storage::delete($path);
            }
            throw $e;
        }
    }

    public function submitTest(Request $request, CourseTodo $todo): RedirectResponse
    {
        $employee = Auth::guard('employee')->user();

        $enrollment = CourseEnrollment::where('course_id', $todo->course_id)
            ->where('employee_id', $employee->id)
            ->firstOrFail();

        $score = (int) $request->input('score', 0);

        return DB::transaction(function () use ($score, $enrollment, $todo) {
            $locked = CourseEnrollment::whereKey($enrollment->id)->lockForUpdate()->first();

            $lockReason = $locked->submissionLockReason();
            if ($lockReason !== null) {
                return back()->with('error', $lockReason);
            }

            $response = CourseTodoResponse::updateOrCreate(
                [
                    'enrollment_id' => $locked->id,
                    'course_todo_id' => $todo->id,
                ],
                []
            );

            $this->evaluateTest->execute($todo, $response, $score);

            $passed = $response->fresh()->status === 'PASSED';

            return back()->with(
                $passed ? 'success' : 'error',
                $passed ? 'Nilai '.$score.' — Lulus!' : 'Nilai '.$score.' — Tidak lulus.'
            );
        });
    }
}
