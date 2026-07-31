<?php

namespace App\Http\Controllers;

use App\Models\CourseEnrollment;
use App\Models\CourseTodo;
use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    public function start(CourseEnrollment $enrollment, CourseTodo $todo)
    {
        $employee = Auth::guard('employee')->user();
        if ($enrollment->employee_id !== $employee->id) {
            abort(403);
        }
        if ($todo->course_id !== $enrollment->course_id) {
            abort(404);
        }

        $questions = Question::where('course_id', $enrollment->course_id)
            ->orderBy('display_order')
            ->get();

        if ($questions->isEmpty()) {
            return back()->with('error', 'Tidak ada soal untuk kursus ini.');
        }

        $attemptNumber = ExamAttempt::where('enrollment_id', $enrollment->id)
            ->where('course_todo_id', $todo->id)
            ->max('attempt_number') ?? 0;

        $attempt = ExamAttempt::create([
            'enrollment_id' => $enrollment->id,
            'course_todo_id' => $todo->id,
            'attempt_number' => $attemptNumber + 1,
            'status' => 'IN_PROGRESS',
            'started_at' => now(),
            'max_score' => $questions->sum('points'),
        ]);

        return view('attendance.exam', compact('enrollment', 'todo', 'questions', 'attempt'));
    }

    public function submit(Request $request, ExamAttempt $attempt): RedirectResponse
    {
        $employee = Auth::guard('employee')->user();
        if ($attempt->enrollment->employee_id !== $employee->id) {
            abort(403);
        }
        if ($attempt->status !== 'IN_PROGRESS') {
            return back()->with('error', 'Ujian ini sudah selesai.');
        }

        $questions = Question::where('course_id', $attempt->enrollment->course_id)->get();
        $totalScore = 0;
        $maxScore = $questions->sum('points');

        foreach ($questions as $question) {
            $answerKey = 'question_'.$question->id;
            $isCorrect = null;
            $pointsEarned = 0;
            $selectedOptionId = null;
            $essayAnswer = null;

            if ($question->question_type === 'MCQ') {
                $selectedOptionId = $request->input($answerKey);
                $option = $question->options()->find($selectedOptionId);
                $isCorrect = $option && $option->is_correct;
                $pointsEarned = $isCorrect ? $question->points : 0;
            } elseif ($question->question_type === 'TRUE_FALSE') {
                $selectedOptionId = $request->input($answerKey);
                $option = $question->options()->find($selectedOptionId);
                $isCorrect = $option && $option->is_correct;
                $pointsEarned = $isCorrect ? $question->points : 0;
            } elseif ($question->question_type === 'ESSAY') {
                $essayAnswer = $request->input($answerKey);
                $isCorrect = null; // manual grading
                $pointsEarned = 0;
            }

            ExamAnswer::create([
                'exam_attempt_id' => $attempt->id,
                'question_id' => $question->id,
                'selected_option_id' => $selectedOptionId,
                'essay_answer' => $essayAnswer,
                'is_correct' => $isCorrect,
                'points_earned' => $pointsEarned,
            ]);

            $totalScore += $pointsEarned;
        }

        $hasEssay = $questions->contains('question_type', 'ESSAY');
        $status = $hasEssay ? 'COMPLETED' : 'COMPLETED';

        $attempt->update([
            'total_score' => $totalScore,
            'max_score' => $maxScore,
            'status' => $status,
            'completed_at' => now(),
        ]);

        // Auto-grade: if no essay, check pass
        if (! $hasEssay) {
            $todo = $attempt->courseTodo;
            $passed = $totalScore >= ($todo->passing_score ?? $maxScore / 2);
            $msg = $passed
                ? 'Nilai: '.$totalScore.'/'.$maxScore.' — '.__('Pass')
                : 'Nilai: '.$totalScore.'/'.$maxScore.' — '.__('Fail');

            $response = $attempt->enrollment->todoResponses()
                ->where('course_todo_id', $todo->id)
                ->first();

            if ($response) {
                $response->update([
                    'score' => $totalScore,
                    'status' => $passed ? 'PASSED' : 'FAILED',
                ]);
            }

            return redirect()->route('attendance.score', $attempt->enrollment)
                ->with($passed ? 'success' : 'error', $msg);
        }

        return redirect()->route('attendance.score', $attempt->enrollment)
            ->with('success', 'Jawaban terkumpul. Menunggu penilaian.');
    }

    public function grade(Request $request, ExamAttempt $attempt): RedirectResponse
    {
        $employee = Auth::guard('employee')->user();
        if (! $employee->hasPermission('can_register_course')) {
            abort(403);
        }

        $data = $request->validate([
            'scores' => 'required|array',
            'scores.*' => 'integer|min:0',
        ]);

        $totalScore = 0;
        foreach ($data['scores'] as $answerId => $score) {
            $answer = ExamAnswer::findOrFail($answerId);
            $answer->update([
                'points_earned' => $score,
                'is_correct' => $score > 0,
            ]);
            $totalScore += $score;
        }

        $todo = $attempt->courseTodo;
        $passed = $totalScore >= ($todo->passing_score ?? $attempt->max_score / 2);

        $attempt->update([
            'total_score' => $totalScore,
            'status' => 'COMPLETED',
            'completed_at' => now(),
        ]);

        $response = $attempt->enrollment->todoResponses()
            ->where('course_todo_id', $todo->id)
            ->first();

        if ($response) {
            $response->update([
                'score' => $totalScore,
                'status' => $passed ? 'PASSED' : 'FAILED',
            ]);
        }

        return back()->with('success', 'Nilai berhasil diperbarui.');
    }
}
