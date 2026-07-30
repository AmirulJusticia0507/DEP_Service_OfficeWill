<?php

namespace App\Http\Controllers;

use App\Actions\Course\EvaluateTestResultAction;
use App\Models\CourseEnrollment;
use App\Models\CourseTodo;
use App\Models\CourseTodoResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $response = CourseTodoResponse::updateOrCreate(
            [
                'enrollment_id' => $enrollment->id,
                'course_todo_id' => $todo->id,
            ],
            [
                'response_content' => $request->input('response_content'),
                'status' => 'PASSED',
            ]
        );

        return back()->with('success', 'Kuesioner berhasil dikirim.');
    }

    public function submitReport(Request $request, CourseTodo $todo): RedirectResponse
    {
        $employee = Auth::guard('employee')->user();

        $enrollment = CourseEnrollment::where('course_id', $todo->course_id)
            ->where('employee_id', $employee->id)
            ->firstOrFail();

        $file = $request->file('report_file');
        $path = $file ? $file->store('reports') : null;

        $response = CourseTodoResponse::updateOrCreate(
            [
                'enrollment_id' => $enrollment->id,
                'course_todo_id' => $todo->id,
            ],
            [
                'response_content' => $path,
                'status' => 'PASSED',
            ]
        );

        return back()->with('success', 'Laporan berhasil diunggah.');
    }

    public function submitTest(Request $request, CourseTodo $todo): RedirectResponse
    {
        $employee = Auth::guard('employee')->user();

        $enrollment = CourseEnrollment::where('course_id', $todo->course_id)
            ->where('employee_id', $employee->id)
            ->firstOrFail();

        $score = (int) $request->input('score', 0);

        $response = CourseTodoResponse::firstOrCreate(
            [
                'enrollment_id' => $enrollment->id,
                'course_todo_id' => $todo->id,
            ]
        );

        $this->evaluateTest->execute($todo, $response, $score);

        $passed = $response->fresh()->status === 'PASSED';

        return back()->with(
            $passed ? 'success' : 'error',
            $passed ? 'Nilai ' . $score . ' — Lulus!' : 'Nilai ' . $score . ' — Tidak lulus.'
        );
    }
}
