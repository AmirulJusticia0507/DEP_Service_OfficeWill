<?php

namespace App\Actions\Course;

use App\Models\CourseTodo;
use App\Models\CourseTodoResponse;

class EvaluateTestResultAction
{
    public function execute(CourseTodo $todo, CourseTodoResponse $response, int $score): CourseTodoResponse
    {
        $passingScore = $todo->passing_score ?? $todo->course->passing_score ?? 70;

        $response->update([
            'score' => $score,
            'status' => $score >= $passingScore ? 'PASSED' : 'FAILED',
        ]);

        return $response->fresh();
    }
}
