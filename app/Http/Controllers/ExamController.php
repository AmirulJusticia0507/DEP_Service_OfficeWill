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
use Illuminate\Support\Facades\DB;

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

        $lockReason = $enrollment->submissionLockReason();
        if ($lockReason !== null) {
            return back()->with('error', $lockReason);
        }

        $questions = Question::where('course_id', $enrollment->course_id)
            ->orderBy('display_order')
            ->get();

        if ($questions->isEmpty()) {
            return back()->with('error', 'Tidak ada soal untuk kursus ini.');
        }

        [$attempt, $error] = DB::transaction(function () use ($enrollment, $todo, $questions) {
            $locked = CourseEnrollment::whereKey($enrollment->id)->lockForUpdate()->first();

            $lockReason = $locked->submissionLockReason();
            if ($lockReason !== null) {
                return [null, $lockReason];
            }

            $existing = ExamAttempt::where('enrollment_id', $locked->id)
                ->where('course_todo_id', $todo->id)
                ->where('status', 'IN_PROGRESS')
                ->first();

            if ($existing !== null) {
                return [$existing, null];
            }

            $attemptNumber = ExamAttempt::where('enrollment_id', $locked->id)
                ->where('course_todo_id', $todo->id)
                ->max('attempt_number') ?? 0;

            $attempt = ExamAttempt::create([
                'enrollment_id' => $locked->id,
                'course_todo_id' => $todo->id,
                'attempt_number' => $attemptNumber + 1,
                'status' => 'IN_PROGRESS',
                'started_at' => now(),
                'max_score' => $questions->sum('points'),
            ]);

            return [$attempt, null];
        });

        if ($error !== null) {
            return back()->with('error', $error);
        }

        return view('attendance.exam', compact('enrollment', 'todo', 'questions', 'attempt'));
    }

    public function submit(Request $request, ExamAttempt $attempt): RedirectResponse
    {
        $employee = Auth::guard('employee')->user();
        if ($attempt->enrollment->employee_id !== $employee->id) {
            abort(403);
        }

        return DB::transaction(function () use ($request, $attempt) {
            $locked = ExamAttempt::whereKey($attempt->id)->lockForUpdate()->first();

            if ($locked->status !== 'IN_PROGRESS') {
                return back()->with('error', 'Ujian ini sudah selesai.');
            }

            $lockReason = $locked->enrollment->submissionLockReason();
            if ($lockReason !== null) {
                return back()->with('error', $lockReason);
            }

            $questions = Question::where('course_id', $locked->enrollment->course_id)->get();
            $totalScore = 0;
            $maxScore = $questions->sum('points');

            foreach ($questions as $question) {
                $answerKey = 'question_'.$question->id;
                $isCorrect = null;
                $pointsEarned = 0;
                $selectedOptionId = null;
                $essayAnswer = null;

                if ($question->question_type === 'MCQ' || $question->question_type === 'TRUE_FALSE') {
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
                    'exam_attempt_id' => $locked->id,
                    'question_id' => $question->id,
                    'selected_option_id' => $selectedOptionId,
                    'essay_answer' => $essayAnswer,
                    'is_correct' => $isCorrect,
                    'points_earned' => $pointsEarned,
                ]);

                $totalScore += $pointsEarned;
            }

            $hasEssay = $questions->contains('question_type', 'ESSAY');

            $locked->update([
                'total_score' => $totalScore,
                'max_score' => $maxScore,
                'status' => 'COMPLETED',
                'completed_at' => now(),
            ]);

            if (! $hasEssay) {
                $todo = $locked->courseTodo;
                $passed = $totalScore >= ($todo->passing_score ?? $maxScore / 2);
                $msg = $passed
                    ? 'Nilai: '.$totalScore.'/'.$maxScore.' — '.__('Pass')
                    : 'Nilai: '.$totalScore.'/'.$maxScore.' — '.__('Fail');

                $response = $locked->enrollment->todoResponses()->updateOrCreate(
                    ['course_todo_id' => $todo->id],
                    [
                        'score' => $totalScore,
                        'status' => $passed ? 'PASSED' : 'FAILED',
                    ]
                );

                return redirect()->route('attendance.score', $locked->enrollment)
                    ->with($passed ? 'success' : 'error', $msg);
            }

            return redirect()->route('attendance.score', $locked->enrollment)
                ->with('success', 'Jawaban terkumpul. Menunggu penilaian.');
        });
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

        return DB::transaction(function () use ($data, $attempt) {
            $locked = ExamAttempt::whereKey($attempt->id)->lockForUpdate()->first();

            $totalScore = 0;
            foreach ($data['scores'] as $answerId => $score) {
                $answer = ExamAnswer::findOrFail($answerId);
                $answer->update([
                    'points_earned' => $score,
                    'is_correct' => $score > 0,
                ]);
                $totalScore += $score;
            }

            $todo = $locked->courseTodo;
            $passed = $totalScore >= ($todo->passing_score ?? $locked->max_score / 2);

            $locked->update([
                'total_score' => $totalScore,
                'status' => 'COMPLETED',
                'completed_at' => now(),
            ]);

            $locked->enrollment->todoResponses()->updateOrCreate(
                ['course_todo_id' => $todo->id],
                [
                    'score' => $totalScore,
                    'status' => $passed ? 'PASSED' : 'FAILED',
                ]
            );

            return back()->with('success', 'Nilai berhasil diperbarui.');
        });
    }
}
